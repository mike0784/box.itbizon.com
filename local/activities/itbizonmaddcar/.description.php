<?php

use \Bitrix\Bizproc\FieldType;

$arActivityDescription = [
    'NAME' => "Тест. Мелешев. Добавление машины.",
    'DESCRIPTION' => "Тест. Мелешев. Добавление машины. Описание",
    'TYPE' => ['activity'],
    'CLASS' => 'ItBizonMAddCar',
    'JSCLASS' => 'BizProcActivity',
    'CATEGORY' => [
        'ID' => 'other',
    ],
    "RETURN" => [
        'autoId' => [
            'NAME' => 'ID',
            'TYPE' => 'int'
        ]
    ]
];