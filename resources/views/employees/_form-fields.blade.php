@php
    use App\Enums\EmploymentStatus;
@endphp

<div class="grid gap-5 md:grid-cols-2">
    <x-form-input name="name" label="Full Name" :value="$employee?->name" required />
    <x-form-select name="department_id" label="Department" :options="$departments" :selected="$employee?->department_id" placeholder="Select department" />
    <x-form-input name="phone" label="Phone" :value="$employee?->phone" />
    <x-form-select name="gender" label="Gender" :options="['male' => 'Male', 'female' => 'Female', 'other' => 'Other']" :selected="$employee?->gender" placeholder="Select gender" />
    <x-form-input name="date_of_birth" label="Date of Birth" type="date" :value="$employee?->date_of_birth?->format('Y-m-d')" />
    <x-form-input name="joining_date" label="Joining Date" type="date" :value="$employee?->joining_date?->format('Y-m-d')" required />
    <x-form-input name="position" label="Position" :value="$employee?->position" placeholder="e.g. Harvester" />
    <x-form-select name="employment_status" label="Employment Status" :options="EmploymentStatus::options()" :selected="$employee?->employment_status?->value ?? EmploymentStatus::Active->value" required />
    <x-form-input name="wage_amount" label="Monthly Wage (MMK)" type="number" step="0.01" :value="$employee?->wage_amount" required />
    <x-form-input name="allowed_days_per_month" label="Holiday Days Per Month" type="number" :value="$employee?->holiday?->allowed_days_per_month ?? 2" required />
    <x-form-input name="emergency_contact" label="Emergency Contact" :value="$employee?->emergency_contact" />
</div>

<x-form-textarea name="address" label="Address" :value="$employee?->address" class="mt-5" />

@if ($employee)
    <div class="mt-5 rounded-lg border border-amber-200 bg-amber-50 p-4">
        <x-form-input name="salary_change_reason" label="Reason for Wage Change (required if wage changes)" :value="old('salary_change_reason')" />
        <p class="mt-2 text-sm text-amber-800">If you change the monthly wage, please explain why. A salary history record will be saved automatically.</p>
    </div>
@endif

<div class="mt-6 flex gap-3">
    <x-button type="submit">{{ $employee ? 'Save Changes' : 'Add Employee' }}</x-button>
    <x-button href="{{ $employee ? route('employees.show', $employee) : route('employees.index') }}" variant="secondary">Cancel</x-button>
</div>
