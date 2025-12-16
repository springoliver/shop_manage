@section('page_header', 'Employee hours')

<x-storeowner-app-layout>
    <!-- Breadcrumb -->
    <div class="mb-6">
        <nav class="flex text-gray-500 text-sm" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('storeowner.dashboard') }}" class="inline-flex items-center hover:text-gray-700">
                        <i class="fas fa-home mr-2"></i>
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <a href="{{ route('storeowner.clocktime.index') }}" class="ml-1 hover:text-gray-700">Clock-in-out</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Employee hours</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Success/Error Messages -->
    @if (session('success'))
        <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded relative" role="alert">
            <button type="button" class="absolute top-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">&times;</button>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded relative" role="alert">
            <button type="button" class="absolute top-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">&times;</button>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Form -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
                Employee - {{ $empPayrollHrs->firstname ?? 'N/A' }} {{ $empPayrollHrs->lastname ?? '' }}
            </h3>
        </div>
        
        <form action="{{ route('storeowner.clocktime.update-employee-hours') }}" method="POST" id="editEmployeeHoursForm" class="p-6">
            @csrf
            
            <div class="space-y-6">
                <!-- Notes -->
                <div class="flex items-start gap-4">
                    <label for="notes" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Notes
                    </label>
                    <div class="w-3/4">
                        <input type="text" 
                               name="notes" 
                               id="notes" 
                               value="{{ old('notes', $empPayrollHrs->notes ?? '') }}" 
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Sunday hrs -->
                <div class="flex items-start gap-4">
                    <label for="sunday_hrs" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Sunday hrs
                    </label>
                    <div class="w-3/4">
                        <input type="number" 
                               step="0.01" 
                               name="sunday_hrs" 
                               id="sunday_hrs" 
                               value="{{ old('sunday_hrs', $empPayrollHrs->sunday_hrs ?? 0) }}" 
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('sunday_hrs')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Overtime 1 -->
                <div class="flex items-start gap-4">
                    <label for="owertime1_hrs" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Overtime
                    </label>
                    <div class="w-3/4">
                        <input type="number" 
                               step="0.01" 
                               name="owertime1_hrs" 
                               id="owertime1_hrs" 
                               value="{{ old('owertime1_hrs', $empPayrollHrs->owertime1_hrs ?? 0) }}" 
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('owertime1_hrs')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Overtime 2 -->
                <div class="flex items-start gap-4">
                    <label for="owertime2_hrs" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Overtime
                    </label>
                    <div class="w-3/4">
                        <input type="number" 
                               step="0.01" 
                               name="owertime2_hrs" 
                               id="owertime2_hrs" 
                               value="{{ old('owertime2_hrs', $empPayrollHrs->owertime2_hrs ?? 0) }}" 
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('owertime2_hrs')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Holiday (Hrs) -->
                <div class="flex items-start gap-4">
                    <label for="holiday_hrs" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Holiday(Hrs)
                    </label>
                    <div class="w-3/4">
                        <input type="number" 
                               step="0.01" 
                               name="holiday_hrs" 
                               id="holiday_hrs" 
                               value="{{ old('holiday_hrs', $empPayrollHrs->holiday_hrs ?? 0) }}" 
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('holiday_hrs')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Holiday (Days) -->
                <div class="flex items-start gap-4">
                    <label for="holiday_days" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Holiday(Days)
                    </label>
                    <div class="w-3/4">
                        <input type="number" 
                               step="0.01" 
                               name="holiday_days" 
                               id="holiday_days" 
                               value="{{ old('holiday_days', $empPayrollHrs->holiday_days ?? 0) }}" 
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('holiday_days')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Sickpay hrs -->
                <div class="flex items-start gap-4">
                    <label for="sickpay_hrs" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Sickpay hrs
                    </label>
                    <div class="w-3/4">
                        <input type="number" 
                               step="0.01" 
                               name="sickpay_hrs" 
                               id="sickpay_hrs" 
                               value="{{ old('sickpay_hrs', $empPayrollHrs->sickpay_hrs ?? 0) }}" 
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('sickpay_hrs')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Bank Holiday hrs -->
                <div class="flex items-start gap-4">
                    <label for="extras1_hrs" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Bank Holiday hrs
                    </label>
                    <div class="w-3/4">
                        <input type="number" 
                               step="0.01" 
                               name="extras1_hrs" 
                               id="extras1_hrs" 
                               value="{{ old('extras1_hrs', $empPayrollHrs->extras1_hrs ?? 0) }}" 
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('extras1_hrs')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Bank Holiday notes -->
                <div class="flex items-start gap-4">
                    <label for="extras2_hrs" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Bank Holiday notes
                    </label>
                    <div class="w-3/4">
                        <input type="text" 
                               name="extras2_hrs" 
                               id="extras2_hrs" 
                               value="{{ old('extras2_hrs', $empPayrollHrs->extras2_hrs ?? '') }}" 
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('extras2_hrs')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Hours to be paid -->
                <div class="flex items-start gap-4">
                    <label for="hours_worked" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Hours to be paid <span class="text-red-500">*</span>
                    </label>
                    <div class="w-3/4">
                        <input type="number" 
                               step="0.01" 
                               name="hours_worked" 
                               id="hours_worked" 
                               value="{{ old('hours_worked', $empPayrollHrs->hours_worked ?? 0) }}" 
                               required
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('hours_worked')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Break Deducted (hidden field, but included for completeness) -->
                <input type="hidden" name="break_deducted" value="{{ old('break_deducted', $empPayrollHrs->break_deducted ?? 0) }}">
                <input type="hidden" name="payroll_id" value="{{ base64_encode($empPayrollHrs->payroll_id) }}">

                <!-- Form Actions -->
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('storeowner.clocktime.compare_weekly_hrs') }}" 
                       class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                        Save
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-storeowner-app-layout>

