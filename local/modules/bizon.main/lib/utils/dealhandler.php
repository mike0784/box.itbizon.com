<?php

namespace Bizon\Main\Utils;

use \Bitrix\Main\Loader;
//use \Bitrix\Main\Engine\CurrentUser;
use \Bitrix\Crm\DealTable;
use Bizon\Main\FieldCollector\Model\DealFieldTable;
use Bizon\Main\FieldCollector\Model\DealFieldValueTable;
//use Bizon\Main\FieldCollector\DealField;
//use \CCrmDeal;
use Exception;

use Itbizon\Service\Log;

if(!Loader::includeModule('itbizon.service')) {
    throw new Exception('Ошибка подключения модуля itbizon.service');
}


class DealHandler
{
    
    public static function onAfterCrmDealUpdate($arFields)
    {
        if(Loader::includeModule('bizon.main')
            && Loader::includeModule('crm')
            && Loader::includeModule('itbizon.service')
        ) {
            try
            {

                $log = new Log('deal_status_change'); // fixme
                $log->Add(print_r($arFields, True)); // fixme


                if(!isset($arFields['ID']))
                    throw new Exception("Отсутствует ID в списке параметров");
                $id = $arFields['ID']; // deal ID

                //
                $res = DealTable::getById($id);
                if (!$res)
                    throw new Exception("Запись не найдена ($id)");
                $deal = $res->fetchObject();

                $catId = $deal->getCategoryId();

                // get history fields list
                $res = DealFieldTable::getList([
                    'select' => ['*'],
                    'filter' => ['=CATEGORY_ID' => $catId],
                ]);
                $historyFields = [];
                while ($item = $res->fetchObject()){
                    $historyFields[$item->getFieldId()] = $item->getId();
                }

                if (count($historyFields)){
                    foreach($arFields as $field => $value){
                        if (array_key_exists($field, $historyFields)){
                            $data = [
                                'DEAL_ID'=>$id,
                                'FIELD_ID'=>$field,
                                'VALUE'=>$value
                            ];
                            DealFieldValueTable::add($data);
                        }
                    }
                }


                /*
                if(isset($arFields['STATUS_ID'])) {
                    $log = new Log('deal_status_change');
        
                    //$isAdmin = CurrentUser::get()->isAdmin();

                    $log->Add('Обновление # '.$arFields['ID'].' старый статус: '.$arFields['OLD_STATUS'].' новый статус: '.$arFields['STATUS_ID']);
                }
                // */



            }
            catch(Exception $ex)
            {
                $log = new Log('deal_event_error');
                $log->Add('onAfterUpdate error deal # '.$arFields['ID']);
                $log->Add($ex->getMessage());
                $log->Add($ex->getMessage().' - '. $ex->getTraceAsString()); // fixme
            }
        }
        
        return true;
    }
    
}
