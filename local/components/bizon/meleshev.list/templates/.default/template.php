<div class="container">
    <div class="col-md-12">
    <h2 class="text-center">Список магазинов</h2>
    <hr>
    <form action="" method="post">
    <? foreach($arResult['SHOPS'] as $shop): ?>
        <div class="form-group">
            <label for="shop_<?= $shop['ID'] ?>['ID']">Id <?=$shop['ID']?></label>
            <input type="hidden" class="form-control" name="shop_<?= $shop['ID'] ?>['ID']" value="<?=$shop['ID']?>">
        </div>
        <div class="form-group">
            <label for="shop_<?= $shop['ID'] ?>['TITLE']">Title</label>
            <input type="text" readonly class="form-control" name="shop_<?= $shop['ID'] ?>['TITLE']" value="<?=$shop['TITLE']?>">
        </div>
        <div class="form-group">
            <label for="shop_<?= $shop['ID'] ?>['AMOUNT']">Amount</label>
            <input type="number" readonly class="form-control" name="shop_<?= $shop['ID'] ?>['AMOUNT']" value="<?=$shop['AMOUNT']?>">
        </div>
        <div class="form-group">
            <label for="shop_<?= $shop['ID'] ?>['COUNT']">Count</label>
            <input type="number" readonly class="form-control" name="shop_<?= $shop['ID'] ?>['COUNT']" value="<?=$shop['COUNT']?>">
        </div>
        <div class="form-group">
            <label for="shop_<?= $shop['ID'] ?>['COMMENT']">Comment</label>
            <input type="text" readonly class="form-control" name="shop_<?= $shop['ID'] ?>['COMMENT']" value="<?=$shop['COMMENT']?>">
        </div>
        <div class="form-check">
            <input type="checkbox" class="form-check-input" name="shop_<?= $shop['ID'] ?>['isDelete']">
            <label for="shop_<?= $shop['ID'] ?>['isDelete']">Удалить?</label>
        </div>
    <div class="form-group">
        <a class="btn btn-primary" href="edit/<?= $shop['ID']?>/">Редактировать</a>
    </div>
    <hr />
    <? endforeach; ?>
        <button type="submit" class="btn btn-primary">Удалить выбранные магазины</button>
    </form>
        <div class="mt-lg-5">
            <a class="btn btn-primary float-left" href="edit/0/">Создать магазин</a>
        </div>
    </div>

    <br>
</div>