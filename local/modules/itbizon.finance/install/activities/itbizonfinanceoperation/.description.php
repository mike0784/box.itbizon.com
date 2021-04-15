<?php

$arActivityDescription = [
    'NAME' => GetMessage('ACTIVITY.FINANCE.OPERATION.MODULE_NAME'),
    'DESCRIPTION' => GetMessage('ACTIVITY.FINANCE.OPERATION.MODULE_DESCRIPTION'),
    'TYPE' => ['activity'],
    'CLASS' => 'ItBizonFinanceOperation',
    'JSCLASS' => 'BizProcActivity',
    'CATEGORY' => array(
        "ID" => "itbizon",
        "OWN_ID" => 'itbizon',
        "OWN_NAME" => GetMessage('ACTIVITY.FINANCE.OPERATION.CATEGORY_NAME'),
    ),
    "RETURN" => [
        'ID' => [
            'NAME' => GetMessage('ACTIVITY.FINANCE.OPERATION.RETURN.ID'),
            'TYPE' => 'int'
        ],
        'CONFIRMED' => [
            'NAME' => GetMessage('ACTIVITY.FINANCE.OPERATION.RETURN.CONFIRMED'),
            'TYPE' => 'bool'
        ],
        'DECLINE' => [
            'NAME' => GetMessage('ACTIVITY.FINANCE.OPERATION.RETURN.DECLINE'),
            'TYPE' => 'bool'
        ],
        'DELETED' => [
            'NAME' => GetMessage('ACTIVITY.FINANCE.OPERATION.RETURN.DELETED'),
            'TYPE' => 'bool'
        ],
    ]
];
