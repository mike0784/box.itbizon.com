<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<tr>
    <td align="right" width="40%"><span class="adm-required-field">Домен:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField('string', 'DOMAIN', $arCurrentValues['DOMAIN'], ['rows' => 1]) ?>
    </td>
</tr>


<tr>
    <td align="right" width="40%"><span class="adm-required-field">Логин:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField('string', 'LOGIN', $arCurrentValues['LOGIN'], ['rows' => 1]) ?>
    </td>
</tr>

<tr>
    <td align="right" width="40%"><span class="adm-required-field">Пароль:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField('string', 'PASSWORD', $arCurrentValues['PASSWORD'], ['rows' => 1]) ?>
    </td>
</tr>