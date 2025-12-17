@section('page_header', 'Search & Print Roster')

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
                        <a href="{{ route('storeowner.roster.index') }}" class="ml-1 hover:text-gray-700">Roster</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Search & Print</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Search Form -->
    <div class="flex justify-between my-6 ">
        <div></div>
        <form action="{{ route('storeowner.roster.searchprintroster') }}" method="POST" class="flex items-center gap-4">
            @csrf
            <label for="dateofbirth" class="text-sm font-medium text-gray-700">Select Week:</label>
            <input type="date" 
                    name="dateofbirth" 
                    id="dateofbirth" 
                    value="{{ $dateofbirth ?? date('Y-m-d') }}" 
                    required
                    class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
            <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition">
                Search
            </button>
        </form>
    </div>

    <!-- Header -->
    <div class="text-center mb-6">
        <button onclick="window.print()" class="px-4 py-2 float-left bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition">
            Print
        </button>
        <h1 class="text-2xl font-bold text-gray-800">Roster for Week {{ $weeknumber }} / {{ $year }}</h1>
    </div>

    <!-- Roster Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Sunday</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Monday</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tuesday</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Wednesday</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Thursday</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Friday</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Saturday</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                        @endphp
                        @forelse($rostersByEmployee as $employeeId => $weekRosters)
                            @php
                                $employee = $employees->firstWhere('employeeid', $employeeId);
                                if (!$employee) continue;
                                $rosterByDay = [];
                                foreach($weekRosters as $roster) {
                                    $rosterByDay[$roster->day] = $roster;
                                }
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                    {{ $employee->firstname }} {{ $employee->lastname }}
                                </td>
                                @foreach($days as $day)
                                    <td class="px-4 py-3 text-sm text-gray-900 text-center">
                                        @if(isset($rosterByDay[$day]) && $rosterByDay[$day]->start_time != '00:00:00')
                                            {{ date('H:i', strtotime($rosterByDay[$day]->start_time)) }} - 
                                            {{ date('H:i', strtotime($rosterByDay[$day]->end_time)) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-6 text-center text-gray-500">No roster found for this week</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        @media print {
            /* Hide sidebar and navigation - target all possible sidebar elements */
            aside,
            aside[class*="bg-gray"],
            .sidebar,
            nav,
            [class*="sidebar"],
            [class*="navigation"],
            [id*="sidebar"],
            [id*="navigation"],
            .fixed,
            .sticky,
            /* Hide top header/navbar - more comprehensive */
            header,
            [role="banner"],
            [class*="header"],
            [id*="header"],
            [class*="navbar"],
            [id*="navbar"],
            [class*="top"],
            [id*="top"],
            /* Hide x-header component and page header section */
            x-header,
            [x-data*="header"],
            header.bg-white,
            header.shadow,
            /* Hide the page heading section */
            header.bg-white.shadow,
            div.max-w-7xl,
            /* Hide breadcrumbs and search form */
            .breadcrumb,
            nav[aria-label="Breadcrumb"],
            form,
            /* Hide buttons */
            button,
            .btn,
            .no-print {
                display: none !important;
                visibility: hidden !important;
                height: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            
            /* Set page margins - minimal margins, especially top */
            @page {
                margin: 1.0cm 1.5cm 1.5cm 1.5cm !important; /* Further reduced top margin to 0.2cm */
                size: A4 landscape; /* Use landscape for wider tables */
            }
            
            /* Remove any top spacing from html and body */
            html, body {
                margin: 0 !important;
                padding: 0 !important;
                height: auto !important;
            }
            
            /* Adjust body and main content for print */
            body {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                position: relative !important;
            }
            
            /* Hide any layout wrapper that might add top space */
            [class*="min-h-screen"],
            [class*="flex"],
            [class*="wrapper"],
            [id*="app"],
            [id*="root"] {
                margin: 0 !important;
                padding: 0 !important;
                min-height: auto !important;
            }
            
            /* Hide the main wrapper padding */
            main.flex-1,
            main[class*="p-"],
            div[class*="p-6"],
            div[class*="p-8"],
            div.bg-white.p-6 {
                padding: 0 !important;
                margin: 0 !important;
            }
            
            /* Ensure main content takes full width with minimal padding */
            main,
            .main-content,
            [class*="content"],
            [id*="content"],
            [class*="container"],
            [class*="container-fluid"],
            [class*="mx-auto"] {
                margin: 0 !important;
                padding: 0 !important; /* Removed padding to reduce top space */
                width: 100% !important;
                max-width: 100% !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
                margin-top: 0 !important; /* Explicitly set top margin to 0 */
                box-sizing: border-box !important;
            }
            
            /* Target the first visible element and remove its top margin */
            body > *:first-child,
            [class*="content"] > *:first-child,
            main > *:first-child,
            main > div:first-child {
                margin-top: 0 !important;
                padding-top: 0 !important;
            }
            
            /* Remove space-y-6 spacing */
            [class*="space-y"] {
                margin-top: 0 !important;
            }
            
            /* Remove top margin from title section */
            .text-center,
            [class*="text-center"] {
                margin-top: 0 !important;
                padding-top: 5px !important; /* Minimal top padding */
            }
            
            /* Remove any flex layouts that might include sidebar */
            [class*="flex"] {
                display: block !important;
            }
            
            /* Table container styling */
            .overflow-x-auto,
            [class*="overflow"],
            div[class*="rounded"] {
                overflow: visible !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-sizing: border-box !important;
            }
            
            /* Table styling for print - fit to page */
            table {
                border-collapse: collapse;
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                table-layout: auto;
                font-size: 11px; /* Slightly smaller font to fit more data */
                box-sizing: border-box !important;
            }
            
            /* Ensure table fits within page */
            thead, tbody, tfoot {
                width: 100% !important;
            }
            
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            
            /* Optimize cell padding and sizing */
            th, td {
                border: 1px solid #ddd;
                padding: 4px 6px !important; /* Reduced padding to fit more content */
                text-align: left;
                word-wrap: break-word;
                overflow-wrap: break-word;
                box-sizing: border-box !important;
            }
            
            /* Employee name column - can be narrower */
            th:first-child,
            td:first-child {
                width: auto;
                min-width: 120px;
                max-width: 150px;
            }
            
            /* Day columns - equal width distribution */
            th:not(:first-child),
            td:not(:first-child) {
                width: auto;
                text-align: center;
            }
            
            th {
                background-color: #f2f2f2 !important;
                font-weight: bold;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            /* Title styling - minimal top margin */
            h1 {
                margin: 0 0 8px 0 !important; /* Removed top margin */
                padding: 0 !important;
                font-size: 18px !important;
            }
            
            /* Remove top margin from table wrapper */
            .p-6,
            [class*="p-"] {
                padding-top: 5px !important; /* Minimal top padding */
            }
            
            /* Remove margins from divs that might add top space */
            div[class*="mb-"],
            .mb-6 {
                margin-bottom: 8px !important;
                margin-top: 0 !important;
            }
            
            /* Remove top margin from header div */
            div.text-center {
                margin-top: 0 !important;
                padding-top: 0 !important;
            }
            
            /* Remove any shadows or rounded corners for print */
            [class*="shadow"],
            [class*="rounded"] {
                box-shadow: none !important;
                border-radius: 0 !important;
            }
        }
    </style>
    @endpush
</x-storeowner-app-layout>
