<a href="<?= $arResult['pathToHome'] ?>" type="button" class="btn btn-primary my-2">На главную</a>
<form method="POST" action="<?= $arResult['path'] ?>">
    <div class="form-group">
        <label for="title">Название</label>
        <input type="text" name="title" id="title" class="form-control is-valid" value="<?= (!empty($arResult['result']['invoice']) ? $arResult['result']['invoice']->get("TITLE") : "Временное название"); ?>">
    </div>

    <div class="form-group">
        <label for="title">Сумма</label>
        <input type="text" class="form-control is-valid" value="<? if(!empty($arResult['result']['invoice'])) echo $arResult['result']['invoice']->get("AMOUNT"); ?>" disabled>
    </div>

    <div class="form-group">
        <label for="title">Товары</label>
        <table class="table invoice-table">
            <thead>
            <tr>
                <th>#</th>
                <th>ID</th>
                <th>Наименование товара</th>
                <th>Дата добавления</th>
                <th>Автор</th>
                <th>Стоимость</th>
                <th>Количество</th>
                <th>Комментарий</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><a id="productPopup" data-path="<?= $arResult['path'] . "?invoiceID=" . $arResult['invoiceID'] ?>" class="btn btn-success" href="#">+</a></td>
            </tr>
            <?php if(isset($arResult['result']['products'])): ?>
                <?php foreach ($arResult['result']['products'] as $product): ?>
                    <tr>
                        <td><a data-path="<?= $arResult['path'] . "?remove=" . $product["ID"]; ?>" class="btn btn-danger removeProduct" href="#">-</a></td>
                        <td><?= $product["ID"]; ?></td>
                        <td><?= $product["TITLE"]; ?></td>
                        <td><?= $product["DATE_CREATE"]; ?></td>
                        <td><?= $arResult['users'][intval($product["CREATOR_ID"])]; ?></td>
                        <td><?= $product["VALUE"]; ?></td>
                        <td><?= $product["COUNT"]; ?></td>
                        <td><?= $product["COMMENT"]; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="form-group">
        <label for="comment">Комментарий</label>
        <textarea name="comment" id="comment" class="form-control"><? if(!empty($arResult['result']['invoice'])) echo $arResult['result']['invoice']->get("COMMENT"); ?></textarea>
    </div>

    <input type="hidden" id="invoiceID" name="invoice" value="<?= $arResult['invoiceID']; ?>">

    <button id="submit" type="button" class="btn btn-success">Сохранить</button>
</form>
