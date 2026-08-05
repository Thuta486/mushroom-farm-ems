@php
    use App\Enums\EmploymentStatus;
@endphp

<div class="grid gap-5 md:grid-cols-2">
    <x-form-input name="name_en" label="{{ __('app.employees.full_name_en') }}" :value="$employee?->name_en ?? old('name_en')" required />
    <x-form-input name="name_my" label="{{ __('app.employees.full_name_my') }}" :value="$employee?->name_my ?? old('name_my')" required/>
    <x-form-select name="department_id" label="{{ __('app.common.department') }}" :options="$departments" :selected="$employee?->department_id" placeholder="{{ __('app.common.select_department') }}" />
    <x-form-input name="phone" label="{{ __('app.common.phone') }}" :value="$employee?->phone" />
    <x-form-select name="gender" label="{{ __('app.employees.gender') }}" :options="['male' => __('app.employees.male'), 'female' => __('app.employees.female'), 'other' => __('app.employees.other')]" :selected="$employee?->gender" placeholder="{{ __('app.common.select_gender') }}" />
    <x-form-input name="date_of_birth" label="{{ __('app.employees.date_of_birth') }}" type="date" :value="$employee?->date_of_birth?->format('Y-m-d')" />
    <x-form-input name="age" label="{{ __('app.employees.age') }}" type="number" :value="$employee?->age" required />
    <x-form-input name="joining_date" label="{{ __('app.employees.joining_date') }}" type="date" :value="$employee?->joining_date?->format('Y-m-d')" required />
    <x-form-input name="position_en" label="{{ __('app.employees.position_en') }}" :value="$employee?->position_en ?? old('position_en')" />
    <x-form-input name="position_my" label="{{ __('app.employees.position_my') }}" :value="$employee?->position_my ?? old('position_my')" />
    <x-form-select name="employment_status" label="{{ __('app.employees.employment_status') }}" :options="EmploymentStatus::options()" :selected="$employee?->employment_status?->value ?? EmploymentStatus::Active->value" required />
    <x-form-input name="wage_amount" label="{{ __('app.employees.monthly_wage_mmk') }}" type="number" step="0.01" :value="$employee?->wage_amount" required />
    <x-form-input name="allowed_days_per_month" label="{{ __('app.employees.holiday_days_per_month') }}" type="number" :value="$employee?->holiday?->allowed_days_per_month ?? 2" required />
    <x-form-input name="emergency_contact" label="{{ __('app.employees.emergency_contact') }}" :value="$employee?->emergency_contact" />
</div>

<x-form-textarea name="address" label="{{ __('app.employees.address') }}" :value="$employee?->address" class="mt-5" />

@if ($employee)
    <div class="mt-5 rounded-lg border border-amber-200 bg-amber-50 p-4 dark:border-amber-700 dark:bg-amber-950">
        <x-form-input name="salary_change_reason" label="{{ __('app.employees.reason_for_wage_change') }}" :value="old('salary_change_reason')" />
        <p class="mt-2 text-sm text-amber-800 dark:text-amber-200">{{ __('app.employees.reason_for_wage_change_help') }}</p>
    </div>
@endif

<div class="mt-6 flex gap-3">
    <x-button type="submit">{{ $employee ? __('app.employees.save_changes') : __('app.employees.add_employee') }}</x-button>
    <x-button href="{{ $employee ? route('employees.show', $employee) : route('employees.index') }}" variant="secondary">{{ __('app.common.cancel') }}</x-button>
</div>
