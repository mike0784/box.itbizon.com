<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentParameters = [
    'GROUPS' => [],
    'PARAMETERS' => [
        'FOLDER' => [
            'PARENT' => 'ADDITIONAL_SETTINGS',
            'NAME' => Loc::getMessage('ITB_FIN.VAULT_GROUP_EDIT.PARAMETER.FOLDER'),
            'TYPE' => 'STRING',
            'REFRESH' => 'N',
            'MULTIPLE' => 'N',
            'ADDITIONAL_VALUES' => 'N',
            'DEFAULT' => '',
        ],
    ],
];
