<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentParameters = [
    'GROUPS' => [],
    'PARAMETERS' => [
        'FOLDER' => [
            'PARENT' => 'ADDITIONAL_SETTINGS',
            'NAME' => Loc::getMessage('ITB_FIN.VAULT_GROUP_ADD.PARAMETER.FOLDER'),
            'TYPE' => 'STRING',
            'REFRESH' => 'N',
            'MULTIPLE' => 'N',
            'ADDITIONAL_VALUES' => 'N',
            'DEFAULT' => '',
        ],
        'TEMPLATE_LIST' => [
            'PARENT' => 'ADDITIONAL_SETTINGS',
            'NAME' => Loc::getMessage('ITB_FIN.VAULT_GROUP_ADD.PARAMETER.TEMPLATE_LIST'),
            'TYPE' => 'STRING',
            'REFRESH' => 'N',
            'MULTIPLE' => 'N',
            'ADDITIONAL_VALUES' => 'N',
            'DEFAULT' => '/',
        ],
        'TEMPLATE_ADD' => [
            'PARENT' => 'ADDITIONAL_SETTINGS',
            'NAME' => Loc::getMessage('ITB_FIN.VAULT_GROUP_ADD.PARAMETER.TEMPLATE_ADD'),
            'TYPE' => 'STRING',
            'REFRESH' => 'N',
            'MULTIPLE' => 'N',
            'ADDITIONAL_VALUES' => 'N',
            'DEFAULT' => 'add/',
        ],
        'TEMPLATE_ADD_GROUP' => [
            'PARENT' => 'ADDITIONAL_SETTINGS',
            'NAME' => Loc::getMessage('ITB_FIN.VAULT_GROUP_ADD.PARAMETER.TEMPLATE_ADD_GROUP'),
            'TYPE' => 'STRING',
            'REFRESH' => 'N',
            'MULTIPLE' => 'N',
            'ADDITIONAL_VALUES' => 'N',
            'DEFAULT' => 'add/',
        ],
        'TEMPLATE_EDIT' => [
            'PARENT' => 'ADDITIONAL_SETTINGS',
            'NAME' => Loc::getMessage('ITB_FIN.VAULT_GROUP_ADD.PARAMETER.TEMPLATE_EDIT'),
            'TYPE' => 'STRING',
            'REFRESH' => 'N',
            'MULTIPLE' => 'N',
            'ADDITIONAL_VALUES' => 'N',
            'DEFAULT' => 'edit/#ID#/',
        ],
        'TEMPLATE_EDIT_GROUP' => [
            'PARENT' => 'ADDITIONAL_SETTINGS',
            'NAME' => Loc::getMessage('ITB_FIN.VAULT_GROUP_ADD.PARAMETER.TEMPLATE_EDIT_GROUP'),
            'TYPE' => 'STRING',
            'REFRESH' => 'N',
            'MULTIPLE' => 'N',
            'ADDITIONAL_VALUES' => 'N',
            'DEFAULT' => 'groupedit/#ID#/',
        ],
    ],
];
