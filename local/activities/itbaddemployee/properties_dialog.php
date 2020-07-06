<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

use \Bitrix\Main\Loader;
use \Bizon\Main\UserFieldHelper;
use \Bitrix\Tasks\Integration\Intranet\Department;

CUtil::InitJSCore(['jquery']);

try
{
    if(!Loader::includeModule('bizon.main'))
        throw new Exception('error load module bizon.main');
    if(!Loader::includeModule('tasks'))
        throw new Exception('error load module tasks');
    
    // get Department list
    $list = Department::getCompanyStructure();
    $departments = [];
    foreach ($list as $department)
        $departments[$department['ID']] = $department['NAME'];
    
    // get Group list
    $list = \Bitrix\Main\GroupTable::getList();
    $groups = [];
    while($item = $list->fetch())
        $groups[$item['ID']] = $item['NAME'];
    
    $utsUser = UserFieldHelper::getUtsList('USER');
    foreach ($utsUser as $key => $item)
    {
        if(!$item['NAME'] || $item['FIELD_NAME'] == 'UF_DEPARTMENT')
            unset($utsUser[$key]);
    }
}
catch (Exception $ex)
{
    $ex->getMessage();
}

?>
<tr>
    <td width="40%" align="right" valign="top"><span>Фамилия</span></td>
    <td width="60%"><?= CBPDocument::ShowParameterField('string', 'LAST_NAME', $arCurrentValues['LAST_NAME']); ?></td>
</tr>
<tr>
    <td width="40%" align="right" valign="top"><span>Имя</span></td>
    <td width="60%"><?= CBPDocument::ShowParameterField('string', 'NAME', $arCurrentValues['NAME']); ?></td>
</tr>
<tr>
    <td width="40%" align="right" valign="top"><span>Отчество</span></td>
    <td width="60%"><?= CBPDocument::ShowParameterField('string', 'SECOND_NAME', $arCurrentValues['SECOND_NAME']); ?></td>
</tr>
<tr>
    <td width="40%" align="right" valign="top"><span>Пол</span></td>
    <td>
        <select name="PERSONAL_GENDER">
            <option value=""></option>
            <option value="M" <? if($arCurrentValues['PERSONAL_GENDER'] == 'M') echo 'selected';?>>Мужчина</option>
            <option value="F" <? if($arCurrentValues['PERSONAL_GENDER'] == 'F') echo 'selected';?>>Женщина</option>
        </select>
    </td>
</tr>
<tr>
    <td width="40%" align="right" valign="top"></td>
    <td width="60%"><?= CBPDocument::ShowParameterField('string', 'PERSONAL_GENDER_X', $arCurrentValues['PERSONAL_GENDER_X']); ?></td>
</tr>
<tr>
    <td width="40%" align="right" valign="top"><span>День рождения</span></td>
    <td width="60%"><?= CBPDocument::ShowParameterField('datetime', 'PERSONAL_BIRTHDAY', $arCurrentValues['PERSONAL_BIRTHDAY']); ?></td>
</tr>
<tr>
    <td width="40%" align="right" valign="top"><span class="adm-required-field">Логин *</span></td>
    <td width="60%"><?= CBPDocument::ShowParameterField('string', 'LOGIN', $arCurrentValues['LOGIN']); ?></td>
</tr>
<tr>
    <td width="40%" align="right" valign="top"><span class="adm-required-field">Email *</span></td>
    <td width="60%"><?= CBPDocument::ShowParameterField('string', 'EMAIL', $arCurrentValues['EMAIL']); ?></td>
</tr>
<tr>
    <td width="40%" align="right" valign="top"><span class="adm-required-field">Пароль *</span></td>
    <td width="60%"><?= CBPDocument::ShowParameterField('string', 'PASSWORD', $arCurrentValues['PASSWORD']); ?></td>
</tr>
<tr>
    <td width="40%" align="right" valign="top"><span class="adm-required-field">Подтверждение пароля *</span></td>
    <td width="60%"><?= CBPDocument::ShowParameterField('string', 'CONFIRM_PASSWORD', $arCurrentValues['CONFIRM_PASSWORD']); ?></td>
</tr>
<tr>
    <td width="40%" align="right" valign="top"><span>Активный</span></td>
    <td width="60%"><?= CBPDocument::ShowParameterField('bool', 'ACTIVE', $arCurrentValues['ACTIVE']); ?></td>
