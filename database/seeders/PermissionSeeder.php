<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    private $permissions = [
        'dashboard' => [
            'menu'
        ],

        'user' => [
            'menu',
            'list',
            'create',
            'edit',
            'delete'
        ],

        'role' => [
            'menu',
            'list',
            'create',
            'edit',
            'delete'
        ],

        'permission' => [
            'menu',
            'list',
            'create',
            'edit',
            'delete'
        ],

        'store' => [
            'menu',
            'list',
            'create',
            'edit',
            'delete'
        ],

        'store-balance' => [
            'menu',
            'list'
        ],

        'store-balance-history' => [
            'list'
        ],

        'withdrawal' => [
            'menu',
            'list',
            'create',
            'edit',
            'delete'
        ],

        'buyer' => [
            'menu',
            'list',
            'create',
            'edit',
            'delete'
        ],

        'product-category' => [
            'menu',
            'list',
            'create',
            'edit',
            'delete'
        ],

        'product' => [
            'menu',
            'list',
            'create',
            'edit',
            'delete'
        ],

        'transaction' => [
            'menu',
            'list',
            'create',
            'edit',
            'delete'
        ],

        'product-review' => [
            'menu',
            'list',
            'create',
            'edit',
            'delete'
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->permissions as $key => $value) {
            foreach ($value as $permissions) {
                Permission::firstOrCreate([
                    'name' => $key . '-' . $permissions,
                    'guard_name' => 'sanctum'
                ]);
            }
        }
    }
}
