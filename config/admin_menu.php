<?php

return [
    [
        'label' => 'Dashboard',
        'route' => 'admin.dashboard',
        'enabled' => false,
        'icon' => '<i class="fa fa-tachometer text-8 text-8"></i>'
    ],
    [
        'label' => 'General Settings',
        'route' => 'admin.settings.index',
        'enabled' => true,
        'icon' => '<i class="fa fa-cog text-8 text-8"></i>'
    ],
    [
        'label' => 'Pages',
        'route' => 'admin.pages.index',
        'enabled' => true,
        'icon' => '<i class="fa fa-file text-8 text-8"></i>'
    ],
    [
        'label' => 'Store Owners',
        'route' => 'admin.store-owners.index',
        'enabled' => true,
        'icon' => '<i class="fa fa-user text-8 text-8"></i>'
    ],
    [
        'label' => 'Stores',
        'route' => 'admin.stores.index',
        'enabled' => true,
        'icon' => '<i class="fa fa-th text-8 text-8"></i>'
    ],
    [
        'label' => 'User Group',
        'route' => 'admin.user-groups.index',
        'enabled' => true,
        'icon' => '<i class="fa fa-users text-8 text-8"></i>'
    ],
    [
        'label' => 'Categories',
        'route' => 'admin.store-types.index',
        'enabled' => true,
        'icon' => '<i class="fa fa-list text-8 text-8"></i>'
    ],
    [
        'label' => 'Department',
        'route' => 'admin.departments.index',
        'enabled' => true,
        'icon' => '<i class="fa fa-university text-8 text-8"></i>'
    ],
    [
        'label' => 'Module Management',
        'route' => 'admin.modules.index',
        'enabled' => true,
        'icon' => '<i class="fa fa-list text-8 text-8"></i>'
    ],
    [
        'label' => 'Request For Modules',
        'route' => 'admin.requested-modules.index',
        'enabled' => true,
        'icon' => '<i class="fa fa-list text-8 text-8"></i>'
    ],
    [
        'label' => 'Email Format',
        'route' => 'admin.email-formats.index',
        'enabled' => true,
        'icon' => '<i class="fa fa-envelope text-8 text-8"></i>'
    ],
];
