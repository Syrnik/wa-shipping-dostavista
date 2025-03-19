<?php
return array(
    'token'               => ['value' => ''],
    'operating_region'    => ['value' => ['77', '50']],
    'location_from'       => ['value' => ['name' => '']],
    'api_server'          => ['value' => 'test'],
    'delivery_time'       => ['value' => ''],
    'transport_type'      => [
        'title'        => 'Тип транспорта',
        'description'  => 'Тип транспорта, которым должен быть доставлен заказ. Если не передавать никакого, то сервер Dostavista попробует определить тип транспорта исходя из параметров заказа.',
        'value'        => 0,
        'control_type' => waHtmlControl::RADIOGROUP,
        'options'      => [
            ['value' => 0, 'title' => 'Не передавать'],
            ['value' => 1, 'title' => 'Легковой автомобиль / джип / пикап (до 500 кг)'],
            ['value' => 2, 'title' => 'Каблук (до 700 кг)'],
            ['value' => 3, 'title' => 'Микроавтобус / портер (до 1000 кг)'],
            ['value' => 4, 'title' => 'Газель (до 1500 кг)'],
            ['value' => 5, 'title' => 'Грузовой автомобиль'],
            ['value' => 6, 'title' => 'Пеший курьер'],
            ['value' => 7, 'title' => 'Легковой автомобиль'],
        ]
    ],
    'weight_limits'       => ['value' => ['min' => null, 'max' => null]], // в кг., float
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
    'insurance'           => ['value' => ['type' => 'none', 'value' => '']],
    'surcharge'           => ['value' => ''],
    'free_delivery'       => ['value' => null],
    'sms_notify'          => ['value' => ['client' => false, 'receiver' => 'no']],
    'detailed_log'        => ['value' => false]
);
