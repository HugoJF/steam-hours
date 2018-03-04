<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PlaytimeController extends Controller
{
	public function treemap()
	{
		return view('treemap');
	}

	public function area()
	{
		return view('area');
	}

	public function perGameAPI()
	{
		$user = Auth::user();

		$response = [];

		$perGame = $user->playtimeDeltas()->with('gameinfo')->select([
			DB::raw('appid'),
			DB::raw('CAST(SUM(playtime_deltas.delta) AS UNSIGNED) as total'),
		])->groupBy(['appid', 'playtime_requests.user_id'])->get();

		$response[] = ['Name', 'Parent', 'Size'];
		$response[] = ['Games', null, 0];

		foreach ($perGame as $game) {
			$response[] = [
				$game->gameinfo->name,
				'Games',
				$game->total,
			];
		}

		return $response;
	}

	public function perDayAPI()
	{
		$user = Auth::user();

		$response = [];

		$perDay = $user->playtimeDeltas()->with('gameinfo')->select([
			DB::raw('DATE(playtime_deltas.created_at) as date'),
			DB::raw('appid'),
			DB::raw('CAST(SUM(playtime_deltas.delta) AS UNSIGNED) as total'),
		])->groupBy(['date', 'appid', 'playtime_requests.user_id'])->orderBy('date')->get();

		$gameNames = $perDay->pluck('gameinfo.name')->unique()->toArray();
		$appids = $perDay->pluck('appid', 'appid')->toArray();

		$header = ['Date'];

		foreach ($gameNames as $name) {
			$header[] = $name;
		}

		$response[] = $header;
		$perDay = $perDay->groupBy('date');
		$perDay->each(function ($item, $key) use (&$perDay) {
			$perDay[ $key ] = $item->keyBy('appid');
		});

		foreach ($perDay as $date => $deltas) {
			$responsePart = [$date];
			foreach ($appids as $appid) {
				if ($deltas->has($appid)) {
					$responsePart[] = round($deltas[ $appid ]->total / 60, 2);
				} else {
					$responsePart[] = 0;
				}
			}

			$response[] = $responsePart;
		}

		return $response;
	}
}
