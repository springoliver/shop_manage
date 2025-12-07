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

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">Payslip Details</h2>
                
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Employee Name</label>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ ucfirst($payslip->firstname ?? '') }} {{ ucfirst($payslip->lastname ?? '') }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Store Name</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $payslip->store_name ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Week Number</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $payslip->weekid ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Year</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $payslip->year ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">User Group</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $payslip->groupname ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $payslip->emailid ?? '-' }}</p>
                    </div>
                </div>

                @if($payrollDetails->count() > 0)
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Payroll Details</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Day</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Shift</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Hours</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($payrollDetails as $detail)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $detail->weekday ?? '-' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ucfirst($detail->shift ?? '-') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $detail->total_hours ?? 0 }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <div class="mt-6 flex justify-end gap-4">
                    <a href="{{ route('storeowner.employeepayroll.downloadpdf', base64_encode($payslip->payslipid)) }}" 
                       target="_blank"
                       class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        <i class="fas fa-download mr-2"></i> Download PDF
                    </a>
                    <a href="{{ route('storeowner.employeepayroll.index') }}" 
                       class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Back
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-storeowner-app-layout>

