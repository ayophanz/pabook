<?php

use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomMeta;
use App\Models\RoomType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class RoomsTableSeeder extends Seeder
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
        Room::truncate();

        /** Generate new data */
        $hotel = Hotel::all();
        foreach ($hotel as $key => $item) {
            $room = Room::firstOrCreate([
                'room_type_id'    => $this->randomId($item->id),
                'name'            => 'room'.($key+1),
                'description'     => 'This is description',
                'price'           => (double) rand(100, 1000),
                'max_adult'       => (int) rand(1, 2),
                'max_child'       => (int) rand(0, 3),
                'status'          => 'active',
            ]);
            
            $data = [];
            for ($i = 0; $i < 3; $i++) {
                array_push($data, (Object) ['value' => 'test'.($i+1)]);
            }

            RoomMeta::firstOrCreate([
                'room_id'  => $room->id,
                'meta_key' => 'room_feature',
                'value'    => json_encode($data),
            ]);
            
            $data = [];
            for ($i = 0; $i < 6; $i++) {
                array_push($data, (Object) ['value' => 'test'.($i+1), 'price' => rand(100, 800)]);
            }
            RoomMeta::firstOrCreate([
                'room_id'  => $room->id,
                'meta_key' => 'room_feature_optional',
                'value'    => json_encode($data),
            ]);
        }

        /** Enabling the referrence */
        Schema::enableForeignKeyConstraints();
    }

    /** Extra function */
    public function randomId($hotelId) 
    {
        $roomType = RoomType::where('hotel_id', $hotelId)->inRandomOrder()->first();
        return $roomType->id;
    }
}
