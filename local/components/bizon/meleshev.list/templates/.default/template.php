<div class="container">
    <div class="col-md-12">
    <div class="col-md-8 text-center">
        <span class="col-md-1">ID</span>
        <span class="col-md-1">Title</span>
        <span class="col-md-1">Amount</span>
        <span class="col-md-1">Count</span>
        <span class="col-md-2">Comment</span>
    </div>
    <hr>
    <? foreach($arResult['SHOPS'] as $shop): ?>
        <div class="col-md-8 text-center">
            <span class="col-md-1"><?= $shop['ID']?></span>
            <span class="col-md-1"><?= $shop['TITLE']?></span>
            <span class="col-md-1"><?= $shop['AMOUNT']?></span>
            <span class="col-md-1"><?= $shop['COUNT']?></span>
            <span class="col-md-2"><?= $shop['COMMENT']?></span>
            <span class="col-md-1">
                <a href="edit/<?= $shop['ID']?>/">
                    Edit
                </a>
            </span>

        </div>
    <? endforeach; ?>
    </div>
    <hr />
    <a class="btn btn-primary float-left" href="edit/0/">Добавить</a>
    <a class="btn btn-primary float-left" href="<?= $arResult['DELETE'] ?>">Удалить</a>
    <br>
</div>