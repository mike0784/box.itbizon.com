<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Socialnetwork\WorkgroupTable;
use Bitrix\Voximplant\Model\CallUserTable;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Type\DateTime;

$dateFormatOutput = "Y-m-d";
$dateFormatFilter = "d.m.Y 00:00:00";

$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$strDateFrom = $request->getCookieRaw("REPORT_FROM");
$strDateTo = $request->getCookieRaw("REPORT_TO");
$groupID = intval($request->getCookieRaw("REPORT_GROUP"));
$groups = Bitrix\Im\Integration\Intranet\Department::GetList();

if(!$groupID)
    $groupID = intval($groups[0]['ID']);

$usersList =  \Bitrix\Main\UserTable::getList([
        "filter" => [
            "UF_DEPARTMENT" => $groupID
        ],
        "select" => [
            '*',
            'UF_DEPARTMENT'
        ]
    ]);

$users = [];
$tasksListScript = [];

//if(!Loader::includeModule('voximplant'))
//    throw new Exception('Ошибка подключения модуля voximplant');
//

$dateFrom = \DateTime::createFromFormat($dateFormatOutput, $strDateFrom);
$dateTo = \DateTime::createFromFormat($dateFormatOutput, $strDateTo);

while ($arItem = $usersList->fetch()) {
    $fullName = $arItem['LAST_NAME'] . " " .
        substr($arItem['NAME'], 0, 1) . ".";
    if(!empty($arItem['SECOND_NAME']))
        $fullName .= substr($arItem['SECOND_NAME'], 0, 1) . ".";

    $voicesList = [];

    $tasksList = \CTasks::GetList([],
        [
//            '::LOGIC' => 'AND',
            '=CREATED_BY' => $arItem['ID'],
//            '=CREATED_BY' => '9',
            '>=DEADLINE' => $dateFrom->format($dateFormatFilter),
            '<=DEADLINE' => $dateTo->format($dateFormatFilter),
        ],
        [
            "ID",
            "TITLE",
//            "CREATED_BY",
//            "DESCRIPTION",
//            "DATE_START",
//            "CLOSED_DATE",
//            "DEADLINE",
            "REAL_STATUS"
        ]
    );

    $taskDoneNum = 0;
    $taskNum = 0;

    while ($task = $tasksList->Fetch()) {
        if($task["REAL_STATUS"] == 5)
            $taskDoneNum++;
        else 
            $taskNum++;
        echo "<pre>" . print_r($task, true) . "</pre>";
    }

    $users[] = [
        'ID' => $arItem['ID'],
        'LOGIN' => $arItem['LOGIN'],
        'FULLNAME' => $fullName,
        'CALL_NUM' => count($voicesList),
        'TASK_DONE_NUM' => $taskDoneNum,
        'TASK_NUM' => $taskNum,
//        'TASKS' => $tasks,
    ];

}

$arResult = array();
$arResult["AJAX_PATH"] = $this->GetPath() . '/templates/.default/ajax.php';

$arResult['DEP_LIST'] = $groups;
$arResult['DEP_ID'] = $groupID;
$arResult['USERS'] = $users;

if(!empty($strDateFrom)) {
    $arResult['INTERVAL'] = [
        'FROM'  => $strDateFrom,
        'TO'    => $strDateTo
    ];
} else {
    $arResult['INTERVAL'] = [
        'FROM'  => date($dateFormatOutput, strtotime("-7 days")),
        'TO'    => date($dateFormatOutput)
    ];
}

$this->IncludeComponentTemplate();