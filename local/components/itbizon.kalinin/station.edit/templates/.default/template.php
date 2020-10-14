<div class="container">
    <h2 class="text-center">Станция <?= $arResult['station']['NAME'] ?></h2>
    <form action="" method="post">
        <div class="form-group">
            <input type="hidden" class="form-control" name="ID" value="<?=$arResult['station']['ID']?>">
        </div>
        <div class="form-group">
            <label for="NAME">Name</label>
            <input type="text" class="form-control" name="NAME" value="<?=$arResult['station']['NAME']?>">
        </div>
        <div class="form-group">
            <label for="AMOUNT">Amount</label>
            <input type="number" class="form-control" name="AMOUNT" value="<?=$arResult['station']['AMOUNT']?>">
        </div>
        <div class="form-group">
            <label for="COUNT">Count</label>
            <input type="number" class="form-control" name="COUNT" value="<?=$arResult['station']['COUNT']?>">
        </div>
        <div class="form-group">
            <label for="COMMENT">Comment</label>
            <input type="text" class="form-control" name="COMMENT" value="<?=$arResult['station']['COMMENT']?>">
        </div>
        <button type="submit" class="btn btn-primary">Сохранить</button>
    </form>
    <hr />

    <? if($arResult['station']['ID'] > 0): ?>
        <h2 class="text-center">Создать корабль</h2>
        <form action="" method="post">
            <div class="form-group">
                <input type="hidden" name="NEW_SHIP" value="true">
            </div>
            <div class="form-group">
                <label for="STATION_ID">ID Станции <?= $arResult['station']['ID'] ?></label>
                <input type="hidden" name="STATION_ID" value="<?= $arResult['station']['ID'] ?>"><br />
            </div>
            <div class="form-group">
                <label for="MARK">Название</label>
                <input type="text" class="form-control" name="NAME" value="">
            </div>
            <div class="form-group">
                <label for="MATERIALS">Материалы</label>
                <input type="text" class="form-control" name="MATERIALS" value="">
            </div>
            <div class="form-group">
                <label for="VALUE">Цена в копейках</label>
                <input type="number" class="form-control" name="VALUE" value="">
            </div>
            <div class="form-group">
                <label for="IS_RELEASED">Выпущен?
                    <input type="checkbox" class="form-check-input" name="IS_RELEASED">
                </label>
            </div>
            <div class="form-group">
                <label for="COMMENT">Комментарий</label>
                <input type="text" class="form-control" name="COMMENT" value="">
            </div>
            <button type="submit" class="btn btn-primary">Добавить Корабль</button>
        </form>
    <? endif; ?>

    <hr />

    <? if ($arResult['ships']): ?>
        <h2 class="text-center">Корабли</h2>

        <? foreach ($arResult['ships'] as $ship): ?>
            <h6>ID Корабля <?= $ship['ID'] ?></h6>
            <form action="" method="post">
                <div class="form-group">
                    <input type="hidden" name="SHIP" value="true">
                </div>
                <div class="form-group">
                    <input type="hidden" name="ID" value="<?= $ship['ID'] ?>">
                </div>
                <div class="form-group">
                    <label for="SHOP_ID">ID Станции <?= $ship['STATION_ID'] ?></label>
                    <input type="hidden" name="SHOP_ID" value="<?= $ship['STATION_ID'] ?>"><br />
                </div>
                <div class="form-group">
                    <label for="NAME">Название</label>
                    <input type="text" class="form-control" name="NAME" value="<?=$ship['NAME']?>">
                </div>
                <div class="form-group">
                    <label for="MATERIALS">Материалы</label>
                    <input type="text" class="form-control" name="MATERIALS" value="<?=$ship['MATERIALS']?>">
                </div>
                <div class="form-group">
                    <label for="VALUE">Цена в копейках</label>
                    <input type="number" class="form-control" name="VALUE" value="<?=$ship['VALUE']?>">
                </div>
                <div class="form-group">
                    <label for="IS_RELEASED">Выпущен?
                        <input type="checkbox" class="form-check-input" name="IS_RELEASED"
                        <?= $ship['IS_RELEASED'] != "Y" ?: "checked";?>>
                    </label>
                </div>
                <div class="form-group">
                    <label for="COMMENT">Комментарий</label>
                    <input type="text" class="form-control" name="COMMENT" value="<?=$ship['COMMENT']?>">
                </div>
                <button type="submit" class="btn btn-primary">Сохранить изменения</button>
            </form>
            <hr />
        <? endforeach; ?>
    <? endif; ?>
    <a class="btn btn-primary" href="/">Назад</a>


</div>