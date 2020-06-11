<? if ($dataTable): ?>
    <tr>
        <td colspan="3">
            <input type="hidden" class="archiveName" value="<?= $fileName ?>">
        </td>
    </tr>
    <? foreach ($dataTable as $data): ?>
        <tr>
            <th scope="row">
                <?= $data['ID'] ?>
            </th>

            <td>
                <input type="hidden" class="name" value="<?= $data['NAME'] ?>">
                <?= $data['NAME'] ?>
            </td>

            <td>
                <input type="hidden" class="link" value="<?= $data['LINK'] ?>">
                <a href="<?= $data['LINK'] ?>">
                    Ссылка
                </a>
            </td>
        </tr>
    <? endforeach ?>
<? else: ?>
    <tr>
        <td colspan="4" style="color: red">
            Данные не прошли проверку
        </td>
    </tr>
<? endif; ?>
