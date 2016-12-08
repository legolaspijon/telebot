<?php

return [
    'adminEmail' => 'admin@example.com',
    'languages' => [
        'ru' => 'Русский',
        'en' => 'English',
    ],
    'commandsLabels' => [
        'ru' => [
            '/settings' => 'опции',
            '/measurement' => 'измерения',
            '/city' => 'город',
            '/language' => 'язык',
            '/notification' => 'уведомления',
            '/today' => 'на сегодня',
            '/tomorrow' => 'на завтра',
            '/5 days' => 'на 5 дней',
        ],
        'en' => [
            '/settings' => 'settings',
            '/measurement' => 'units',
            '/city' => 'city',
            '/language' => 'language',
            '/notification' => 'notification',
            '/today' => 'for today',
            '/tomorrow' => 'for tomorrow',
            '/5 days' => 'for 5 days',
        ],
    ],
    'units' => [
        'C' => 'metric',
        'F' => 'imperial'
    ],
    'emoji' => [
        'weather' => [
            '01d' => "\u2600",
            '02d' => "\u26C5",
            '03d' => "\u2601",
            '04d' => "\u2601",
            '09d' => "\u2614",
            '10d' => "\u2614",
            '11d' => "\u26A1",
            '13d' => "\u2744"
        ],
        'menu' => [
            'location' => "\uD83D\uDCCD",
            'units' => "\uD83D\uDD27",
            'language' => "\uD83C\uDF0F",
            'back' => "\u2B05"
        ]
    ],
];
