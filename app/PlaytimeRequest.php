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
		// Check for previous request
		if (!$this->previous) {
			return false;
		}

		// Load playtimes filled from Steam
		$this->load('playtimes');
		$previousRequest = $this->previous()->with('playtimes')->first();

		// Playtimes keyed by appid
		$actual = [];
		$previous = [];

		// Fill Playtime arrays
		foreach ($this->playtimes as $playtime) {
			$actual[ $playtime->appid ] = $playtime->playtime;
		}
		foreach ($previousRequest->playtimes as $playtime) {
			$previous[ $playtime->appid ] = $playtime->playtime;
		}

		// Computes de delta between requests and generate PlaytimeDelta instance
		foreach ($actual as $appid => $playtime) {
			// If no previous playtime exist, the delta is the actual playtime
			if (array_key_exists($appid, $previous)) {
				$delta = $actual[ $appid ] - $previous[ $appid ];
			} else {
				$delta = $actual[ $appid ];
			}

			// Only logs deltas that changed
			if ($delta > 0) {
				// Check if there are duplicates
				$playtimeDelta = PlaytimeDelta::make();

				$playtimeDelta->delta = $delta;

				$playtimeDelta->gameInfo()->associate(GameInfo::find($appid));
				$playtimeDelta->playtimeRequest()->associate($this);

				// Allows PlaytimeDeltas grouping to be exactly like PlaytimeRequests
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
