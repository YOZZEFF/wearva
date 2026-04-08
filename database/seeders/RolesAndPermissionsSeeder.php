<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::create(['name' => 'store categories']);
        Permission::create(['name' => 'update categories']);
        Permission::create(['name' => 'destroy categories']);
        Permission::create(['name' => 'view categories']);


        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        $adminRole->givePermissionTo([
            'store categories',
            'update categories',
            'destroy categories'

        ]);

        $customerRole = Role::firstOrCreate(['name' => 'customer']);

        $customerRole->givePermissionTo([
            'view categories'
        ]);



    }
}
