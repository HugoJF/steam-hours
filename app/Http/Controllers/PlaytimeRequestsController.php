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
		if (Auth::check()) {
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
		$user = Auth::user();

		$days = [];

		$daily = $user->playtimeDeltas()->select([
			DB::raw('DATE(playtime_deltas.created_at) as date'),
			DB::raw('SUM(playtime_deltas.delta) as total'),
		])->groupBy(['date', 'playtime_requests.user_id'])->get();

		$requestDays = PlaytimeRequest::select([
			DB::raw('DATE(created_at) as date'),
		])->groupBy('date')->get();

		foreach ($requestDays as $requestDay) {
			$days[ $requestDay->date ] = 0;
		}

		foreach ($daily as $day) {
			$days[ $day->date ] = $day->total;
		}


		//		dd($daily->toArray());
		//				dd($requestDays->toArray());

		return view('playtime_requests.daily', [
			'days' => $days,
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
