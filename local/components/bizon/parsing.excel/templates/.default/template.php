<?php
$cells = $arResult['CELLS'];
$errorMessage = $arResult['ERROR'];
?>

<div class="container-fluid">
    <? if ($errorMessage): ?>
        <div class="alert alert-danger" role="alert"><?= $errorMessage ?></div>
    <? endif; ?>
    <div class="row">
        <div class="col-md-12">
            <form method="POST" style=" width: 40%; margin: 0 auto" enctype="multipart/form-data">
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

                <button type="submit" class="btn btn-success float-right">Sign in</button>

            </form>
        </div>
    </div>
</div>