<?php

namespace App\Console\Commands;

use App\GameInfo;
use App\Playtime;
use App\PlaytimeRequest;
use App\SteamAPI;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FillPlaytime extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'playtime:fill {amount=200}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Fills every playtime request in database';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$requests = PlaytimeRequest::unfilled()->with('user')->get();

		$requests->sortBy(function ($item, $key) {
			return $item->getScore();
		});

		$maxRequests = $this->option('amount');

		$this->info("Running request filled with {$maxRequests} max requests!");

		while ($maxRequests-- > 0 && $requests->count() > 0) {

			$request = $requests->pop();

			$user = $request->user;

			$response = SteamAPI::call()->GetOwnedGames($user->steamid)->get();

			try {
				if (!property_exists($response, 'response')) {
					$this->error('Invalid response received');
					continue;
				}
			} catch (\Exception $e) {
				$this->warn(SteamAPI::call()->GetOwnedGames($user->steamid)->raw()->get());
			}

			foreach ($response->response->games as $game) {
				if ($game->playtime_forever == 0)
					continue;

				$gameInfo = $this->checkGameInfo($game);
				$this->createPlaytime($request, $gameInfo, $game->playtime_forever);
			}

			$request->filled = true;
			$request->save();

			$this->info("Filled request: {$request->id}");
		}
	}

	protected function checkGameInfo($game)
	{
		$info = GameInfo::where('appid', $game->appid)->first();

		if (is_null($info)) {
			$gameInfo = GameInfo::make();

			$gameInfo->appid = $game->appid;
			$gameInfo->name = $game->name;
			$gameInfo->icon = $game->img_icon_url;
			$gameInfo->logo = $game->img_logo_url;

			$gameInfo->save();

			return $gameInfo;
		}

		return $info;
	}

	protected function createPlaytime($request, $gameInfo, $playtime_forever)
	{
		$playtime = Playtime::make();

		$playtime->playtime = $playtime_forever;

		$playtime->gameInfo()->associate($gameInfo);
		$playtime->playtimeRequest()->associate($request);

		$playtime->save();

		return $playtime;
	}
}
