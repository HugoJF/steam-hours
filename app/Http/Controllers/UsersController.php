<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kris\LaravelFormBuilder\FormBuilderTrait;

class UsersController extends Controller
{
	use FormBuilderTrait;

	public function settings()
	{
		$form = $this->form('App\Forms\UserSettingsForm', [
			'method' => 'POST',
			'route'  => ['users.storeSettings'],
			'model'  => Auth::user(),
		]);


		return view('users.settings', [
			'form' => $form,
		]);
	}

	public function storeSettings(Request $request)
	{

		$user = Auth::user();

		$user->fill($request->all());

		$user->save();

		return redirect()->back();
	}
}
