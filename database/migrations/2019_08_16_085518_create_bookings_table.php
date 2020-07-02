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
            $table->unsignedBigInteger('roomId');
            $table->foreign('roomId')->references('id')->on('rooms')->onDelete('cascade');
            $table->string('name');
            $table->string('email');
            $table->string('phoneNo');
            $table->timestamp('dateStart')->nullable();
            $table->timestamp('dateEnd')->nullable();
            $table->float('amount', 8, 2);
            $table->string('currency')->default('USD');
            $table->unsignedBigInteger('userId');
            $table->foreign('userId')->references('id')->on('users');
            $table->integer('manyRoom');
            $table->integer('manyAdult');
            $table->integer('manyChild');
            $table->longText('roomsNo');
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
