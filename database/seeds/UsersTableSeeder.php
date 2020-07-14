<?php

use App\Models\User;
use App\Models\Hotel;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class UsersTableSeeder extends Seeder
{
    const owner_count = 1;
    const receptionist_count = 1;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::firstOrCreate([
            'name'              => 'super admin',
            'password'          => '$2y$10$AWcOU9P8T04C70vO49h.O.yA1ny4vH3kYrfb2oHomujCUVrqpp/Jq',
            'role'              => 'super_admin',
            'email'             => 'admin@pabook.rf.gd',
            'email_verified_at' => date("Y-m-d H:i:s"),
            'status'            => 'active'
        ]);

        for ($i = 0; $i < self::owner_count; $i++) {
            $owner = User::firstOrCreate([
                'name'              => 'owner'.($i+1),
                'password'          => '$2y$10$AWcOU9P8T04C70vO49h.O.yA1ny4vH3kYrfb2oHomujCUVrqpp/Jq',
                'role'              => 'hotel_owner',
                'email'             => 'owner'.($i+1).'@gmail.com',
                'email_verified_at' => date("Y-m-d H:i:s"),
                'status'            => 'active'
            ]);
        }

        for ($i = 0; $i < self::receptionist_count; $i++) {
            $receptionist = User::firstOrCreate([
                'name'              => 'receptionist'.($i+1),
                'password'          => '$2y$10$AWcOU9P8T04C70vO49h.O.yA1ny4vH3kYrfb2oHomujCUVrqpp/Jq',
                'role'              => 'hotel_receptionist',
                'email'             => 'receptionist'.($i+1).'@gmail.com',
                'email_verified_at' => date("Y-m-d H:i:s"),
                'status'            => 'active'
            ]);
        }
    }
}
