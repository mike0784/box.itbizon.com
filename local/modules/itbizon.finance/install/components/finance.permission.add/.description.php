<?php

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = [
    'NAME' => Loc::getMessage('ITB_FINANCE.PERMISSION.ADD.NAME'),
    'DESCRIPTION' => Loc::getMessage('ITB_FINANCE.PERMISSION.ADD.DESCRIPTION'),
    'PATH' => [
        'ID' => 'itbizon',
        'NAME' => Loc::getMessage('ITB_FINANCE.PERMISSION.ADD.PATH.NAME'),
        'CHILD' => [
            'ID' => 'finance',
            'NAME' => Loc::getMessage('ITB_FINANCE.PERMISSION.ADD.PATH.CHILD.NAME'),
        ]
    ],
];
