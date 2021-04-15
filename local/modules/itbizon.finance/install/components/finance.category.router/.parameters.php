<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentParameters = [
    'PARAMETERS' => [
        'SEF_MODE' => [
            'list' => [
                'NAME' => Loc::getMessage('ITB_FIN.CATEGORY_ROUTER.PARAMETER.TEMPLATE_LIST'),
                'DEFAULT' => '/',
                'VARIABLES' => []
            ],
            'add' => [
                'NAME' => Loc::getMessage('ITB_FIN.CATEGORY_ROUTER.PARAMETER.TEMPLATE_ADD'),
                'DEFAULT' => 'add/',
                'VARIABLES' => []
            ],
            'edit' => [
                'NAME' => Loc::getMessage('ITB_FIN.CATEGORY_ROUTER.PARAMETER.TEMPLATE_EDIT'),
                'DEFAULT' => 'edit/#ID#/',
                'VARIABLES' => ['ID']
            ],
        ],
    ],
];
