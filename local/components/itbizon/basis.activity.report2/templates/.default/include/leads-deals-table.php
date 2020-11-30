<style>
    a {
        text-decoration: underline;
        color: black;
    }
</style>
<div class="table-responsive-md">
    <table class="table text-center">
        <thead class="thead-dark">
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Название</th>
            <th scope="col">Дата последней активности</th>
        </tr>
        </thead>
        <tbody>
        <? foreach ($data as $row) : ?>
            <tr>
                <th scope="row"><?= $row['ID'] ?></th>
                <td><a href="<?= $row['LINK'] ?>" target="_blank"><?= $row['TITLE'] ?></a></td>
                <td><?= (!empty($row['DATE'])) ? $row['DATE'] : '-' ?></td>
            </tr>
        <? endforeach; ?>
        </tbody>
    </table>
</div>