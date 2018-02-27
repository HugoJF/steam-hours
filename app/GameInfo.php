<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GameInfo extends Model
{

	protected $primaryKey = 'appid';

	public $incrementing = false;

	public $timestamps = false;

	public function playtime()
	{
		return $this->hasMany('App\Playtime');
	}

	public function playtimeDelta()
	{
		return $this->hasMany('App\PlaytimeDelta');
	}
}
