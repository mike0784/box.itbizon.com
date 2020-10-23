<div class="container">
    <div class="col-md-12">
        <h2 class="text-center">Список станций</h2>
        <hr>
        <table class="table table-bordered">
            <thead class="table-dark">
            <tr>
                <th><input type="checkbox" id="selectAll"></th>
                <th scope="col">#</th>
                <th scope="col">Название</th>
                <th scope="col">Общая стоимость кораблей</th>
                <th scope="col">Количество кораблей</th>
                <th scope="col">Комментарий</th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <? foreach ($arResult['stations'] as $station): ?>
            <tr>
                <td><input class="select-remove" type="checkbox" id="<?= $station['ID']; ?>"></td>
                <th scope="row"><?=$station['ID']?></th>
                <td><?=$station['NAME']?></td>
                <td><?= intval($station['AMOUNT']) / 100 ?></td>
                <td><?=$station['COUNT']?></td>
                <td><?=$station['COMMENT']?></td>
                <td><a class="btn btn-secondary" href="edit/<?= $station['ID']?>/">Редактировать</a></td>
            </tr>
            <? endforeach; ?>
            </tbody>
        </table>
        <button id="remove" type="button" class="btn btn-danger" data-path="<?= $arResult['path'] . "?remove"; ?>" disabled>Удалить выбраные</button>
        <hr>
        <div class="mt-lg-5">
            <a class="btn btn-primary float-left" href="edit/0/">Создать станцию</a>
        </div>
    </div>
</div>
