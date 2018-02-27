<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Playtime extends Model
{
	public function playtimeRequest()
	{
		return $this->belongsTo('App\PlaytimeRequest');
	}

	public function gameInfo()
	{
		return $this->belongsTo('App\GameInfo', 'appid', 'appid');
	}
}
