<div class="container">
    <div class="col-md-8">
        <ul class="list-group">
            <li class="list-group-item">TITLE: <?= $arResult['FINE']->getTitle() ?></li>
            <li class="list-group-item">VALUE: <?= $arResult['FINE']->getValue() ?></li>
            <li class="list-group-item">TARGET: <?= $arResult['FINE']->getTarget()->getName() ?></li>
            <li class="list-group-item">CREATOR: <?= $arResult['FINE']->getCreator()->getName() ?></li>
            <li class="list-group-item">COMMENT: <?= $arResult['FINE']->getComment() ?></li>
        </ul>
        <br>
        <button type="button" class="btn btn-primary" id="showPopup"
                data-path="<?= $arResult['PATH'] . '?ID=' . $arResult['FINE']->getId() ?>">
            Редактировать
        </button>
        <a href="/local/test" class="btn btn-primary float-right">Назад</a>
    </div>
</div>