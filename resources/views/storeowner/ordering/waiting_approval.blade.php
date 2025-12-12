@section('page_header', 'Orders waiting approval')
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
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Orders waiting approval</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <div class="py-4">
        <div class="mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {!! session('success') !!}
                </div>
            @endif
            
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif
            
            @if(request('removed'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    Purchase Order removed successfully
                </div>
            @endif
            
            <div class="mb-4">
                <a href="{{ route('storeowner.ordering.order') }}" 
                   class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    New Purchase orders
                </a>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column: Orders Waiting Approval -->
                <div class="lg:col-span-1 bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-file-text mr-2"></i> Orders Waiting Approval
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="overflow-y-auto max-h-[600px]">
                            <table class="min-w-full divide-y divide-gray-200" id="table-supplier">
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @if($ordersWaitingApproval->count() > 0)
                                        @foreach($ordersWaitingApproval as $order)
                                            <tr onclick="selectOrder('{{ $order->purchase_orders_id }}', '{{ $order->supplier->supplier_name ?? 'N/A' }}', this);" 
                                                class="cursor-pointer hover:bg-gray-100">
                                                <td class="px-4 py-3 text-sm text-gray-900">
                                                    <a class="text-blue-600 hover:underline" 
                                                       title="{{ $order->supplier->supplier_name ?? 'N/A' }}">
                                                        {{ $order->supplier->supplier_name ?? 'N/A' }} (Purchase Order ID: {{ $order->purchase_orders_id }})
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td class="px-4 py-3 text-sm text-center text-gray-500">
                                                No orders waiting approval
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column: Order Details -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-800">
                                <i class="fas fa-file-text mr-2"></i> 
                                <span id="supplier_name"></span>
                                <span id="order_date" class="ml-2"></span>
                                <span id="purchase_orders_id" class="ml-2"></span>
                            </h3>
                            <div class="flex space-x-2">
                                <a href="javascript:;" id="po_delete_button" title="Delete PO" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                        <div class="p-6">
                            <form action="{{ route('storeowner.ordering.waiting_approval.submit') }}" method="POST" id="orderform" name="orderform">
                                @csrf
                                <div class="overflow-y-auto max-h-[400px] mb-4">
                                    <table class="min-w-full divide-y divide-gray-200" id="table-order">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Measure</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Qty.</th>
                                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tax</th>
                                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <tr>
                                                <td colspan="6" class="px-4 py-3 text-center text-sm text-gray-500">
                                                    Select Purchase Order to view details
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot class="bg-gray-50">
                                            <tr>
                                                <th colspan="5" class="px-4 py-3 text-right text-sm font-medium text-gray-700">Total:</th>
                                                <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">€ <span id="total_price_text">0</span></th>
                                            </tr>
                                            <tr>
                                                <th colspan="5" class="px-4 py-3 text-right text-sm font-medium text-gray-700">Tax:</th>
                                                <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">€ <span id="total_tax_text">0</span></th>
                                            </tr>
                                            <tr>
                                                <th colspan="5" class="px-4 py-3 text-right text-sm font-medium text-gray-700">Inc. Tax:</th>
                                                <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">€ <span id="total_inc_tax_text">0</span></th>
                                            </tr>
                                            <tr>
                                                <td colspan="6" class="px-4 py-3">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Invoice Number:</label>
                                                    <input type="text" 
                                                           name="invoicenumber" 
                                                           id="invoicenumber" 
                                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500"
                                                           required>
                                                </td>
                                            </tr>
                                            <input type="hidden" name="order_total_price" id="total_price" value="0">
                                            <input type="hidden" name="order_total_tax" id="total_tax" value="0">
                                            <input type="hidden" name="order_total_inc_tax" id="total_inc_tax" value="0">
                                            <input type="hidden" name="purchase_orders_id" id="purchase_orders_id" value="">
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="flex justify-between items-center mt-4">
                                    <div class="flex space-x-2">
                                        <a href="javascript:;" onclick="" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 hidden" id="email_button">Email</a>
                                        <a href="#" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 hidden" id="phone_button">Phone</a>
                                    </div>
                                    <button type="submit" 
                                            name="submit" 
                                            value="submit"
                                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed" 
                                            disabled
                                            id="submit_button">
                                        Order Complete
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        $(document).ready(function() {
            $('#email_button').hide();
            $('#phone_button').hide();
        });
        
        function deletePO(purchase_orders_id) {
            if(confirm("Do you really want to remove this Purchase Order?")) {
                $.ajax({
                    url: '{{ route("storeowner.ajax.remove-purchase-order") }}',
                    method: 'GET',
                    data: {
                        purchase_orders_id: purchase_orders_id
                    },
                    dataType: 'json',
                    success: function(res) {
                        if( res.status ) {
                            window.location.href = '{{ route("storeowner.ordering.waiting_approval") }}?removed=1';
                        } else {
                            alert("Error removing Purchase Order. Please try again later.");
                        }
                    },
                    error: function() {
                        alert("Error removing Purchase Order. Please try again later.");
                    }
                });
            }
            return false;
        }
        
        function selectOrder(purchase_orders_id, supplier_name, e) {
            $('#po_delete_button').attr('onclick', 'deletePO(' + purchase_orders_id + ')');
            
            $('#table-order tfoot #purchase_orders_id').val(purchase_orders_id);
            $('#table-supplier tr').css('background-color', '#FCFCFC');
            $(e).css('background-color', '#EEEEEE');
            
            $.ajax({
                url: '{{ route("storeowner.ajax.get-purchase-order-detail") }}',
                method: 'GET',
                data: {
                    purchase_orders_id: purchase_orders_id
                },
                dataType: 'json',
                beforeSend: function() {
                    $('#table-order tbody').html('<tr><td colspan="6" class="px-4 py-3 text-center text-sm text-gray-500">Loading...</td></tr>');
                },
                success: function(res) {
                    var htm = '';
                    if( res.data.length == 0 ) {
                        htm += '<tr><td colspan="6" class="px-4 py-3 text-center text-sm text-gray-500">No product found for this order</td></tr>';
                    } else {
                        for( var i = 0; i < res.data.length; i++ ) {
                            htm += '<tr id="product_row_' + res.data[i].productid + '">';
                            htm += '<td class="px-4 py-3 text-sm text-gray-900">';
                            htm += res.data[i].product_name;
                            htm += '</td>';
                            htm += '<td class="px-4 py-3 text-sm text-gray-900">';
                            htm += res.data[i].purchasemeasure || '';
                            htm += '</td>';
                            htm += '<td class="px-4 py-3 text-sm text-gray-900">';
                            htm += '€ ' + res.data[i].product_price;
                            htm += '</td>';
                            htm += '<td class="px-4 py-3 text-center text-sm text-gray-900">';
                            htm += 'x ' + res.data[i].quantity;
                            htm += '</td>';
                            htm += '<td class="px-4 py-3 text-center text-sm text-gray-900">';
                            var ttx = Number(res.data[i].totalamount) * (Number(res.data[i].tax_amount || 0) / 100);
                            htm += 'Tax: ' + (res.data[i].tax_name || 'N/A') + ' = €' + ttx.toFixed(2);
                            htm += '</td>';
                            htm += '<td class="px-4 py-3 text-right text-sm text-gray-900">';
                            htm += '€ ' + res.data[i].totalamount;
                            htm += '</td>';
                            htm += '</tr>';
                        }
                    }
                    
                    $('#supplier_name').text(supplier_name);
                    var orderDate = res.purchase_order.insertdate ? new Date(res.purchase_order.insertdate).toLocaleDateString() : '';
                    $('#order_date').text("Order Date: " + orderDate);
                    $('#purchase_orders_id').text("Purchase Order ID: " + res.purchase_order.purchase_orders_id);
                    
                    $('#table-order tfoot #total_price_text').text(res.purchase_order.total_amount || 0);
                    $('#table-order tfoot #total_tax_text').text(res.purchase_order.total_tax || 0);
                    $('#table-order tfoot #total_inc_tax_text').text(res.purchase_order.amount_inc_tax || 0);
                    
                    $('#table-order tfoot #total_price').val(res.purchase_order.total_amount || 0);
                    $('#table-order tfoot #total_tax').val(res.purchase_order.total_tax || 0);
                    $('#table-order tfoot #total_inc_tax').val(res.purchase_order.amount_inc_tax || 0);
                    
                    if( res.purchase_order.supplier_email && res.purchase_order.supplier_email != '' ) {
                        $('#email_button').attr('onclick', 'emailOrderSheet(' + res.purchase_order.purchase_orders_id + ')');
                        $('#email_button').html("<i class='fa fa-envelope'></i> Order by email");
                        $('#email_button').removeClass('hidden').show();
                    } else {
                        $('#email_button').hide();
                    }
                    
                    if( res.purchase_order.supplier_phone && res.purchase_order.supplier_phone != '' ) {
                        $('#phone_button').attr('href', 'tel:' + res.purchase_order.supplier_phone);
                        $('#phone_button').html("<i class='fa fa-phone'></i> " + res.purchase_order.supplier_phone);
                        $('#phone_button').removeClass('hidden').show();
                    } else {
                        $('#phone_button').hide();
                    }
                    
                    $('#submit_button').prop('disabled', false);
                },
                error: function() {
                    $('#table-order tbody').html('<tr><td colspan="6" class="px-4 py-3 text-center text-sm text-red-500">Error loading order details</td></tr>');
                }
            });
        }
        
        function emailOrderSheet(purchase_orders_id) {
            $.ajax({
                url: '{{ route("storeowner.ajax.send-order-sheet") }}',
                method: 'GET',
                data: {
                    purchase_orders_id: purchase_orders_id
                },
                dataType: 'json',
                beforeSend: function() {
                    // Can add loading indicator here
                },
                success: function(res) {
                    if( res.status ) {
                        alert("Order sheet has been sent to supplier!");
                    } else {
                        alert(res.message || "Error sending order sheet. Please try again later.");
                    }
                },
                error: function() {
                    alert("Error sending order sheet. Please try again later.");
                }
            });
        }
    </script>
    @endpush
</x-storeowner-app-layout>

