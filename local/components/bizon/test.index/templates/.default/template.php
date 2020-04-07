<div class="container">
    <table class="table table-dark">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Название</th>
            <th scope="col">Размер</th>
            <th scope="col">Дата создания</th>
            <th scope="col">На кого</th>
            <th scope="col">Кто</th>
            <th scope="col">Действие</th>
        </tr>
        </thead>
        <tbody>
        <? foreach ($arResult['FINES'] as $fine): ?>
            <tr>
                <th scope="row"><?= $fine->getId() ?></th>
                <td><?= $fine->getTitle() ?></td>
                <td><?= $fine->getValue() ?></td>
                <td><?= $fine->getDateCreate() ?></td>
                <td><?= $fine->getCreator()->getName() ?></td>
                <td><?= $fine->getTarget()->getName() ?></td>
                <td><a href="<?= $fine->getId() ?>/edit/">Редактировать</a>
                    <a id="deleteFine" data-path="<?= $arResult['PATH'] . '?ID=' . $fine->getId() ?>"
                       href="#">Удалить</a>
                </td>
            </tr>
        <? endforeach ?>
        </tbody>
    </table>

    <button type="button" class="btn btn-primary" id="showPopup" data-toggle="modal" data-target="#myModal"
            data-path="<?= $arResult['PATH'] ?>">
        создать
    </button>
</div>


