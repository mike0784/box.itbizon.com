<?php

$arActivityDescription = [
    'NAME' => "[itbizon] Создать лид через вебхук",
    'DESCRIPTION' => "[itbizon] Создает лид на любом портале через вебхук",
    'TYPE' => ['activity'],
    'CLASS' => 'ItBizonSendLeadRest',
    'JSCLASS' => 'BizProcActivity',
    'CATEGORY' => [
        'ID' => 'other',
    ],
    "RETURN" => [
        'ID' => [
            'NAME' => 'ID',
            'TYPE' => 'int'
        ]
    ]
];