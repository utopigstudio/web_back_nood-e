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
            $table->dateTime('start_date')->default(now());
            $table->dateTime('end_date')->default(now());
            $table->string('room')->default('Add room');
            $table->decimal('price', 8, 2)->default(0.00);
            $table->string('image')->default('Add image');
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
