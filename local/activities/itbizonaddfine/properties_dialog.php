<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>

<tr>
    <td align="right" width="40%"><span class="adm-required-field">Название:</span></td>
    <td width="60%">
        <?=CBPDocument::ShowParameterField('string', 'TITLE', $arCurrentValues['TITLE'], Array('rows' => 1))?>
    </td>
</tr>

<tr>
    <td align="right" width="40%"><span class="adm-required-field">Число:</span></td>
    <td width="60%">
        <?=CBPDocument::ShowParameterField('string', 'VALUE', $arCurrentValues['VALUE'], Array('rows' => 1))?>
    </td>
</tr>

<tr>
    <td align="right" width="40%"><span class="adm-required-field">На кокого пользователя:</span></td>
    <td width="60%">
        <?=CBPDocument::ShowParameterField('user', 'TARGET_ID', $arCurrentValues['TARGET_ID'], Array('rows' => 1))?>
    </td>
</tr>

<tr>
    <td align="right" width="40%"><span class="adm-required-field">Кто создал:</span></td>
    <td width="60%">
        <?=CBPDocument::ShowParameterField('user', 'CREATOR_ID', $arCurrentValues['CREATOR_ID'], Array('rows' => 1))?>
    </td>
</tr>
<tr>
    <td align="right" width="40%"><span class="adm-required-field">Коментарий:</span></td>
    <td width="60%">
        <?=CBPDocument::ShowParameterField('text', 'COMMENT', $arCurrentValues['COMMENT'], Array('rows' => 1))?>
    </td>
</tr>