<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$startTime=strtotime('2020-03-02');
$endTime=time();
$arResult = floor(($endTime-$startTime)/86400);



$this->IncludeComponentTemplate();
?>