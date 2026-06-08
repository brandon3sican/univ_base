<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::find(1);
        
        if ($user) {
            $adminRole = Role::where('slug', 'admin')->first();
            
            if ($adminRole) {
                $user->roles()->sync([$adminRole->id]);
                $this->command->info('Admin role assigned to user ID 1 (' . $user->email . ')');
            } else {
                $this->command->error('Admin role not found');
            }
        } else {
            $this->command->error('User ID 1 not found');
        }
    }
}
