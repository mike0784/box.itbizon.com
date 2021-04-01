<?php
use \Bitrix\Main\UI\Extension;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Генератор комионентов");

Extension::load('ui.bootstrap4');


$data = $_REQUEST;
$localizationList = ['ru', 'en'];
?>
<form method="POST">
    <div class="form-group">
        <label for="moduleId">ID модуля</label>
        <input id="moduleId" class="form-control" type="text" name="moduleId" value="<?= strval($data['moduleId']) ?>" required>
    </div>
    <div class="form-group">
        <label for="moduleName">Название модуля</label>
        <input id="moduleName" class="form-control" type="text" name="moduleName" value="<?= strval($data['moduleName']) ?>" required>
    </div>
    <div class="form-group">
        <label for="moduleDescription">Описание модуля</label>
        <input id="moduleDescription" class="form-control" type="text" name="moduleDescription" value="<?= strval($data['moduleDescription']) ?>" required>
    </div>
    <div class="form-group">
        <label for="localization">Локализация</label>
        <select id="localization" class="form-control" name="localization" size="<?= count($localizationList) ?>" multiple>
            <? foreach($localizationList as $loc): ?>
                <option value="<?= $loc ?>" <?= in_array($loc, $data['localization']) ? 'selected' : '' ?>><?= $loc ?></option>
            <? endforeach; ?>
        </select>
    </div>
    <div class="form-group">

    </div>
</form>
<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
