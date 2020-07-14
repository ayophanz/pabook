<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceptionistAssignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receptionist_assigns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('receptionist_id');
            $table->unsignedBigInteger('owner_id');
            $table->unsignedBigInteger('hotel_id');
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('receptionist_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('hotel_id')->references('id')->on('hotels');
            $table->boolean('can_add_room')->default(0);
            $table->boolean('can_edit_room')->default(0);
            $table->boolean('can_delete_room')->default(0);
            $table->boolean('can_add_room_type')->default(0);
            $table->boolean('can_edit_room_type')->default(0);
            $table->boolean('can_delete_room_type')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('receptionist_assigns');
    }
}
