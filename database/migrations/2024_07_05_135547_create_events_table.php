<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('Untitled event');
            $table->longText('description')->nullable();
            $table->dateTime('start')->default(now());
            $table->dateTime('end')->default(now());
            $table->foreignId('room_id');
            $table->decimal('price', 8, 2)->default(0.00);
            $table->string('image')->default('Add image');
            $table->string('link')->nullable();
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
        Schema::dropIfExists('events');
    }
};
