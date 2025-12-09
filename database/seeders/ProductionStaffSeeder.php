<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ProductionStaffSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Create Production Staff Role
    $productionStaff = Role::firstOrCreate(
      ['name' => 'production_staff'],
      ['guard_name' => 'web']
    );

    $this->command->info('✅ Production Staff role created');
    $this->command->info('   Permissions will be assigned manually by admin via UI');

    // Create a test Production Staff user
    $user = User::firstOrCreate(
      ['email' => 'production@bakery.com'],
      [
        'name' => 'Joko Susilo (Production Staff)',
        'password' => Hash::make('password'),
        'email_verified_at' => now(),
      ]
    );

    // Assign role to user
    $user->assignRole('production_staff');

    $this->command->info('✅ Test user created: production@bakery.com / password');
  }
}
