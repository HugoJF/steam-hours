<?php

namespace App\Http\Controllers;

use App\User;
use Auth;
use Invisnik\LaravelSteamAuth\SteamAuth;

class AuthController extends Controller
{
	/**
	 * The SteamAuth instance.
	 *
	 * @var SteamAuth
	 */
	protected $steam;
	/**
	 * The redirect URL.
	 *
	 * @var string
	 */
	protected $redirectURL = '/';

	/**
	 * AuthController constructor.
	 *
	 * @param SteamAuth $steam
	 */
	public function __construct(SteamAuth $steam)
	{
		$this->steam = $steam;
	}

	/**
	 * Redirect the user to the authentication page.
	 *
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function redirectToSteam()
	{
		return $this->steam->redirect();
	}

	public function login()
	{
		return view('login');
	}

	/**
	 * Get user info and log in.
	 *
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function handle()
	{
		if ($this->steam->validate()) {
			$info = $this->steam->getUserInfo();
			if (!is_null($info)) {
				$user = $this->findOrNewUser($info);
				Auth::login($user, true);
				if (isset($user->tradelink)) {
					return redirect()->route('home');
				} else {
					return redirect($this->redirectURL); // redirect to site
				}
			}
		}

		return $this->redirectToSteam();
	}

	/**
	 * Getting user by info or created if not exists.
	 *
	 * @param $info
	 *
	 * @return User
	 */
	protected function findOrNewUser($info)
	{
		$user = User::where('steamid', $info->steamID64)->first();
		if (!is_null($user)) {
			return $user;
		}

		$user = User::make();

		$user->name = $info->personaname;
		$user->steamid = $info->steamID64;
		$user->playtime_expiration = 24;

		$user->save();

		return $user;
	}
}