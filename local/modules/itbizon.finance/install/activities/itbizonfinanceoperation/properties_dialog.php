<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

/**
 * @var array $arCurrentValues
 * @var array $formFieldsData
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CJSCore::Init(array("jquery"));
?>

<tr>
    <td align="right" width="40%"><span
                class="adm-required-field"><?= GetMessage('ACTIVITY.FINANCE.OPERATION.FIELD.NAME') ?>:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField('int', 'NAME', $arCurrentValues['NAME'], ['rows' => 1]) ?>
    </td>
</tr>
<tr>
    <td align="right" width="40%"><span
                class="adm-required-field"><?= GetMessage('ACTIVITY.FINANCE.OPERATION.FIELD.TYPE') ?>:</span></td>
    <td width="60%">
        <select id="operation-type" name="<?= htmlspecialcharsbx('TYPE') ?>">
            <option><?= GetMessage('ACTIVITY.FINANCE.OPERATION.SELECT_MESSAGE.TYPE') ?></option>
            <?php foreach ($formFieldsData['TYPE_LIST'] as $id => $value) : ?>
                <option value="<?= $id ?>" <?= $id == $arCurrentValues['TYPE'] ? 'selected' : '' ?>><?= $value ?></option>
            <?php endforeach; ?>
        </select>
        <?= CBPDocument::ShowParameterField('int', 'TYPE_TEXT', $arCurrentValues['TYPE_TEXT'], ['rows' => 1]) ?>
    </td>
</tr>
<tr class="type d-hidden d-none" id="operation-src-vault">
    <td align="right" width="40%"><span
                class="adm-required-field"><?= GetMessage('ACTIVITY.FINANCE.OPERATION.FIELD.SRC_VAULT') ?>:</span></td>
    <td width="60%">
        <select name="<?= htmlspecialcharsbx('SRC_VAULT_ID') ?>">
            <option value=""><?= GetMessage('ACTIVITY.FINANCE.OPERATION.SELECT_MESSAGE.SRC_VAULT') ?></option>
            <?php foreach ($formFieldsData['VAULT_LIST'] as $id => $value) : ?>
                <option value="<?= $id ?>" <?= $id == $arCurrentValues['SRC_VAULT_ID'] ? 'selected' : '' ?>><?= $value ?></option>
            <?php endforeach; ?>
        </select>
        <?= CBPDocument::ShowParameterField('int', 'SRC_VAULT_ID_TEXT', $arCurrentValues['SRC_VAULT_ID_TEXT'], ['rows' => 1]) ?>
    </td>
</tr>
<tr class="type d-hidden d-none" id="operation-dst-vault">
    <td align="right" width="40%"><span
                class="adm-required-field"><?= GetMessage('ACTIVITY.FINANCE.OPERATION.FIELD.DST_VAULT') ?>:</span></td>
    <td width="60%">
        <select name="<?= htmlspecialcharsbx('DST_VAULT_ID') ?>">
            <option value=""><?= GetMessage('ACTIVITY.FINANCE.OPERATION.SELECT_MESSAGE.DST_VAULT') ?></option>
            <?php foreach ($formFieldsData['VAULT_LIST'] as $id => $value) : ?>
                <option value="<?= $id ?>" <?= $id == $arCurrentValues['DST_VAULT_ID'] ? 'selected' : '' ?>><?= $value ?></option>
            <?php endforeach; ?>
        </select>
        <?= CBPDocument::ShowParameterField('int', 'DST_VAULT_ID_TEXT', $arCurrentValues['DST_VAULT_ID_TEXT'], ['rows' => 1]) ?>
    </td>
</tr>
<tr class="type d-hidden d-none" id="operation-category">
    <td align="right" width="40%"><span
                class="adm-required-field"><?= GetMessage('ACTIVITY.FINANCE.OPERATION.FIELD.CATEGORY') ?>:</span></td>
    <td width="60%">
        <select name="<?= htmlspecialcharsbx('CATEGORY_ID') ?>">
            <option value=""><?= GetMessage('ACTIVITY.FINANCE.OPERATION.SELECT_MESSAGE.CATEGORY') ?></option>
            <?php foreach ($formFieldsData['CATEGORY_LIST'] as $categoryName => $category) : ?>
                <?php foreach ($category as $id => $value) : ?>
                    <option class="category <?= $categoryName ?>"
                            value="<?= $id ?>" <?= $id == $arCurrentValues['CATEGORY_ID'] ? 'selected' : '' ?>><?= $value ?></option>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </select>
        <?= CBPDocument::ShowParameterField('int', 'CATEGORY_ID_TEXT', $arCurrentValues['CATEGORY_ID_TEXT'], ['rows' => 1]) ?>
    </td>
</tr>
<tr>
    <td align="right" width="40%"><span
                class="adm-required-field"><?= GetMessage('ACTIVITY.FINANCE.OPERATION.FIELD.RESPONSIBLE') ?>:</span>
    </td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField('user', 'RESPONSIBLE_ID', $arCurrentValues['RESPONSIBLE_ID'], ['rows' => 1]) ?>
    </td>
</tr>
<tr>
    <td align="right" width="40%"><span
                class="adm-required-field"><?= GetMessage('ACTIVITY.FINANCE.OPERATION.FIELD.AMOUNT') ?>:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField('int', 'AMOUNT', $arCurrentValues['AMOUNT'], ['rows' => 1]) ?>
    </td>
</tr>
<tr>
    <td align="right" width="40%"><span><?= GetMessage('ACTIVITY.FINANCE.OPERATION.FIELD.CRM') ?>:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField('string', 'ENTITY_CRM', $arCurrentValues['ENTITY_CRM'], ['rows' => 1]) ?>
    </td>
</tr>
<tr id="operation-entity-type">
    <td align="right" width="40%"><span><?= GetMessage('ACTIVITY.FINANCE.OPERATION.FIELD.ENTITY_TYPE') ?>:</span></td>
    <td width="60%">
        <select name="<?= htmlspecialcharsbx('ENTITY_TYPE_ID') ?>">
            <option value=""><?= GetMessage('ACTIVITY.FINANCE.OPERATION.SELECT_MESSAGE.ENTITY_TYPE') ?></option>
            <?php foreach ($formFieldsData['ENTITY_TYPE_LIST'] as $id => $value) : ?>
                <option class="<?= $value['NAME'] ?>"
                        value="<?= $id ?>" <?= $id == $arCurrentValues['ENTITY_TYPE_ID'] ? 'selected' : '' ?>><?= $value['DESCRIPTION'] ?></option>
            <?php endforeach; ?>
        </select>
        <?= CBPDocument::ShowParameterField('string', 'ENTITY_TYPE_ID_TEXT', $arCurrentValues['ENTITY_TYPE_ID_TEXT'], ['rows' => 1]) ?>
    </td>
</tr>
<tr id="operation-entity" class="d-hidden d-none">
    <td align="right" width="40%"><span
                class="adm-required-field"><?= GetMessage('ACTIVITY.FINANCE.OPERATION.FIELD.ENTITY') ?>:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField('int', 'ENTITY_ID', $arCurrentValues['ENTITY_ID'], ['rows' => 1]) ?>
    </td>
</tr>
<tr>
    <td align="right" width="40%"><span><?= GetMessage('ACTIVITY.FINANCE.OPERATION.FIELD.COMMENT') ?>:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField('text', 'COMMENT', $arCurrentValues['COMMENT'], ['rows' => 1]) ?>
    </td>
</tr>
<?php if (count($formFieldsData['USER_FIELDS'])) : ?>
    <tr>
        <td align="right" width="40%"><span>Пользовательские поля:</span></td>
        <td width="60%">
            <hr>
        </td>
    </tr>
<?php endif; ?>
<?php foreach ($formFieldsData['USER_FIELDS'] as $key => $userField) : ?>
    <tr>
        <td align="right" width="40%"><span><?= $userField['EDIT_FORM_LABEL'] ?>:</span></td>
        <td width="60%">
            <?php
            if ($userField["USER_TYPE"]["USER_TYPE_ID"] != 'string')
                $APPLICATION->IncludeComponent(
                    "bitrix:system.field.edit",
                    $userField["USER_TYPE"]["USER_TYPE_ID"],
                    [
                        "bVarsFromForm" => false,
                        "arUserField" => $userField
                    ],
                    null,
                    array("HIDE_ICONS" => "Y"))
            ?>
            <?= CBPDocument::ShowParameterField('string', $key . "_TEXT", $arCurrentValues[$key . "_TEXT"], ['rows' => 1]);
            ?>
        </td>
    </tr>
<?php endforeach; ?>
<tr>
    <td align="right" width="40%"><span
                class="adm-required-field"><?= GetMessage('ACTIVITY.FINANCE.OPERATION.FIELD.MODE_CREATE') ?>:</span>
    </td>
    <td width="60%">
        <select name="<?= htmlspecialcharsbx('MODE_CREATE') ?>">
            <option value=""><?= GetMessage('ACTIVITY.FINANCE.OPERATION.SELECT_MESSAGE.MODE_CREATE') ?></option>
            <?php foreach ($formFieldsData['MODE_CREATE'] as $id => $value) : ?>
                <option value="<?= $id ?>" <?= $id == $arCurrentValues['MODE_CREATE'] ? 'selected' : '' ?>><?= $value ?></option>
            <?php endforeach; ?>
        </select>
        <?= CBPDocument::ShowParameterField('string', 'MODE_CREATE_TEXT', $arCurrentValues['MODE_CREATE'], ['rows' => 1]) ?>
    </td>
</tr>

<style>
    .d-none {
        display: none;
    }
</style>

<script>
    $(document).ready(function () {
        $('#operation-type').on("change", function () {
            filterCategory(true);
        });

        $('#operation-entity-type select').on("change", function () {
            $('#operation-entity').addClass('d-none');
            if (parseInt($(this).val())) {
                $('#operation-entity').removeClass('d-none');
            }
        });

        filterCategory();
        $('#operation-entity-type select').change();
    });

    function filterCategory(clear = false) {
        $('.type.d-hidden').addClass('d-none');

        switch ($('#operation-type').val()) {
            // 'Расход'
            case '1':
                showDstVault();
                showCategory("ALLOW_INCOME");
                break;
            // 'Приход'
            case '2':
                showSrcVault();
                showCategory("ALLOW_OUTGO");
                break;
            // 'Перевод'
            case '3':
                showDstVault();
                showSrcVault();
                showCategory("ALLOW_TRANSFER");
                break;
        }

        if (clear) $('#operation-category select').val('');
    }

    function showSrcVault() {
        $('#operation-src-vault').removeClass('d-none');
    }

    function showDstVault() {
        $('#operation-dst-vault').removeClass('d-none');
    }

    function showCategory(type) {
        $('#operation-category').removeClass('d-none');
        $('#operation-category option').addClass('d-none');
        $('#operation-category option.' + type).removeClass('d-none');
    }
</script>