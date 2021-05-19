<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = [
    'NAME' => Loc::getMessage('ITB_SCRATCH.BOX_ADD.NAME'),
    'DESCRIPTION' => Loc::getMessage('ITB_SCRATCH.BOX_ADD.DESCRIPTION'),
//	'ICON'        => '/images/icon.gif',
    'PATH' => [
        'ID' => 'itbizon',
        'NAME' => Loc::getMessage('ITB_SCRATCH.BOX_ADD.PATH.NAME'),
        'CHILD' => [
            'ID' => 'finance',
            'NAME' => Loc::getMessage('ITB_SCRATCH.BOX_ADD.PATH.CHILD.NAME'),
        ]
    ],
];
