<?php

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$arComponentDescription = [
    'NAME' => 'Отчет по сделкам/лидам для которых не было активности',
    'DESCRIPTION' => 'Компонент для просмотра отчета для которых не было активности',
    'ICON' => '/images/icon.gif',
    'PATH' => [
        'ID' => 'itbizon',
        'NAME' =>'Бизон',
        'CHILD' => [
            'ID' => 'basis',
            'NAME' => 'basis',
        ]
    ],
];
