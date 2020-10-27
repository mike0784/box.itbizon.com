<?php

use Bizon\Main\Utils\SimpleXLSX;

try {
    $isWrite = false;
    $xlsx = SimpleXLSX::parse($filePath);
    if (!$xlsx) {
        throw new Exception(SimpleXLSX::parseError());
    }
    $countRealRows = 0;
    $isCount = false;
    foreach ($xlsx->rows() as $row) {
        if (is_numeric(strpos(implode('', $row), '№ п/п'))) {
            $isCount = true;
        }
        if ($isCount) {
            $countRealRows++;
        }
    }
    $countRealRows -= 2;
    ?>
    <div class="col-md-12">
        <input type="hidden" class="archiveName" value="<?= $archiveName ?>">
        <div class="mb-3">
            <a href="#" class="btn btn-danger" id="create__archive" role="button">Генерация документов</a>
        </div>
        <p>Кол-во записей <?= $countRealRows ?></p>
        <div class="table-responsive-xl">
            <table class="table text-center table-sm table-bordered table-striped table-hover">
                <thead class="thead-dark">
                <? foreach ($xlsx->rows() as $row): ?>
                <? if (is_numeric(strpos(implode('', $row), '№ п/п'))):
                $isWrite = true; ?>
                <tr>
                    <th scope="col">
                        <?= $row[0] ?>
                    </th>
                    <th scope="col">
                        <?= $row[1] ?>
                    </th>
                    <th scope="col">
                        <?= $row[2] ?>
                    </th>
                    <th scope="col">
                        <?= $row[3] ?>
                    </th>
                    <th scope="col">
                        <?= $row[4] ?>
                    </th>
                    <th scope="col">
                        <?= $row[6] ?>
                    </th>
                    <th scope="col">
                        <?= $row[8] ?>
                    </th>
                </tr>
                </thead>
                <tbody id="tbody__data">
                <? elseif ($isWrite): ?>
                    <tr>
                        <td>
                            <input type='hidden' name='number' value='<?= $row[0] ?>'>
                            <?= $row[0] ?>
                        </td>
                        <td>
                            <input type='hidden' name='accountNumber' value='<?= $row[1] ?>'>
                            <?= $row[1] ?>
                        </td>
                        <td>
                            <input type='hidden' name='debtorFIO' value='<?= $row[2] ?>'>
                            <?= $row[2] ?>
                        </td>
                        <td>
                            <input type='hidden' name='court' value='<?= $row[3] ?>'>
                            <?= $row[3] ?>
                        </td>
                        <td>
                            <input type='hidden' name='debtorAddress' value='<?= $row[4] ?>'>
                            <?= $row[4] ?>
                        </td>
                        <td>
                            <input type='hidden' name='debtSum' value='<?= $row[6] ?>'>
                            <?= $row[6] ?>
                        </td>
                        <td>
                            <input type='hidden' name='countMonth' value='<?= $row[8] ?>'>
                            <?= $row[8] ?>
                        </td>
                    </tr>
                <? endif; ?>
                <? endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
} catch (\Exception $e) {
    echo "<div class='alert alert-danger'>{$e->getMessage()}</div>";
}
?>