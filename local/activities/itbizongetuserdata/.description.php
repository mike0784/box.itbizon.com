<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arActivityDescription = [
    'NAME'        => '[itbizon] Получить данные пользователя',
    'DESCRIPTION' => 'Позволяет получать информацию о пользователе',
    'TYPE'        => ['activity'],
    'CLASS'       => 'ItbizonGetUserData',
    'JSCLASS'     => 'BizProcActivity',
    'CATEGORY'    => [
        'ID' => 'other',
    ],
    'ADDITIONAL_RESULT' => [
        'InfoData'
    ]
];