<?php

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = [
    'NAME' => Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.ADD.NAME'),
    'DESCRIPTION' => Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.ADD.DESCRIPTION'),
    'PATH' => [
        'ID' => 'itbizon',
        'NAME' => Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.ADD.PATH.NAME'),
        'CHILD' => [
            'ID' => 'finance',
            'NAME' => Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.ADD.PATH.CHILD.NAME'),
        ]
    ],
];
