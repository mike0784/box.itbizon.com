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
        <?php foreach ($arResult['FINES'] as $fine) {
            $targetUser = Bitrix\Main\UserTable::getByPrimary($fine['TARGET_ID'])->fetchObject();
            $creatorUser = Bitrix\Main\UserTable::getByPrimary($fine['CREATOR_ID'])->fetchObject();
            ?>
            <tr>
                <th scope="row"><?= $fine['ID'] ?></th>
                <td><?= $fine['TITLE'] ?></td>
                <td><?= $fine['VALUE'] ?></td>
                <td><?= $fine['DATE_CREATE'] ?></td>
                <td><?= $targetUser->getName() ?></td>
                <td><?= $creatorUser->getName() ?></td>
                <td><a href="#">Редактировать</a>
                    <a href="#">Удалить</a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

