<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions
        $permissions = [
            // Performance Module Permissions
            'Kategori Yönetimi',
            'Keyword Yönetimi',
            'Aktivite Yönetimi',
            'İstatistikler',
            'Bilgisayar Kullanıcıları',
            
            // Administrative Permissions
            'Birim Yönetimi',
            'Ünvan Yönetimi',
            'Kullanıcı Yönetimi',
            'Rol Yönetimi',
            'Firma Ayarları',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Ensure Admin and Super Admin roles exist
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        
        // Create a standard User role if it doesn't exist
        $userRole = Role::firstOrCreate(['name' => 'User']);

        // Assign ALL permissions to Admin & Super Admin
        foreach ($permissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                $adminRole->givePermissionTo($permission);
                $superAdminRole->givePermissionTo($permission);
            }
        }

        // Assign performance-related permissions to User role
        $userPermissions = [
            'Kategori Yönetimi',
            'Keyword Yönetimi',
            'Aktivite Yönetimi',
            'İstatistikler',
            'Bilgisayar Kullanıcıları',
        ];

        foreach ($userPermissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                $userRole->givePermissionTo($permission);
            }
        }

        $this->command->info('Permissions created and assigned successfully!');
    }
}
