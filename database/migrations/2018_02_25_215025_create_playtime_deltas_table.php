<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlaytimeDeltasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('playtime_deltas', function (Blueprint $table) {
            $table->increments('id');

			$table->integer('delta')->unsigned();

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
        Schema::dropIfExists('playtime_caches');
    }
}
