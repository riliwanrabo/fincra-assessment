<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    private $adminRole;

    public function __construct()
    {
        $this->adminRole = Role::whereName('admin')->first();
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (app()->isLocal()) {
            User::factory(50)
                ->hasWallet()
                ->create();
        }

        $adminAccount = User::updateOrCreate([
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'email' => 'admin@fincra.test',
            'password' => bcrypt('password'),
        ]);

        $adminAccount->assignRole($this->adminRole);
    }
}
