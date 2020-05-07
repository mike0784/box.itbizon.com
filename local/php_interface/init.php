<?php
/**
 * В данном файле размещается вызов обработчиков событий
 * Файл подключается на каждой странице - ошибки чреваты неработоспособностью системы
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/event_handlers.php')) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/event_handlers.php';
}