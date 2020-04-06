<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => 'Test',
	"DESCRIPTION" => "TEST",
	"COMPLEX" => "Y",
	"PATH" => array(
		"ID" => "Тестовый компонент",
		"CHILD" => array(
			"ID" => "router",
            'NAME'  => 'Роутер',
		),
	),
);

?>