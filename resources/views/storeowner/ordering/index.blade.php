@section('page_header', 'Ordering')
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
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Add</span>
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
                <!-- Left Column: Suppliers -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-file-text mr-2"></i> Suppliers
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="overflow-y-auto max-h-96">
                            <table class="min-w-full divide-y divide-gray-200" id="table-supplier">
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @if($storeSuppliers && count($storeSuppliers) > 0)
                                        @foreach($storeSuppliers as $supplier)
                                            <tr onclick="selectSupplier('{{ $supplier->supplierid }}', '{{ $supplier->supplier_name }}', this);" 
                                                class="cursor-pointer hover:bg-gray-100" style="background-color: #FCFCFC;">
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900" title="{{ $supplier->supplier_name }}">
                                                    <a class="text-blue-600 hover:text-blue-800">
                                                        {{ $supplier->supplier_name }}
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td class="px-4 py-3 text-center text-sm text-gray-500">
                                                No suppliers found.
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div>
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
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tax Band</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Measure</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty.</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr>
                                            <td colspan="6" class="px-4 py-3 text-center text-sm text-gray-500">
                                                Please select supplier to load products
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Order Sheet -->
                    <div class="mt-4 bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">
                                <i class="fas fa-file-text mr-2"></i> Order Sheet <span id="order_title"></span>
                            </h3>
                        </div>
                        <div class="p-6">
                            <form action="{{ route('storeowner.ordering.order.submit') }}" method="POST" id="orderform" name="orderform">
                                @csrf
                                <div class="overflow-y-auto max-h-96 mb-4">
                                    <table class="min-w-full divide-y divide-gray-200" id="table-order">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty.</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tax</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <tr>
                                                <td colspan="6" class="px-4 py-3 text-center text-sm text-gray-500">
                                                    Add products to Order Sheet
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot class="bg-gray-50">
                                            <tr>
                                                <td colspan="3" class="px-4 py-3 text-sm text-gray-700">
                                                    Purchase Order ID: [will be generated after request]
                                                </td>
                                                <th colspan="3" class="px-4 py-3 text-right text-sm font-medium text-gray-700">
                                                    Total: <span id="total_price_text">0</span>
                                                </th>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="px-4 py-3">
                                                    <input type="date" 
                                                        name="delivery_date" 
                                                        id="delivery_date" 
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500"
                                                        required>
                                                </td>
                                                <th colspan="3" class="px-4 py-3 text-right text-sm font-medium text-gray-700">
                                                    Tax: <span id="total_tax_text">0</span>
                                                </th>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="px-4 py-3">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Note:</label>
                                                    <textarea name="po_note" 
                                                            id="po_note" 
                                                            rows="2" 
                                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500"></textarea>
                                                </td>
                                                <th colspan="3" class="px-4 py-3 text-right text-sm font-medium text-gray-700">
                                                    Inc. Tax: <span id="total_inc_tax_text">0</span>
                                                </th>
                                            </tr>
                                            <input type="hidden" name="order_total_price" id="total_price" value="0">
                                            <input type="hidden" name="order_total_tax" id="total_tax" value="0">
                                            <input type="hidden" name="order_total_inc_tax" id="total_inc_tax" value="0">
                                            <input type="hidden" name="order_supplier_id" id="supplier_id" value="">
                                            <input type="hidden" name="insertby" id="insertby" value="{{ Auth::guard('storeowner')->user()->username ?? '' }}">
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="mt-4">
                                    <button type="submit" 
                                            name="submit" 
                                            value="submit"
                                            class="w-full px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 disabled:bg-gray-400 disabled:cursor-not-allowed" 
                                            disabled>
                                        Request Approval
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
        function selectSupplier(supplier_id, supplier_name, e) {
            if(confirm("Switching supplier will reset the current order sheet. Proceed?")) {
                resetOrderSheet();
                $('#supplier_id').val(supplier_id);
                $('#table-supplier tr').css('background-color', '#FCFCFC');
                $(e).css('background-color', '#EEEEEE');
                
                $.ajax({
                    url: '{{ route("storeowner.ajax.products-by-supplier") }}',
                    method: 'GET',
                    data: {
                        supplier_id: supplier_id
                    },
                    dataType: 'json',
                    beforeSend: function() {
                        $('#table-product tbody').html('<tr><td colspan="6" class="px-4 py-3 text-center text-sm text-gray-500">Loading...</td></tr>');
                    },
                    success: function(res) {
                        var htm = '';
                        if(res.data.length == 0) {
                            htm += '<tr><td colspan="6" class="px-4 py-3 text-center text-sm text-gray-500">No product found for this supplier</td></tr>';
                        } else {
                            for(var i = 0; i < res.data.length; i++) {
                                htm += '<tr id="product_row_' + res.data[i].productid + '">';
                                htm += '<td class="px-3 py-2 text-sm text-gray-900" title="' + res.data[i].product_name + '">';
                                htm += '<input type="hidden" id="product_name" name="product_name" value="' + res.data[i].product_name + '">';
                                htm += '<input type="hidden" id="productid" name="productid" value="' + res.data[i].productid + '">';
                                htm += '<input type="hidden" id="supplierid" name="supplierid" value="' + res.data[i].supplierid + '">';
                                htm += '<input type="hidden" id="shipmentid" name="shipmentid" value="' + res.data[i].shipmentid + '">';
                                htm += '<input type="hidden" id="departmentid" name="departmentid" value="' + res.data[i].departmentid + '">';
                                htm += res.data[i].product_name;
                                htm += '</td>';
                                
                                htm += '<td class="px-3 py-2 text-sm text-gray-900" title="' + res.data[i].product_price + '">';
                                htm += '<input type="hidden" id="product_price" name="product_price" value="' + res.data[i].product_price + '">';
                                htm += res.data[i].product_price;
                                htm += '</td>';
                                
                                htm += '<td class="px-3 py-2 text-sm text-gray-900" title="' + (res.data[i].tax_name || '') + '">';
                                htm += '<input type="hidden" id="tax_amount" name="tax_amount" value="' + (res.data[i].tax_amount || 0) + '">';
                                htm += '<input type="hidden" id="taxid" name="taxid" value="' + (res.data[i].taxid || 0) + '">';
                                htm += (res.data[i].tax_name || '-');
                                htm += '</td>';
                                
                                htm += '<td class="px-3 py-2 text-sm text-gray-900" title="' + (res.data[i].purchasemeasure || '') + '">';
                                htm += '<input type="hidden" id="purchasemeasuresid" name="purchasemeasuresid" value="' + (res.data[i].purchasemeasuresid || 0) + '">';
                                htm += (res.data[i].purchasemeasure || '-');
                                htm += '</td>';
                                
                                htm += '<td class="px-3 py-2 text-center">';
                                htm += '<input type="number" id="quantity" name="quantity" value="" size="1" min="1" class="w-16 px-2 py-1 border border-gray-300 rounded-md text-center">';
                                htm += '</td>';
                                
                                htm += '<td class="px-3 py-2 text-center">';
                                htm += '<button type="button" class="px-3 py-1 bg-gray-800 text-white rounded-md hover:bg-gray-700 text-sm" onclick="addToOrderSheet(' + res.data[i].productid + ', this);">Add</button>';
                                htm += '</td>';
                                
                                htm += '</tr>';
                            }
                        }
                        
                        $('#table-product tbody').html('');
                        $('#table-product tbody').html(htm);
                        $('#order_title').text(' to ' + supplier_name);
                    },
                    error: function() {
                        $('#table-product tbody').html('<tr><td colspan="6" class="px-4 py-3 text-center text-sm text-red-500">Error loading products. Please try again.</td></tr>');
                    }
                });
            } else {
                return false;
            }
        }
        
        function addToOrderSheet(product_id, e) {
            $('#orderform button[type=submit]').removeAttr('disabled');
            
            var quantityInput = $('#product_row_' + product_id + ' #quantity');
            if(quantityInput.val() == '' || quantityInput.val() < 1) {
                alert('Please enter a valid quantity');
                return false;
            }
            
            var new_quantity = parseInt(quantityInput.val());
            if($('#order_row_' + product_id).length > 0) {
                var old_quantity = parseInt($('#order_row_' + product_id + ' #quantity').val());
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
            htm += productPrice;
            htm += '</td>';
            
            htm += '<td class="px-3 py-2 text-center text-sm text-gray-900">';
            htm += '<input type="hidden" name="quantity[]" value="' + new_quantity + '">';
            htm += new_quantity;
            htm += '</td>';
            
            htm += '<td class="px-3 py-2 text-center text-sm text-gray-900">';
            htm += '<input type="hidden" name="tax_amount[]" value="' + taxAmount + '">';
            htm += '<input type="hidden" name="taxid[]" value="' + taxId + '">';
            htm += '<input type="hidden" name="tax[]" value="' + taxTotal + '">';
            htm += taxAmount + '%';
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
        
        function resetOrderSheet() {
            $('#table-order tbody tr.order-row').remove();
            
            $('#total_price').val(0);
            $('#total_price_text').text('0');
            
            $('#total_tax').val(0);
            $('#total_tax_text').text('0');
            
            $('#total_inc_tax').val(0);
            $('#total_inc_tax_text').text('0');
            
            $('#delivery_date').val('');
            $('#po_note').val('');
            
            $('#orderform button[type=submit]').attr('disabled', 'disabled');
        }
    </script>
    @endpush
</x-storeowner-app-layout>

