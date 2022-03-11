<?php

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = [
    'NAME' => Loc::getMessage('ITB_SERVICE.MAILDOMAIN.ADD.NAME'),
    'DESCRIPTION' => Loc::getMessage('ITB_SERVICE.MAILDOMAIN.ADD.DESCRIPTION'),
    'PATH' => [
        'ID' => 'itbizon',
        'NAME' => Loc::getMessage('ITB_SERVICE.MAILDOMAIN.ADD.PATH.NAME'),
        'CHILD' => [
            'ID' => 'service',
            'NAME' => Loc::getMessage('ITB_SERVICE.MAILDOMAIN.ADD.PATH.CHILD.NAME'),
        ]
    ],
];
