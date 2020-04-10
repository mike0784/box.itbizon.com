<?php

use \Bitrix\Bizproc\FieldType;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

$arActivityDescription = [
    'NAME' => '[itbizon] Штрафы/бонусы',
    'DESCRIPTION' => '[itbizon] Штрафы/бонусы',
    'TYPE' => ['activity'],
    'CLASS' => 'ItbizonAddFine',
    'JSCLASS' => 'BizProcActivity',
    'CATEGORY' => [
        'ID' => 'other',
    ]
];
?>