<?php

use \Bitrix\Main\Loader;

defined('B_PROLOG_INCLUDED') || die;

global $APPLICATION, $USER;
$module_id = $_GET['mid'];
if (!$USER->IsAdmin())
    return;

Loader::includeModule($module_id);
$tabs = [
    [
        'DIV' => 'general',
        'TAB' => 'Основное',
        'TITLE' => 'Основные настройки'
    ],
    [
        'DIV' => 'tasks',
        'TAB' => 'Задачи',
        'TITLE' => 'Настройка задачи'
    ],
];
$options = [
    'general' => [

    ],
    'tasks' => [
        ['number_week', 'Поле номера недели', '', ['text', '40']],
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
      action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= htmlspecialcharsbx($moduleId) ?>&lang=<?= LANGUAGE_ID ?>">
    <? foreach ($options as $tab => $option) : ?>
        <? $tabControl->BeginNextTab(); ?>
        <? __AdmSettingsDrawList($moduleId, $options[$tab]); ?>
    <? endforeach; ?>
    <? $tabControl->Buttons(['btnApply' => false, 'btnCancel' => false, 'btnSaveAndAdd' => false]); ?>
    <?= bitrix_sessid_post(); ?>
    <? $tabControl->End(); ?>
</form>