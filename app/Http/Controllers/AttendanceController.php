<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceStatus;
use App\Enums\EmploymentStatus;
use App\Http\Requests\StoreDailyAttendanceRequest;
use App\Http\Requests\UpdateAttendanceRequest;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $attendances = Attendance::query()
            ->with(['employee.department'])
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $query->whereDate('date', '>=', $request->string('date_from'));
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $query->whereDate('date', '<=', $request->string('date_to'));
            })
            ->when($request->filled('employee_id'), function ($query) use ($request) {
                $query->where('employee_id', $request->integer('employee_id'));
            })
            ->when($request->filled('department_id'), function ($query) use ($request) {
                $query->whereHas('employee', function ($employeeQuery) use ($request) {
                    $employeeQuery->where('department_id', $request->integer('department_id'));
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->string('status'));
            })
            ->orderByDesc('date')
            ->orderBy(
                Employee::select('name_en')
                    ->whereColumn('employees.id', 'attendances.employee_id')
                    ->limit(1),
            )
            ->paginate(20)
            ->withQueryString();

        return view('attendances.index', [
            'attendances' => $attendances,
            'employees' => Employee::orderBy('name_en')->get()->pluck('display_name', 'id'),
            'departments' => Department::orderBy('name_en')->get()->pluck('display_name', 'id'),
        ]);
    }

    public function daily(Request $request): View
    {
        $date = Carbon::parse($request->input('date', now()->toDateString()))->startOfDay();

        $employees = Employee::query()
            ->with('department')
            ->where('employment_status', EmploymentStatus::Active)
            ->whereDate('joining_date', '<=', $date)
            ->orderBy('name_en')
            ->get();

        $existingAttendances = Attendance::query()
            ->whereDate('date', $date)
            ->whereIn('employee_id', $employees->pluck('id'))
            ->get()
            ->keyBy('employee_id');

        return view('attendances.daily', [
            'date' => $date,
            'employees' => $employees,
            'existingAttendances' => $existingAttendances,
            'previousDate' => $date->copy()->subDay()->toDateString(),
            'nextDate' => $date->copy()->addDay()->toDateString(),
        ]);
    }

    public function storeDaily(StoreDailyAttendanceRequest $request): RedirectResponse
    {
        $date = $request->date('date')->toDateString();

        DB::transaction(function () use ($request, $date): void {
            foreach ($request->input('attendances', []) as $row) {
                $attributes = [
                    'status' => $row['status'],
                    'hours_worked' => $row['status'] === AttendanceStatus::Present->value
                        ? (int) $row['hours_worked']
                        : 0,
                    'minutes_worked' => $row['status'] === AttendanceStatus::Present->value
                        ? (int) $row['minutes_worked']
                        : 0,
                    'notes' => $row['notes'] ?? null,
                ];

                $attendance = Attendance::query()
                    ->where('employee_id', $row['employee_id'])
                    ->whereDate('date', $date)
                    ->first();

                if ($attendance) {
                    $attendance->update($attributes);
                } else {
                    Attendance::create([
                        'employee_id' => $row['employee_id'],
                        'date' => $date,
                        ...$attributes,
                    ]);
                }
            }
        });

        return redirect()
            ->route('attendances.daily', ['date' => $date])
            ->with('success', __('app.flash.attendance_saved', ['date' => $request->date('date')->format('d M Y')]));
    }

    public function edit(Attendance $attendance): View
    {
        $attendance->load('employee.department');

        return view('attendances.edit', [
            'attendance' => $attendance,
        ]);
    }

    public function update(UpdateAttendanceRequest $request, Attendance $attendance): RedirectResponse
    {
        $data = $request->validated();

        if ($data['status'] === AttendanceStatus::Absent->value) {
            $data['hours_worked'] = 0;
            $data['minutes_worked'] = 0;
        }

        $attendance->update($data);

        return redirect()
            ->route('attendances.index', [
                'date_from' => $attendance->date->toDateString(),
                'date_to' => $attendance->date->toDateString(),
            ])
            ->with('success', __('app.flash.attendance_updated'));
    }

    public function destroy(Attendance $attendance): RedirectResponse
    {
        $date = $attendance->date->toDateString();
        $attendance->delete();

        return redirect()
            ->route('attendances.index', [
                'date_from' => $date,
                'date_to' => $date,
            ])
            ->with('success', __('app.flash.attendance_removed'));
    }
}
