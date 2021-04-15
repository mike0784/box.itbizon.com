<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentParameters = [
    'GROUPS' => [],
    'PARAMETERS' => [
        'FOLDER' => [
            'PARENT' => 'ADDITIONAL_SETTINGS',
            'NAME' => Loc::getMessage('ITB_FIN.CATEGORY_ADD.PARAMETER.FOLDER'),
            'TYPE' => 'STRING',
            'REFRESH' => 'N',
            'MULTIPLE' => 'N',
            'ADDITIONAL_VALUES' => 'N',
            'DEFAULT' => '',
        ],
        'TEMPLATE_LIST' => [
            'PARENT' => 'ADDITIONAL_SETTINGS',
            'NAME' => Loc::getMessage('ITB_FIN.CATEGORY_ADD.PARAMETER.TEMPLATE_LIST'),
            'TYPE' => 'STRING',
            'REFRESH' => 'N',
            'MULTIPLE' => 'N',
            'ADDITIONAL_VALUES' => 'N',
            'DEFAULT' => '/',
        ],
        'TEMPLATE_ADD' => [
            'PARENT' => 'ADDITIONAL_SETTINGS',
            'NAME' => Loc::getMessage('ITB_FIN.CATEGORY_ADD.PARAMETER.TEMPLATE_ADD'),
            'TYPE' => 'STRING',
            'REFRESH' => 'N',
            'MULTIPLE' => 'N',
            'ADDITIONAL_VALUES' => 'N',
            'DEFAULT' => 'add/',
        ],
        'TEMPLATE_EDIT' => [
            'PARENT' => 'ADDITIONAL_SETTINGS',
            'NAME' => Loc::getMessage('ITB_FIN.CATEGORY_ADD.PARAMETER.TEMPLATE_EDIT'),
            'TYPE' => 'STRING',
            'REFRESH' => 'N',
            'MULTIPLE' => 'N',
            'ADDITIONAL_VALUES' => 'N',
            'DEFAULT' => 'edit/#ID#/',
        ],
    ],
];
