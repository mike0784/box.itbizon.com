<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$arComponentDescription = [
    'NAME'        => 'История изменения полей',
    'DESCRIPTION' => 'Компонент для управления историей полей',
    'ICON'        => '/images/icon.gif',
    'PATH'        => [
        'ID'    => 'itbizon',
        'NAME'  => 'Бизон',
        'CHILD' => [
            'ID'   => 'fieldcollector.dealfield',
            'NAME' => 'Журнал полей',
        ],
    ],
    'COMPLEX' => 'Y',
];