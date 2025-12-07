@section('page_header', 'Add Employee')

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
                        <a href="{{ route('storeowner.employee.index') }}" class="ml-1 text-gray-500 hover:text-gray-700 md:ml-2">Employees</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Add</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Add Employee</h1>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <form action="{{ route('storeowner.employee.store') }}" method="POST" enctype="multipart/form-data" id="employeeForm">
                @csrf

                <!-- Group Name -->
                <div class="flex items-start gap-4 mb-6">
                    <label for="groupid" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Group Name <span class="text-red-500">*</span>
                    </label>
                    <div class="w-3/4">
                        <select name="groupid" id="groupid" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Group</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->usergroupid }}" {{ old('groupid') == $group->usergroupid ? 'selected' : '' }}>
                                    {{ $group->groupname }}
                                </option>
                            @endforeach
                        </select>
                        @error('groupid')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Maximum Working Hours in Week -->
                <div class="flex items-start gap-4 mb-6">
                    <label for="wroster" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Maximum Working Hours in Week <span class="text-red-500">*</span>
                    </label>
                    <div class="w-3/4">
                        <input type="number" name="wroster" id="wroster" value="{{ old('wroster', 50) }}" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('wroster')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Maximum Working Hours in Day -->
                <div class="flex items-start gap-4 mb-6">
                    <label for="droster" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Maximum Working Hours in Day <span class="text-red-500">*</span>
                    </label>
                    <div class="w-3/4">
                        <input type="number" name="droster" id="droster" value="{{ old('droster', 12) }}" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('droster')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Shift Break -->
                <div class="flex items-start gap-4 mb-6">
                    <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Shift Break <span class="text-red-500">*</span>
                    </label>
                    <div class="w-3/4">
                        <div class="flex items-center gap-6">
                            <label class="inline-flex items-center">
                                <input type="radio" name="paid_break" value="Yes" id="paid_break1" {{ old('paid_break') == 'Yes' ? 'checked' : '' }} class="form-radio">
                                <span class="ml-2">Paid</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="paid_break" value="No" id="paid_break2" {{ old('paid_break', 'No') == 'No' ? 'checked' : '' }} class="form-radio">
                                <span class="ml-2">Unpaid</span>
                            </label>
                            <label class="inline-flex items-center">
                                <span class="mr-2">Every</span>
                                <input type="number" name="every_hrs" id="every_hrs" value="{{ old('every_hrs', 1) }}" maxlength="2" size="2" required class="w-16 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <span class="ml-2">Hrs.</span>
                            </label>
                            <label class="inline-flex items-center">
                                <span class="mr-2">deduct</span>
                                <input type="number" name="break_min" id="break_min" value="{{ old('break_min', 4) }}" maxlength="2" size="2" required class="w-16 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <span class="ml-2">min.</span>
                            </label>
                        </div>
                        @error('paid_break')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Display Weekly Hrs -->
                <div class="flex items-start gap-4 mb-6">
                    <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Display Weekly Hrs (Employees Section)
                    </label>
                    <div class="w-3/4">
                        <label class="inline-flex items-center mr-6">
                            <input type="radio" name="display_hrs_hols" value="Yes" id="display_hrs_hols1" {{ old('display_hrs_hols') == 'Yes' ? 'checked' : '' }} class="form-radio">
                            <span class="ml-2">Yes</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="display_hrs_hols" value="No" id="display_hrs_hols2" {{ old('display_hrs_hols', 'No') == 'No' ? 'checked' : '' }} class="form-radio">
                            <span class="ml-2">No</span>
                        </label>
                    </div>
                </div>

                <!-- First Name -->
                <div class="flex items-start gap-4 mb-6">
                    <label for="firstname" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        First Name <span class="text-red-500">*</span>
                    </label>
                    <div class="w-3/4">
                        <input type="text" name="firstname" id="firstname" value="{{ old('firstname') }}" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('firstname')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Last Name -->
                <div class="flex items-start gap-4 mb-6">
                    <label for="lastname" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Last Name <span class="text-red-500">*</span>
                    </label>
                    <div class="w-3/4">
                        <input type="text" name="lastname" id="lastname" value="{{ old('lastname') }}" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('lastname')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Username -->
                <div class="flex items-start gap-4 mb-6">
                    <label for="username" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Username <span class="text-red-500">*</span>
                    </label>
                    <div class="w-3/4">
                        <input type="text" name="username" id="username" value="{{ old('username') }}" onblur="checkusername()" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <span id="existuser" class="text-red-500 text-sm hidden">Username Already Exist</span>
                        @error('username')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Password -->
                <div class="flex items-start gap-4 mb-6">
                    <label for="password" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <div class="w-3/4">
                        <input type="password" name="password" id="password" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="mt-1 text-sm text-gray-500">Password must be between 6 to 16 characters, it must contain at least one upper case, lower case and number</p>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Email Id -->
                <div class="flex items-start gap-4 mb-6">
                    <label for="emailid" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Email Id <span class="text-red-500">*</span>
                    </label>
                    <div class="w-3/4">
                        <input type="email" name="emailid" id="emailid" value="{{ old('emailid') }}" onblur="checkemail()" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <span id="existemail" class="text-red-500 text-sm hidden">Email Already Exist</span>
                        @error('emailid')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Employee tax number (PPS) -->
                <div class="flex items-start gap-4 mb-6">
                    <label for="emptaxnumber" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Employee tax number (PPS) <span class="text-red-500">*</span>
                    </label>
                    <div class="w-3/4">
                        <input type="text" name="emptaxnumber" id="emptaxnumber" value="{{ old('emptaxnumber') }}" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('emptaxnumber')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Employee Nationality -->
                <div class="flex items-start gap-4 mb-6">
                    <label for="empnationality" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Employee Nationality <span class="text-red-500">*</span>
                    </label>
                    <div class="w-3/4">
                        <input type="text" name="empnationality" id="empnationality" value="{{ old('empnationality') }}" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('empnationality')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Employee Join date -->
                <div class="flex items-start gap-4 mb-6">
                    <label for="empjoindate" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Employee Join date <span class="text-red-500">*</span>
                    </label>
                    <div class="w-3/4">
                        <input type="date" name="empjoindate" id="empjoindate" value="{{ old('empjoindate') }}" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('empjoindate')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Employee Bank Details 1 -->
                <div class="flex items-start gap-4 mb-6">
                    <label for="empbankdetails1" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Employee Bank Details 1 <span class="text-red-500">*</span>
                    </label>
                    <div class="w-3/4">
                        <input type="text" name="empbankdetails1" id="empbankdetails1" value="{{ old('empbankdetails1') }}" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('empbankdetails1')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Employee Bank Details 2 -->
                <div class="flex items-start gap-4 mb-6">
                    <label for="empbankdetails2" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Employee Bank Details 2 <span class="text-red-500">*</span>
                    </label>
                    <div class="w-3/4">
                        <input type="text" name="empbankdetails2" id="empbankdetails2" value="{{ old('empbankdetails2') }}" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('empbankdetails2')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Salary Method -->
                <div class="flex items-start gap-4 mb-6">
                    <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Salary Method <span class="text-red-500">*</span>
                    </label>
                    <div class="w-3/4">
                        <label class="inline-flex items-center mr-6">
                            <input type="radio" name="sallary_method" value="hourly" id="sallary_method" {{ old('sallary_method', 'hourly') == 'hourly' ? 'checked' : '' }} class="form-radio">
                            <span class="ml-2">Hourly</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="sallary_method" value="yearly" id="sallary_method2" {{ old('sallary_method') == 'yearly' ? 'checked' : '' }} class="form-radio">
                            <span class="ml-2">Yearly</span>
                        </label>
                    </div>
                </div>

                <!-- Hourly Holiday Entitlement -->
                <div class="flex items-start gap-4 mb-6">
                    <label for="holiday_percent" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Hourly Holiday Entitilement (% of worked hours) *For hourly paid employees
                    </label>
                    <div class="w-3/4">
                        <input type="number" name="holiday_percent" id="holiday_percent" value="{{ old('holiday_percent', 8) }}" step="0.01" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('holiday_percent')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Yearly Holiday Entitlement -->
                <div class="flex items-start gap-4 mb-6">
                    <label for="holiday_day_entitiled" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Yearly Holiday Entitilement (Days)*eg. 20 Days per year, for sallary paid employees
                    </label>
                    <div class="w-3/4">
                        <input type="number" name="holiday_day_entitiled" id="holiday_day_entitiled" value="{{ old('holiday_day_entitiled', 0) }}" step="0.01" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('holiday_day_entitiled')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Pay Method -->
                <div class="flex items-start gap-4 mb-6">
                    <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Pay Method <span class="text-red-500">*</span>
                    </label>
                    <div class="w-3/4">
                        <div class="flex flex-wrap gap-4">
                            <label class="inline-flex items-center">
                                <input type="radio" name="payment_method" value="monthly" id="pay_method1" {{ old('payment_method', 'monthly') == 'monthly' ? 'checked' : '' }} class="form-radio">
                                <span class="ml-2">Monthly</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="payment_method" value="weekly" id="pay_method2" {{ old('payment_method') == 'weekly' ? 'checked' : '' }} class="form-radio">
                                <span class="ml-2">Weekly</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="payment_method" value="fortnightly" id="pay_method3" {{ old('payment_method') == 'fortnightly' ? 'checked' : '' }} class="form-radio">
                                <span class="ml-2">Fortnightly (2 Week)</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="payment_method" value="lunar" id="pay_method4" {{ old('payment_method') == 'lunar' ? 'checked' : '' }} class="form-radio">
                                <span class="ml-2">Lunar (4 Week)</span>
                            </label>
                        </div>
                        @error('payment_method')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Pay rate(per hour) -->
                <div class="flex items-start gap-4 mb-6">
                    <label for="pay_rate_hour" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Pay rate(per hour) $ <span class="text-red-500">*</span>
                    </label>
                    <div class="w-3/4">
                        <input type="number" name="pay_rate_hour" id="pay_rate_hour" value="{{ old('pay_rate_hour', 0) }}" step="0.01" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('pay_rate_hour')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Pay rate(per week) -->
                <div class="flex items-start gap-4 mb-6">
                    <label for="pay_rate_week" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Pay rate(per week) $
                    </label>
                    <div class="w-3/4">
                        <input type="number" name="pay_rate_week" id="pay_rate_week" value="{{ old('pay_rate_week', 0) }}" step="0.01" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('pay_rate_week')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Pay rate(per year) -->
                <div class="flex items-start gap-4 mb-6">
                    <label for="pay_rate_year" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Pay rate(per year) $
                    </label>
                    <div class="w-3/4">
                        <input type="number" name="pay_rate_year" id="pay_rate_year" value="{{ old('pay_rate_year', 0) }}" step="0.01" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('pay_rate_year')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Department -->
                <div class="flex items-start gap-4 mb-6">
                    <label for="departmentid" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Department <span class="text-red-500">*</span>
                    </label>
                    <div class="w-3/4">
                        <select name="departmentid" id="departmentid" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->departmentid }}" {{ old('departmentid') == $department->departmentid ? 'selected' : '' }}>
                                    {{ $department->department }}
                                </option>
                            @endforeach
                        </select>
                        @error('departmentid')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Profile Photo -->
                <div class="flex items-start gap-4 mb-6">
                    <label for="profile_photo" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Profile Photo <span class="text-red-500">*</span>
                    </label>
                    <div class="w-3/4">
                        <input type="file" name="profile_img" id="profile_img" accept="image/jpeg,image/png,image/jpg" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="mt-1 text-sm text-gray-500">Select .jpg, .jpeg or .png file size up to 2MB only.</p>
                        @error('profile_img')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Phone -->
                <div class="flex items-start gap-4 mb-6">
                    <label for="phone" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Phone <span class="text-red-500">*</span>
                    </label>
                    <div class="w-3/4">
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Country -->
                <div class="flex items-start gap-4 mb-6">
                    <label for="country" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Country <span class="text-red-500">*</span>
                    </label>
                    <div class="w-3/4">
                        <select id="country" name="country" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                            <option value="">Select Country</option>
                            <option value="Ireland" {{ old('country') == 'Ireland' ? 'selected' : '' }}>Ireland (+353)</option>
                            <option value="UK" {{ old('country') == 'UK' ? 'selected' : '' }}>UK (+44)</option>
                            <option value="USA" {{ old('country', 'USA') == 'USA' ? 'selected' : '' }}>USA (+1)</option>
                            <option disabled>Other Countries</option>
                            <option value="Algeria" {{ old('country') == 'Algeria' ? 'selected' : '' }}>Algeria (+213)</option>
                            <option value="Andorra" {{ old('country') == 'Andorra' ? 'selected' : '' }}>Andorra (+376)</option>
                            <option value="Angola" {{ old('country') == 'Angola' ? 'selected' : '' }}>Angola (+244)</option>
                            <option value="Anguilla" {{ old('country') == 'Anguilla' ? 'selected' : '' }}>Anguilla (+1264)</option>
                            <option value="Antigua & Barbuda" {{ old('country') == 'Antigua & Barbuda' ? 'selected' : '' }}>Antigua & Barbuda (+1268)</option>
                            <option value="Argentina" {{ old('country') == 'Argentina' ? 'selected' : '' }}>Argentina (+54)</option>
                            <option value="Armenia" {{ old('country') == 'Armenia' ? 'selected' : '' }}>Armenia (+374)</option>
                            <option value="Aruba" {{ old('country') == 'Aruba' ? 'selected' : '' }}>Aruba (+297)</option>
                            <option value="Australia" {{ old('country') == 'Australia' ? 'selected' : '' }}>Australia (+61)</option>
                            <option value="Austria" {{ old('country') == 'Austria' ? 'selected' : '' }}>Austria (+43)</option>
                            <option value="Azerbaijan" {{ old('country') == 'Azerbaijan' ? 'selected' : '' }}>Azerbaijan (+994)</option>
                            <option value="Bahamas" {{ old('country') == 'Bahamas' ? 'selected' : '' }}>Bahamas (+1242)</option>
                            <option value="Bahrain" {{ old('country') == 'Bahrain' ? 'selected' : '' }}>Bahrain (+973)</option>
                            <option value="Bangladesh" {{ old('country') == 'Bangladesh' ? 'selected' : '' }}>Bangladesh (+880)</option>
                            <option value="Barbados" {{ old('country') == 'Barbados' ? 'selected' : '' }}>Barbados (+1246)</option>
                            <option value="Belarus" {{ old('country') == 'Belarus' ? 'selected' : '' }}>Belarus (+375)</option>
                            <option value="Belgium" {{ old('country') == 'Belgium' ? 'selected' : '' }}>Belgium (+32)</option>
                            <option value="Belize" {{ old('country') == 'Belize' ? 'selected' : '' }}>Belize (+501)</option>
                            <option value="Benin" {{ old('country') == 'Benin' ? 'selected' : '' }}>Benin (+229)</option>
                            <option value="Bermuda" {{ old('country') == 'Bermuda' ? 'selected' : '' }}>Bermuda (+1441)</option>
                            <option value="Bhutan" {{ old('country') == 'Bhutan' ? 'selected' : '' }}>Bhutan (+975)</option>
                            <option value="Bolivia" {{ old('country') == 'Bolivia' ? 'selected' : '' }}>Bolivia (+591)</option>
                            <option value="Bosnia Herzegovina" {{ old('country') == 'Bosnia Herzegovina' ? 'selected' : '' }}>Bosnia Herzegovina (+387)</option>
                            <option value="Botswana" {{ old('country') == 'Botswana' ? 'selected' : '' }}>Botswana (+267)</option>
                            <option value="Brazil" {{ old('country') == 'Brazil' ? 'selected' : '' }}>Brazil (+55)</option>
                            <option value="Brunei" {{ old('country') == 'Brunei' ? 'selected' : '' }}>Brunei (+673)</option>
                            <option value="Bulgaria" {{ old('country') == 'Bulgaria' ? 'selected' : '' }}>Bulgaria (+359)</option>
                            <option value="Burkina Faso" {{ old('country') == 'Burkina Faso' ? 'selected' : '' }}>Burkina Faso (+226)</option>
                            <option value="Burundi" {{ old('country') == 'Burundi' ? 'selected' : '' }}>Burundi (+257)</option>
                            <option value="Cambodia" {{ old('country') == 'Cambodia' ? 'selected' : '' }}>Cambodia (+855)</option>
                            <option value="Cameroon" {{ old('country') == 'Cameroon' ? 'selected' : '' }}>Cameroon (+237)</option>
                            <option value="Canada" {{ old('country') == 'Canada' ? 'selected' : '' }}>Canada (+1)</option>
                            <option value="Cape Verde Islands" {{ old('country') == 'Cape Verde Islands' ? 'selected' : '' }}>Cape Verde Islands (+238)</option>
                            <option value="Cayman Islands" {{ old('country') == 'Cayman Islands' ? 'selected' : '' }}>Cayman Islands (+1345)</option>
                            <option value="Central African Republic" {{ old('country') == 'Central African Republic' ? 'selected' : '' }}>Central African Republic (+236)</option>
                            <option value="Chile" {{ old('country') == 'Chile' ? 'selected' : '' }}>Chile (+56)</option>
                            <option value="China" {{ old('country') == 'China' ? 'selected' : '' }}>China (+86)</option>
                            <option value="Colombia" {{ old('country') == 'Colombia' ? 'selected' : '' }}>Colombia (+57)</option>
                            <option value="Comoros" {{ old('country') == 'Comoros' ? 'selected' : '' }}>Comoros (+269)</option>
                            <option value="Congo" {{ old('country') == 'Congo' ? 'selected' : '' }}>Congo (+242)</option>
                            <option value="Cook Islands" {{ old('country') == 'Cook Islands' ? 'selected' : '' }}>Cook Islands (+682)</option>
                            <option value="Costa Rica" {{ old('country') == 'Costa Rica' ? 'selected' : '' }}>Costa Rica (+506)</option>
                            <option value="Croatia" {{ old('country') == 'Croatia' ? 'selected' : '' }}>Croatia (+385)</option>
                            <option value="Cyprus - North" {{ old('country') == 'Cyprus - North' ? 'selected' : '' }}>Cyprus - North (+90)</option>
                            <option value="Cyprus - South" {{ old('country') == 'Cyprus - South' ? 'selected' : '' }}>Cyprus - South (+357)</option>
                            <option value="Czech Republic" {{ old('country') == 'Czech Republic' ? 'selected' : '' }}>Czech Republic (+420)</option>
                            <option value="Denmark" {{ old('country') == 'Denmark' ? 'selected' : '' }}>Denmark (+45)</option>
                            <option value="Djibouti" {{ old('country') == 'Djibouti' ? 'selected' : '' }}>Djibouti (+253)</option>
                            <option value="Dominica" {{ old('country') == 'Dominica' ? 'selected' : '' }}>Dominica (+1809)</option>
                            <option value="Dominican Republic" {{ old('country') == 'Dominican Republic' ? 'selected' : '' }}>Dominican Republic (+1809)</option>
                            <option value="Ecuador" {{ old('country') == 'Ecuador' ? 'selected' : '' }}>Ecuador (+593)</option>
                            <option value="Egypt" {{ old('country') == 'Egypt' ? 'selected' : '' }}>Egypt (+20)</option>
                            <option value="El Salvador" {{ old('country') == 'El Salvador' ? 'selected' : '' }}>El Salvador (+503)</option>
                            <option value="Equatorial Guinea" {{ old('country') == 'Equatorial Guinea' ? 'selected' : '' }}>Equatorial Guinea (+240)</option>
                            <option value="Eritrea" {{ old('country') == 'Eritrea' ? 'selected' : '' }}>Eritrea (+291)</option>
                            <option value="Estonia" {{ old('country') == 'Estonia' ? 'selected' : '' }}>Estonia (+372)</option>
                            <option value="Ethiopia" {{ old('country') == 'Ethiopia' ? 'selected' : '' }}>Ethiopia (+251)</option>
                            <option value="Falkland Islands" {{ old('country') == 'Falkland Islands' ? 'selected' : '' }}>Falkland Islands (+500)</option>
                            <option value="Faroe Islands" {{ old('country') == 'Faroe Islands' ? 'selected' : '' }}>Faroe Islands (+298)</option>
                            <option value="Fiji" {{ old('country') == 'Fiji' ? 'selected' : '' }}>Fiji (+679)</option>
                            <option value="Finland" {{ old('country') == 'Finland' ? 'selected' : '' }}>Finland (+358)</option>
                            <option value="France" {{ old('country') == 'France' ? 'selected' : '' }}>France (+33)</option>
                            <option value="French Guiana" {{ old('country') == 'French Guiana' ? 'selected' : '' }}>French Guiana (+594)</option>
                            <option value="French Polynesia" {{ old('country') == 'French Polynesia' ? 'selected' : '' }}>French Polynesia (+689)</option>
                            <option value="Gabon" {{ old('country') == 'Gabon' ? 'selected' : '' }}>Gabon (+241)</option>
                            <option value="Gambia" {{ old('country') == 'Gambia' ? 'selected' : '' }}>Gambia (+220)</option>
                            <option value="Georgia" {{ old('country') == 'Georgia' ? 'selected' : '' }}>Georgia (+7880)</option>
                            <option value="Germany" {{ old('country') == 'Germany' ? 'selected' : '' }}>Germany (+49)</option>
                            <option value="Ghana" {{ old('country') == 'Ghana' ? 'selected' : '' }}>Ghana (+233)</option>
                            <option value="Gibraltar" {{ old('country') == 'Gibraltar' ? 'selected' : '' }}>Gibraltar (+350)</option>
                            <option value="Greece" {{ old('country') == 'Greece' ? 'selected' : '' }}>Greece (+30)</option>
                            <option value="Greenland" {{ old('country') == 'Greenland' ? 'selected' : '' }}>Greenland (+299)</option>
                            <option value="Grenada" {{ old('country') == 'Grenada' ? 'selected' : '' }}>Grenada (+1473)</option>
                            <option value="Guadeloupe" {{ old('country') == 'Guadeloupe' ? 'selected' : '' }}>Guadeloupe (+590)</option>
                            <option value="Guam" {{ old('country') == 'Guam' ? 'selected' : '' }}>Guam (+671)</option>
                            <option value="Guatemala" {{ old('country') == 'Guatemala' ? 'selected' : '' }}>Guatemala (+502)</option>
                            <option value="Guinea" {{ old('country') == 'Guinea' ? 'selected' : '' }}>Guinea (+224)</option>
                            <option value="Guinea - Bissau" {{ old('country') == 'Guinea - Bissau' ? 'selected' : '' }}>Guinea - Bissau (+245)</option>
                            <option value="Guyana" {{ old('country') == 'Guyana' ? 'selected' : '' }}>Guyana (+592)</option>
                            <option value="Haiti" {{ old('country') == 'Haiti' ? 'selected' : '' }}>Haiti (+509)</option>
                            <option value="Honduras" {{ old('country') == 'Honduras' ? 'selected' : '' }}>Honduras (+504)</option>
                            <option value="Hong Kong" {{ old('country') == 'Hong Kong' ? 'selected' : '' }}>Hong Kong (+852)</option>
                            <option value="Hungary" {{ old('country') == 'Hungary' ? 'selected' : '' }}>Hungary (+36)</option>
                            <option value="Iceland" {{ old('country') == 'Iceland' ? 'selected' : '' }}>Iceland (+354)</option>
                            <option value="India" {{ old('country') == 'India' ? 'selected' : '' }}>India (+91)</option>
                            <option value="Indonesia" {{ old('country') == 'Indonesia' ? 'selected' : '' }}>Indonesia (+62)</option>
                            <option value="Iran" {{ old('country') == 'Iran' ? 'selected' : '' }}>Iran (+98)</option>
                            <option value="Iraq" {{ old('country') == 'Iraq' ? 'selected' : '' }}>Iraq (+964)</option>
                            <option value="Israel" {{ old('country') == 'Israel' ? 'selected' : '' }}>Israel (+972)</option>
                            <option value="Italy" {{ old('country') == 'Italy' ? 'selected' : '' }}>Italy (+39)</option>
                            <option value="Jamaica" {{ old('country') == 'Jamaica' ? 'selected' : '' }}>Jamaica (+1876)</option>
                            <option value="Japan" {{ old('country') == 'Japan' ? 'selected' : '' }}>Japan (+81)</option>
                            <option value="Jordan" {{ old('country') == 'Jordan' ? 'selected' : '' }}>Jordan (+962)</option>
                            <option value="Kazakhstan" {{ old('country') == 'Kazakhstan' ? 'selected' : '' }}>Kazakhstan (+7)</option>
                            <option value="Kenya" {{ old('country') == 'Kenya' ? 'selected' : '' }}>Kenya (+254)</option>
                            <option value="Kiribati" {{ old('country') == 'Kiribati' ? 'selected' : '' }}>Kiribati (+686)</option>
                            <option value="Korea - North" {{ old('country') == 'Korea - North' ? 'selected' : '' }}>Korea - North (+850)</option>
                            <option value="Korea - South" {{ old('country') == 'Korea - South' ? 'selected' : '' }}>Korea - South (+82)</option>
                            <option value="Kuwait" {{ old('country') == 'Kuwait' ? 'selected' : '' }}>Kuwait (+965)</option>
                            <option value="Kyrgyzstan" {{ old('country') == 'Kyrgyzstan' ? 'selected' : '' }}>Kyrgyzstan (+996)</option>
                            <option value="Laos" {{ old('country') == 'Laos' ? 'selected' : '' }}>Laos (+856)</option>
                            <option value="Latvia" {{ old('country') == 'Latvia' ? 'selected' : '' }}>Latvia (+371)</option>
                            <option value="Lebanon" {{ old('country') == 'Lebanon' ? 'selected' : '' }}>Lebanon (+961)</option>
                            <option value="Lesotho" {{ old('country') == 'Lesotho' ? 'selected' : '' }}>Lesotho (+266)</option>
                            <option value="Liberia" {{ old('country') == 'Liberia' ? 'selected' : '' }}>Liberia (+231)</option>
                            <option value="Libya" {{ old('country') == 'Libya' ? 'selected' : '' }}>Libya (+218)</option>
                            <option value="Liechtenstein" {{ old('country') == 'Liechtenstein' ? 'selected' : '' }}>Liechtenstein (+417)</option>
                            <option value="Lithuania" {{ old('country') == 'Lithuania' ? 'selected' : '' }}>Lithuania (+370)</option>
                            <option value="Luxembourg" {{ old('country') == 'Luxembourg' ? 'selected' : '' }}>Luxembourg (+352)</option>
                            <option value="Macao" {{ old('country') == 'Macao' ? 'selected' : '' }}>Macao (+853)</option>
                            <option value="Macedonia" {{ old('country') == 'Macedonia' ? 'selected' : '' }}>Macedonia (+389)</option>
                            <option value="Madagascar" {{ old('country') == 'Madagascar' ? 'selected' : '' }}>Madagascar (+261)</option>
                            <option value="Malawi" {{ old('country') == 'Malawi' ? 'selected' : '' }}>Malawi (+265)</option>
                            <option value="Malaysia" {{ old('country') == 'Malaysia' ? 'selected' : '' }}>Malaysia (+60)</option>
                            <option value="Maldives" {{ old('country') == 'Maldives' ? 'selected' : '' }}>Maldives (+960)</option>
                            <option value="Mali" {{ old('country') == 'Mali' ? 'selected' : '' }}>Mali (+223)</option>
                            <option value="Malta" {{ old('country') == 'Malta' ? 'selected' : '' }}>Malta (+356)</option>
                            <option value="Marshall Islands" {{ old('country') == 'Marshall Islands' ? 'selected' : '' }}>Marshall Islands (+692)</option>
                            <option value="Martinique" {{ old('country') == 'Martinique' ? 'selected' : '' }}>Martinique (+596)</option>
                            <option value="Mauritania" {{ old('country') == 'Mauritania' ? 'selected' : '' }}>Mauritania (+222)</option>
                            <option value="Mayotte" {{ old('country') == 'Mayotte' ? 'selected' : '' }}>Mayotte (+269)</option>
                            <option value="Mexico" {{ old('country') == 'Mexico' ? 'selected' : '' }}>Mexico (+52)</option>
                            <option value="Micronesia" {{ old('country') == 'Micronesia' ? 'selected' : '' }}>Micronesia (+691)</option>
                            <option value="Moldova" {{ old('country') == 'Moldova' ? 'selected' : '' }}>Moldova (+373)</option>
                            <option value="Monaco" {{ old('country') == 'Monaco' ? 'selected' : '' }}>Monaco (+377)</option>
                            <option value="Mongolia" {{ old('country') == 'Mongolia' ? 'selected' : '' }}>Mongolia (+976)</option>
                            <option value="Montserrat" {{ old('country') == 'Montserrat' ? 'selected' : '' }}>Montserrat (+1664)</option>
                            <option value="Morocco" {{ old('country') == 'Morocco' ? 'selected' : '' }}>Morocco (+212)</option>
                            <option value="Mozambique" {{ old('country') == 'Mozambique' ? 'selected' : '' }}>Mozambique (+258)</option>
                            <option value="Myanmar" {{ old('country') == 'Myanmar' ? 'selected' : '' }}>Myanmar (+95)</option>
                            <option value="Namibia" {{ old('country') == 'Namibia' ? 'selected' : '' }}>Namibia (+264)</option>
                            <option value="Nauru" {{ old('country') == 'Nauru' ? 'selected' : '' }}>Nauru (+674)</option>
                            <option value="Nepal" {{ old('country') == 'Nepal' ? 'selected' : '' }}>Nepal (+977)</option>
                            <option value="Netherlands" {{ old('country') == 'Netherlands' ? 'selected' : '' }}>Netherlands (+31)</option>
                            <option value="New Caledonia" {{ old('country') == 'New Caledonia' ? 'selected' : '' }}>New Caledonia (+687)</option>
                            <option value="New Zealand" {{ old('country') == 'New Zealand' ? 'selected' : '' }}>New Zealand (+64)</option>
                            <option value="Nicaragua" {{ old('country') == 'Nicaragua' ? 'selected' : '' }}>Nicaragua (+505)</option>
                            <option value="Niger" {{ old('country') == 'Niger' ? 'selected' : '' }}>Niger (+227)</option>
                            <option value="Nigeria" {{ old('country') == 'Nigeria' ? 'selected' : '' }}>Nigeria (+234)</option>
                            <option value="Niue" {{ old('country') == 'Niue' ? 'selected' : '' }}>Niue (+683)</option>
                            <option value="Norfolk Islands" {{ old('country') == 'Norfolk Islands' ? 'selected' : '' }}>Norfolk Islands (+672)</option>
                            <option value="Northern Marianas" {{ old('country') == 'Northern Marianas' ? 'selected' : '' }}>Northern Marianas (+670)</option>
                            <option value="Norway" {{ old('country') == 'Norway' ? 'selected' : '' }}>Norway (+47)</option>
                            <option value="Oman" {{ old('country') == 'Oman' ? 'selected' : '' }}>Oman (+968)</option>
                            <option value="Pakistan" {{ old('country') == 'Pakistan' ? 'selected' : '' }}>Pakistan (+92)</option>
                            <option value="Palau" {{ old('country') == 'Palau' ? 'selected' : '' }}>Palau (+680)</option>
                            <option value="Panama" {{ old('country') == 'Panama' ? 'selected' : '' }}>Panama (+507)</option>
                            <option value="Papua New Guinea" {{ old('country') == 'Papua New Guinea' ? 'selected' : '' }}>Papua New Guinea (+675)</option>
                            <option value="Paraguay" {{ old('country') == 'Paraguay' ? 'selected' : '' }}>Paraguay (+595)</option>
                            <option value="Peru" {{ old('country') == 'Peru' ? 'selected' : '' }}>Peru (+51)</option>
                            <option value="Philippines" {{ old('country') == 'Philippines' ? 'selected' : '' }}>Philippines (+63)</option>
                            <option value="Poland" {{ old('country') == 'Poland' ? 'selected' : '' }}>Poland (+48)</option>
                            <option value="Portugal" {{ old('country') == 'Portugal' ? 'selected' : '' }}>Portugal (+351)</option>
                            <option value="Puerto Rico" {{ old('country') == 'Puerto Rico' ? 'selected' : '' }}>Puerto Rico (+1787)</option>
                            <option value="Qatar" {{ old('country') == 'Qatar' ? 'selected' : '' }}>Qatar (+974)</option>
                            <option value="Reunion" {{ old('country') == 'Reunion' ? 'selected' : '' }}>Reunion (+262)</option>
                            <option value="Romania" {{ old('country') == 'Romania' ? 'selected' : '' }}>Romania (+40)</option>
                            <option value="Russia" {{ old('country') == 'Russia' ? 'selected' : '' }}>Russia (+7)</option>
                            <option value="Rwanda" {{ old('country') == 'Rwanda' ? 'selected' : '' }}>Rwanda (+250)</option>
                            <option value="San Marino" {{ old('country') == 'San Marino' ? 'selected' : '' }}>San Marino (+378)</option>
                            <option value="Sao Tome & Principe" {{ old('country') == 'Sao Tome & Principe' ? 'selected' : '' }}>Sao Tome & Principe (+239)</option>
                            <option value="Saudi Arabia" {{ old('country') == 'Saudi Arabia' ? 'selected' : '' }}>Saudi Arabia (+966)</option>
                            <option value="Senegal" {{ old('country') == 'Senegal' ? 'selected' : '' }}>Senegal (+221)</option>
                            <option value="Serbia" {{ old('country') == 'Serbia' ? 'selected' : '' }}>Serbia (+381)</option>
                            <option value="Seychelles" {{ old('country') == 'Seychelles' ? 'selected' : '' }}>Seychelles (+248)</option>
                            <option value="Sierra Leone" {{ old('country') == 'Sierra Leone' ? 'selected' : '' }}>Sierra Leone (+232)</option>
                            <option value="Singapore" {{ old('country') == 'Singapore' ? 'selected' : '' }}>Singapore (+65)</option>
                            <option value="Slovak Republic" {{ old('country') == 'Slovak Republic' ? 'selected' : '' }}>Slovak Republic (+421)</option>
                            <option value="Slovenia" {{ old('country') == 'Slovenia' ? 'selected' : '' }}>Slovenia (+386)</option>
                            <option value="Solomon Islands" {{ old('country') == 'Solomon Islands' ? 'selected' : '' }}>Solomon Islands (+677)</option>
                            <option value="Somalia" {{ old('country') == 'Somalia' ? 'selected' : '' }}>Somalia (+252)</option>
                            <option value="South Africa" {{ old('country') == 'South Africa' ? 'selected' : '' }}>South Africa (+27)</option>
                            <option value="Spain" {{ old('country') == 'Spain' ? 'selected' : '' }}>Spain (+34)</option>
                            <option value="Sri Lanka" {{ old('country') == 'Sri Lanka' ? 'selected' : '' }}>Sri Lanka (+94)</option>
                            <option value="St. Helena" {{ old('country') == 'St. Helena' ? 'selected' : '' }}>St. Helena (+290)</option>
                            <option value="St. Kitts" {{ old('country') == 'St. Kitts' ? 'selected' : '' }}>St. Kitts (+1869)</option>
                            <option value="St. Lucia" {{ old('country') == 'St. Lucia' ? 'selected' : '' }}>St. Lucia (+1758)</option>
                            <option value="Sudan" {{ old('country') == 'Sudan' ? 'selected' : '' }}>Sudan (+249)</option>
                            <option value="Suriname" {{ old('country') == 'Suriname' ? 'selected' : '' }}>Suriname (+597)</option>
                            <option value="Swaziland" {{ old('country') == 'Swaziland' ? 'selected' : '' }}>Swaziland (+268)</option>
                            <option value="Sweden" {{ old('country') == 'Sweden' ? 'selected' : '' }}>Sweden (+46)</option>
                            <option value="Switzerland" {{ old('country') == 'Switzerland' ? 'selected' : '' }}>Switzerland (+41)</option>
                            <option value="Syria" {{ old('country') == 'Syria' ? 'selected' : '' }}>Syria (+963)</option>
                            <option value="Taiwan" {{ old('country') == 'Taiwan' ? 'selected' : '' }}>Taiwan (+886)</option>
                            <option value="Tajikistan" {{ old('country') == 'Tajikistan' ? 'selected' : '' }}>Tajikistan (+992)</option>
                            <option value="Thailand" {{ old('country') == 'Thailand' ? 'selected' : '' }}>Thailand (+66)</option>
                            <option value="Togo" {{ old('country') == 'Togo' ? 'selected' : '' }}>Togo (+228)</option>
                            <option value="Tonga" {{ old('country') == 'Tonga' ? 'selected' : '' }}>Tonga (+676)</option>
                            <option value="Trinidad & Tobago" {{ old('country') == 'Trinidad & Tobago' ? 'selected' : '' }}>Trinidad & Tobago (+1868)</option>
                            <option value="Tunisia" {{ old('country') == 'Tunisia' ? 'selected' : '' }}>Tunisia (+216)</option>
                            <option value="Turkey" {{ old('country') == 'Turkey' ? 'selected' : '' }}>Turkey (+90)</option>
                            <option value="Turkmenistan" {{ old('country') == 'Turkmenistan' ? 'selected' : '' }}>Turkmenistan (+993)</option>
                            <option value="Turks & Caicos Islands" {{ old('country') == 'Turks & Caicos Islands' ? 'selected' : '' }}>Turks & Caicos Islands (+1649)</option>
                            <option value="Tuvalu" {{ old('country') == 'Tuvalu' ? 'selected' : '' }}>Tuvalu (+688)</option>
                            <option value="Uganda" {{ old('country') == 'Uganda' ? 'selected' : '' }}>Uganda (+256)</option>
                            <option value="Ukraine" {{ old('country') == 'Ukraine' ? 'selected' : '' }}>Ukraine (+380)</option>
                            <option value="United Arab Emirates" {{ old('country') == 'United Arab Emirates' ? 'selected' : '' }}>United Arab Emirates (+971)</option>
                            <option value="Uruguay" {{ old('country') == 'Uruguay' ? 'selected' : '' }}>Uruguay (+598)</option>
                            <option value="Uzbekistan" {{ old('country') == 'Uzbekistan' ? 'selected' : '' }}>Uzbekistan (+998)</option>
                            <option value="Vanuatu" {{ old('country') == 'Vanuatu' ? 'selected' : '' }}>Vanuatu (+678)</option>
                            <option value="Vatican City" {{ old('country') == 'Vatican City' ? 'selected' : '' }}>Vatican City (+379)</option>
                            <option value="Venezuela" {{ old('country') == 'Venezuela' ? 'selected' : '' }}>Venezuela (+58)</option>
                            <option value="Vietnam" {{ old('country') == 'Vietnam' ? 'selected' : '' }}>Vietnam (+84)</option>
                            <option value="Virgin Islands - British" {{ old('country') == 'Virgin Islands - British' ? 'selected' : '' }}>Virgin Islands - British (+1)</option>
                            <option value="Virgin Islands - US" {{ old('country') == 'Virgin Islands - US' ? 'selected' : '' }}>Virgin Islands - US (+1)</option>
                            <option value="Wallis & Futuna" {{ old('country') == 'Wallis & Futuna' ? 'selected' : '' }}>Wallis & Futuna (+681)</option>
                            <option value="Yemen (North)" {{ old('country') == 'Yemen (North)' ? 'selected' : '' }}>Yemen (North)(+969)</option>
                            <option value="Yemen (South)" {{ old('country') == 'Yemen (South)' ? 'selected' : '' }}>Yemen (South)(+967)</option>
                            <option value="Zambia" {{ old('country') == 'Zambia' ? 'selected' : '' }}>Zambia (+260)</option>
                            <option value="Zimbabwe" {{ old('country') == 'Zimbabwe' ? 'selected' : '' }}>Zimbabwe (+263)</option>
                        </select>
                        @error('country')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Address 1 -->
                <div class="flex items-start gap-4 mb-6">
                    <label for="address" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Address 1 <span class="text-red-500">*</span>
                    </label>
                    <div class="w-3/4">
                        <input type="text" name="address" id="address" value="{{ old('address') }}" placeholder="Location" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 search-box">
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Address 2 -->
                <div class="flex items-start gap-4 mb-6">
                    <label for="address1" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Address 2 <span class="text-red-500">*</span>
                    </label>
                    <div class="w-3/4">
                        <input type="text" name="address1" id="address1" value="{{ old('address1') }}" placeholder="Location" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 search-box">
                        @error('address1')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Hidden fields for Google Places API data -->
                <div id="details" style="display: none;">
                    <input type="hidden" name="address_lat" data-geo="lat" value="">
                    <input type="hidden" name="address_lng" data-geo="lng" value="">
                    <input type="hidden" name="address_formatted_address" data-geo="formatted_address" value="">
                    <input type="hidden" name="address_street_number" data-geo="street_number" value="">
                    <input type="hidden" name="address_street" data-geo="route" value="">
                    <input type="hidden" name="address_airport" data-geo="code" value="">
                    <input type="hidden" name="address_state" data-geo="administrative_area_level_1" value="">
                    <input type="hidden" name="address_country" data-geo="country" value="">
                    <input type="hidden" name="address_city" data-geo="locality" value="">
                    <input type="hidden" name="address_zipcode" data-geo="postal_code" value="">
                    <input type="hidden" name="address_location_type" data-geo="location_type" value="">
                    <input type="hidden" name="address2_lat" data-geo1="lat" value="">
                    <input type="hidden" name="address2_lng" data-geo1="lng" value="">
                    <input type="hidden" name="address2_formatted_address" data-geo1="formatted_address" value="">
                    <input type="hidden" name="address2_street_number" data-geo1="street_number" value="">
                    <input type="hidden" name="address2_street" data-geo1="route" value="">
                    <input type="hidden" name="address2_state" data-geo1="administrative_area_level_1" value="">
                    <input type="hidden" name="address2_country" data-geo="country" value="">
                    <input type="hidden" name="address2_city" data-geo1="locality" value="">
                    <input type="hidden" name="address2_zipcode" data-geo1="postal_code" value="">
                    <input type="hidden" name="address2_location_type" data-geo1="location_type" value="">
                </div>

                <!-- Date of Birth -->
                <div class="flex items-start gap-4 mb-6">
                    <label for="dateofbirth" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Date of Birth <span class="text-red-500">*</span>
                    </label>
                    <div class="w-3/4">
                        <input type="date" name="dateofbirth" id="dateofbirth" value="{{ old('dateofbirth') }}" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('dateofbirth')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('storeowner.employee.index') }}" class="px-4 py-2 bg-gray-500 text-white text-sm font-medium rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-storeowner-app-layout>

