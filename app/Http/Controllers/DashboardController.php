<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard', [
            'totalEmployees' => Employee::count(),
            'activeEmployees' => Employee::where('employment_status', 'active')->count(),
            'departments' => Department::withCount('employees')->orderBy('name')->get(),
        ]);
    }
}
