<?php
/**
 * В данном файле размещается вызов обработчиков событий
 * Файл подключается на каждой странице - ошибки чреваты неработоспособностью системы
 */

use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Itbizon\Service\Mail\Postman;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/event_handlers.php')) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/event_handlers.php';
}

if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/functions.php')) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/functions.php';
}

if(Loader::includeModule('itbizon.service')) {
    if(Option::get('itbizon.service', 'mail_send_active', 'N') === 'Y') {
        function custom_mail($to, $subject, $message, $additional_headers='', $additional_parameters='')
        {
            return Postman::send($to, $subject, $message, $additional_headers, $additional_parameters);
        }
    }
}