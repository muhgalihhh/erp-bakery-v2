<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin user first
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@bakery.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Buat Role Super Admin
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super_admin'],
            ['guard_name' => 'web']
        );

        // Beri Super Admin SEMUA permissions yang ada
        $allPermissions = Permission::all();
        $superAdminRole->syncPermissions($allPermissions);

        // Assign role to user
        $superAdmin->assignRole('super_admin');

        $this->command->info('✅ Super Admin role created with ALL permissions');
        $this->command->info('✅ Super Admin user created:');
        $this->command->info('   Email: admin@bakery.com');
        $this->command->info('   Password: password');
    }
}
