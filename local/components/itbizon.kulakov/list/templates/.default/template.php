<div class="container">
    <h2>Список накладных</h2>

    <a href="edit/0/" type="button" class="btn btn-success my-2">Добавить</a>
    <button id="remove" type="button" class="btn btn-danger my-2" data-path="<?= $arResult['path'] . "?remove"; ?>" disabled>Удалить</button>

    <table class="table invoice-table">
        <thead>
            <tr>
                <th><input type="checkbox" id="selectAll"></th>
                <th>ID</th>
                <th>Название</th>
                <th>Дата создания</th>
                <th>Автор</th>
                <th>Сумма</th>
                <th>Комментарий</th>
<!--                <th><i class="fa fa-cog"></i></th>-->
            </tr>
        </thead>
        <tbody>
            <? foreach($arResult['invoices'] as $invoice): ?>
                <tr>
                    <td><input class="select-remove" type="checkbox" id="<?= $invoice['invoice']->get("ID"); ?>"></td>
                    <td><?= $invoice['invoice']->get("ID"); ?></td>
                    <td><a type="button" href="edit/<?= $invoice['invoice']->get("ID"); ?>/"><?= $invoice['invoice']->get("TITLE"); ?></a></td>
                    <td><?= $invoice['invoice']->get("DATE_CREATE"); ?></td>
                    <td><?= $arResult['users'][$invoice['invoice']->get("CREATOR_ID")] ?></td>
                    <td><?= $invoice['invoice']->get("AMOUNT"); ?></td>
                    <td><?= $invoice['invoice']->get("COMMENT"); ?></td>
                </tr>
            <? endforeach; ?>
        </tbody>
    </table>
</div>
<style>
    .invoice-table th,
    .invoice-table td {
        text-align: center;
    }
</style>