@push('scripts')
<script>
// Ensure jQuery is loaded before using it
(function() {
    var checkJQuery = setInterval(function() {
        if (typeof jQuery !== 'undefined') {
            clearInterval(checkJQuery);
            initEmployeeForm();
        }
    }, 100);
    
    // Timeout after 5 seconds
    setTimeout(function() {
        clearInterval(checkJQuery);
        if (typeof jQuery === 'undefined') {
            console.warn('jQuery did not load within 5 seconds');
        }
    }, 5000);
})();

function initEmployeeForm() {
    try {
        // Sync country dropdown to hidden address_country field
        $(document).ready(function() {
            $('#country').on('change', function() {
                var countryValue = $(this).val();
                $('input[name="address_country"]').val(countryValue);
            });
            
            // Set initial value if country is already selected
            var initialCountry = $('#country').val();
            if (initialCountry) {
                $('input[name="address_country"]').val(initialCountry);
            }
        });
        
        // Make functions globally accessible
        window.checkemail = function() {
            try {
                var email = $('#emailid').val();
                if (!email) {
                    $("#existemail").hide();
                    return;
                }
                
                $.ajax({
                    url: "{{ route('storeowner.employee.check-email') }}",
                    method: "POST",
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'email': email,
                        'emailid': email
                    },
                    success: function(response) {
                        if (response && response.exists) {
                            $("#existemail").show();
                        } else {
                            $("#existemail").hide();
                        }
                    },
                    error: function() {
                        $("#existemail").hide();
                    }
                });
            } catch (e) {
                console.error('Error in checkemail:', e);
            }
        };

        window.checkusername = function() {
            try {
                var username = $('#username').val();
                if (!username) {
                    $("#existuser").hide();
                    return;
                }
                
                $.ajax({
                    url: "{{ route('storeowner.employee.check-username') }}",
                    method: "POST",
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'username': username
                    },
                    success: function(response) {
                        if (response && response.exists) {
                            $("#existuser").show();
                        } else {
                            $("#existuser").hide();
                        }
                    },
                    error: function() {
                        $("#existuser").hide();
                    }
                });
            } catch (e) {
                console.error('Error in checkusername:', e);
            }
        };

        // Google Places API initialization
        $(document).ready(function() {
            try {
                // Load Google Places API
                if (typeof google === 'undefined') {
                    window.initGeocomplete = function() {
                        if (typeof $.fn.geocomplete === 'undefined') {
                            var script = document.createElement('script');
                            script.src = 'https://cdn.jsdelivr.net/npm/jquery.geocomplete@1.7.0/jquery.geocomplete.min.js';
                            script.onload = function() {
                                initializeGeocomplete();
                            };
                            script.onerror = function() {
                                console.warn('Failed to load geocomplete plugin');
                            };
                            document.head.appendChild(script);
                        } else {
                            initializeGeocomplete();
                        }
                    };
                    
                    var script = document.createElement('script');
                    script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&key=AIzaSyCSmlTnCXc8o9GQZvhXV0NjuZXG57uo1lo&libraries=places&callback=initGeocomplete';
                    script.onerror = function() {
                        console.warn('Failed to load Google Places API');
                    };
                    document.head.appendChild(script);
                } else {
                    if (typeof $.fn.geocomplete === 'undefined') {
                        var script = document.createElement('script');
                        script.src = 'https://cdn.jsdelivr.net/npm/jquery.geocomplete@1.7.0/jquery.geocomplete.min.js';
                        script.onload = function() {
                            initializeGeocomplete();
                        };
                        script.onerror = function() {
                            console.warn('Failed to load geocomplete plugin');
                        };
                        document.head.appendChild(script);
                    } else {
                        initializeGeocomplete();
                    }
                }
            } catch (e) {
                console.error('Error initializing Google Places:', e);
            }
        });

        function initializeGeocomplete() {
            try {
                if (typeof jQuery === 'undefined' || typeof $.fn.geocomplete === 'undefined') {
                    return;
                }
                
                if ($("#address").length) {
                    $("#address").geocomplete({
                        details: "#details",
                        detailsAttribute: "data-geo",
                        types: ["geocode", "establishment"]
                    });
                }
                
                if ($("#address1").length) {
                    $("#address1").geocomplete({
                        details: "#details",
                        detailsAttribute: "data-geo1",
                        types: ["geocode", "establishment"]
                    });
                }
            } catch (e) {
                console.error('Error initializing geocomplete:', e);
            }
        }
    } catch (e) {
        console.error('Error initializing employee form:', e);
    }
}
</script>
@endpush

