<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Itbizon\Finance\Model\OperationCategoryTable;
use Itbizon\Finance\Model\StockTable;

Loc::loadMessages(__FILE__);

defined('B_PROLOG_INCLUDED') || die;

global $APPLICATION, $USER;
$module_id = $_GET['mid'];
if (!$USER->IsAdmin())
    return;

Loader::includeModule($module_id);

// С учетом возможного перевода на другие локали
$oldLocale = setlocale(LC_TIME, 0);
setlocale(LC_TIME, 'ru_RU.UTF-8');
$weekNames = [
    "Monday",
    "Tuesday",
    "Wednesday",
    "Thursday",
    "Friday",
    "Saturday",
    "Sunday",
];
$week = [];
for ($day = 0; 7 != $day; $day++)
    $week[$day] = strftime("%A", strtotime("{$weekNames[$day]} this week"));

$stocks = [];
$result = StockTable::getList([
    'select' => ['ID', 'NAME'],
    'order' => ['NAME' => 'ASC']
]);
while($row = $result->fetchObject()) {
    $stocks[$row->getId()] = $row->getName();
}

$incomeCategories = [];
$result = OperationCategoryTable::getList([
    'order' => ['NAME' => 'ASC']
]);
while($row = $result->fetchObject()) {
    if($row->getAllowIncome()) {
        $incomeCategories[$row->getId()] = $row->getName();
    }
}

setlocale(LC_TIME, $oldLocale);
$tabs = [
    [
        'DIV' => 'financial_planning',
        'TAB' => Loc::getMessage('ITB_FIN.OPTIONS.TAB.FINANCIAL_PLANNING'),
        'TITLE' => Loc::getMessage('ITB_FIN.OPTIONS.TAB.FINANCIAL_PLANNING')
    ],

];
$options = [
    'financial_planning' => [
        ['startWeek', Loc::getMessage('ITB_FIN.OPTIONS.TAB.FINANCIAL_PLANNING.START_WEEK'), '', ['selectbox', $week]],
        ['startTime', Loc::getMessage('ITB_FIN.OPTIONS.TAB.FINANCIAL_PLANNING.START_TIME'), '', ['text', '12:00']],
        ['reserveStockId', Loc::getMessage('ITB_FIN.OPTIONS.TAB.FINANCIAL_PLANNING.RESERVE_STOCK_ID'), '', ['selectbox', $stocks]],
        ['incomeCategoryId', Loc::getMessage('ITB_FIN.OPTIONS.TAB.FINANCIAL_PLANNING.INCOME_CATEGORY_ID'), '', ['selectbox', $incomeCategories]],
    ],
];

if (check_bitrix_sessid() && strlen($_POST['save']) > 0) {
    foreach ($options as $option) {
        __AdmSettingsSaveOptions($module_id, $option);
    }
    LocalRedirect($APPLICATION->GetCurPageParam());
}

$tabControl = new CAdminTabControl('tabControl', $tabs);
$tabControl->Begin();
?>
<form method="POST"
      action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= htmlspecialcharsbx($module_id) ?>&lang=<?= LANGUAGE_ID ?>">
    <? foreach ($options as $tab => $option) : ?>
        <? $tabControl->BeginNextTab(); ?>
        <? __AdmSettingsDrawList($module_id, $options[$tab]); ?>
    <? endforeach; ?>
    <? $tabControl->Buttons(['btnApply' => false, 'btnCancel' => false, 'btnSaveAndAdd' => false]); ?>
    <?= bitrix_sessid_post(); ?>
    <? $tabControl->End(); ?>
</form>