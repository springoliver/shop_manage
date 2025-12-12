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
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Employee Payroll Settings</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Main Tabs -->
    <div class="mb-6 flex space-x-2">
        <a href="{{ route('storeowner.employeepayroll.employee-settings') }}" 
           class="px-4 py-2 bg-gray-800 text-white rounded-t-md">
            Employee Settings
        </a>
        <a href="{{ route('storeowner.employeepayroll.index') }}" 
           class="px-4 py-2 bg-gray-600 text-white rounded-t-md hover:bg-gray-700">
            Employee Payslips
        </a>
        <a href="{{ route('storeowner.employeepayroll.process-payroll') }}" 
           class="px-4 py-2 bg-gray-600 text-white rounded-t-md hover:bg-gray-700">
            Process Payroll
        </a>
    </div>

    <!-- Success/Error Messages -->
    @if (session('success'))
        <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="py-4">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-12 gap-6">
                <!-- Left Sidebar: Employee List (2 columns) -->
                <div class="col-span-12 md:col-span-2">
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-800">Employees</h3>
                        </div>
                        <div class="overflow-y-auto" style="max-height: 600px;">
                            @if($employeeSettings->count() > 0)
                                <table class="min-w-full divide-y divide-gray-200">
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($employeeSettings as $empSetting)
                                            <tr class="hover:bg-gray-50 cursor-pointer" 
                                                onclick="window.location.href='{{ route('storeowner.employeepayroll.edit-employee-settings', base64_encode($empSetting->employee_settings_id)) }}'">
                                                <td class="px-4 py-3 text-sm text-gray-900">
                                                    {{ ucfirst($empSetting->firstname ?? '') }} {{ ucfirst($empSetting->lastname ?? '') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="px-4 py-3 text-sm text-gray-500 text-center">No employee settings found</div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Content: Form (10 columns) -->
                <div class="col-span-12 md:col-span-10">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Employee Payroll Settings</h2>
                        
                        <form action="{{ route('storeowner.employeepayroll.update-employee-settings') }}" method="POST" id="employeeSettingsForm" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="employee_settings_id" id="employee_settings_id" value="{{ isset($existingSettings) ? $existingSettings->employee_settings_id : '' }}">
                            
                            <!-- Employee Name Dropdown -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Employee Name <span class="text-red-500">*</span></label>
                                <select name="employeeid" id="employeeid" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                                        required>
                                    <option value="">Select Employee</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->employeeid }}" 
                                                {{ (isset($existingSettings) && $existingSettings->employeeid == $employee->employeeid) ? 'selected' : '' }}>
                                            {{ $employee->firstname }} {{ $employee->lastname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tabs -->
                            <div class="mb-6">
                                <div class="border-b border-gray-200">
                                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                                        <button type="button" 
                                                onclick="switchTab('previous-employment')" 
                                                id="tab-previous-employment"
                                                class="tab-button active whitespace-nowrap py-4 px-1 border-b-2 border-gray-800 font-medium text-sm text-gray-800">
                                            Previous Employment
                                        </button>
                                        <button type="button" 
                                                onclick="switchTab('revenue-details')" 
                                                id="tab-revenue-details"
                                                class="tab-button whitespace-nowrap py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                            Revenue Details
                                        </button>
                                        <button type="button" 
                                                onclick="switchTab('mid-year-totals')" 
                                                id="tab-mid-year-totals"
                                                class="tab-button whitespace-nowrap py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                            Mid Year Totals
                                        </button>
                                    </nav>
                                </div>

                                <!-- Tab Content: Previous Employment -->
                                <div id="tab-content-previous-employment" class="tab-content mt-6">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Previous Employer Registration Number:</label>
                                            <input type="text" name="prev_employer_no" id="prev_employer_no" 
                                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Previous employment leave date:</label>
                                            <input type="text" name="prev_employment_leavedate" id="prev_employment_leavedate" 
                                                   placeholder="DD-MM-YYYY"
                                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Previous employment figures:</label>
                                            <div class="grid grid-cols-2 gap-4 mt-2">
                                                <div>
                                                    <p class="text-sm text-gray-600 mb-1">Total liable to PAYE</p>
                                                    <input type="text" name="gross_pay_for_paye" id="gross_pay_for_paye" 
                                                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                                                </div>
                                                <div>
                                                    <p class="text-sm text-gray-600 mb-1">Total PAYE</p>
                                                    <input type="text" name="total_pay_for_paye" id="total_pay_for_paye" 
                                                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                                                </div>
                                                <div>
                                                    <p class="text-sm text-gray-600 mb-1">Total liable to USC</p>
                                                    <input type="text" name="gross_pay_for_usc" id="gross_pay_for_usc" 
                                                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                                                </div>
                                                <div>
                                                    <p class="text-sm text-gray-600 mb-1">Total USC</p>
                                                    <input type="text" name="total_pay_for_usc" id="total_pay_for_usc" 
                                                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                                                </div>
                                                <div>
                                                    <p class="text-sm text-gray-600 mb-1">Total liable to PRD</p>
                                                    <input type="text" name="gross_pay_for_prd" id="gross_pay_for_prd" 
                                                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                                                </div>
                                                <div>
                                                    <p class="text-sm text-gray-600 mb-1">Total PRD</p>
                                                    <input type="text" name="total_pay_for_prd" id="total_pay_for_prd" 
                                                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                                                </div>
                                                <div>
                                                    <p class="text-sm text-gray-600 mb-1">Total LPT</p>
                                                    <input type="text" name="total_pay_for_lpt" id="total_pay_for_lpt" 
                                                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab Content: Revenue Details -->
                                <div id="tab-content-revenue-details" class="tab-content hidden mt-6">
                                    <div class="space-y-6">
                                        <!-- Tax Basis -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Tax basis</label>
                                            <div class="space-y-2">
                                                <label class="flex items-center">
                                                    <input type="radio" name="tax_basis" value="emergency_basis" class="mr-2" checked>
                                                    <span>Emergency basis</span>
                                                </label>
                                                <label class="flex items-center">
                                                    <input type="radio" name="tax_basis" value="week_one_basis" class="mr-2">
                                                    <span>Week one basis</span>
                                                </label>
                                                <label class="flex items-center">
                                                    <input type="radio" name="tax_basis" value="cumulitive_basis" class="mr-2">
                                                    <span>Cumulitive basis</span>
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Exemption/Exclusion -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Exemption / exclusion</label>
                                            <select name="tax_exemption_id" id="tax_exemption_id" 
                                                    class="w-full md:w-1/3 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                                                <option value="">Please Select</option>
                                                @foreach($taxExemptions as $exemption)
                                                    <option value="{{ $exemption->tax_exemption_id }}">{{ $exemption->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- PAYE Section -->
                                        <div>
                                            <h4 class="text-lg font-semibold text-gray-800 mb-4">PAYE</h4>
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full border border-gray-300">
                                                    <thead class="bg-gray-50">
                                                        <tr>
                                                            <th class="px-4 py-2 border border-gray-300 text-left text-sm font-medium text-gray-700"></th>
                                                            <th class="px-4 py-2 border border-gray-300 text-left text-sm font-medium text-gray-700">Weekly</th>
                                                            <th class="px-4 py-2 border border-gray-300 text-left text-sm font-medium text-gray-700">Annual</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td class="px-4 py-2 border border-gray-300 text-sm">Credit</td>
                                                            <td class="px-4 py-2 border border-gray-300">
                                                                <input type="text" name="weekly_tax_credit" id="weekly_tax_credit" 
                                                                       class="w-full px-2 py-1 border border-gray-300 rounded">
                                                            </td>
                                                            <td class="px-4 py-2 border border-gray-300">
                                                                <input type="text" name="annualy_tax_credit" id="annualy_tax_credit" 
                                                                       class="w-full px-2 py-1 border border-gray-300 rounded">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="px-4 py-2 border border-gray-300 text-sm">Cut off point</td>
                                                            <td class="px-4 py-2 border border-gray-300">
                                                                <input type="text" name="weekly_cut_off" id="weekly_cut_off" 
                                                                       class="w-full px-2 py-1 border border-gray-300 rounded">
                                                            </td>
                                                            <td class="px-4 py-2 border border-gray-300">
                                                                <input type="text" name="annualy_cut_off" id="annualy_cut_off" 
                                                                       class="w-full px-2 py-1 border border-gray-300 rounded">
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- USC Section -->
                                        <div>
                                            <h4 class="text-lg font-semibold text-gray-800 mb-4">USC</h4>
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full border border-gray-300">
                                                    <thead class="bg-gray-50">
                                                        <tr>
                                                            <th class="px-4 py-2 border border-gray-300 text-left text-sm font-medium text-gray-700"></th>
                                                            <th class="px-4 py-2 border border-gray-300 text-left text-sm font-medium text-gray-700">Weekly</th>
                                                            <th class="px-4 py-2 border border-gray-300 text-left text-sm font-medium text-gray-700">Annual</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td class="px-4 py-2 border border-gray-300 text-sm">0.5% cut off point</td>
                                                            <td class="px-4 py-2 border border-gray-300">
                                                                <input type="text" name="weekly_cutoff_point0_5" id="weekly_cutoff_point0_5" 
                                                                       class="w-full px-2 py-1 border border-gray-300 rounded">
                                                            </td>
                                                            <td class="px-4 py-2 border border-gray-300">
                                                                <input type="text" name="anualy_cutoff_point0_5" id="anualy_cutoff_point0_5" 
                                                                       class="w-full px-2 py-1 border border-gray-300 rounded">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="px-4 py-2 border border-gray-300 text-sm">2.5% cut off point</td>
                                                            <td class="px-4 py-2 border border-gray-300">
                                                                <input type="text" name="weekly_cutoff_point2_5" id="weekly_cutoff_point2_5" 
                                                                       class="w-full px-2 py-1 border border-gray-300 rounded">
                                                            </td>
                                                            <td class="px-4 py-2 border border-gray-300">
                                                                <input type="text" name="anualy_cutoff_point2_5" id="anualy_cutoff_point2_5" 
                                                                       class="w-full px-2 py-1 border border-gray-300 rounded">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="px-4 py-2 border border-gray-300 text-sm">5% cut off point</td>
                                                            <td class="px-4 py-2 border border-gray-300">
                                                                <input type="text" name="weekly_cutoff_point5" id="weekly_cutoff_point5" 
                                                                       class="w-full px-2 py-1 border border-gray-300 rounded">
                                                            </td>
                                                            <td class="px-4 py-2 border border-gray-300">
                                                                <input type="text" name="anualy_cutoff_point5" id="anualy_cutoff_point5" 
                                                                       class="w-full px-2 py-1 border border-gray-300 rounded">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="px-4 py-2 border border-gray-300 text-sm">8% cut off point</td>
                                                            <td class="px-4 py-2 border border-gray-300">
                                                                <input type="text" name="weekly_cutoff_point8" id="weekly_cutoff_point8" 
                                                                       class="w-full px-2 py-1 border border-gray-300 rounded">
                                                            </td>
                                                            <td class="px-4 py-2 border border-gray-300">
                                                                <input type="text" name="anualy_cutoff_point8" id="anualy_cutoff_point8" 
                                                                       class="w-full px-2 py-1 border border-gray-300 rounded">
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- PRSI Category, PRD Calculation Method, LPT -->
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">PRSI Category</label>
                                                <select name="prsi_category_id" id="prsi_category_id" 
                                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                                                    <option value="">Please Select</option>
                                                    @foreach($prsiCategories as $category)
                                                        <option value="{{ $category->prsi_category_id }}">{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">PRD Calculation method</label>
                                                <select name="calculation_methods_id" id="calculation_methods_id" 
                                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                                                    <option value="">Please Select</option>
                                                    @foreach($prdCalculationMethods as $method)
                                                        <option value="{{ $method->calculation_methods_id }}">{{ $method->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Total LPT to be deducted</label>
                                                <input type="text" name="lpd_tobe_reduced" id="lpd_tobe_reduced" 
                                                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab Content: Mid Year Totals -->
                                <div id="tab-content-mid-year-totals" class="tab-content hidden mt-6">
                                    <div class="space-y-6">
                                        <!-- First Table -->
                                        <div class="overflow-x-auto">
                                            <table class="min-w-full border border-gray-300">
                                                <tbody>
                                                    <tr>
                                                        <td class="px-4 py-2 border border-gray-300 text-sm font-medium w-1/4">National pay to date</td>
                                                        <td class="px-4 py-2 border border-gray-300 w-1/4">
                                                            <input type="text" name="national_pay_todate" id="national_pay_todate" 
                                                                   class="w-full px-2 py-1 border border-gray-300 rounded">
                                                        </td>
                                                        <td class="px-4 py-2 border border-gray-300 text-sm font-medium w-1/4">Total Employee PRSI-able pay to date</td>
                                                        <td class="px-4 py-2 border border-gray-300 w-1/4">
                                                            <input type="text" name="total_employee_prsi_able_pay_todate" id="total_employee_prsi_able_pay_todate" 
                                                                   class="w-full px-2 py-1 border border-gray-300 rounded">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-4 py-2 border border-gray-300 text-sm">(of which Medical Insurance)</td>
                                                        <td class="px-4 py-2 border border-gray-300">
                                                            <input type="text" name="medical_insurance_pay_todate" id="medical_insurance_pay_todate" 
                                                                   class="w-full px-2 py-1 border border-gray-300 rounded">
                                                        </td>
                                                        <td class="px-4 py-2 border border-gray-300 text-sm">Total Employee PRSI to date</td>
                                                        <td class="px-4 py-2 border border-gray-300">
                                                            <input type="text" name="total_employee_prsi_pay_todate" id="total_employee_prsi_pay_todate" 
                                                                   class="w-full px-2 py-1 border border-gray-300 rounded">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-4 py-2 border border-gray-300"></td>
                                                        <td class="px-4 py-2 border border-gray-300"></td>
                                                        <td class="px-4 py-2 border border-gray-300 text-sm">Total Employeer PRSI-able pay to date</td>
                                                        <td class="px-4 py-2 border border-gray-300">
                                                            <input type="text" name="total_employer_prsi_able_pay_todate" id="total_employer_prsi_able_pay_todate" 
                                                                   class="w-full px-2 py-1 border border-gray-300 rounded">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-4 py-2 border border-gray-300 text-sm">Taxable Illness Benefit to date</td>
                                                        <td class="px-4 py-2 border border-gray-300">
                                                            <input type="text" name="taxable_ilness_benefit_todate" id="taxable_ilness_benefit_todate" 
                                                                   class="w-full px-2 py-1 border border-gray-300 rounded">
                                                        </td>
                                                        <td class="px-4 py-2 border border-gray-300 text-sm">Total Employeer PRSI to date</td>
                                                        <td class="px-4 py-2 border border-gray-300">
                                                            <input type="text" name="total_employer_prsi_pay_todate" id="total_employer_prsi_pay_todate" 
                                                                   class="w-full px-2 py-1 border border-gray-300 rounded">
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Second Table -->
                                        <div class="overflow-x-auto">
                                            <table class="min-w-full border border-gray-300">
                                                <tbody>
                                                    <tr>
                                                        <td class="px-4 py-2 border border-gray-300 text-sm font-medium w-1/4">PAYE-able pay to date</td>
                                                        <td class="px-4 py-2 border border-gray-300 w-1/4">
                                                            <input type="text" name="paye_able_pay_todate" id="paye_able_pay_todate" 
                                                                   class="w-full px-2 py-1 border border-gray-300 rounded">
                                                        </td>
                                                        <td class="px-4 py-2 border border-gray-300 text-sm font-medium w-1/4">Pension-able pay to date</td>
                                                        <td class="px-4 py-2 border border-gray-300 w-1/4">
                                                            <input type="text" name="pension_able_pay_todate" id="pension_able_pay_todate" 
                                                                   class="w-full px-2 py-1 border border-gray-300 rounded">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-4 py-2 border border-gray-300 text-sm">PAYE to date</td>
                                                        <td class="px-4 py-2 border border-gray-300">
                                                            <input type="text" name="pay_todate" id="pay_todate" 
                                                                   class="w-full px-2 py-1 border border-gray-300 rounded">
                                                        </td>
                                                        <td class="px-4 py-2 border border-gray-300 text-sm">Type of pension to date</td>
                                                        <td class="px-4 py-2 border border-gray-300">
                                                            <select name="pension_types_id" id="pension_types_id" 
                                                                    class="w-full px-2 py-1 border border-gray-300 rounded">
                                                                <option value="">Please Select</option>
                                                                @foreach($pensionTypes as $type)
                                                                    <option value="{{ $type->pension_types_id }}">{{ $type->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-4 py-2 border border-gray-300 text-sm">USC-able pay to date</td>
                                                        <td class="px-4 py-2 border border-gray-300">
                                                            <input type="text" name="usc_able_pay_todate" id="usc_able_pay_todate" 
                                                                   class="w-full px-2 py-1 border border-gray-300 rounded">
                                                        </td>
                                                        <td class="px-4 py-2 border border-gray-300 text-sm">Employee pension to date</td>
                                                        <td class="px-4 py-2 border border-gray-300">
                                                            <input type="text" name="employee_pension_todate" id="employee_pension_todate" 
                                                                   class="w-full px-2 py-1 border border-gray-300 rounded">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-4 py-2 border border-gray-300 text-sm">USC to date</td>
                                                        <td class="px-4 py-2 border border-gray-300">
                                                            <input type="text" name="employer_pension_todate" id="employer_pension_todate" 
                                                                   class="w-full px-2 py-1 border border-gray-300 rounded">
                                                        </td>
                                                        <td class="px-4 py-2 border border-gray-300 text-sm">Employer pension to date</td>
                                                        <td class="px-4 py-2 border border-gray-300"></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-4 py-2 border border-gray-300 text-sm">PRD-able pay to date</td>
                                                        <td class="px-4 py-2 border border-gray-300">
                                                            <input type="text" name="prd_able_todate" id="prd_able_todate" 
                                                                   class="w-full px-2 py-1 border border-gray-300 rounded">
                                                        </td>
                                                        <td class="px-4 py-2 border border-gray-300"></td>
                                                        <td class="px-4 py-2 border border-gray-300"></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-4 py-2 border border-gray-300 text-sm">PRD to date</td>
                                                        <td class="px-4 py-2 border border-gray-300">
                                                            <input type="text" name="prd_todate" id="prd_todate" 
                                                                   class="w-full px-2 py-1 border border-gray-300 rounded">
                                                        </td>
                                                        <td class="px-4 py-2 border border-gray-300"></td>
                                                        <td class="px-4 py-2 border border-gray-300"></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-4 py-2 border border-gray-300 text-sm">LPD to date</td>
                                                        <td class="px-4 py-2 border border-gray-300">
                                                            <input type="text" name="lpd_todate" id="lpd_todate" 
                                                                   class="w-full px-2 py-1 border border-gray-300 rounded">
                                                        </td>
                                                        <td class="px-4 py-2 border border-gray-300"></td>
                                                        <td class="px-4 py-2 border border-gray-300"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- PRSI Class Section -->
                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Class</label>
                                                <select name="prsi_class_id" id="prsi_class_id" 
                                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                                                    <option value="">Please Select</option>
                                                    @foreach($prsiClasses as $class)
                                                        <option value="{{ $class->prsi_class_id }}">{{ $class->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Weeks</label>
                                                <input type="text" name="employee_previous_prsi_class" id="employee_previous_prsi_class" 
                                                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex justify-end space-x-4 mt-8">
                                <button type="submit" class="px-6 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                                    Save
                                </button>
                                <a href="{{ route('storeowner.employeepayroll.index') }}" 
                                   class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Tab switching functionality
        function switchTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('border-gray-800', 'text-gray-800');
                button.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Show selected tab content
            document.getElementById('tab-content-' + tabName).classList.remove('hidden');
            
            // Add active class to selected tab
            const activeTab = document.getElementById('tab-' + tabName);
            activeTab.classList.remove('border-transparent', 'text-gray-500');
            activeTab.classList.add('border-gray-800', 'text-gray-800');
        }

        // Initialize Flatpickr for date picker
        (function() {
            var retries = 0;
            var maxRetries = 50;
            
            function initDatePicker() {
                if (typeof flatpickr === 'undefined') {
                    retries++;
                    if (retries < maxRetries) {
                        setTimeout(initDatePicker, 100);
                        return;
                    }
                    return;
                }
                
                flatpickr('#prev_employment_leavedate', {
                    dateFormat: 'd-m-Y',
                    allowInput: true
                });
            }
            
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initDatePicker);
            } else {
                initDatePicker();
            }
        })();
    </script>
    @endpush
</x-storeowner-app-layout>