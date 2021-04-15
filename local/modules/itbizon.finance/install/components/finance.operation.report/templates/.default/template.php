<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;
use Itbizon\Finance\Utils\Money;


if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);
Extension::load('itbizon.finance.bootstrap4');

/**@var CBitrixComponentTemplate $this * */
/**@var CITBFinanceOperationReport $component * */
$component = $this->getComponent();
$periods = $component->getPeriods();
$categories = $component->getCategories();
$data = $component->getData();

function getCellClass(int $value) {
    $class = 'text-center table-warning';
    if($value < 0) {
        $class = 'text-center table-danger';
    } else if($value > 0) {
        $class = 'text-center table-success';
    }
    return $class;
}
?>
<?php if ($component->getError()): ?>
    <div class="alert alert-danger"><?= $component->getError() ?></div>
<?php endif; ?>
<div class="container-fluid">
    <form class="p-1" method="post">
        <div class="row">
            <div class="col">
                <input class="form-control" type="text" name="from"
                       value="<?= $component->getFrom()->format('d.m.Y') ?>"
                       onclick="BX.calendar({node: this, field: this, bTime: false});">
            </div>
            <div class="col">
                <input class="form-control" type="text" name="to"
                       value="<?= $component->getTo()->format('d.m.Y') ?>"
                       onclick="BX.calendar({node: this, field: this, bTime: false});">
            </div>
            <div class="col">
                <button class="btn btn-primary" type="submit">Показать</button>
            </div>
        </div>
    </form>
    <? if($categories && $periods): ?>
        <table class="table table-sm table-striped table-bordered">
            <thead class="thead-dark">
            <tr>
                <th class="text-right">Категория</th>
                <? foreach($periods as $period): ?>
                    <th class="text-center"><?= $period ?></th>
                <? endforeach; ?>
                <th class="text-center">Итого</th>
            </tr>
            </thead>
            <tbody>
            <? $totalPeriod = []; $total = 0; ?>

            <tr><th colspan="<?=count($periods)+2?>">Приход</th></tr>
            <? foreach($categories['INCOME'] as $category): /**@var $category \Itbizon\Finance\OperationCategory*/ ?>
                <tr>
                    <td class="text-right"><?= $category->getName() ?></td>
                    <? $totalCategory = 0; ?>
                    <? foreach($periods as $periodId => $period): ?>
                        <?
                        $categoryId = $category->getId();
                        $value = (isset($data[$categoryId][$periodId])) ? intval($data[$categoryId][$periodId]['balance']) : 0;
                        $totalCategory += $value;
                        $totalPeriod[$periodId] += $value;
                        $total += $value;
                        ?>
                        <td class="<?= getCellClass($value) ?>"><?= Money::formatFromBase($value) ?></td>
                    <? endforeach; ?>
                    <td class="<?= getCellClass($totalCategory) ?>"><?= Money::formatFromBase($totalCategory) ?></td>
                </tr>
            <? endforeach; ?>
            <tr><th colspan="<?=count($periods)+2?>">Расход</th></tr>
            <? foreach($categories['OUTGO'] as $category): /**@var $category \Itbizon\Finance\OperationCategory*/ ?>
                <tr>
                    <td class="text-right"><?= $category->getName() ?></td>
                    <? $totalCategory = 0; ?>
                    <? foreach($periods as $periodId => $period): ?>
                        <?
                        $categoryId = $category->getId();
                        $value = (isset($data[$categoryId][$periodId])) ? intval($data[$categoryId][$periodId]['balance']) : 0;
                        $totalCategory += $value;
                        $totalPeriod[$periodId] += $value;
                        $total += $value;
                        ?>
                        <td class="<?= getCellClass($value) ?>"><?= Money::formatFromBase($value) ?></td>
                    <? endforeach; ?>
                    <td class="<?= getCellClass($totalCategory) ?>"><?= Money::formatFromBase($totalCategory) ?></td>
                </tr>
            <? endforeach; ?>
            <tr><th colspan="<?=count($periods)+2?>">Другое</th></tr>
            <? foreach($categories['OTHER'] as $category): /**@var $category \Itbizon\Finance\OperationCategory*/ ?>
                <tr>
                    <td class="text-right"><?= $category->getName() ?></td>
                    <? $totalCategory = 0; ?>
                    <? foreach($periods as $periodId => $period): ?>
                        <?
                        $categoryId = $category->getId();
                        $value = (isset($data[$categoryId][$periodId])) ? intval($data[$categoryId][$periodId]['balance']) : 0;
                        $totalCategory += $value;
                        $totalPeriod[$periodId] += $value;
                        $total += $value;
                        ?>
                        <td class="<?= getCellClass($value) ?>"><?= Money::formatFromBase($value) ?></td>
                    <? endforeach; ?>
                    <td class="<?= getCellClass($totalCategory) ?>"><?= Money::formatFromBase($totalCategory) ?></td>
                </tr>
            <? endforeach; ?>
            </tbody>
            <tfoot>
            <tr>
                <th class="text-right">Итого</th>
                <? foreach($periods as $periodId => $period): ?>
                    <th class="<?= getCellClass($totalPeriod[$periodId]) ?>"><?= Money::formatFromBase($totalPeriod[$periodId]) ?></th>
                <? endforeach; ?>
                <th class="<?= getCellClass($total) ?>"><?= Money::formatFromBase($total) ?></th>
            </tr>
            </tfoot>
        </table>
    <? endif; ?>
</div>