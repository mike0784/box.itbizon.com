<?php

use Itbizon\Finance\Model\OperationActionTable;
use Itbizon\Finance\OperationAction;

/** @var OperationAction $record */
/** @var OperationActionTable $records */
?>
<table class="table table-sm table-bordered table-striped">
    <thead class="thead-dark text-center">
    <tr>
        <th>Дата</th>
        <th>Пользователь</th>
        <th>Тип</th>
    </tr>
    </thead>
    <tbody>
    <?php while ($record = $records->fetchObject()) : ?>
        <tr>
            <td><?= $record->getDateCreate() ?></td>
            <td><a href="/company/personal/user/<?= $record->getUser()->getId() ?>/">
                    <?= $record->getUser()->getName() . " " . $record->getUser()->getLastName() ?>
                </a>
            </td>
            <td><?= OperationActionTable::getTypes($record->getType()) ?></td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>