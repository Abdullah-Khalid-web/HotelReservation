<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */

        $permissions = [
            // Dashboard
            'view dashboard',

            // Hotels
            'view hotels',
            'create hotels',
            'edit hotels',
            'delete hotels',

            // Tables
            'view tables',
            'create tables',
            'edit tables',
            'delete tables',

            // Reservations
            'view reservations',
            'create reservations',
            'edit reservations',
            'delete reservations',
            'confirm reservations',
            'cancel reservations',

            // Products
            'view products',
            'create products',
            'edit products',
            'delete products',

            // Pre-Orders
            'view pre-orders',
            'create pre-orders',
            'edit pre-orders',
            'delete pre-orders',
            'manage pre-orders',   // kitchen: confirm/preparing/ready/served

            // Users
            'view users',
            'create users',
            'edit users',
            'delete users',

            // Voice
            'use voice reservation',
            'view voice requests',

            // Settings
            'manage settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name'       => $permission,
                'guard_name' => 'web',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */

        $superAdmin = Role::firstOrCreate([
            'name'       => 'superadmin',
            'guard_name' => 'web',
        ]);

        $admin = Role::firstOrCreate([
            'name'       => 'admin',
            'guard_name' => 'web',
        ]);

        $kitchen = Role::firstOrCreate([
            'name'       => 'kitchen',
            'guard_name' => 'web',
        ]);

        $user = Role::firstOrCreate([
            'name'       => 'user',
            'guard_name' => 'web',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Super Admin — everything
        |--------------------------------------------------------------------------
        */

        $superAdmin->syncPermissions(
            Permission::all()
        );

        /*
        |--------------------------------------------------------------------------
        | Hotel Admin / Owner
        |--------------------------------------------------------------------------
        */

        $admin->syncPermissions([
            'view dashboard',

            'view hotels',
            'create hotels',
            'edit hotels',

            'view tables',
            'create tables',
            'edit tables',
            'delete tables',

            'view reservations',
            'create reservations',
            'edit reservations',
            'confirm reservations',
            'cancel reservations',

            'view products',
            'create products',
            'edit products',
            'delete products',

            'view pre-orders',
            'create pre-orders',
            'edit pre-orders',
            'delete pre-orders',
            'manage pre-orders',

            'view users',

            'use voice reservation',
            'view voice requests',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Kitchen Staff — only orders + read-only products
        |--------------------------------------------------------------------------
        */

        $kitchen->syncPermissions([
            'view dashboard',

            'view products',

            'view pre-orders',
            'manage pre-orders',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Normal User — view-only for products, full for their own pre-orders
        |--------------------------------------------------------------------------
        */
        $user->syncPermissions([
            'view dashboard',

            // Reservations
            'view reservations',
            'create reservations',
            'edit reservations',
            'cancel reservations',

            // Products
            'view products',

            // Pre-Orders
            'view pre-orders',
            'create pre-orders',
            'edit pre-orders',
            'delete pre-orders',

            // Voice
            'use voice reservation',
        ]);
    }
}
