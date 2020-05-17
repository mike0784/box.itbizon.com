<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

$arComponentDescription = [
    'NAME'        => 'Meleshev',
    'DESCRIPTION' => 'Meleshev',
    'ICON'        => '/images/icon.gif',
    'PATH'        => [
        'ID'    => 'Тестовый компонент',
        'CHILD' => [
            'ID'   => 'index',
            'NAME' => 'Индекс',
        ],
    ],
    'COMPLEX'     => 'Y',
];