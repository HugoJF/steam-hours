<?php

namespace App\Forms;

use Kris\LaravelFormBuilder\Form;
use DateTimeZone;
use DateTime;

class UserSettingsForm extends Form
{
	public function buildForm()
	{
		$this
			->add('request_preference', 'timepicker', [
				'label'      => 'Request Time Preference',
				'rules'      => ['required'],
				'help_block' => [
					'text' => 'When the system will try to update your account.',
				],
			])
			->add('request_correction_limit', 'number', [
				'label'      => 'Request Correction Limitation',
				'rules'      => ['required', 'numeric'],
				'help_block' => [
					'text' => 'What\'s the maximum amount of hours we can delay or advance the update in order to reach the preference hour.',
				],
			])->add('timezone', 'select', [
				'label'       => 'Timezone',
				'rules'       => ['required'],
				'choices'     => collect(DateTimeZone::listIdentifiers())->keyBy(function ($item, $key) {
					return $item;
				})->map(function ($item, $key) {
					$a = new DateTimeZone($key);
					$b = new DateTimeZone('UTC');

					$offset = $a->getOffset(new DateTime('now', $b));

					$hours = round($offset / 3600);
					$minutes = round($offset % 3600 / 60);

					return sprintf('%s (GMT%+03d:%02d)', $item, $hours, $minutes);
				})->toArray(),
				'empty_value' => 'Select Timezone...',
				'help_block'  => [
					'text' => 'Your timezone',
				],
			]);
	}
}
