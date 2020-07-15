<?php

use App\Models\User;
use App\Models\Hotel;
use App\Models\ReceptionistAssign;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ReceptionistsTableSeeder extends Seeder
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
        ReceptionistAssign::truncate();

        /** Generate new data */
        $receptionist = User::where('role', 'hotel_receptionist')->get();
        foreach ($receptionist as $key => $item) {
            ReceptionistAssign::firstOrCreate([
                'receptionist_id'      => $item->id,
                'owner_id'             => $this->randomException(),
                'hotel_id'             => 1,
                'can_add_room'         => 0,
                'can_edit_room'        => 0,
                'can_delete_room'      => 0,
                'can_add_room_type'    => 0,
                'can_edit_room_type'   => 0,
                'can_delete_room_type' => 0,
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
