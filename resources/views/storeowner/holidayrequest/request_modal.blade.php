<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead>
            <tr class="bg-gray-50">
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">From Date</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">To Date</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($requests as $request)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $request->from_date->format('F d, Y') }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $request->to_date->format('F d, Y') }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $request->subject }}</td>
                    <td class="px-4 py-3 text-sm">
                        @if($request->status == 'Pending')
                            <span class="px-2 py-1 text-xs font-medium rounded-md bg-yellow-100 text-yellow-800">Pending</span>
                        @elseif($request->status == 'Declined')
                            <span class="px-2 py-1 text-xs font-medium rounded-md bg-red-100 text-red-800">Declined</span>
                        @else
                            <span class="px-2 py-1 text-xs font-medium rounded-md bg-green-100 text-green-800">Approved</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-6 text-center text-gray-500">No requests found for this employee</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

