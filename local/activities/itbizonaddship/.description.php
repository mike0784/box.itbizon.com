<?php
defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arActivityDescription = [
    'NAME' => Loc::getMessage('NAME'),
    'DESCRIPTION' => Loc::getMessage('DESCRIPTION'),
    'TYPE' => 'activity',
    'CLASS' => 'ItbizonAddShip',
    'JSCLASS' => 'BizProcActivity',
    'CATEGORY' => [
        'ID' => 'other'
    ],
    "RETURN" => [
        'ShipID' => [
            'NAME' => 'ID',
            'TYPE' => 'int'
        ]
    ]
];