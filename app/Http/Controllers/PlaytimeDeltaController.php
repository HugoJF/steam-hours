<?php

namespace App\Http\Controllers;

use App\GameInfo;
use App\PlaytimeDelta;
use App\PlaytimeRequest;
use Illuminate\Http\Request;

class PlaytimeDeltaController extends Controller
{
	public function index()
	{
		return PlaytimeDelta::all();
	}
}
