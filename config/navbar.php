<?php

return [

	'items' => [
		[
			'title' => 'Home',
			'route' => 'home',
		],
		[
			'title' => 'Daily',
			'route' => 'playtime_requests.daily',
		],
		[
			'title' => 'Requests',
			'route' => 'playtime_requests.index',
		],
		[
			'title'    => 'Charts',
			'children' => [
				[
					'header' => 'Per-game stats',
				],
				[
					'separator',
				],
				[
					'title' => 'Area Chart',
					'route' => 'charts.area',
				],
				[
					'title' => 'Treemap',
					'route' => 'charts.treemap',
				],
				[
					'title' => 'Sankey',
					'route' => 'charts.sankey',
				],
			],
		],
	],


];
