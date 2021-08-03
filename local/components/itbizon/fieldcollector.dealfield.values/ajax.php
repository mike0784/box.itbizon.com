<?php

use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Loader;
use Bizon\Main\FieldCollector\Model\DealFieldTable;

/**
 * Class CITBFieldcollectorDealfieldList
 */
class FieldcollectorDealfieldValueListAjaxController extends Controller
{
    /**
     * @param string $id
     * @return bool
     */
    public function deleteItemAction($id = 'id')
    {
        try {
            if(!Loader::includeModule('itbizon.service')) {
                throw new Exception('Ошибка загрузки модуля itbizon.service');
            }

            $id = intval($id);
            $item = DealFieldTable::getByPrimary($id)->fetchObject();
            if(!$item) {
                throw new Exception('Запись не найдена');
            }
            $result = DealFieldTable::delete($id);
            if(!$result->isSuccess()) {
                throw new Exception('Ошибка удаления: '.implode(';', $result->getErrorMessages()));
            }
        } catch (Exception $e) {
            $this->addError(
                new \Bitrix\Main\Error($e->getMessage())
            );
        }
        return true;
    }
}