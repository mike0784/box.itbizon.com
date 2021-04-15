<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
?>
<table class="table table-sm table-striped">
    <thead class="thead-dark">
    <tr>
        <th class="pl-3">#</th>
        <th><?= Loc::getMessage("ITB_FIN.VAULT_EDIT.ACCESS_RIGHTS.TYPE") ?></th>
        <th><?= Loc::getMessage("ITB_FIN.VAULT_EDIT.ACCESS_RIGHTS.USER") ?></th>
        <th><?= Loc::getMessage("ITB_FIN.VAULT_EDIT.ACCESS_RIGHTS.ACTION") ?></th>
    </tr>
    </thead>
    <tbody>
    <? /** @var array $arrAccessRights */ ?>
    <?php foreach ($arrAccessRights as $indexAccessRight => $accessRight) : ?>
        <tr>
            <td>
                <button data-id="<?= $indexAccessRight ?>" class="btn btn-danger btn-sm remove-access">-</button>
            </td>
            <td><?= $accessRight['USER_TYPE'] ?></td>
            <td><?= $accessRight['USER_ID'] ?></td>
            <td><?= $accessRight['ACTION'] ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

