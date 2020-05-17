<?php

use \Bitrix\Bizproc\FieldType;

$arActivityDescription = [
    'NAME' => '[itbizon] Товары',
    'DESCRIPTION' => '[itbizon] Товары',
    'TYPE' => ['activity'],
    'CLASS' => 'ItbizonAddProduct',
    'JSCLASS' => 'BizProcActivity',
    'CATEGORY' => [
        'ID' => 'other',
    ],
    "RETURN" => [
        'ProductID' => [
            'NAME' => 'ID',
            'TYPE' => 'int'
        ]
    ]
];
?>