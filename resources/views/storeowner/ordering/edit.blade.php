@section('page_header', 'Edit Purchase Order')
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
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Edit</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Success/Error Messages -->
    @if (session('success'))
        <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded relative" role="alert">
            <button type="button" class="absolute top-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">&times;</button>
            <span class="block sm:inline">{!! session('success') !!}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded relative" role="alert">
            <button type="button" class="absolute top-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">&times;</button>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="py-4">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <a href="{{ route('storeowner.ordering.waiting_approval') }}" 
                   class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Waiting Approval
                </a>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column: Products -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-file-text mr-2"></i> Products
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="overflow-y-auto max-h-96">
                            <table class="min-w-full divide-y divide-gray-200" id="table-product">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tax Band</th>
                                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Measure</th>
                                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Qty.</th>
                                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @if($storeProducts && count($storeProducts) > 0)
                                        @foreach($storeProducts as $product)
                                            <tr id="product_row_{{ $product->productid }}">
                                                <td class="px-3 py-2 text-sm text-gray-900" title="{{ $product->product_name }}">
                                                    <input type="hidden" id="product_name" name="product_name" value="{{ $product->product_name }}">
                                                    <input type="hidden" id="productid" name="productid" value="{{ $product->productid }}">
                                                    <input type="hidden" id="supplierid" name="supplierid" value="{{ $product->supplierid }}">
                                                    <input type="hidden" id="shipmentid" name="shipmentid" value="{{ $product->shipmentid ?? 0 }}">
                                                    <input type="hidden" id="departmentid" name="departmentid" value="{{ $product->departmentid ?? 0 }}">
                                                    {{ $product->product_name }}
                                                </td>
                                                <td class="px-3 py-2 text-center text-sm text-gray-900" title="{{ $product->product_price }}">
                                                    <input type="hidden" id="product_price" name="product_price" value="{{ $product->product_price }}">
                                                    {{ number_format($product->product_price, 2) }}
                                                </td>
                                                <td class="px-3 py-2 text-center text-sm text-gray-900" title="{{ $product->taxSetting->tax_name ?? '' }}">
                                                    <input type="hidden" id="tax_amount" name="tax_amount" value="{{ $product->taxSetting->tax_amount ?? 0 }}">
                                                    <input type="hidden" id="taxid" name="taxid" value="{{ $product->taxid ?? 0 }}">
                                                    {{ $product->taxSetting->tax_name ?? '-' }}
                                                </td>
                                                <td class="px-3 py-2 text-center text-sm text-gray-900" title="{{ $product->purchaseMeasure->purchasemeasure ?? '' }}">
                                                    <input type="hidden" id="purchasemeasuresid" name="purchasemeasuresid" value="{{ $product->purchasemeasuresid ?? 0 }}">
                                                    {{ $product->purchaseMeasure->purchasemeasure ?? '-' }}
                                                </td>
                                                <td class="px-3 py-2 text-center">
                                                    <input type="number" id="quantity" name="quantity" value="" size="1" min="1" class="w-16 px-2 py-1 border border-gray-300 rounded-md text-center">
                                                </td>
                                                <td class="px-3 py-2 text-center">
                                                    <button type="button" class="px-3 py-1 bg-gray-800 text-white rounded-md hover:bg-gray-700 text-sm" onclick="addToOrderSheet({{ $product->productid }}, this);">Add</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="px-4 py-3 text-center text-sm text-gray-500">
                                                No products found for this supplier
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Order Sheet -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-file-text mr-2"></i> Order Sheet to <span id="order_title">{{ $purchaseOrder->supplier->supplier_name ?? 'Supplier' }}</span>
                        </h3>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('storeowner.ordering.edit.submit', $purchaseOrder->purchase_orders_id) }}" method="POST" id="orderform" name="orderform">
                            @csrf
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200" id="table-order">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Qty.</th>
                                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tax</th>
                                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @if($purchasedProducts && $purchasedProducts->count() > 0)
                                            @foreach($purchasedProducts as $product)
                                                @php
                                                    $taxAmount = $product->tax_amount ?? 0;
                                                    $taxTotal = ($product->product_price * $taxAmount / 100) * $product->quantity;
                                                @endphp
                                                <tr id="order_row_{{ $product->productid }}" class="order-row">
                                                    <td class="px-3 py-2 text-sm text-gray-900" title="{{ $product->product_name ?? 'N/A' }}">
                                                        <input type="hidden" name="product_name[]" value="{{ $product->product_name ?? '' }}">
                                                        <input type="hidden" name="productid[]" value="{{ $product->productid }}">
                                                        <input type="hidden" name="supplierid[]" value="{{ $product->supplierid ?? 0 }}">
                                                        <input type="hidden" name="shipmentid[]" value="{{ $product->shipmentid ?? 0 }}">
                                                        <input type="hidden" name="departmentid[]" value="{{ $product->departmentid ?? 0 }}">
                                                        {{ $product->product_name ?? 'N/A' }}
                                                    </td>
                                                    <td class="px-3 py-2 text-center text-sm text-gray-900" title="{{ $product->product_price }}">
                                                        <input type="hidden" name="product_price[]" value="{{ $product->product_price }}">
                                                        {{ number_format($product->product_price, 2) }}
                                                    </td>
                                                    <td class="px-3 py-2 text-center text-sm text-gray-900">
                                                        <input type="hidden" name="quantity[]" value="{{ $product->quantity }}">
                                                        {{ $product->quantity }}
                                                    </td>
                                                    <td class="px-3 py-2 text-center text-sm text-gray-900">
                                                        <input type="hidden" name="tax_amount[]" value="{{ $taxAmount }}">
                                                        <input type="hidden" name="taxid[]" value="{{ $product->taxid ?? 0 }}">
                                                        <input type="hidden" name="tax[]" value="{{ $taxTotal }}">
                                                        + {{ number_format($taxAmount, 2) }}%
                                                    </td>
                                                    <td class="px-3 py-2 text-center text-sm text-gray-900">
                                                        <input type="hidden" name="product_total[]" value="{{ $product->totalamount }}">
                                                        {{ number_format($product->totalamount, 2) }}
                                                    </td>
                                                    <input type="hidden" name="purchasemeasuresid[]" value="{{ $product->purchasemeasuresid ?? 0 }}">
                                                    <td class="px-3 py-2 text-center">
                                                        <a href="javascript:;" title="Remove" class="text-red-600 hover:text-red-800" onclick="removeFromOrderSheet({{ $product->productid }});">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="6" class="px-4 py-3 text-center text-sm text-gray-500">
                                                    Add products to Order Sheet
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="px-3 py-2 text-sm text-gray-900">
                                                Purchase Order ID: {{ $purchaseOrder->purchase_orders_id }}
                                            </td>
                                            <th colspan="3" class="px-3 py-2 text-sm text-gray-900 text-right">
                                                Total: <span id="total_price_text">{{ number_format($purchaseOrder->total_amount ?? 0, 2) }}</span>
                                            </th>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="px-3 py-2 text-sm text-gray-900">
                                                @php
                                                    $orderDate = $purchaseOrder->delivery_date ?? $purchaseOrder->insertdate ?? '';
                                                    if ($orderDate) {
                                                        if (is_string($orderDate)) {
                                                            $orderDate = explode(' ', $orderDate)[0];
                                                        } else {
                                                            $orderDate = $orderDate->format('Y-m-d');
                                                        }
                                                    }
                                                @endphp
                                                Order Date: {{ $orderDate }}
                                            </td>
                                            <th colspan="3" class="px-3 py-2 text-sm text-gray-900 text-right">
                                                Tax: <span id="total_tax_text">{{ number_format($purchaseOrder->total_tax ?? 0, 2) }}</span>
                                            </th>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="px-3 py-2 text-sm text-gray-900">
                                                <div class="space-y-2">
                                                    <label class="flex items-center">
                                                        <input type="checkbox" name="deliverydocketstatus" value="1" {{ $purchaseOrder->deliverydocketstatus == 'Yes' ? 'checked' : '' }} class="mr-2">
                                                        Delivery Docket
                                                    </label>
                                                    <label class="flex items-center">
                                                        <input type="checkbox" name="invoicestatus" value="1" {{ $purchaseOrder->invoicestatus == 'Yes' ? 'checked' : '' }} class="mr-2">
                                                        Invoice
                                                    </label>
                                                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md" id="invoicenumber" name="invoicenumber" placeholder="Invoice Number" value="{{ $purchaseOrder->invoicenumber ?? '' }}">
                                                </div>
                                            </td>
                                            <th colspan="3" class="px-3 py-2 text-sm text-gray-900 text-right">
                                                Inc. Tax: <span id="total_inc_tax_text">{{ number_format($purchaseOrder->amount_inc_tax ?? 0, 2) }}</span>
                                            </th>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="px-3 py-2 text-sm text-gray-900">
                                                <div class="space-y-2">
                                                    <label class="flex items-center">
                                                        <input type="checkbox" name="creditnote" value="1" {{ $purchaseOrder->creditnote == 'Yes' ? 'checked' : '' }} class="mr-2">
                                                        Credit Note
                                                    </label>
                                                    <input type="text" name="creditnotedesc" class="w-full px-3 py-2 border border-gray-300 rounded-md" id="creditnotedesc" placeholder="Credit Note desc" value="{{ $purchaseOrder->creditnotedesc ?? '' }}">
                                                </div>
                                            </td>
                                            <th colspan="3" class="px-3 py-2 text-sm text-gray-900 text-right">
                                                &nbsp;
                                            </th>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="px-3 py-2 text-sm text-gray-900">
                                                Products note:<br />
                                                <input type="text" name="products_bought" class="w-full px-3 py-2 border border-gray-300 rounded-md" id="products_bought" placeholder="Products Note:" value="{{ $purchaseOrder->products_bought ?? '' }}">
                                            </td>
                                            <th colspan="3" class="px-3 py-2 text-sm text-gray-900 text-right">
                                                &nbsp;
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            
                            <input type="hidden" name="insertby" id="insertby" value="{{ auth('storeowner')->user()->username ?? '' }}"/>
                            <input type="hidden" name="order_total_price" id="total_price" value="{{ $purchaseOrder->total_amount ?? 0 }}">
                            <input type="hidden" name="order_total_tax" id="total_tax" value="{{ $purchaseOrder->total_tax ?? 0 }}">
                            <input type="hidden" name="order_total_inc_tax" id="total_inc_tax" value="{{ $purchaseOrder->amount_inc_tax ?? 0 }}">
                            <input type="hidden" name="purchase_orders_id" id="purchase_orders_id" value="{{ $purchaseOrder->purchase_orders_id }}">
                            
                            <div class="mt-4 flex justify-end">
                                <button type="submit" name="submit" value="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                    Update Order
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function addToOrderSheet(product_id, e) {
            $('#orderform button[type=submit]').removeAttr('disabled');
            
            var quantityInput = $('#product_row_' + product_id + ' #quantity');
            if(quantityInput.val() == '' || quantityInput.val() < 1) {
                alert('Please enter a valid quantity');
                return false;
            }
            
            var new_quantity = parseInt(quantityInput.val());
            if($('#order_row_' + product_id).length > 0) {
                var old_quantity = parseInt($('#order_row_' + product_id + ' input[name="quantity[]"]').val());
                new_quantity = new_quantity + old_quantity;
                $('#order_row_' + product_id).remove();
            }
            
            var productName = $('#product_row_' + product_id + ' #product_name').val();
            var productPrice = parseFloat($('#product_row_' + product_id + ' #product_price').val());
            var supplierId = $('#product_row_' + product_id + ' #supplierid').val();
            var shipmentId = $('#product_row_' + product_id + ' #shipmentid').val();
            var departmentId = $('#product_row_' + product_id + ' #departmentid').val();
            var taxAmount = parseFloat($('#product_row_' + product_id + ' #tax_amount').val() || 0);
            var taxId = $('#product_row_' + product_id + ' #taxid').val() || 0;
            var purchaseMeasureId = $('#product_row_' + product_id + ' #purchasemeasuresid').val() || 0;
            
            var productTotal = productPrice * new_quantity;
            var taxTotal = (productPrice * taxAmount / 100) * new_quantity;
            
            var htm = '';
            htm += '<tr id="order_row_' + product_id + '" class="order-row">';
            htm += '<td class="px-3 py-2 text-sm text-gray-900" title="' + productName + '">';
            htm += '<input type="hidden" name="product_name[]" value="' + productName + '">';
            htm += '<input type="hidden" name="productid[]" value="' + product_id + '">';
            htm += '<input type="hidden" name="supplierid[]" value="' + supplierId + '">';
            htm += '<input type="hidden" name="shipmentid[]" value="' + shipmentId + '">';
            htm += '<input type="hidden" name="departmentid[]" value="' + departmentId + '">';
            htm += productName;
            htm += '</td>';
            
            htm += '<td class="px-3 py-2 text-center text-sm text-gray-900" title="' + productPrice + '">';
            htm += '<input type="hidden" name="product_price[]" value="' + productPrice + '">';
            htm += productPrice.toFixed(2);
            htm += '</td>';
            
            htm += '<td class="px-3 py-2 text-center text-sm text-gray-900">';
            htm += '<input type="hidden" name="quantity[]" value="' + new_quantity + '">';
            htm += new_quantity;
            htm += '</td>';
            
            htm += '<td class="px-3 py-2 text-center text-sm text-gray-900">';
            htm += '<input type="hidden" name="tax_amount[]" value="' + taxAmount + '">';
            htm += '<input type="hidden" name="taxid[]" value="' + taxId + '">';
            htm += '<input type="hidden" name="tax[]" value="' + taxTotal + '">';
            htm += '+' + taxAmount.toFixed(2) + '%';
            htm += '</td>';
            
            htm += '<td class="px-3 py-2 text-center text-sm text-gray-900">';
            htm += '<input type="hidden" name="product_total[]" value="' + productTotal + '">';
            htm += productTotal.toFixed(2);
            htm += '</td>';
            
            htm += '<input type="hidden" name="purchasemeasuresid[]" value="' + purchaseMeasureId + '">';
            
            htm += '<td class="px-3 py-2 text-center">';
            htm += '<a href="javascript:;" title="Remove" class="text-red-600 hover:text-red-800" onclick="removeFromOrderSheet(' + product_id + ');"><i class="fas fa-trash"></i></a>';
            htm += '</td>';
            
            htm += '</tr>';
            
            quantityInput.val('');
            
            $('#table-order tbody').append(htm);
            calculateTotal();
        }
        
        function removeFromOrderSheet(product_id) {
            $('#order_row_' + product_id).remove();
            calculateTotal();
            if($('#table-order tbody tr.order-row').length == 0) {
                $('#orderform button[type=submit]').attr('disabled', 'disabled');
            }
        }
        
        function calculateTotal() {
            var total_price = 0;
            var total_tax = 0;
            var total_inc_tax = 0;
            
            $('#table-order tbody tr.order-row').each(function(index) {
                var productPrice = parseFloat($(this).find('input[name="product_price[]"]').val());
                var quantity = parseFloat($(this).find('input[name="quantity[]"]').val());
                var taxAmount = parseFloat($(this).find('input[name="tax_amount[]"]').val() || 0);
                
                var productTotal = productPrice * quantity;
                var tax = (productPrice * taxAmount / 100) * quantity;
                
                total_price += productTotal;
                total_tax += tax;
            });
            
            total_inc_tax = total_price + total_tax;
            
            $('#total_price').val(total_price.toFixed(2));
            $('#total_price_text').text(total_price.toFixed(2));
            
            $('#total_tax').val(total_tax.toFixed(2));
            $('#total_tax_text').text(total_tax.toFixed(2));
            
            $('#total_inc_tax').val(total_inc_tax.toFixed(2));
            $('#total_inc_tax_text').text(total_inc_tax.toFixed(2));
        }
    </script>
    @endpush
</x-storeowner-app-layout>

