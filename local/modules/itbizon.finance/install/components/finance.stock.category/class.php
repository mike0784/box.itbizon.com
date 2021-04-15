<?php

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Uri;
use Itbizon\Finance\Model\CategoryBindTable;
use Itbizon\Finance\Model\EO_OperationCategory_Collection;
use Itbizon\Finance\Model\EO_Stock_Collection;
use Itbizon\Finance\Model\OperationCategoryTable;
use Itbizon\Finance\Model\StockTable;
use Itbizon\Finance\Permission;
use Itbizon\Service\Component\Simple;

Loc::loadMessages(__FILE__);

if(!Loader::includeModule('itbizon.service')) {
    throw new Exception(Loc::getMessage('Error load module itbizon.service'));
}

/**
 * Class CITBFinanceStockCategory
 */
class CITBFinanceStockCategory extends Simple
{
    protected $stocks;
    protected $categories;
    protected $data;

    /**
     * @return mixed|void|null
     * @throws Exception
     */
    public function executeComponent()
    {
        try {
            if(!Loader::includeModule('itbizon.finance')) {
                throw new Exception(Loc::getMessage('ITB_FIN.STOCK.CATEGORY.ERROR.INCLUDE_FINANCE'));
            }

            $this->setRoute($this->arParams['HELPER']);

            if(!Permission::getInstance()->isAllowStockEdit())
                throw new Exception(Loc::getMessage('ITB_FIN.STOCK.CATEGORY.ERROR.ACCESS_DENY'));

            $this->data = [];
            $this->stocks = StockTable::getList([
                'select' => ['ID', 'NAME'],
                'order' => ['NAME' => 'ASC']
            ])->fetchCollection();
            $this->categories = OperationCategoryTable::getList([
                'select' => ['ID', 'NAME'],
                'filter' => ['ALLOW_OUTGO' => 'Y'],
                'order' => ['NAME' => 'ASC']
            ])->fetchCollection();
            $binds = CategoryBindTable::getList()->fetchCollection();
            foreach($binds as $bind) {
                $this->data[$bind->getCategoryId()] = $bind->getStockId();
            }

            $request = Application::getInstance()->getContext()->getRequest();
            if($request->getPost('save') === 'Y') {
                $data = $request->getPost('DATA');
                if(is_array($data)) {
                    foreach($data as $categoryId => $stockId) {
                        if($stockId) {
                            $result = CategoryBindTable::upsert($categoryId, $stockId);
                            if(!$result->isSuccess()) {
                                throw new Exception(implode(', ', $result->getErrorMessages()));
                            }
                        } else {
                            $result = CategoryBindTable::delete($categoryId);
                            if(!$result->isSuccess()) {
                                throw new Exception(implode(', ', $result->getErrorMessages()));
                            }
                        }
                    }

                    $uri = new Uri($this->getRoute()->getUrl('add'));
                    if($this->getRoute()->isInSliderMode()) {
                        $uri->addParams(['IFRAME' => 'Y']);
                    }
                    LocalRedirect($uri->getLocator());
                }
            }
        } catch (Exception $e) {
            $this->addError($e->getMessage());
        }
        $this->IncludeComponentTemplate();
    }

    /**
     * @return array|null
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    /**
     * @return EO_Stock_Collection|null
     */
    public function getStocks(): ?EO_Stock_Collection
    {
        return $this->stocks;
    }

    /**
     * @return EO_OperationCategory_Collection|null
     */
    public function getCategories(): ?EO_OperationCategory_Collection
    {
        return $this->categories;
    }
}
