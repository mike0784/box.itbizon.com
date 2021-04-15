<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = [
    'NAME' => Loc::getMessage('ITB_FIN.VAULT_ROUTER.NAME'),
    'DESCRIPTION' => Loc::getMessage('ITB_FIN.VAULT_ROUTER.DESCRIPTION'),
    'ICON' => '/images/icon.gif',
    'PATH' => [
        'ID' => 'itbizon',
        'NAME' => Loc::getMessage('ITB_FIN.VAULT_ROUTER.PATH.NAME'),
        'CHILD' => [
            'ID' => 'finance',
            'NAME' => Loc::getMessage('ITB_FIN.VAULT_ROUTER.PATH.CHILD.NAME'),
        ]
    ],
    'COMPLEX' => 'Y'
];
