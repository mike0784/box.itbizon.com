<?php
$cells = $arResult['CELLS'];
$errorMessage = $arResult['ERROR'];
$archives = $arResult['ARCHIVES'];
$folders = $arResult['FOLDERS'];
$pathToDownloadsArchive = $arResult['PATH_DOWNLOAD_ARCHIVE'];
?>
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">

<div class="container-fluid">
    <? if ($errorMessage): ?>
        <div class="alert alert-danger" role="alert"><?= $errorMessage ?></div>
    <? endif; ?>
    <div class="row mb-3 justify-content-end">
        <div class="btn-toolbar col-md-12" role="toolbar">
            <div class="btn-group mr-2" role="group" aria-label="Third group">
                <button type="button" id="clear-all" class="btn btn-danger">Очистить</button>
            </div>
        </div>
    </div>
    <div class="row">
        <input type="hidden" id="path-to-ajax" data-path="<?= $arResult['PATH'] ?>">
        <div class="col-md-4">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="columnName">Название</label>
                    <select class="form-control" id="cellName" name="cellName">
                        <option></option>
                        <? foreach ($cells as $cell): ?>
                            <option value="<?= $cell ?>"><?= $cell ?></option>
                        <? endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="columnLink">Ссылка</label>
                    <select class="form-control" id="cellLink" name="cellLink">
                        <option></option>
                        <? foreach ($cells as $cell): ?>
                            <option value="<?= $cell ?>"><?= $cell ?></option>
                        <? endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="excelFile">Выберите Excel фаил</label>
                    <input type="file" class="form-control-file" id="excelFile" name="excelFile"
                           accept="application/vnd.sealed.xls,application/vnd.ms-excel,
                           application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                </div>
                <div class="btn-group mr-2" role="group" aria-label="First group">
                    <button type="submit" class="btn btn-success get__data">Получить данные</button>
                </div>
                <div class="btn-group mr-2" role="group" aria-label="First group">
                    <a class="btn btn-info create__archive" style="color: white">
                        Скачать данные
                    </a>
                </div>
            </form>
        </div>

        <div class="col-md-8">
            <table class="table table-sm table-striped table-hover text-center align-middle table-bordered">
                <thead class="thead-dark">
                <tr>
                    <th scope="col">Имя</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <? foreach ($archives as $archive): ?>
                    <tr>
                        <td>
                            <a href="<?= '/' . $pathToDownloadsArchive . '/' . $archive ?>">
                                <?= $archive ?>
                            </a>
                        </td>
                        <td>
                            <a href="#" style="color: black" class="remove-archive"
                               data-value="<?= $archive ?>"
                            >
                                &#10006;
                            </a>
                        </td>
                    </tr>
                <? endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <br>
    <div class="w3-light-grey progress-bar">

    </div>
    <br>
    <div class="row">
        <table class="table table-sm table-striped table-hover text-center align-middle table-bordered">
            <thead class="thead-dark">
            <tr>
                <th>№</th>
                <th scope="col">Имя</th>
                <th scope="col">Сссылка</th>
            </tr>
            </thead>
            <tbody id="tbody__data">

            </tbody>
        </table>
    </div>
</div>