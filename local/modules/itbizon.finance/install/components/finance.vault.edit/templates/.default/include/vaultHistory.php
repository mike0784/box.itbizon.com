<?php

use Bitrix\Main\Localization\Loc;
use Itbizon\Finance\VaultHistory;

Loc::loadMessages(__FILE__);
?>
<table class="table table-sm table-bordered table-striped">
    <thead class="thead-dark text-center">
    <tr>
        <th><?= Loc::getMessage('ITB_FIN.VAULT_EDIT.FIELD.DATE') ?></th>
        <th><?= Loc::getMessage('ITB_FIN.VAULT_EDIT.FIELD.SUM') ?></th>
        <th><?= Loc::getMessage('ITB_FIN.VAULT_EDIT.FIELD.OPERATION') ?></th>
        <th><?= Loc::getMessage('ITB_FIN.VAULT_EDIT.FIELD.COMMENT') ?></th>
    </tr>
    </thead>
    <tbody>
    <? /** @var VaultHistory $records */
    foreach ($records as $indexRecord => $record) : ?>
        <tr>
            <td><?= $record->getDateCreate() ?></td>
            <td><?= $record->getBalancePrint() ?></td>
            <?php if($record->getOperation()) : ?>
                <td><a href="<?= $record->getOperation()->getUrl() ?>"><?= $record->getOperation()->getName() ?></a>
                </td>
            <?php else: ?>
                <td><?= Loc::getMessage('ITB_FIN.VAULT_EDIT.VALUE.OPERATION_UNKNOWN') ?></td>
            <?php endif; ?>
            <td><?= $record->getComment() ?></td>
        </tr>
    <? endforeach; ?>
    </tbody>
</table>