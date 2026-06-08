<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Full access to all features including user management',
            ],
            [
                'name' => 'Chief PMD',
                'slug' => 'chief-pmd',
                'description' => 'All features except user management',
            ],
            [
                'name' => 'PMD Division',
                'slug' => 'pmd-division',
                'description' => 'CRUD operations, approve/disapprove, assign division to edit',
            ],
            [
                'name' => 'Other Division',
                'slug' => 'other-division',
                'description' => 'Edit/update operations, assign PENRO to edit',
            ],
            [
                'name' => 'PENRO',
                'slug' => 'penro',
                'description' => 'Edit/update operations only',
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['slug' => $role['slug']],
                [
                    'name' => $role['name'],
                    'description' => $role['description'],
                ]
            );
        }
    }
}