</tr>
<tr>
    <td width="40%" align="right" valign="top"><span>Рабочий телефон</span></td>
    <td width="60%"><?= CBPDocument::ShowParameterField('string', 'PHONE_NUMBER', $arCurrentValues['PHONE_NUMBER']); ?></td>
</tr>
<tr>
    <td width="40%" align="right" valign="top"><span>Личный телефон</span></td>
    <td width="60%"><?= CBPDocument::ShowParameterField('string', 'PERSONAL_PHONE', $arCurrentValues['PERSONAL_PHONE']); ?></td>
</tr>
<tr>
    <td width="40%" align="right" valign="top"><span>Страна</span></td>
    <td width="60%"><?= CBPDocument::ShowParameterField('string', 'PERSONAL_COUNTRY', $arCurrentValues['PERSONAL_COUNTRY']); ?></td>
</tr>
<tr>
    <td width="40%" align="right" valign="top"><span>Регион</span></td>
    <td width="60%"><?= CBPDocument::ShowParameterField('string', 'PERSONAL_STATE', $arCurrentValues['PERSONAL_STATE']); ?></td>
</tr>
<tr>
    <td width="40%" align="right" valign="top"><span>Город</span></td>
    <td width="60%"><?= CBPDocument::ShowParameterField('string', 'PERSONAL_CITY', $arCurrentValues['PERSONAL_CITY']); ?></td>
</tr>
<tr>
    <td width="40%" align="right" valign="top"><span>Улица</span></td>
    <td width="60%"><?= CBPDocument::ShowParameterField('string', 'PERSONAL_STREET', $arCurrentValues['PERSONAL_STREET']); ?></td>
</tr>
<tr>
    <td width="40%" align="right" valign="top"><span>Почтовый ящик</span></td>
    <td width="60%"><?= CBPDocument::ShowParameterField('string', 'PERSONAL_MAILBOX', $arCurrentValues['PERSONAL_MAILBOX']); ?></td>
</tr>
<tr>
    <td width="40%" align="right" valign="top"><span>Индекс</span></td>
    <td width="60%"><?= CBPDocument::ShowParameterField('string', 'PERSONAL_ZIP', $arCurrentValues['PERSONAL_ZIP']); ?></td>
</tr>
<tr>
    <td width="40%" align="right" valign="top"><span>Группы</span></td>
    <td>
        <select name="GROUP_ID[]" multiple>
            <? foreach ($groups as $key => $value) : ?>
                <option value="<?=$key?>"
                    <?
                        foreach ($arCurrentValues['GROUP_ID'] as $item)
                            if($item == $key) echo 'selected';
                    ?>><?=$value?></option>
            <? endforeach; ?>
        </select>
    </td>
</tr>
<tr>
    <td width="40%" align="right" valign="top"><span></span></td>
    <td width="60%"><?= CBPDocument::ShowParameterField('string', 'GROUP_ID_X', $arCurrentValues['GROUP_ID_X']); ?></td>
</tr>
<tr>
    <td width="40%" align="right" valign="top"><span>Подразделения</span></td>
    <td>
        <select name="UF_DEPARTMENT[]" multiple>
            <? foreach ($departments as $key => $value) : ?>
                <option value="<?=$key?>"
                    <?
                        foreach ($arCurrentValues['UF_DEPARTMENT'] as $item)
                            if($item == $key) echo 'selected';
                    ?>><?=$value?></option>
            <? endforeach; ?>
        </select>
    </td>
</tr>
<tr>
    <td width="40%" align="right" valign="top"><span></span></td>
    <td width="60%"><?= CBPDocument::ShowParameterField('string', 'UF_DEPARTMENT_X', $arCurrentValues['UF_DEPARTMENT_X']); ?></td>
</tr>
<tr>
    <td width="40%" align="right" valign="top"><span>Должность</span></td>
    <td width="60%"><?= CBPDocument::ShowParameterField('string', 'WORK_POSITION', $arCurrentValues['WORK_POSITION']); ?></td>
</tr>
<tr>
    <td width="40%" align="right" valign="top"><h3>Пользовательские поля</h3></td>
</tr>
<? foreach ($utsUser as $item) : ?>
<tr>
    <td width="40%" align="right" valign="top"><span><?=$item['NAME']?></span></td>
    <td width="60%"><?= CBPDocument::ShowParameterField('string', $item['FIELD_NAME'], $arCurrentValues[$item['FIELD_NAME']]); ?></td>
</tr>
<? endforeach; ?>