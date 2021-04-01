<?php

use \Bitrix\Main\Loader;

defined('B_PROLOG_INCLUDED') || die;

global $APPLICATION, $USER;
$moduleId = $_GET['mid'];
if (!$USER->IsAdmin())
    return;

Loader::includeModule($moduleId);

$tabs = [
    [
        'DIV' => 'general',
        'TAB' => 'Основные',
        'TITLE' => 'Основные настройки'
    ],
    [
        'DIV' => 'sberbank_eq',
        'TAB' => 'API Сбербанк эквайринг',
        'TITLE' => 'Настройки подключения к API эквайринга Сбербанка'
    ],
    [
        'DIV' => 'module_bank',
        'TAB' => 'API Модульбанк',
        'TITLE' => 'Настройки подключения к API Моудльбанка'
    ],
];
$options = [
    'general' => [

    ],
    'sberbank_eq' => [
        ['sberbank_eq_api_login', 'Логин', '', ['text', '32']],
        ['sberbank_eq_api_token', 'Токен', '', ['text', '32']],
        ['sberbank_eq_api_password', 'Пароль', '', ['text', '32']],
        ['sberbank_eq_api_debug_password', 'Пароль (отладка)', '', ['text', '32']],
        ['sberbank_eq_api_debug_token', 'Токен (отладка)', '', ['text', '32']],
        ['sberbank_eq_api_debug', 'Режим отладки', 'N', ['checkbox']],
        ['sberbank_eq_api_use_token', 'Использовать токен вместо пароля', 'N', ['checkbox']],
        ['sberbank_eq_lead_field', 'Поле лида с номером заказа', '', ['text', '32']],
        ['sberbank_eq_deal_field', 'Поле сделки с номером заказа', '', ['text', '32']],
    ],
    'module_bank' => [
        ['module_bank_api_client_id', 'ID приложения (ClientId)', '', ['text', '32']],
        ['module_bank_api_client_secret', 'Ключ приложения (ClientSecret)', '', ['text', '32']],
        ['module_bank_api_token', 'Токен', '', ['text', '64']],
        ['module_bank_api_debug', 'Режим отладки', 'N', ['checkbox']],
        ['module_bank_api_use_token', 'Использовать токен вместо Oauth', 'N', ['checkbox']],
    ],
];

if (check_bitrix_sessid() && strlen($_POST['save']) > 0) {
    foreach ($options as $option) {
        __AdmSettingsSaveOptions($moduleId, $option);
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