<?php

namespace App\Console\Commands;

use App\GameInfo;
use App\Playtime;
use App\PlaytimeRequest;
use App\SteamAPI;
use Illuminate\Console\Command;

class FillPlaytime extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'playtime:fill';

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
		$requests = PlaytimeRequest::unfilled()->get();

		foreach ($requests as $request) {

			$user = $request->user;

			$response = SteamAPI::call()->GetOwnedGames($user->steamid)->get();

			if(!property_exists($response, 'response')) {
				$this->error('Invalid response received');
				continue;
			}

			foreach ($response->response->games as $game) {
				if($game->playtime_forever == 0) continue;

				$gameInfo = $this->checkGameInfo($game);
				$this->createPlaytime($request, $gameInfo, $game->playtime_forever);
			}

			$request->filled = true;
			$request->save();

			$this->info('Filled');
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