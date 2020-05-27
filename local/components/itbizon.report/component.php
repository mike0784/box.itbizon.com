<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Socialnetwork\WorkgroupTable;

$dateFormat = "Y-m-d";

$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$dateFrom = $request->getCookieRaw("REPORT_FROM");
$dateTo = $request->getCookieRaw("REPORT_TO");
$groupID = intval($request->getCookieRaw("REPORT_GROUP"));
$groups = Bitrix\Im\Integration\Intranet\Department::GetList();


var_dump($groupID);
//var_dump(!$groupID);
if(!$groupID)
    $groupID = intval($groups[0]['ID']);

//$groups_ = CIBlockSection::getGroup([]);
//var_dump($groups_);

$usersList = CUser::GetList(
    ($by = "id"),
    ($order = "desc"),
    [
//        "UF_DEPARTMENT" => "57"
    ],
    ["SELECT" => ["UF_*"]]
);


$users = [];

while ($arItem = $usersList->GetNext()) {
    $fullName = $arItem['LAST_NAME'] . " " .
        substr($arItem['NAME'], 0, 1) . ".";
    if(!empty($arItem['SECOND_NAME']))
        $fullName .= substr($arItem['SECOND_NAME'], 0, 1) . ".";

    $users[] = [
        'ID' => $arItem['ID'],
        'LOGIN' => $arItem['LOGIN'],
        'FULLNAME' => $fullName,
        'CALL' => 3,
        'TASK_DONE' => 2,
        'TASK_' => 7
    ];
//    echo $arItem['NAME'];
//    if($arItem['ID'] == 9){
 var_dump($arItem);

    break;
//}
}

//var_dump($arUsers);

//var_dump($r->result);

$arResult = array();
//$arResult["DOMAIN"] = isset($_REQUEST["domain"]) ? $_REQUEST["domain"] : '';
//$arResult["AJAX_PATH"] = $componentPath."/ajax.php";

$arResult['DEP_LIST'] = $groups;
$arResult['DEP_ID'] = $groupID;
$arResult['USERS'] = $users;

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