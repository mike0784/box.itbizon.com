<?php

use Bitrix\Bizproc\FieldType;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

$arActivityDescription = [
    'NAME'        => '[itbizon] Добавить сотрудника',
    'DESCRIPTION' => 'Добавить сотрудника',
    'TYPE'        => ['activity'],
    'CLASS'       => 'ItbAddEmployee',
    'JSCLASS'     => 'BizProcActivity',
    'CATEGORY'    => [
        'ID' => 'other',
    ],
    'RETURN'      => [
        'EmployeeId'     => [
            'NAME' => 'ID нового сотрудника',
            'TYPE' => FieldType::INT,
        ],
    ],
];