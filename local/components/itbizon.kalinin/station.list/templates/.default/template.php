<div class="container">
    <div class="col-md-12">
        <h2 class="text-center">Список станций</h2>
        <hr>
        <table class="table">
            <thead class="thead-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Amount</th>
                <th scope="col">Count</th>
                <th scope="col">Comment</th>
                <th scope="col"></th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <? foreach ($arResult['stations'] as $station): ?>
            <tr>
                <th scope="row"><?=$station['ID']?></th>
                <td><?=$station['NAME']?></td>
                <td><?=$station['AMOUNT']?></td>
                <td><?=$station['COUNT']?></td>
                <td><?=$station['COMMENT']?></td>
                <td><a class="btn btn-primary" href="edit/<?= $station['ID']?>/">Редактировать</a></td>
                <td><a class="btn btn-secondary" href="/delete">Удалить?</a></td>
            </tr>
            <? endforeach; ?>
            </tbody>
        </table>
        <hr>
        <div class="mt-lg-5">
            <a class="btn btn-primary float-left" href="edit/0/">Создать станцию</a>
        </div>
    </div>
</div>
