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
            $table->string('title')->default('');
            $table->string('description')->default('');
            $table->dateTime('start_date')->default('2024-07-05 13:55:47');
            $table->dateTime('end_date')->default('2024-07-05 13:55:47');
            $table->string('room')->default('');
            $table->decimal('price', 8, 2)->default(0.00);
            $table->string('image')->default('');
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
