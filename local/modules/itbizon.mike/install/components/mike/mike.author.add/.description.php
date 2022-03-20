<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = [
    'NAME' => Loc::getMessage('ITB_MIKE_AUTHOR_ADD_NAME'),
    'DESCRIPTION' => Loc::getMessage('ITB_MIKE_AUTHOR_ADD_DESCRIPTION'),
    'PATH' => [
        'ID' => 'itbizon',
        'NAME' => Loc::getMessage('ITB_MIKE_AUTHOR_ADD_PATH_NAME'),
        'CHILD' => [
            'ID' => 'mike',
            'NAME' => Loc::getMessage('ITB_MIKE_AUTHOR_ADD_PATH_CHILD_NAME'),
        ]
    ],
];