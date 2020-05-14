<?php

use \Bitrix\Bizproc\FieldType;

$arActivityDescription = [
    'NAME' => '[itbizon] Накладные',
    'DESCRIPTION' => '[itbizon] Накладные',
    'TYPE' => ['activity'],
    'CLASS' => 'ItbizonAddInvoice',
    'JSCLASS' => 'BizProcActivity',
    'CATEGORY' => [
        'ID' => 'other',
    ],
    "RETURN" => [
        'InvoiceID' => [
            'NAME' => 'ID',
            'TYPE' => 'int'
        ]
    ]
];
?>