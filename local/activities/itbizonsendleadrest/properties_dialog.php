<?php

/**
 * @var array $arCurrentValues
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>

<tr>
    <td align="right" width="40%"><span class="adm-required-field">Адрес вебхука целевого портала:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField('string', 'REST_PATH', $arCurrentValues['REST_PATH'], ['rows' => 1]) ?>
    </td>
</tr>
<tr>
    <td align="right" width="40%"><span class="adm-required-field">Название:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField('string', 'TITLE', $arCurrentValues['TITLE'], ['rows' => 1]) ?>
    </td>
</tr>
<tr>
    <td align="right" width="40%"><span class="adm-required-field">Имя:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField('string', 'NAME', $arCurrentValues['NAME'], ['rows' => 1]) ?>
    </td>
</tr>
<tr>
    <td align="right" width="40%"><span class="adm-required-field">Фамилия:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField('string', 'LAST_NAME', $arCurrentValues['LAST_NAME'], ['rows' => 1]) ?>
    </td>
</tr>
<tr>
    <td align="right" width="40%"><span class="adm-required-field">Ответственный:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField('int', 'RESPONSIBLE_ID', $arCurrentValues['RESPONSIBLE_ID'], ['rows' => 1]) ?>
    </td>
</tr>
<tr>
    <td align="right" width="40%"><span class="adm-required-field">Отчество:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField('string', 'SECOND_NAME', $arCurrentValues['SECOND_NAME'], ['rows' => 1]) ?>
    </td>
</tr>
<tr>
    <td align="right" width="40%"><span class="adm-required-field">Источник:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField('string', 'SOURCE_ID', $arCurrentValues['SOURCE_ID'], ['rows' => 1]) ?>
    </td>
</tr>
<tr>
    <td align="right" width="40%"><span class="adm-required-field">Телефон(ы):</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField('string', 'PHONE', $arCurrentValues['PHONE'], ['rows' => 1]) ?>
    </td>
</tr>
<tr>
    <td align="right" width="40%"><span class="adm-required-field">Email(ы):</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField('string', 'EMAIL', $arCurrentValues['EMAIL'], ['rows' => 1]) ?>
    </td>
</tr>
<tr>
    <td align="right" width="40%"><span class="adm-required-field">Комментарии:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField('text', 'COMMENT', $arCurrentValues['COMMENT'], ['rows' => 1]) ?>
    </td>
</tr>
<tr id="next-userfields">
    <td><span class="adm-required-field">Пользовательские поля</span></td>
    <td><input value="Добавить поле" onclick="addUserField()" type="button"></td>
</tr>

<?php foreach ($arCurrentValues['USER_FIELD'] as $uf_key => $uf_val) : ?>
<tr id="key_<?= htmlspecialcharsbx($uf_key) ?>">
    <td align="right" width="40%" class="adm-detail-content-cell-l">
        <span class="adm-required-field">
            <textarea name="UF_KEYS[]" cols="20" rows="1"><?= $uf_key ?></textarea>
        </span>
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <div class="d-flex">
            <textarea name="UF_VALS[]" cols="50" rows="1"><?= $uf_val ?></textarea>
            <input value="-" onclick="removeById('<?= htmlspecialcharsbx($uf_key) ?>')" type="button">
        </div>
    </td>
</tr>
<?php endforeach; ?>

<style>
    .d-flex {
        display: flex;
    }
</style>
<script>
    function removeUserField(el) {
        el.remove();
    }

    function removeById(id) {
        var el = document.getElementById("key_"+id);
        removeUserField(el);
    }

    function addUserField() {
        let tr = document.createElement('tr');

        let tdKey = Object.assign(document.createElement('td'),{
            className: 'adm-detail-content-cell-l',
            align: 'right',
            width: '40%',
        });

        let span = Object.assign(document.createElement('span'),{
            className:'adm-required-field',
        });

        let inputKey = Object.assign(document.createElement('textarea'),{
            name:'UF_KEYS[]',
            rows:1,
            cols:20,
        });

        let tdVal = Object.assign(document.createElement('td'),{
            className:'adm-detail-content-cell-r',
            width:'60%',
        });

        let div = Object.assign(document.createElement('div'),{
            className:'d-flex',
        });

        let inputVal = Object.assign(document.createElement('textarea'),{
            name:'UF_VALS[]',
            rows:1,
            cols:50,
        });

        let inputValRemove = Object.assign(document.createElement('input'),{
            type:'button',
            value:'-',
            onclick:function() {
                removeUserField(tr)
            },
        });

        span.append(inputKey);
        div.append(inputVal);
        div.append(inputValRemove);
        tdKey.append(span);
        tdVal.append(div);
        tr.append(tdKey,tdVal);

        document.getElementById("next-userfields").parentElement.append(tr);
        return false;
    }
</script>