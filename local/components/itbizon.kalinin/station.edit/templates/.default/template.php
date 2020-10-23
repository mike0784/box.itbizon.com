<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<div class="container">
    <h2 class="text-center">Станция <?= $arResult['station']['NAME'] ?></h2>
    <form action="" method="post">
        <div class="form-group">
            <input type="hidden" class="form-control" name="ID" value="<?=$arResult['station']['ID']?>">
        </div>
        <div class="form-group">
            <label for="NAME">Название</label>
            <input type="text" class="form-control" name="NAME" value="<?=$arResult['station']['NAME']?>" required>
        </div>
        <div class="form-group">
            <label for="COMMENT">Комментарий</label>
            <input type="text" class="form-control" name="COMMENT" value="<?=$arResult['station']['COMMENT']?>">
        </div>
    <hr/>
        <h2 class="text-center">Корабли</h2>

        <table class="table table-bordered">
            <thead class="table-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Название</th>
                <th scope="col">Материалы</th>
                <th scope="col">Стоимость ($)</th>
                <th scope="col">Статус</th>
                <th scope="col">Комментарий</th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <? if ($arResult['ships']): ?>
            <? foreach ($arResult['ships'] as $ship): ?>
                <tr>
                    <th scope="row"><?=$ship['ID']?></th>
                    <td><?=$ship['NAME']?></td>
                    <td><?=$ship['MATERIALS']?></td>
                    <td><?= intval($ship['VALUE']) / 100 ?></td>
                    <td><?=$ship['IS_RELEASED'] == 'Y' ? "Выпущен" : "Не выпущен"?></td>
                    <td><?=$ship['COMMENT']?></td>
                    <td><a data-path="<?= $arResult['path'] . "?remove=" . $ship["ID"]; ?>" class="btn btn-danger removeShip" href="#"><i class="fa fa-trash"></i></a></td>
                </tr>
            <? endforeach; ?>
            <? endif; ?>
                <tr>
                    <? if($arResult['station']['ID'] > 0): ?>
                        <td><a id="shipPopup" data-path="<?= $arResult['path'] . "?StationID=" . $arResult['station']['ID'] ?>" class="btn btn-success" href="#">+</a></td>
                    <? endif; ?>
                </tr>
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary">Сохранить</button>
    </form>
    <hr />
    <a class="btn btn-primary" href="/local/test/kalinin">Назад</a>


</div>