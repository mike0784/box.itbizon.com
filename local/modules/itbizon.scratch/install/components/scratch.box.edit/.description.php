<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = [
    'NAME' => Loc::getMessage('ITB_SCRATCH.BOX_EDIT.NAME'),
    'DESCRIPTION' => Loc::getMessage('ITB_SCRATCH.BOX_EDIT.DESCRIPTION'),
    'ICON' => '/images/icon.gif',
    'PATH' => [
        'ID' => 'itbizon',
        'NAME' => Loc::getMessage('ITB_SCRATCH.BOX_EDIT.PATH.NAME'),
        'CHILD' => [
            'ID' => 'scratch',
            'NAME' => Loc::getMessage('ITB_SCRATCH.BOX_EDIT.PATH.CHILD.NAME'),
        ]
    ],
];
