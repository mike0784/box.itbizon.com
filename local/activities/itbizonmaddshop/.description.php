<?php

defined('B_PROLOG_INCLUDED') || die();

use Bitrix\Bizproc\FieldType;

$arActivityDescription = [
    'NAME' => "Тест. Мелешев. Добавление магазина.",
    'DESCRIPTION' => "Тест. Мелешев. Добавление магазина. Описание",
    'TYPE' => 'activity',
    'CLASS' => 'ItBizonMAddShop',
    'JSCLASS' => 'BizProcActivity',
    'CATEGORY' => [
        'ID' => 'other',
    ],
    "RETURN" => [
        'shopId' => [
            'NAME' => 'ID',
            'TYPE' => 'int'
        ]
    ]
];