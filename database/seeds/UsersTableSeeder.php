<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\User::create([
            'name'     => 'super admin',
            'password' => '$2y$10$AWcOU9P8T04C70vO49h.O.yA1ny4vH3kYrfb2oHomujCUVrqpp/Jq',
            'role'     => 'super_admin',
            'email'    => 'admin@pabook.rf.gd',
            'status'   => 'active'
        ]);
    }
}
