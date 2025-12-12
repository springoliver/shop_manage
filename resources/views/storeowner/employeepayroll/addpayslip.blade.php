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
                        <a href="{{ route('storeowner.employeepayroll.index') }}" class="ml-1 hover:text-gray-700">Employee Payslips</a>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Main Tabs -->
    <div class="mb-6 flex space-x-2">
        <a href="{{ route('storeowner.employeepayroll.employee-settings') }}" 
           class="px-4 py-2 bg-gray-600 text-white rounded-t-md hover:bg-gray-700">
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

    @if ($errors->any())
        <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded relative" role="alert">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">Add Payslip</h2>
                
                <form action="{{ route('storeowner.employeepayroll.storepayslip') }}" method="POST" enctype="multipart/form-data" id="myform">
                    @csrf
                    
                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Select Week:
                        </label>
                        <div class="w-3/4">
                            <input type="date" name="dateofbirth" id="dateofbirth" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                                   autocomplete="off" required>
                            <p class="mt-2 text-sm text-gray-500">Week Number: <span id="week_num"></span></p>
                            <p class="mt-1 text-xs text-red-500" id="week_error" style="display: none;">Please wait for week details to load...</p>
                        </div>
                    </div>

                    <input type="hidden" name="myweek_num" id="myweek_num">
                    <input type="hidden" name="myweek_start" id="myweek_start">
                    <input type="hidden" name="myweek_end" id="myweek_end">
                    <input type="hidden" name="my_year" id="my_year">

                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Employee Name <span class="text-red-500">*</span>
                        </label>
                        <div class="w-3/4">
                            <select name="employeeid" id="employeeid" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" required>
                                <option value="">Select Employee</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->employeeid }}" {{ old('employeeid') == $employee->employeeid ? 'selected' : '' }}>
                                        {{ $employee->firstname }} {{ $employee->lastname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Payslip File <span class="text-red-500">*</span>
                        </label>
                        <div class="w-3/4">
                            <input type="file" name="doc" id="doc"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                                   required>
                            <p class="mt-2 text-sm text-gray-500">(Max: 50MB)</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 mt-8">
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

    @push('scripts')
    <script>
        // Wait for jQuery to be available
        (function() {
            var retries = 0;
            var maxRetries = 50; // 5 seconds max wait (50 * 100ms)
            
            function initPayslipForm() {
                // Check if jQuery is available
                var $ = window.jQuery || window.$;
                
                if (!$ || typeof $ !== 'function') {
                    retries++;
                    if (retries < maxRetries) {
                        setTimeout(initPayslipForm, 100);
                        return;
                    } else {
                        console.error('jQuery failed to load after ' + maxRetries + ' retries');
                        return;
                    }
                }
                
                $(document).ready(function() {
                    // Date picker change handler
                    $('#dateofbirth').on('change', function() {
                        const date = $(this).val();
                        if (date) {
                            // Show loading message
                            $('#week_error').text('Loading week details...').show();
                            
                            $.ajax({
                                url: '{{ route("storeowner.employeepayroll.get-week-details") }}',
                                method: 'POST',
                                data: {
                                    '_token': '{{ csrf_token() }}',
                                    'date': date
                                },
                                success: function(response) {
                                    if (response.week_num && response.year) {
                                        $('#week_num').text(response.week_num);
                                        $('#myweek_num').val(response.week_num);
                                        $('#my_year').val(response.year);
                                        $('#myweek_start').val(response.week_start || '');
                                        $('#myweek_end').val(response.week_end || '');
                                        $('#week_error').hide();
                                    } else {
                                        $('#week_error').text('Unable to get week details. Please try again.').show();
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error('Error getting week details:', error);
                                    $('#week_error').text('Error getting week details. Please try again.').show();
                                    // Clear hidden fields on error
                                    $('#myweek_num').val('');
                                    $('#my_year').val('');
                                    $('#myweek_start').val('');
                                    $('#myweek_end').val('');
                                }
                            });
                        } else {
                            // Clear hidden fields if date is cleared
                            $('#week_num').text('');
                            $('#myweek_num').val('');
                            $('#my_year').val('');
                            $('#myweek_start').val('');
                            $('#myweek_end').val('');
                            $('#week_error').hide();
                        }
                    });
                    
                    // Also trigger on page load if date is already selected
                    if ($('#dateofbirth').val()) {
                        $('#dateofbirth').trigger('change');
                    }

                    // Form validation
                    $('#myform').on('submit', function(e) {
                        const employeeid = $('#employeeid').val();
                        const dateofbirth = $('#dateofbirth').val();
                        const doc = $('#doc')[0].files.length;
                        const myweek_num = $('#myweek_num').val();
                        const my_year = $('#my_year').val();
                        
                        if (!employeeid || !dateofbirth || !doc) {
                            e.preventDefault();
                            alert('Please fill in all required fields.');
                            return false;
                        }
                        
                        // Validate that week details were populated
                        if (!myweek_num || !my_year) {
                            e.preventDefault();
                            alert('Please select a valid date to get week details.');
                            return false;
                        }
                    });
                });
            }
            
            // Start initialization when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initPayslipForm);
            } else {
                initPayslipForm();
            }
        })();
    </script>
    @endpush
</x-storeowner-app-layout>

