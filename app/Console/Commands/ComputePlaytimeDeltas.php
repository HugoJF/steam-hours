<?php

namespace App\Console\Commands;

use App\PlaytimeRequest;
use Illuminate\Console\Command;

class ComputePlaytimeDeltas extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'playtime:deltas';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Computes missing deltas';

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
		$requests = PlaytimeRequest::filled()->notCached()->get();

		foreach ($requests as $request) {
			$request->computeDeltas();
			$this->info("Computed delta for request {$request->id}");
		}
	}
}
