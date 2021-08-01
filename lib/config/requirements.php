<?php
return [
    'php.mbstring'  => ['description' => 'Необходимо для корректной работы с текстовыми данными', 'strict' => true],
    'php.simplexml' => ['description' => 'Необходимо для работы с кэшируемыми данными', 'strict' => true],
    'app.installer' => ['version' => '>=2.0.0', 'strict' => true],
    'php'           => ['version' => '>=7.2', 'strict' => true]
];
