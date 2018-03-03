<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends \TCG\Voyager\Models\User
{
	use Notifiable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name',
		'email',
		'password',
		'request_preference',
		'request_correction_limit',
		'timezone',
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password', 'remember_token',
	];


	public function playtimeRequests()
	{
		return $this->hasMany('App\PlaytimeRequest');
	}

	public function playtimeDeltas()
	{
		return $this->hasManyThrough('App\PlaytimeDelta', 'App\PlaytimeRequest');
	}
}
