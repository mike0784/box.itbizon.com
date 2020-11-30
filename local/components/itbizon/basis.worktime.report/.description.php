<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

$arComponentDescription = [
    'NAME'        => 'Отчет по трудоемкости',
    'DESCRIPTION' => 'Отчет по трудоемкости',
    'ICON'        => '/images/icon.gif',
    'PATH'        => [
        'ID'    => 'itbizon',
        'NAME'  => 'Бизон',
        'CHILD' => [
            'ID'   => 'basis',
            'NAME' => 'basis',
        ],
    ],
];