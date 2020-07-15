<?php

use App\Models\Hotel;
use App\Models\RoomType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class RoomTypesTableSeeder extends Seeder
{
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
        RoomType::truncate();

        /** Generate new data */
        $hotel = Hotel::all();
        foreach ($hotel as $item) {
            RoomType::firstOrCreate([
                'hotel_id' => $item->id,
                'name'     => 'King',
            ]);

            RoomType::firstOrCreate([
                'hotel_id' => $item->id,
                'name'     => 'Queen',
            ]);

            RoomType::firstOrCreate([
                'hotel_id' => $item->id,
                'name'     => 'Prince',
            ]);
        }

        /** Enabling the referrence */
        Schema::enableForeignKeyConstraints();
    }
}
