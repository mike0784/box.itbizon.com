<?php

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Uri;
use Itbizon\Finance\Model\StockTable;
use Itbizon\Finance\Permission;
use Itbizon\Finance\Stock;
use Itbizon\Finance\Utils\Money;
use Itbizon\Service\Component\Helper;
use Itbizon\Service\Component\Simple;

Loc::loadMessages(__FILE__);

if(!Loader::includeModule('itbizon.service')) {
    throw new Exception(Loc::getMessage('Error load module itbizon.service'));
}

/**
 * Class CITBFinanceStockEdit
 */
class CITBFinanceStockEdit extends Simple
{
    protected $stock;

    /**
     * @return mixed|void|null
     * @throws Exception
     */
    public function executeComponent()
    {
        try {
            if(!Loader::includeModule('itbizon.finance')) {
                throw new Exception(Loc::getMessage('ITB_FIN.STOCK.EDIT.ERROR.INCLUDE_FINANCE'));
            }

            $this->setRoute($this->arParams['HELPER']);

            $stockId = intval($this->getRoute()->getVariable('ID'));
            if($stockId < 0) {
                $stock = Stock::createVirtualStockById($stockId);
            } else {
                $stock = StockTable::getById($stockId)->fetchObject();
            }
            if(!$stock) {
                throw new Exception(Loc::getMessage('ITB_FIN.STOCK.EDIT.ERROR.NOT_FOUND'));
            }
            if(!Permission::getInstance()->isAllowStockEdit($stock))
                throw new Exception(Loc::getMessage('ITB_FIN.STOCK.EDIT.ERROR.ACCESS_DENY'));

            $this->stock = $stock;

            $request = Application::getInstance()->getContext()->getRequest();
            if($request->getPost('save') === 'Y') {
                $data = $request->getPost('DATA');

                if($stock->isVirtualStock()) {
                    $stock->setVirtualPercent(Money::toBase($data['PERCENT']));
                } else {
                    $stock->setName($data['NAME'])
                        ->setStockGroupId($data['STOCK_GROUP_ID'])
                        ->setPercent(Money::toBase($data['PERCENT']))
                        ->setResponsibleId($data['RESPONSIBLE_ID'])
                        ->setHideOnPlanning($data['HIDE_ON_PLANNING']);
                    $result = $stock->save();
                    if(!$result->isSuccess()) {
                        throw new Exception(implode('; ', $result->getErrorMessages()));
                    }
                }

                $uri = new Uri($this->getRoute()->getUrl('edit', ['ID' => $stock->getId()]));
                if($this->getRoute()->isInSliderMode()) {
                    $uri->addParams(['IFRAME' => 'Y']);
                }
                LocalRedirect($uri->getLocator());
            }
        } catch (Exception $e) {
            $this->setError($e->getMessage());
        }
        // Include template
        $this->IncludeComponentTemplate();
    }

    /**
     * @return mixed
     */
    public function getStock(): ?Stock
    {
        return $this->stock;
    }
}
