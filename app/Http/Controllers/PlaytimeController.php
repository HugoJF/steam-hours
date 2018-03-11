<?php

namespace App\Http\Controllers;

use App\Playtime;
use App\PlaytimeDelta;
use App\PlaytimeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PlaytimeController extends Controller
{
	public function home(Request $request)
	{
		return $this->summary();
	}

	public function show(Request $request)
	{
		return $this->summary($request->input('date'));
	}

	protected function summary($date = 'now')
	{

		$options = [];

		$requestIds = Auth::user()->playtimeRequests();

		if ($date) {
			try {
				$day = Carbon::parse($date);

				$options['title'] = 'Playtime Requests for ' . $day->toDateString();

				$requestIds = $requestIds->whereDate('created_at', '=', $day);
			} catch (\Exception $e) {
				dd($e);
			}
		}
		$requestIds = $requestIds->select('id')->get()->pluck('id')->toArray();

		$playtimes = PlaytimeDelta::with('gameinfo')->whereIn('playtime_request_id', $requestIds)->select([
			'appid',
			DB::raw('CAST(SUM(delta) AS UNSIGNED) AS total'),
		])->groupBy('appid')->get()->keyBy('appid');

		return view('playtimes.show', [
				'playtimes' => $playtimes,
			] + $options);
	}

	public function treemap()
	{
		return view('treemap', [
			'api' => route('api.charts.treemap'),
		]);
	}

	public function area()
	{
		return view('area', [
			'api' => route('api.charts.area'),
		]);
	}

	public function sankey()
	{
		return view('sankey', [
			'api' => route('api.charts.sankey'),
		]);
	}

	public function sankeyAPI()
	{
		$user = Auth::user();

		$response = [];
		$response[] = ['Date', 'Game', 'Hours'];

		$a = new \DateTimeZone($user->timezone);
		$b = new \DateTimeZone('UTC');

		$offset = $a->getOffset(new \DateTime('now', $b));

		$hours = round($offset / 3600);
		$minutes = round($offset % 3600 / 60);

		$tz = sprintf('%+03d:%02d', $hours, $minutes);

		$data = Auth::user()->playtimeDeltas()->with('gameinfo')->select([
			'appid',
			DB::raw("DATE(CONVERT_TZ(playtime_deltas.created_at, '+00:00', '$tz')) AS date"),
			DB::raw('CAST(SUM(playtime_deltas.delta) AS UNSIGNED) AS total'),
		])->groupBy(['date', 'appid', 'playtime_requests.user_id'])->get();

		foreach ($data as $entry) {
			$response[] = [$entry->date, $entry->gameinfo->name, round($entry->total / 60, 2)];
		}

		return $response;
	}

	public function treemapAPI()
	{
		$user = Auth::user();

		$response = [];

		$perGame = $user->playtimeDeltas()->with('gameinfo')->select([
			DB::raw('appid'),
			DB::raw('CAST(SUM(playtime_deltas.delta) AS UNSIGNED) AS total'),
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

	public function areaAPI()
	{
		$user = Auth::user();

		$response = [];

		$perDay = $user->playtimeDeltas()->with('gameinfo')->select([
			DB::raw('DATE(playtime_deltas.created_at) as date'),
			'appid',
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
