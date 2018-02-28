<?php

namespace App\Http\Controllers;

use App\GameInfo;
use App\Playtime;
use App\PlaytimeDelta;
use App\PlaytimeRequest;
use App\SteamAPI;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PlaytimeRequestsController extends Controller
{
	public function index()
	{
		if(Auth::check()) {
			$requests = Auth::user()->playtimeRequests()->with('playtimes')->get();
		} else {
			$requests = [];
		}

		return view('playtime_requests.index', [
			'playtimeRequests' => $requests,
		]);
	}

	public function daily()
	{
		$daily = PlaytimeDelta::select([
			DB::raw('DATE(created_at) as date'),
			DB::raw('SUM(delta) as sum'),
		])->groupBy('date')->get();

		return view('playtime_requests.daily', [
			'daily' => $daily,
		]);
	}

	public function show(PlaytimeRequest $playtimeRequest)
	{
		$playtimeRequest->load('playtimeDeltas', 'playtimeDeltas.gameInfo');

		return view('playtime_requests.view', [
			'playtimeRequest' => $playtimeRequest,
		]);
	}
}
