<?php
return array(
    'token'               => ['value' => ''],
    'location_from'       => ['value' => ['name' => '']],
    'api_server'          => ['value' => 'test'],
    'delivery_time'       => ['value' => ''],
    'exact_delivery_time' => ['value' => 2],
    'customer_interval'   => ['value' => [
        'date'      => true,
        'interval'  => true,
        'intervals' => [
            [
                'from'    => '10',
                'from_m'  => '00',
                'to'      => '12',
                'to_m'    => '00',
                'day'     => [1, 2, 3, 4, 5],
                'workday' => true,
                'holiday' => false
            ]
        ]
    ]],
    'holidays'            => ['value' => []],
    'workdays'            => ['value' => []],
    'insurance'           => ['value' => ['type' => 'none', 'value' => '']]
);