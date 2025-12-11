@section('page_header', 'My Roster')
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
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">My Roster</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Roster List Card -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <div class="flex items-center mb-4">
                <i class="fas fa-th mr-2 text-gray-600"></i>
                <h5 class="text-lg font-semibold text-gray-800">My Roster</h5>
            </div>

            @if($rosters->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Week no:</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($rosters as $roster)
                                @php
                                    $insertDateTime = is_string($roster->insertdatetime) 
                                        ? $roster->insertdatetime 
                                        : ($roster->insertdatetime ? $roster->insertdatetime->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'));
                                    $time = strtotime($insertDateTime);
                                    $mymonth = date('F', $time);
                                    $myyear = date('Y', $time);
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        Week {{ $roster->weeknumber ?? 'N/A' }} - {{ $mymonth }} - {{ $myyear }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('employee.roster.show', [
                                            'storeid' => base64_encode($roster->storeid),
                                            'employeeid' => base64_encode($roster->employeeid),
                                            'weekid' => base64_encode($roster->weekid)
                                        ]) }}" 
                                           class="text-indigo-600 hover:text-indigo-900" 
                                           title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-gray-500">No roster found.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Legend -->
    <div class="mt-4 text-sm text-gray-600">
        <strong>Legend(s):</strong> 
        <i class="fas fa-eye ml-2 mr-1"></i> View
    </div>
</x-employee-app-layout>

