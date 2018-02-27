<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlaytimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('playtimes', function (Blueprint $table) {
            $table->increments('id');

			$table->integer('playtime')->unsigned();

			$table->integer('appid')->unsigned();
			$table->foreign('appid')->references('appid')->on('game_infos');

            $table->integer('playtime_request_id')->unsigned();
            $table->foreign('playtime_request_id')->references('id')->on('playtime_requests');

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
        Schema::dropIfExists('playtimes');
    }
}
