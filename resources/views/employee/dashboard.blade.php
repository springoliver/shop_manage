<x-employee-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Welcome, {{ Auth::guard('employee')->user()->name }}!</h3>

                    <div class="mb-4">
                        <p class="text-sm text-gray-600">Your Role:
                            <span class="font-semibold text-blue-600">
                                @if(Auth::guard('employee')->user()->hasRole('Owner'))
                                    Owner
                                @elseif(Auth::guard('employee')->user()->hasRole('Admin'))
                                    Admin
                                @elseif(Auth::guard('employee')->user()->hasRole('Viewer'))
                                    Viewer
                                @else
                                    No Role Assigned
                                @endif
                            </span>
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Owner Content -->
                        @role('Owner', 'employee')
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <h4 class="font-semibold text-red-800 mb-2">Owner Dashboard</h4>
                            <p class="text-sm text-red-600">You have full access to all features including:</p>
                            <ul class="text-sm text-red-600 mt-2 list-disc list-inside">
                                <li>Store Management</li>
                                <li>User Management</li>
                                <li>Financial Reports</li>
                                <li>System Settings</li>
                            </ul>
                        </div>
                        @endrole

                        <!-- Admin Content -->
                        @hasanyrole('Owner|Admin', 'employee')
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="font-semibold text-blue-800 mb-2">Admin Dashboard</h4>
                            <p class="text-sm text-blue-600">You have administrative access to:</p>
                            <ul class="text-sm text-blue-600 mt-2 list-disc list-inside">
                                <li>User Management</li>
                                <li>Inventory Management</li>
                                <li>Reports & Analytics</li>
                                <li>Order Management</li>
                            </ul>
                        </div>
                        @endhasanyrole

                        <!-- Viewer Content -->
                        @hasanyrole('Owner|Admin|Viewer', 'employee')
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <h4 class="font-semibold text-green-800 mb-2">Viewer Dashboard</h4>
                            <p class="text-sm text-green-600">You can view:</p>
                            <ul class="text-sm text-green-600 mt-2 list-disc list-inside">
                                <li>Basic Reports</li>
                                <li>Product Catalog</li>
                                <li>Order History</li>
                                <li>Profile Settings</li>
                            </ul>
                        </div>
                        @endhasanyrole
                    </div>

                    <!-- Role-specific Actions -->
                    <div class="mt-6">
                        <h4 class="font-semibold mb-3">Available Actions</h4>
                        <div class="flex flex-wrap gap-2">
                            @role('Owner', 'employee')
                            <a href="#" class="bg-red-600 text-white px-4 py-2 rounded text-sm hover:bg-red-700">Manage Store</a>
                            <a href="#" class="bg-red-600 text-white px-4 py-2 rounded text-sm hover:bg-red-700">User Management</a>
                            <a href="#" class="bg-red-600 text-white px-4 py-2 rounded text-sm hover:bg-red-700">Financial Reports</a>
                            @endrole

                            @hasanyrole('Owner|Admin', 'employee')
                            <a href="#" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">Manage Inventory</a>
                            <a href="#" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">View Reports</a>
                            @endhasanyrole

                            @hasanyrole('Owner|Admin|Viewer', 'employee')
                            <a href="#" class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700">View Products</a>
                            <a href="#" class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700">Order History</a>
                            @endhasanyrole
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-employee-app-layout>
