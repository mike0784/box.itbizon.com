<?php

use Bitrix\Main\Loader;
use Bitrix\Tasks\Item\Task;
use Itbizon\Debit\Form\Form;
use Itbizon\Debit\Form\Model\FormTable;

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
define('CURRENT_DIR', str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__));

$APPLICATION->ShowHead();
CJSCore::Init(array("jquery", "date"));
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <script src="//api.bitrix24.com/api/v1/"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
          crossorigin="anonymous">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <script>
        BX24.init(function () {
            BX24.ready(function () {
                BX24.fitWindow();
            });
        });
    </script>
</head>
<body>
<div class="container-fluid">
    <div class="info__container alert" style="display: none"></div>
    <input type="hidden" id="path-to-ajax" value="<?= CURRENT_DIR ?>/ajax.php">
    <div class="row justify-content-center">
        <form method="POST" id="form-document" class="form-inline">
            <div class="col-md-8">
                <div class="custom-file">
                    <input type="file" class="custom-file-input" name="document"
                           accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" id="document" required>
                    <label class="custom-file-label" for="document">Выберите файл ...</label>
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-success" id="submit__button">Загрузить таблицу</button>
            </div>
        </form>
    </div>

    <div class="w3-light-grey progress-bar">

    </div>

    <div class="table-content mt-5">

    </div>
</div>
<script src="<?= CURRENT_DIR ?>/js/script.js?date=<?= date('d.m.YH:i:s') ?>"></script>
<?php
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php');
?>
</body>
</html>
