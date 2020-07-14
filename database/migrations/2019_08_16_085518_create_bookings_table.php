<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('hotel_id');
            $table->unsignedBigInteger('room_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('hotel_id')->references('id')->on('hotels');
            $table->foreign('room_id')->references('id')->on('rooms');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone_no');
            $table->timestamp('date_start')->nullable();
            $table->timestamp('date_end')->nullable();
            $table->float('amount', 8, 2);
            $table->string('currency')->default('USD');
            $table->integer('many_room');
            $table->integer('many_adult');
            $table->integer('many_child');
            $table->longText('rooms_no');
            $table->longText('optional_amen')->nullable();
            $table->string('status')->default('active');
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
        Schema::dropIfExists('bookings');
    }
}
