<?php

namespace App\Console\Commands;

use App\PlaytimeRequest;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class QueuePlaytime extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'playtime:queue {--F|force}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Queues playtime update requests';

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
		$users = User::all();
		$now = Carbon::now();


		foreach ($users as $user) {
			$lastRequest = $user->playtimeRequests()->orderBy('created_at', 'DESC')->first();

			if($this->option('force') || is_null($lastRequest) || $now->diffInHours($lastRequest->created_at, true) > $user->playtime_expiration) {
				$request = PlaytimeRequest::make();

				$request->user()->associate($user);
				$request->previous()->associate(PlaytimeRequest::orderBy('created_at', 'desc')->first());

				$request->save();

				$this->info("Queued request: {$request->id}");
			} else {
				$this->warn('Skipping user');
			}
		}
	}
}
