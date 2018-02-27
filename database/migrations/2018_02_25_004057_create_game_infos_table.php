<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_infos', function (Blueprint $table) {
            $table->integer('appid')->unique()->unsigned();
            $table->string('name');
            $table->string('icon');
            $table->string('logo');
            $table->boolean('has_community_visible_stats')->default();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_infos');
    }
}
