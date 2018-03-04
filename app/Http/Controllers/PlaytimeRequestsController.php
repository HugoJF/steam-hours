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
	public function index(Request $request)
	{
		$options = [];

		if (Auth::check()) {
			$requests = Auth::user()->playtimeRequests()->with('playtimeDeltas');

			if ($request->input('date')) {
				try {
					$day = Carbon::parse($request->input('date'));
					$nextDay = $day->copy()->addDay();

					$options['title'] = 'Playtime Requests for ' . $day->toDateString();

					$requests->where([
						['created_at', '>', $day],
						['created_at', '<', $nextDay],
					]);
				} catch (\Exception $e) {
					dd($e);
				}
			}

			$requests = $requests->paginate(15);
		} else {
			$requests = [];
		}

		return view('playtime_requests.index', [
			'playtimeRequests' => $requests,
		] + $options);
	}

	public function daily()
	{
		$user = Auth::user();

		$days = [];

		$daily = $user->playtimeDeltas()->select([
			DB::raw('DATE(playtime_deltas.created_at) as date'),
			DB::raw('SUM(playtime_deltas.delta) as total'),
		])->groupBy(['date', 'playtime_requests.user_id'])->get();

		$requestDays = $user->playtimeRequests()->select([
			DB::raw('DATE(created_at) as date'),
			DB::raw('COUNT(id) as count'),
		])->groupBy('date')->get();

		foreach ($requestDays as $requestDay) {
			$days[ $requestDay->date ] = [
				'count' => $requestDay->count,
				'total' => 0,
			];
		}

		foreach ($daily as $day) {
			$days[ $day->date ] = [
				'total' => $day->total,
			] + $days[ $day->date ];
		}

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
