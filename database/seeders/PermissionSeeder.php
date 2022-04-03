<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roleNames = [
            'admin', 'agent'
        ];

        collect($roleNames)->each(fn ($role) => Role::updateOrCreate(['name' => $role], ['name' => $role, 'guard_name' => 'api']));
    }
}
