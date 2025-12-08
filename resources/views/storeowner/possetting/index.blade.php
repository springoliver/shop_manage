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
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">POS</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Navigation Tabs -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('storeowner.possetting.sections') }}" 
                       class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                        Sections
                    </a>
                    <a href="{{ route('storeowner.possetting.tables') }}" 
                       class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                        Tables
                    </a>
                    <a href="{{ route('storeowner.possetting.floor-layout') }}" 
                       class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                        Floor Layout
                    </a>
                    <a href="{{ route('storeowner.possetting.printers') }}" 
                       class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                        Printers Setup
                    </a>
                    <a href="{{ route('storeowner.possetting.sales-types') }}" 
                       class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                        Sales Types
                    </a>
                    <a href="{{ route('storeowner.possetting.payment-types') }}" 
                       class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                        Payment Types
                    </a>
                    <a href="{{ route('storeowner.possetting.refund-reasons') }}" 
                       class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                        Refund Reasons
                    </a>
                    <a href="{{ route('storeowner.possetting.gratuity') }}" 
                       class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                        Graduity
                    </a>
                    <a href="{{ route('storeowner.possetting.discounts') }}" 
                       class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                        Discounts
                    </a>
                    <a href="{{ route('storeowner.possetting.modifiers') }}" 
                       class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                        Modifiers
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-storeowner-app-layout>

