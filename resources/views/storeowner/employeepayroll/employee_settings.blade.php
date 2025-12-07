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

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">Employee Payroll Settings</h2>
                <p class="text-gray-600">Employee Settings functionality will be implemented in the next phase. This includes complex Irish tax system settings (PAYE, USC, PRSI, PRD, Pension).</p>
            </div>
        </div>
    </div>
</x-storeowner-app-layout>

