<?php

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Uri;
use Itbizon\Finance\Permission;
use Itbizon\Finance\Stock;
use Itbizon\Finance\Utils\Money;
use Itbizon\Service\Component\Simple;

Loc::loadMessages(__FILE__);

if(!Loader::includeModule('itbizon.service')) {
    throw new Exception(Loc::getMessage('Error load module itbizon.service'));
}

/**
 * Class CITBFinanceStockAdd
 */
class CITBFinanceStockAdd extends Simple
{
    /**
     * @return mixed|void|null
     * @throws Exception
     */
    public function executeComponent()
    {
        try {
            if(!Loader::includeModule('itbizon.finance')) {
                throw new Exception(Loc::getMessage('ITB_FIN.STOCK.ADD.ERROR.INCLUDE_FINANCE'));
            }

            $this->setRoute($this->arParams['HELPER']);

            if(!Permission::getInstance()->isAllowStockAdd())
                throw new Exception(Loc::getMessage('ITB_FIN.STOCK.ADD.ERROR.ACCESS_DENY'));

            $request = Application::getInstance()->getContext()->getRequest();
            if($request->getPost('save') === 'Y') {
                $data = $request->getPost('DATA');
                $stock = (new Stock())
                    ->setStockGroupId($data['STOCK_GROUP_ID'])
                    ->setName($data['NAME'])
                    ->setResponsibleId($data['RESPONSIBLE_ID'])
                    ->setBalance(Money::toBase($data['BALANCE']))
                    ->setHideOnPlanning($data['HIDE_ON_PLANNING'])
                    ->setPercent(Money::toBase($data['PERCENT']));

                $result = $stock->save();
                if(!$result->isSuccess()) {
                    throw new Exception(implode('; ', $result->getErrorMessages()));
                }

                $uri = new Uri($this->getRoute()->getUrl('add'));
                if($this->getRoute()->isInSliderMode()) {
                    $uri->addParams(['IFRAME' => 'Y']);
                }
                LocalRedirect($uri->getLocator());
            }
        } catch (Exception $e) {
            $this->addError($e->getMessage());
        }
        // Include template
        $this->IncludeComponentTemplate();
    }
}
