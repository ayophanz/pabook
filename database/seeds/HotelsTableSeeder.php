<?php

use App\Models\User;
use App\Models\Hotel;
use App\Models\option;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class HotelsTableSeeder extends Seeder
{
    const hotel_count = 100;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /** Disabling the referrence */
        Schema::disableForeignKeyConstraints();
        
        /** Cleaning table */
        Hotel::truncate();

        /** Generate new data */
        for ($i = 0; $i < self::hotel_count; $i++) {

            $hotel = Hotel::firstOrCreate([
                'user_id'         => $this->randomException(),
                'name'            => 'hotel'.($i+1),
                'address'         => 'street 1, address 1',
                'city'            => 'cagayan de oro',
                'state_province'  => 'misamis oriental',
                'country'         => 'philippines',
                'zip_code'        => '9000',
                'phone_number'    => '123456',
                'email'           => 'hotel'.($i+1).'@gmail.com',
                'status'          => 'verified',
                'verify_token'    => '123456',
                'hotel_rooms_no'  => '[{"value":"100","code":"108135841","status":"ready","assign_id":"no"}]',
                'website'         => 'hotel'.($i+1).'.com',
                'check_in'        => '02:00 PM',
                'check_out'       => '12:00 PM',
            ]);

            Option::firstOrCreate([
                'meta_key'   => 'base_currency',
                'meta_value' => $hotel->id,
                'value' => 'PHP',
            ]);
        }

        /** Enabling the referrence */
        Schema::enableForeignKeyConstraints();
    }

    /** Extra function */
    public function randomException() 
    {
        $user = User::where('role', 'hotel_owner')->inRandomOrder()->first();
        return $user->id;
    }
}
