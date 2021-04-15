<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = [
    'NAME' => Loc::getMessage("ITB_FIN.REQUEST_TEMPLATE.EDIT.NAME"),
    'DESCRIPTION' => Loc::getMessage("ITB_FIN.REQUEST_TEMPLATE.EDIT.NAME"),
    'PATH' => [
        'ID' => 'itbizon',
        'NAME' => Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE.EDIT.PATH.NAME'),
        'CHILD' => [
            'ID' => 'finance',
            'NAME' => Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE.EDIT.CHILD.NAME'),
        ]
    ],
];