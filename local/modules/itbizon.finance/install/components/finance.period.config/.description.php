<?php

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = [
    'NAME' => Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.CONFIG.NAME'),
    'DESCRIPTION' => Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.CONFIG.DESCRIPTION'),
    'PATH' => [
        'ID' => 'itbizon',
        'NAME' => Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.CONFIG.PATH.NAME'),
        'CHILD' => [
            'ID' => 'finance',
            'NAME' => Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.ADD.CONFIG.CHILD.NAME'),
        ]
    ],
];
