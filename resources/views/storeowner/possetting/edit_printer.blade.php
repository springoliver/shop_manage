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
                        <a href="{{ route('storeowner.possetting.index') }}" class="ml-1 hover:text-gray-700">POS</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <a href="{{ route('storeowner.possetting.printers') }}" class="ml-1 hover:text-gray-700">Printers</a>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Success/Error Messages -->
    @if (session('success'))
        <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded relative" role="alert">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Navigation Tabs -->
            @include('storeowner.possetting._navigation')

            <!-- Edit Printer Form -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">
                    <i class="fas fa-align-justify mr-2"></i> Edit Printer
                </h2>
                
                <form action="{{ route('storeowner.possetting.update-printers') }}" method="POST" id="myform">
                    @csrf
                    <input type="hidden" name="pos_receiptprinters_id" value="{{ base64_encode($printer->pos_receiptprinters_id) }}">
                    
                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Printer Name <span class="text-red-500">*</span>
                        </label>
                        <div class="w-3/4">
                            <input type="text" name="pos_receiptprinters_name" id="pos_receiptprinters_name" 
                                   value="{{ old('pos_receiptprinters_name', $printer->pos_receiptprinters_name) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                                   required>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Printer IP Address <span class="text-red-500">*</span>
                        </label>
                        <div class="w-3/4">
                            <input type="text" name="pos_receiptprinters_ipadress" id="pos_receiptprinters_ipadress" 
                                   value="{{ old('pos_receiptprinters_ipadress', $printer->pos_receiptprinters_ipadress) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                                   required>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Type <span class="text-red-500">*</span>
                        </label>
                        <div class="w-3/4">
                            <select name="pos_receiptprinters_type" id="pos_receiptprinters_type" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" required>
                                <option value="network" {{ ($printer->pos_receiptprinters_type == 'network' || old('pos_receiptprinters_type') == 'network') ? 'selected' : '' }}>Network</option>
                                <option value="windows" {{ ($printer->pos_receiptprinters_type == 'windows' || old('pos_receiptprinters_type') == 'windows') ? 'selected' : '' }}>Windows</option>
                                <option value="linux" {{ ($printer->pos_receiptprinters_type == 'linux' || old('pos_receiptprinters_type') == 'linux') ? 'selected' : '' }}>Linux</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Profile <span class="text-red-500">*</span>
                        </label>
                        <div class="w-3/4">
                            <select name="pos_receiptprinters_profile" id="pos_receiptprinters_profile" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" required>
                                <option value="default" {{ ($printer->pos_receiptprinters_profile == 'default' || old('pos_receiptprinters_profile') == 'default') ? 'selected' : '' }}>Default</option>
                                <option value="simple" {{ ($printer->pos_receiptprinters_profile == 'simple' || old('pos_receiptprinters_profile') == 'simple') ? 'selected' : '' }}>Simple</option>
                                <option value="SP2000" {{ ($printer->pos_receiptprinters_profile == 'SP2000' || old('pos_receiptprinters_profile') == 'SP2000') ? 'selected' : '' }}>Star-branded</option>
                                <option value="TEP-200M" {{ ($printer->pos_receiptprinters_profile == 'TEP-200M' || old('pos_receiptprinters_profile') == 'TEP-200M') ? 'selected' : '' }}>Espon Tep</option>
                                <option value="P822D" {{ ($printer->pos_receiptprinters_profile == 'P822D' || old('pos_receiptprinters_profile') == 'P822D') ? 'selected' : '' }}>P822D</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Characters per line <span class="text-red-500">*</span>
                        </label>
                        <div class="w-3/4">
                            <input type="text" name="pos_receiptprinters_char_per_line" id="pos_receiptprinters_char_per_line" 
                                   value="{{ old('pos_receiptprinters_char_per_line', $printer->pos_receiptprinters_char_per_line) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                                   required>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Path
                        </label>
                        <div class="w-3/4">
                            <input type="text" name="pos_receiptprinters_path" id="pos_receiptprinters_path" 
                                   value="{{ old('pos_receiptprinters_path', $printer->pos_receiptprinters_path) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                            <p class="mt-2 text-sm text-gray-600">
                                <strong>For Windows:</strong> (Local USB, Serial or Parallel Printer): Share the printer and enter the share name for your printer here or for Server Message Block (SMB): enter as a smb:// url format such as <code>smb://computername/Receipt Printer</code><br>
                                <strong>For Linux:</strong> Parallel as <code>/dev/lp0</code>, USB as <code>/dev/usb/lp1</code>, USB-Serial as <code>/dev/ttyUSB0</code>, Serial as <code>/dev/ttyS0</code>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Port
                        </label>
                        <div class="w-3/4">
                            <input type="text" name="pos_receiptprinters_port" id="pos_receiptprinters_port" 
                                   value="{{ old('pos_receiptprinters_port', $printer->pos_receiptprinters_port) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                            <p class="mt-2 text-sm text-gray-600">Most printers are open on port 9100</p>
                        </div>
                    </div>

                    <!-- Receipt Template Options -->
                    <div class="border-t border-gray-200 mt-6 pt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Receipt Template Options</h3>
                        
                        @php
                            $receiptOptions = [
                                'store_name' => 'Store Name',
                                'table_number' => 'Table Number',
                                'customer_number' => 'Customer Number',
                                'server_name' => 'Server Name',
                                'receipt_number' => 'Receipt Number',
                                'date_time' => 'Date & Time',
                                'tax_summary' => 'Tax Summary',
                                'tender_details' => 'Tender Details',
                                'customer_address' => 'Customer Address',
                                'customer_email' => 'Customer Email',
                                'customer_tel' => 'Customer Tel',
                                'service_charge' => 'Service Charge',
                                'cut_paper' => 'Cut Paper',
                                'barcode' => 'Barcode',
                            ];
                        @endphp

                        @foreach($receiptOptions as $field => $label)
                            <div class="flex items-start gap-4 mb-4">
                                <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                                    {{ $label }}
                                </label>
                                <div class="w-3/4">
                                    <div class="flex items-center gap-6">
                                        <label class="flex items-center">
                                            <input type="radio" name="{{ $field }}" value="Enable" 
                                                   {{ ($printer->$field == 'Enable' || old($field) == 'Enable') ? 'checked' : '' }}
                                                   class="mr-2">
                                            <span>Enable</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="{{ $field }}" value="Disable" 
                                                   {{ ($printer->$field == 'Disable' || old($field) == 'Disable') ? 'checked' : '' }}
                                                   class="mr-2">
                                            <span>Disable</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="flex items-start gap-4 mb-6">
                            <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                                Service Charge Message
                            </label>
                            <div class="w-3/4">
                                <input type="text" name="sc_message" id="sc_message" 
                                       value="{{ old('sc_message', $printer->sc_message) }}"
                                       placeholder="e.g Service Charge not included"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 mt-8">
                        <button type="submit" class="px-6 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                            Update Template
                        </button>
                        <a href="{{ route('storeowner.possetting.printers') }}" 
                           class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-storeowner-app-layout>

