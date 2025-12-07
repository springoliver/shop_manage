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

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Payslips for {{ ucfirst($payslips->first()->firstname ?? '') }} {{ ucfirst($payslips->first()->lastname ?? '') }}
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Id</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Week - Year</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User Group Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee Email id</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @if($payslips->count() > 0)
                                @foreach($payslips as $payslip)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $payslip->payslipid }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $payslip->weeknumber ?? $payslip->weekid }} - {{ $payslip->year }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ ucfirst($payslip->firstname ?? '') }} {{ ucfirst($payslip->lastname ?? '') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $payslip->groupname ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $payslip->emailid ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('storeowner.employeepayroll.view', base64_encode($payslip->payslipid)) }}" 
                                               class="text-blue-600 hover:text-blue-800 mr-3" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('storeowner.employeepayroll.downloadpdf', base64_encode($payslip->payslipid)) }}" 
                                               target="_blank"
                                               class="text-green-600 hover:text-green-800 mr-3" title="Download">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <a href="#" 
                                               onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this payslip?')) { document.getElementById('delete-form-{{ $payslip->payslipid }}').submit(); }"
                                               class="text-red-600 hover:text-red-800" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <form id="delete-form-{{ $payslip->payslipid }}" 
                                                  action="{{ route('storeowner.employeepayroll.destroy', base64_encode($payslip->payslipid)) }}" 
                                                  method="POST" 
                                                  style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
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
</x-storeowner-app-layout>

