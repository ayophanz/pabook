<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHotelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('name');
            $table->string('address');
            $table->string('city');
            $table->string('state_province');
            $table->string('country');
            $table->string('zip_code');
            $table->string('phone_number');
            $table->string('email')->unique();
            $table->string('image')->nullable();
            $table->string('status')->default('verifying');
            $table->string('verify_token')->nullable();  
            $table->longText('hotel_rooms_no');   
            $table->string('website')->nullable();
            $table->string('check_in')->nullable();
            $table->string('check_out')->nullable();
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
        Schema::dropIfExists('hotels');
    }
}
