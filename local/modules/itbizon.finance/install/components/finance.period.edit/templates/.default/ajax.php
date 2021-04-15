<?php

use Bitrix\Main\Application;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Loader;
use Bitrix\Crm\CompanyTable;
use Itbizon\Finance\Helper;
use Itbizon\Finance\Model\OperationCategoryTable;
use Itbizon\Finance\Model\PeriodTable;
use Itbizon\Finance\Model\RequestTable;
use Itbizon\Finance\Model\StockTable;
use Itbizon\Finance\Model\VaultTable;
use Itbizon\Finance\Permission;
use Itbizon\Finance\Utils\Money;

define("NO_KEEP_STATISTIC", "Y");
define("NO_AGENT_STATISTIC","Y");
define("NOT_CHECK_PERMISSIONS", true);
define("DisableEventsCheck", true);
define("NO_AGENT_CHECK", true);

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
header('Content-Type: application/json');

/**
 * @param bool $result
 * @param string $message
 * @param array $data
 * @param int $code
 */
function answer(bool $result, string $message, $data = [], int $code = 200)
{
    http_response_code($code);
    echo json_encode(['result' => $result, 'message' => $message, 'data' => $data]);
    die();
}

try
{
    $request = Application::getInstance()->getContext()->getRequest();
    $action = strval($request->getPost('action'));

    if(!Loader::includeModule('itbizon.finance'))
        throw new Exception('Ошибка подключения модуля itbizon.finance');
    if(!Loader::includeModule('crm'))
        throw new Exception('Ошибка подключения модуля crm');

    if(!Permission::getInstance()->isAllowPeriodEdit())
        throw new Exception('Нет доступа');

    if($action == 'get-form')
    {
        $formId = strval($request->getPost('formId'));

        ob_start();
        if($formId == 'change-form') {
            $requestId = intval($request->getPost('requestId'));

            $request = RequestTable::getById($requestId)->fetchObject();
            if(!$request)
                throw new Exception('Заявка не найдена');

            $vaults = VaultTable::getList([
                'select' => ['*'],
                'order' => ['NAME' => 'ASC'],
                'filter' => [
                    '!=HIDE_ON_PLANNING' => true,
                    '!=TYPE' => [VaultTable::TYPE_VIRTUAL, VaultTable::TYPE_STOCK]
                ]
            ])->fetchCollection();
            $stocks = StockTable::getList([
                'select' => ['*'],
                'order' => ['NAME' => 'ASC'],
                'filter' => [
                    '!=HIDE_ON_PLANNING' => true,
                    'TYPE' => [VaultTable::TYPE_STOCK]
                ]
            ])->fetchCollection();
            $categories = OperationCategoryTable::getList([
                'select' => ['*'],
                'order' => ['NAME' => 'ASC'],
                'filter' => [
                    '=ALLOW_OUTGO' => true
                ]
            ])->fetchCollection();
            $companies = [];
            $list = CompanyTable::getList([
                'select'=>[
                    'ID',
                    'TITLE',
                ],
            ]);
            while($row = $list->fetch()) {
                $companies[$row['ID']] = $row['TITLE'];
            }
            
            ?>
            <form class="<?= $formId ?>">
                <div class="form-group">
                    <label for="name">Название</label>
                    <input id="name" type="text" class="form-control" name="name" value="<?= $request->getName() ?>" required>
                </div>
                <div class="form-group">
                    <label for="categoryId">Категория</label>
                    <select id="categoryId" class="form-control" name="categoryId" required>
                        <option value="">Выберите фонд</option>
                        <? foreach($categories as $category): ?>
                            <option value="<?= $category->getId() ?>" <?= ($category->getId() == $request->getCategoryId()) ? 'selected' : '' ?> >
                                <?= $category->getName() ?>
                            </option>
                        <? endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="object">Объект</label>
                    <select name="object" id="object" class="form-control">
                        <option value="0"></option>
                        <?foreach ($companies as $cid => $name):?>
                            <option value="<?= $cid ?>" <?if($cid == $request->getEntityId()) echo ' selected';?>><?=$name?></option>
                        <?endforeach;?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="vaultId">Кошелек</label>
                    <select id="vaultId" class="form-control" name="vaultId">
                        <option value="">Выберите кошелек</option>
                        <? foreach($vaults as $vault): ?>
                            <option value="<?= $vault->getId() ?>" <?= ($vault->getId() == $request->getVaultId()) ? 'selected' : '' ?> >
                                <?= $vault->getName() ?> ( <?= Money::formatFromBase($vault->getBalance() - $vault->getLockBalance()) ?> / <?= Money::formatFromBase($vault->getBalance()) ?>)
                            </option>
                        <? endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="stockId">Фонд</label>
                    <select id="stockId" class="form-control" name="stockId">
                        <option value="">Выберите фонд</option>
                        <? foreach($stocks as $stock): ?>
                            <option value="<?= $stock->getId() ?>" <?= ($stock->getId() == $request->getStockId()) ? 'selected' : '' ?> >
                                <?= $stock->getName() ?> ( <?= Money::formatFromBase($stock->getBalance() - $stock->getLockBalance()) ?> / <?= Money::formatFromBase($stock->getBalance()) ?>)
                            </option>
                        <? endforeach; ?>
                    </select>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="autoApprove" class="form-check-input" id="autoApproveCheckbox" value="1" checked>
                    <label class="form-check-label" for="autoApproveCheckbox">Автоматически утверждать заявку если достаточно средств</label>
                </div>
                <div class="form-group">
                    <label for="amount">Сумма, руб</label>
                    <input id="amount" type="number" class="form-control" name="amount" min="0.01" max="<?= Money::fromBase($request->getAmount()) ?>" value="<?= Money::fromBase($request->getAmount()) ?>" required>
                </div>
                <div class="form-group">
                    <label for="comment">Комментарии</label>
                    <textarea id="comment" class="form-control" name="comment"><?= $request->getApproverComment() ?></textarea>
                </div>
                <div class="form-group">
                    <label for="comment">Файл</label><br>
                    <? if($request->getFileId() > 0): ?>
                        <a href="<?= $request->getFileUrl() ?>" target="_blank" download>Скачать</a>
                    <? endif; ?>
                </div>
                <input type="hidden" name="requestId" value="<?= $requestId ?>">
            </form>
            <?
        }
        else if($formId == 'decline-form') {
            $requestId = intval($request->getPost('requestId'));
            $comment   = strval($request->getPost('comment'));

            $request = RequestTable::getById($requestId)->fetchObject();
            if(!$request)
                throw new Exception('Заявка не найдена');
            ?>
            <form class="<?= $formId ?>">
                <div class="form-group">
                    <label for="vaultId">Комментарии</label>
                    <textarea id="vaultId" class="form-control" name="comment"><?= $request->getApproverComment() ?></textarea>
                    <input type="hidden" name="requestId" value="<?= $requestId ?>">
                </div>
            </form>
            <?
        } else {
            ?>
            <div class="alert alert-danger">Форма не найдена</div>
            <?php
        }
        $content = ob_get_clean();
        answer(true, 'Успешно', $content);
    }
    else if($action == 'decline')
    {
        $requestId = intval($request->getPost('requestId'));
        $comment   = strval($request->getPost('comment'));
        $request   = RequestTable::getById($requestId)->fetchObject();
        if(!$request)
            throw new Exception('Заявка не найдена');

        $result = $request->decline(CurrentUser::get()->getId(), $comment);
        if(!$result->isSuccess())
            throw new Exception(implode('; ', $result->getErrorMessages()));
        answer(true, 'Успешно');
    }
    else if($action == 'renew')
    {
        $requestId = intval($request->getPost('requestId'));
        $request = RequestTable::getById($requestId)->fetchObject();
        if(!$request)
            throw new Exception('Заявка не найдена');

        $result = $request->renew();
        if(!$result->isSuccess())
            throw new Exception(implode('; ', $result->getErrorMessages()));
        answer(true, 'Успешно');
    }
    else if($action == 'close-period')
    {
        $periodId = intval($request->getPost('periodId'));
        $period = PeriodTable::getById($periodId)->fetchObject();
        if(!$period)
            throw new Exception('Период не найден');

        $result = $period->close(CurrentUser::get()->getId());
        if(!$result->isSuccess())
            throw new Exception(implode('; ', $result->getErrorMessages()));
        answer(true, 'Успешно');
    }
    else if($action == 'change')
    {
        $requestId = intval($request->getPost('requestId'));
        $autoApprove = intval($request->getPost('autoApprove'));
        $amount   = Money::toBase(floatval($request->getPost('amount')));
        $comment = strval($request->getPost('comment'));
        $data = [
            'NAME' => strval($request->getPost('name')),
            'CATEGORY_ID' => intval($request->getPost('categoryId')),
            'VAULT_ID' => intval($request->getPost('vaultId')),
            'STOCK_ID' => intval($request->getPost('stockId')),
            'ENTITY_ID'=> intval($request->getPost('objectId')),
        ];

        $request = RequestTable::getById($requestId)->fetchObject();
        if(!$request)
            throw new Exception('Заявка не найдена');

        $result = $request->change($data);
        if(!$result->isSuccess())
            throw new Exception(implode('; ', $result->getErrorMessages()));

        if($autoApprove) {
            $request->approve(CurrentUser::get()->getId(), $amount, $comment);
        }
        answer(true, 'Успешно');
    }
    else
        throw new Exception('Неизвестная команда');

    answer(false, 'INVALID_PROCESS');
}
catch(Exception $e)
{
    answer(false, $e->getMessage());
}