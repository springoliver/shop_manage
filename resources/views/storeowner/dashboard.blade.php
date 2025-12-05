<x-storeowner-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Store Owner Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Welcome, {{ Auth::guard('storeowner')->user()->firstname }} {{ Auth::guard('storeowner')->user()->lastname }}!</h3>

                    <div class="mb-4">
                        <p class="text-sm text-gray-600">
                            Username: <span class="font-semibold text-blue-600">{{ Auth::guard('storeowner')->user()->username }}</span>
                        </p>
                        <p class="text-sm text-gray-600">
                            Email: <span class="font-semibold text-blue-600">{{ Auth::guard('storeowner')->user()->emailid }}</span>
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Store Management -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="font-semibold text-blue-800 mb-2">Store Management</h4>
                            <p class="text-sm text-blue-600">Manage your store settings and information</p>
                            <ul class="text-sm text-blue-600 mt-2 list-disc list-inside">
                                <li>Update store details</li>
                                <li>Manage products</li>
                                <li>View orders</li>
                                <li>Track inventory</li>
                            </ul>
                        </div>

                        <!-- Financial Overview -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <h4 class="font-semibold text-green-800 mb-2">Financial Overview</h4>
                            <p class="text-sm text-green-600">Track your revenue and expenses</p>
                            <ul class="text-sm text-green-600 mt-2 list-disc list-inside">
                                <li>Sales reports</li>
                                <li>Revenue analytics</li>
                                <li>Payment history</li>
                                <li>Financial statements</li>
                            </ul>
                        </div>

                        <!-- Customer Management -->
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                            <h4 class="font-semibold text-purple-800 mb-2">Customer Management</h4>
                            <p class="text-sm text-purple-600">Manage your customer base</p>
                            <ul class="text-sm text-purple-600 mt-2 list-disc list-inside">
                                <li>Customer database</li>
                                <li>Order history</li>
                                <li>Customer feedback</li>
                                <li>Loyalty programs</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="mt-6">
                        <h4 class="font-semibold mb-3">Quick Actions</h4>
                        <div class="flex flex-wrap gap-2">
                            <a href="#" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">Manage Store</a>
                            <a href="#" class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700">View Reports</a>
                            <a href="#" class="bg-purple-600 text-white px-4 py-2 rounded text-sm hover:bg-purple-700">Customer List</a>
                            <a href="{{ route('storeowner.profile.edit') }}" class="bg-gray-600 text-white px-4 py-2 rounded text-sm hover:bg-gray-700">Edit Profile</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-storeowner-app-layout>

