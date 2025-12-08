<div class="bg-white rounded-lg shadow p-4 mb-6">
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('storeowner.possetting.sections') }}" 
           class="px-4 py-2 {{ request()->routeIs('storeowner.possetting.sections') || request()->routeIs('storeowner.possetting.edit-section') ? 'bg-gray-600' : 'bg-gray-800' }} text-white rounded-md hover:bg-gray-700">
            Sections
        </a>
        <a href="{{ route('storeowner.possetting.tables') }}" 
           class="px-4 py-2 {{ request()->routeIs('storeowner.possetting.tables') || request()->routeIs('storeowner.possetting.edit-table') ? 'bg-gray-600' : 'bg-gray-800' }} text-white rounded-md hover:bg-gray-700">
            Tables
        </a>
        <a href="{{ route('storeowner.possetting.floor-layout') }}" 
           class="px-4 py-2 {{ request()->routeIs('storeowner.possetting.floor-layout') || request()->routeIs('storeowner.possetting.floor-layout-section') ? 'bg-gray-600' : 'bg-gray-800' }} text-white rounded-md hover:bg-gray-700">
            Floor Layout
        </a>
        <a href="{{ route('storeowner.possetting.printers') }}" 
           class="px-4 py-2 {{ request()->routeIs('storeowner.possetting.printers') || request()->routeIs('storeowner.possetting.edit-printer') ? 'bg-gray-600' : 'bg-gray-800' }} text-white rounded-md hover:bg-gray-700">
            Printers Setup
        </a>
        <a href="{{ route('storeowner.possetting.sales-types') }}" 
           class="px-4 py-2 {{ request()->routeIs('storeowner.possetting.sales-types') || request()->routeIs('storeowner.possetting.edit-sales-type') ? 'bg-gray-600' : 'bg-gray-800' }} text-white rounded-md hover:bg-gray-700">
            Sales Types
        </a>
        <a href="{{ route('storeowner.possetting.payment-types') }}" 
           class="px-4 py-2 {{ request()->routeIs('storeowner.possetting.payment-types') || request()->routeIs('storeowner.possetting.edit-payment-type') ? 'bg-gray-600' : 'bg-gray-800' }} text-white rounded-md hover:bg-gray-700">
            Payment Types
        </a>
        <a href="{{ route('storeowner.possetting.refund-reasons') }}" 
           class="px-4 py-2 {{ request()->routeIs('storeowner.possetting.refund-reasons') || request()->routeIs('storeowner.possetting.edit-refund-reason') ? 'bg-gray-600' : 'bg-gray-800' }} text-white rounded-md hover:bg-gray-700">
            Refund Reasons
        </a>
        <a href="{{ route('storeowner.possetting.gratuity') }}" 
           class="px-4 py-2 {{ request()->routeIs('storeowner.possetting.gratuity') || request()->routeIs('storeowner.possetting.edit-gratuity') ? 'bg-gray-600' : 'bg-gray-800' }} text-white rounded-md hover:bg-gray-700">
            Graduity
        </a>
        <a href="{{ route('storeowner.possetting.discounts') }}" 
           class="px-4 py-2 {{ request()->routeIs('storeowner.possetting.discounts') || request()->routeIs('storeowner.possetting.edit-discount') ? 'bg-gray-600' : 'bg-gray-800' }} text-white rounded-md hover:bg-gray-700">
            Discounts
        </a>
        <a href="{{ route('storeowner.possetting.modifiers') }}" 
           class="px-4 py-2 {{ request()->routeIs('storeowner.possetting.modifiers') || request()->routeIs('storeowner.possetting.edit-modifier') ? 'bg-gray-600' : 'bg-gray-800' }} text-white rounded-md hover:bg-gray-700">
            Modifiers
        </a>
    </div>
</div>

