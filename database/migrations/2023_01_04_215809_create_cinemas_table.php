<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCinemasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('cinemas')) {
        Schema::create('cinemas', function (Blueprint $table) {
            $table->id();
            $table->text('description');
            $table->string('name');
            $table->text('images');
            $table->string('logo');
            $table->text('capacity');
            $table->unsignedBigInteger('cinema_location_id')->nullable();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

                $table->foreign('owner_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');

                $table->foreign('cinema_location_id')
                    ->references('id')
                    ->on('cinema_locations')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cinemas');
    }
}
