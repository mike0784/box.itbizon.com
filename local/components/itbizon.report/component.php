<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Socialnetwork\WorkgroupTable;

$dateFormat = "Y-m-d";
$workGroup = WorkgroupTable::getList()->fetchAll();

$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$dateFrom = $request->getCookieRaw("REPORT_FROM");
$dateTo = $request->getCookieRaw("REPORT_TO");
$groupID = $request->getCookieRaw("REPORT_GROUP");

$users = [];



$r = CUser::GetList(
    ($by = "id"),
    ($order = "desc"),
    [
        "GROUPS_ID" => [12]
    ]
);

while($arItem = $r->GetNext())
{
    echo "[". $arItem['ID']."] (".$arItem['LOGIN'].") ".$arItem['NAME']." ".$arItem['LAST_NAME']."<br>";
}

//var_dump($r->result);

$arResult = array();
//$arResult["DOMAIN"] = isset($_REQUEST["domain"]) ? $_REQUEST["domain"] : '';
//$arResult["AJAX_PATH"] = $componentPath."/ajax.php";

$arResult['WORKGROUP_LIST'] = $workGroup;
$arResult['WORKGROUP_ID'] = $groupID;

if(!empty($dateFrom)) {
    $arResult['INTERVAL'] = [
        'FROM'  => $dateFrom,
        'TO'    => $dateTo
    ];
} else {
    $arResult['INTERVAL'] = [
        'FROM'  => date($dateFormat, strtotime("-7 days")),
        'TO'    => date($dateFormat)
    ];
}

$this->IncludeComponentTemplate();