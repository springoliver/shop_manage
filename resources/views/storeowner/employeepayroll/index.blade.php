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
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Employee Payslips</span>
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
           class="px-4 py-2 bg-gray-800 text-white rounded-t-md">
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

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search and Add -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <label class="block text-sm font-medium text-gray-700 mr-4">Search:</label>
                        <input type="text" id="searchbox" placeholder="Enter Keyword" 
                               class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                    </div>
                    <div>
                        <a href="{{ route('storeowner.employeepayroll.addpayslip') }}" 
                           class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                            <i class="fas fa-plus mr-2"></i> Manually Upload Payslips
                        </a>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="table-new">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    Id
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    Week - Year
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    Employee Name
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    User Group Name
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    Employee Email id
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @if($payslips->count() > 0)
                                @foreach($payslips as $payslip)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $payslip->payslipid }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $payslip->year }} - {{ $payslip->weeknumber ?? $payslip->weekid }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <a href="{{ route('storeowner.employeepayroll.payslipsby-employee', base64_encode($payslip->employeeid)) }}" 
                                               class="text-blue-600 hover:text-blue-800">
                                                {{ ucfirst($payslip->firstname ?? '') }} {{ ucfirst($payslip->lastname ?? '') }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $payslip->groupname ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $payslip->emailid ?? '-' }}
                                        </td>
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
                                               class="text-red-600 hover:text-red-800 delete-payslip" 
                                               title="Delete"
                                               data-href="{{ route('storeowner.employeepayroll.destroy', base64_encode($payslip->payslipid)) }}"
                                               data-payslipid="{{ $payslip->payslipid }}">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No records found.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                @if($payslips->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $payslips->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="mt-4 text-sm text-gray-600">
        <strong>Legend(s):</strong>
        <i class="fas fa-eye ml-2"></i> View
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden" id="confirm-delete-modal">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Delete</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600" onclick="document.getElementById('confirm-delete-modal').classList.add('hidden')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">Are you sure you want to delete this Payslip?</p>
                </div>
                <div class="flex justify-end space-x-3 mt-4">
                    <button type="button" 
                            class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400"
                            onclick="document.getElementById('confirm-delete-modal').classList.add('hidden')">
                        Cancel
                    </button>
                    <form id="delete-confirm-form" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Wait for jQuery to be available
        (function() {
            var retries = 0;
            var maxRetries = 50;
            
            function initPayrollPage() {
                var $ = window.jQuery || window.$;
                
                if (!$ || typeof $ !== 'function') {
                    retries++;
                    if (retries < maxRetries) {
                        setTimeout(initPayrollPage, 100);
                        return;
                    } else {
                        console.error('jQuery failed to load');
                        return;
                    }
                }
                
                $(document).ready(function() {
                    // Search functionality
                    $('#searchbox').on('keyup', function() {
                        const searchTerm = $(this).val().toLowerCase();
                        $('#table-new tbody tr').each(function() {
                            const text = $(this).text().toLowerCase();
                            if (text.includes(searchTerm)) {
                                $(this).show();
                            } else {
                                $(this).hide();
                            }
                        });
                    });
                    
                    // Delete confirmation modal
                    $('.delete-payslip').on('click', function(e) {
                        e.preventDefault();
                        const deleteUrl = $(this).data('href');
                        $('#delete-confirm-form').attr('action', deleteUrl);
                        $('#confirm-delete-modal').removeClass('hidden');
                    });
                    
                    // Close modal when clicking outside
                    $('#confirm-delete-modal').on('click', function(e) {
                        if ($(e.target).is('#confirm-delete-modal')) {
                            $(this).addClass('hidden');
                        }
                    });
                });
            }
            
            initPayrollPage();
        })();
    </script>
    @endpush
</x-storeowner-app-layout>

