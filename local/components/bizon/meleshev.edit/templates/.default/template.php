<div class="container">
    <h2 class="text-center">Магазин <?= $arResult['SHOP']['TITLE'] ?></h2>
    <form action="" method="post">
        <div class="form-group">
            <input type="hidden" class="form-control" name="ID" value="<?=$arResult['SHOP']['ID']?>">
        </div>
        <div class="form-group">
            <label for="TITLE">Title</label>
            <input type="text" class="form-control" name="TITLE" value="<?=$arResult['SHOP']['TITLE']?>">
        </div>
        <div class="form-group">
            <label for="AMOUNT">Amount</label>
            <input type="number" class="form-control" name="AMOUNT" value="<?=$arResult['SHOP']['AMOUNT']?>">
        </div>
        <div class="form-group">
            <label for="COUNT">Count</label>
            <input type="number" class="form-control" name="COUNT" value="<?=$arResult['SHOP']['COUNT']?>">
        </div>
        <div class="form-group">
            <label for="COMMENT">Comment</label>
            <input type="text" class="form-control" name="COMMENT" value="<?=$arResult['SHOP']['COMMENT']?>">
        </div>
        <button type="submit" class="btn btn-primary">Сохранить</button>
    </form>
    <hr />

    <? if($arResult['SHOP']['ID'] > 0): ?>
    <h2 class="text-center">Добавить автомобиль</h2>
    <form action="" method="post">
        <div class="form-group">
            <input type="hidden" name="NEW_CAR" value="true">
        </div>
        <div class="form-group">
            <label for="SHOP_ID">ID магазина <?= $arResult['SHOP']['ID'] ?></label>
            <input type="hidden" name="SHOP_ID" value="<?= $arResult['SHOP']['ID'] ?>"><br />
        </div>
        <div class="form-group">
            <label for="MARK">Марка</label>
            <input type="text" class="form-control" name="MARK" value="">
        </div>
        <div class="form-group">
            <label for="MODEL">Модель</label>
            <input type="text" class="form-control" name="MODEL" value="">
        </div>
        <div class="form-group">
            <label for="VALUE">Цена в копейках</label>
            <input type="number" class="form-control" name="VALUE" value="">
        </div>
        <div class="form-group">
            <label for="IS_USED">Используется?</label>
            <input type="checkbox" class="form-control" name="IS_USED" checked="checked">
        </div>
        <div class="form-group">
            <label for="COMMENT">Комментарий</label>
            <input type="text" class="form-control" name="COMMENT" value="">
        </div>
        <button type="submit" class="btn btn-primary">Добавить автомобиль</button>
    </form>
    <? endif; ?>

    <hr />

    <? if ($arResult['CARS']): ?>
        <h2 class="text-center">Автомобили</h2>

        <? foreach ($arResult['CARS'] as $car): ?>
            <h6>ID автомобиля <?= $car['ID'] ?></h6>
            <form action="" method="post">
                <div class="form-group">
                    <input type="hidden" name="CAR" value="true">
                </div>
                <div class="form-group">
                    <input type="hidden" name="ID" value="<?= $car['ID'] ?>">
                </div>
                <div class="form-group">
                    <label for="SHOP_ID">ID магазина <?= $car['SHOP_ID'] ?></label>
                    <input type="hidden" name="SHOP_ID" value="<?= $car['SHOP_ID'] ?>"><br />
                </div>
                <div class="form-group">
                    <label for="MARK">Марка</label>
                    <input type="text" class="form-control" name="MARK" value="<?=$car['MARK']?>">
                </div>
                <div class="form-group">
                    <label for="MODEL">Модель</label>
                    <input type="text" class="form-control" name="MODEL" value="<?=$car['MODEL']?>">
                </div>
                <div class="form-group">
                    <label for="VALUE">Цена в копейках</label>
                    <input type="number" class="form-control" name="VALUE" value="<?=$car['VALUE']?>">
                </div>
                <div class="form-group">
                    <label for="IS_USED">Используется?</label>
                    <input type="checkbox" class="form-control" name="IS_USED"
                           <?= $car['IS_USED'] != "Y" ?: "checked";?>>
                </div>
                <div class="form-group">
                    <label for="COMMENT">Комментарий</label>
                    <input type="text" class="form-control" name="COMMENT" value="<?=$car['COMMENT']?>">
                </div>
                <button type="submit" class="btn btn-primary">Сохранить изменения</button>
            </form>
            <hr />
        <? endforeach; ?>
    <? endif; ?>
    <a class="btn btn-primary" href="/mtest">Назад</a>


</div>
