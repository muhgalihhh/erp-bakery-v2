<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CashierSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Create Cashier Role
    $cashier = Role::firstOrCreate(
      ['name' => 'cashier'],
      ['guard_name' => 'web']
    );

    $this->command->info('✅ Cashier role created');
    $this->command->info('   Permissions will be assigned manually by admin via UI');

    // Create a test Cashier user
    $user = User::firstOrCreate(
      ['email' => 'cashier@bakery.com'],
      [
        'name' => 'Ani Wijaya (Cashier)',
        'password' => Hash::make('password'),
        'email_verified_at' => now(),
      ]
    );

    // Assign role to user
    $user->assignRole('cashier');

    $this->command->info('✅ Test user created: cashier@bakery.com / password');
  }
}
