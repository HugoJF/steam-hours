<?php
/**
 * Created by PhpStorm.
 * User: Hugo
 * Date: 2/25/2018
 * Time: 3:43 PM
 */

namespace App;


use Ixudra\Curl\Facades\Curl;

class SteamAPI
{
	// This mode will overwrite any param already present
	const MODE_OVERWRITE = 0;
	// This mode will replace the entire param variable
	const MODE_REPLACE = 1;
	// This mode will only add missing params
	const MODE_ADD = 2;

	private $interface;

	private $method;

	private $version;

	private $params = [];

	private $response;

	public function __construct($interface = null, $method = null, $version = 'v0001')
	{
		$this->interface = $interface;
		$this->method = $method;
		$this->version = $version;
	}

	public static function call()
	{
		return new static();
	}

	public function get()
	{
		if (empty($this->interface)) {
			throw new \Exception('You must specify an Steam API interface');
		}

		if (empty($this->method)) {
			throw new \Exception('You must specify an Steam API interface method');
		}

		$apikey = env('STEAM_APIKEY');

		$url = "http://api.steampowered.com/{$this->interface}/{$this->method}/{$this->version}";

		$response = Curl::to($url)
						->withData(['key' => $apikey, 'format' => 'json'] + $this->params)
						->asJson()
						->get();

		$this->response = $response;

		return $response;
	}

	public function interface($interface)
	{
		$this->interface = $interface;

		return $this;
	}

	public function method($method)
	{
		$this->method = $method;

		return $this;
	}

	public function params($params, $mode = 0)
	{
		if ($mode === SteamAPI::MODE_OVERWRITE) {
			$this->params = $params + $this->params;
		} else if ($mode === SteamAPI::MODE_REPLACE) {
			$this->params = $params;
		} else if ($mode === SteamAPI::MODE_ADD) {
			$this->params = $this->params + $params;
		} else {
			throw new \Exception('Unknown param mode specified');
		}

		return $this;
	}

	public function with($params, $mode = 0)
	{
		return $this->params($params, $mode);
	}

	public function GetOwnedGames($steamid)
	{
		$this->interface('IPlayerService');
		$this->method('GetOwnedGames');
		$this->params([
			'steamid'                   => $steamid,
			'include_appinfo'           => 1,
			'include_played_free_games' => 1,
		]);

		return $this;
	}


	public static function mapImageUrl($appid, $hash)
	{
		return "http://media.steampowered.com/steamcommunity/public/images/apps/{$appid}/{$hash}.jpg";
	}

	public static function mapCommunityStatsUrl($steamid, $appid)
	{
		return "http://steamcommunity.com/profiles/{$steamid}/stats/{$appid}";
	}
}