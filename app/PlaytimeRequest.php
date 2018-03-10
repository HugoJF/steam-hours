<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PlaytimeRequest extends Model
{
	public function user()
	{
		return $this->belongsTo('App\User');
	}

	public function playtimeDeltas()
	{
		return $this->hasMany('App\PlaytimeDelta');
	}

	public function previous()
	{
		return $this->belongsTo('App\PlaytimeRequest');
	}

	public function next()
	{
		return $this->hasOne('App\PlaytimeRequest');
	}

	public function playtimes()
	{
		return $this->hasMany('App\Playtime');
	}

	public function scopeUnfilled(Builder $query)
	{
		return $query->where('filled', 0);
	}

	public function scopeFilled(Builder $query)
	{
		return $query->where('filled', 1);
	}

	public function scopeCached(Builder $query)
	{
		return $query->where('cached', 1);
	}

	public function scopeNotCached(Builder $query)
	{
		return $query->where('cached', 0)->whereNotNull('previous_id');
	}


	public function computeDeltas()
	{
		if (!$this->previous) {
			return false;
		}

		$this->load('playtimes');
		$previousRequest = $this->previous()->with('playtimes')->first();

		$actual = [];
		$previous = [];

		foreach ($this->playtimes as $playtime) {
			$actual[ $playtime->appid ] = $playtime->playtime;
		}

		foreach ($previousRequest->playtimes as $playtime) {
			$previous[ $playtime->appid ] = $playtime->playtime;
		}

		foreach ($actual as $appid => $playtime) {
			$delta = 0;

			if (array_key_exists($appid, $previous)) {
				$delta = $actual[ $appid ] - $previous[ $appid ];
			} else {
				$delta = $actual[ $appid ];
			}
			if ($delta > 0) {
				// Check if there are duplicates
				$playtimeDelta = PlaytimeDelta::make();

				$playtimeDelta->delta = $delta;

				$playtimeDelta->gameInfo()->associate(GameInfo::find($appid));
				$playtimeDelta->playtimeRequest()->associate($this);
				$playtimeDelta->created_at = $this->created_at;
				$playtimeDelta->updated_at = $this->updated_at;

				$playtimeDelta->save();
			}
		}

		$this->cached = true;
		$this->touch();
		$saved = $this->save();

		return $saved;
	}

	public function getScore()
	{
		return $this->created_at->diffInMinutes() * 1;
	}

}
