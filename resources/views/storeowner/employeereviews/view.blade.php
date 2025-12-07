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
                        <a href="{{ route('storeowner.employeereviews.index') }}" class="ml-1 hover:text-gray-700">Employee Reviews</a>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-end mb-4">
                    <button onclick="printDiv('printableArea')" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                        Print
                    </button>
                </div>
                
                <div id="printableArea">
                    @if($reviews->count() > 0)
                        <div class="mb-6">
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <span class="font-medium">Review Date:</span>
                                    <span class="ml-2">{{ $reviews->first()->insertdatetime ? \Carbon\Carbon::parse($reviews->first()->insertdatetime)->format('Y-m-d H:i:s') : '-' }}</span>
                                </div>
                                <div>
                                    <span class="font-medium">Employee:</span>
                                    <span class="ml-2">{{ ucfirst($reviews->first()->employee->firstname ?? '') }} {{ ucfirst($reviews->first()->employee->lastname ?? '') }}</span>
                                </div>
                                <div>
                                    <span class="font-medium">Next Review Date:</span>
                                    <span class="ml-2">{{ $reviews->first()->next_review_date ? \Carbon\Carbon::parse($reviews->first()->next_review_date)->format('Y-m-d') : '-' }}</span>
                                </div>
                            </div>
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Review Subject</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Comment</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($reviews as $review)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $review->reviewSubject->subject_name ?? '-' }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900">
                                                    {{ $review->comments }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function printDiv(divName) {
            const printContents = document.getElementById(divName).innerHTML;
            const originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload();
        }
    </script>
    @endpush
</x-storeowner-app-layout>

