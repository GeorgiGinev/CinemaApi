<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCinemaIdToMovieSlotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('movie_slots', function (Blueprint $table) {
            $table->unsignedBigInteger('cinema_id')->nullable();

            $table->foreign('cinema_id')
                ->references('id')
                ->on('cinemas')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('movie_slots', function (Blueprint $table) {
            //
        });
    }
}
