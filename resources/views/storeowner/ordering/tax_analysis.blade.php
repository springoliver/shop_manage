@section('page_header', 'Reports - Monthly')
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
                        <a href="{{ route('storeowner.ordering.index') }}" class="ml-1 hover:text-gray-700">Ordering</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Orders Reporting</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <div class="py-4">
        <div class="mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Navigation Tabs -->
            <div class="mb-4">
                <div class="flex space-x-2 border-b border-gray-200 mb-4">
                    <a href="{{ route('storeowner.ordering.tax_analysis') }}" 
                       class="inline-block px-4 py-2 text-blue-600 border-b-2 border-blue-600 font-medium">
                        Tax Analysis
                    </a>
                    <a href="{{ route('storeowner.ordering.add_invoice') }}" 
                       class="inline-block px-4 py-2 text-gray-600 hover:text-blue-600 hover:border-b-2 hover:border-blue-600">
                        Add Bills
                    </a>
                </div>
                <div class="flex space-x-2 border-b border-gray-200">
                    <a href="{{ route('storeowner.ordering.reports_chart_yearly') }}" 
                       class="inline-block px-4 py-2 text-gray-600 hover:text-blue-600 hover:border-b-2 hover:border-blue-600">
                        Yearly Chart View
                    </a>
                    <a href="{{ route('storeowner.ordering.reports_chart_monthly') }}" 
                       class="inline-block px-4 py-2 text-gray-600 hover:text-blue-600 hover:border-b-2 hover:border-blue-600">
                        Monthly Chart View
                    </a>
                    <a href="{{ route('storeowner.ordering.reports_chart_weekly') }}" 
                       class="inline-block px-4 py-2 text-gray-600 hover:text-blue-600 hover:border-b-2 hover:border-blue-600">
                        Weekly Chart View
                    </a>
                </div>
            </div>

            <!-- Report Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-file-text mr-2"></i> Orders Reporting
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="table-report">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tax</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($allPurchOrdersTotal as $item)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        @php
                                            $monthName = date('F', mktime(0, 0, 0, $item->pmonth, 1));
                                            $year = $item->pyear;
                                        @endphp
                                        <a href="{{ route('storeowner.ordering.add_bills', base64_encode($item->delivery_date)) }}" 
                                           class="text-blue-600 hover:underline">
                                            {{ $monthName }} - {{ $year }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">€ {{ number_format($item->total_amount ?? 0, 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">€ {{ number_format($item->total_tax ?? 0, 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">€ {{ number_format($item->amount_inc_tax ?? 0, 2) }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <a href="{{ route('storeowner.ordering.add_bills', base64_encode($item->delivery_date)) }}" 
                                           class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-sm">
                                            Add
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-3 text-center text-sm text-gray-500">
                                        No orders found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-storeowner-app-layout>

