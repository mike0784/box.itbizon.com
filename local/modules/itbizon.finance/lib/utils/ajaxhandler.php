<?php


namespace Itbizon\Finance\Utils;


use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectNotFoundException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Exception;
use Itbizon\Finance\Model\StockTable;
use Itbizon\Finance\Permission;
use Itbizon\Finance\Stock;
use Itbizon\Service\Processor;

/**
 * Class AjaxHandler
 * @package Itbizon\Finance\Utils
 */
class AjaxHandler
{
    /**
     * @param Processor $processor
     * @throws ArgumentException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws Exception
     */
    public static function deleteStock(Processor $processor) {
        $request = Application::getInstance()->getContext()->getRequest();
        $data = $request->getPost('DATA');

        $stockId = intval($data['ID']);
        $stock = StockTable::getById($stockId)->fetchObject();
        if(!$stock)
            throw new Exception(Loc::getMessage('ITB_FIN.AJAX_HANDLER.STOCK.EDIT.ERROR.NOT_FOUND'));

        if(!Permission::getInstance()->isAllowStockDelete($stock))
            throw new Exception(Loc::getMessage('ITB_FIN.AJAX_HANDLER.STOCK.EDIT.ERROR.ACCESS_DENY'));

        $result = $stock->delete();
        if(!$result->isSuccess()) {
            throw new Exception(implode('; ', $result->getErrorMessages()));
        }
        $processor->send(true, 'OK', ['ID' => $stockId]);
    }
}