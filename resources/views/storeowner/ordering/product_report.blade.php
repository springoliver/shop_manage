@section('page_header', 'Ordered Products Reporting')
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

            <form action="{{ route('storeowner.ordering.product_report') }}" method="POST" id="myform" name="myform">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date From:</label>
                        <input type="date" 
                               name="date_from" 
                               value="{{ old('date_from', request('date_from')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date To:</label>
                        <input type="date" 
                               name="date_to" 
                               value="{{ old('date_to', request('date_to')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Supplier:</label>
                        <select name="supplierid" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                            <option value="">--All Suppliers--</option>
                            @foreach($storeSuppliers as $supplier)
                                <option value="{{ $supplier->supplierid }}" {{ old('supplierid', request('supplierid')) == $supplier->supplierid ? 'selected' : '' }}>
                                    {{ $supplier->supplier_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">&nbsp;</label>
                        <button type="submit" name="submit" value="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Search
                        </button>
                    </div>
                </div>
            </form>

            <!-- Navigation Tabs -->
            <div class="mb-4">
                <ul class="flex space-x-2 border-b border-gray-200">
                    <li>
                        <a href="{{ route('storeowner.ordering.report') }}" 
                           class="inline-block px-4 py-2 text-gray-600 hover:text-blue-600 hover:border-b-2 hover:border-blue-600">
                            Purchase orders
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('storeowner.ordering.product_report') }}" 
                           class="inline-block px-4 py-2 text-blue-600 border-b-2 border-blue-600 font-medium">
                            Purchased Products
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('storeowner.ordering.missing_delivery_dockets') }}" 
                           class="inline-block px-4 py-2 text-gray-600 hover:text-blue-600 hover:border-b-2 hover:border-blue-600">
                            Delivery Dockets
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('storeowner.ordering.credit_notes') }}" 
                           class="inline-block px-4 py-2 text-gray-600 hover:text-blue-600 hover:border-b-2 hover:border-blue-600">
                            Credit Notes
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Report Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-file-text mr-2"></i> Ordered Products Reporting
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="table-report">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier Name</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($productReports as $productReport)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $productReport->department->departmentname ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $productReport->supplier->supplier_name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $productReport->product->product_name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $productReport->insertdate ? $productReport->insertdate->format('Y-m-d') : '' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $productReport->quantity }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-3 text-center text-sm text-gray-500">
                                        No products found
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

