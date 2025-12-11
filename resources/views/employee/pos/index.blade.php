@section('page_header', 'POINT OF SALE')
<x-employee-app-layout>
    <!-- Breadcrumb -->
    <div class="mb-6">
        <nav class="flex text-gray-500 text-sm" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('employee.dashboard') }}" class="inline-flex items-center hover:text-gray-700">
                        <i class="fas fa-home mr-2"></i>
                        Home
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">POINT OF SALE</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-xs-6 col-md-4"></div>
        <div class="col-xs-6 col-md-4">
            <p>&nbsp;</p>
            <a href="{{ route('employee.pos.main') }}" 
               class="block w-full px-6 py-4 bg-gray-800 text-white text-center text-lg font-semibold rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition"
               target="_blank"
               title="START POS">
                START POS
            </a>
            <p>&nbsp;</p>
        </div>
        <div class="col-xs-6 col-md-4"></div>
    </div>
</x-employee-app-layout>

