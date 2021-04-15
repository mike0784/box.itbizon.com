<?php

use Bitrix\Main\UI\Extension;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Extension::load('itbizon.finance.bootstrap4');

/**@var $APPLICATION CAllMain * */
/**@var $arResult array * */
$APPLICATION->IncludeComponent(
/** @var array $arResult */
    'itbizon:finance.operation.' . $arResult['VARIABLES']['ACTION'],
    '',
    [
        'FOLDER' => $arResult['FOLDER'],
        'TEMPLATE_LIST' => $arResult['URL_TEMPLATES']['list'],
        'TEMPLATE_ADD' => $arResult['URL_TEMPLATES']['add'],
        'TEMPLATE_EDIT' => $arResult['URL_TEMPLATES']['edit'],
        'VARIABLES' => $arResult['VARIABLES'],
    ]
);
