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
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Employee Payroll</span>
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
           class="px-4 py-2 bg-gray-800 text-white rounded-t-md">
            Process Payroll
        </a>
    </div>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex gap-6">
                <!-- Left Panel - Employee List -->
                <div class="w-1/4">
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-4 py-3 border-b border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-800">Employees</h4>
                        </div>
                        <div class="p-4">
                            <ul class="space-y-2">
                                @foreach($employees as $employee)
                                    <li>
                                        <a href="#" class="text-sm text-blue-600 hover:text-blue-800 hover:underline">
                                            {{ $employee->firstname }} {{ $employee->lastname }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Right Panel - Payroll Hours Table -->
                <div class="w-3/4">
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-800">Employee Payroll</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Week</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @if($payrollHrs->count() > 0)
                                        @foreach($payrollHrs as $hrs)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $hrs->weekno }} - {{ $hrs->week_start }} - {{ \Carbon\Carbon::parse($hrs->week_start)->format('F') }} - {{ $hrs->year }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">-</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $hrs->hours_worked ?? 0 }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ \Carbon\Carbon::parse($hrs->week_start)->format('F') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $hrs->year }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <a href="#" class="text-blue-600 hover:text-blue-800 mr-3" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="#" class="text-red-600 hover:text-red-800" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No records found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-storeowner-app-layout>

