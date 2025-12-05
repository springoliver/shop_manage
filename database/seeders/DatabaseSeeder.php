<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Employee;
use App\Models\Store;
use App\Models\Group;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create roles for employee guard
        foreach (['Owner', 'Admin', 'Viewer'] as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'employee']);
        }

        // Create sample Admin
        Admin::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        // Create sample Employee
        // $employee = Employee::create([
        //     'name' => 'Employee User',
        //     'email' => 'employee@example.com',
        //     'password' => bcrypt('password'),
        //     'email_verified_at' => now(),
        // ]);

        // // Create sample Store and Group
        // $store = Store::create(['name' => 'Main Store']);
        // $group = Group::create(['name' => 'Default Group', 'store_id' => $store->id]);

        // // Attach employee to group
        // $employee->groups()->attach($group->id);

        // // Assign role to employee (no team context needed)
        // $employee->assignRole('Owner');
    }
}
