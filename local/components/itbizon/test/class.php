<?php

use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Loader;
use Bitrix\Main\UI\Filter\Options;

use Itbizon\Service\Component\Simple;

if (!Loader::includeModule('itbizon.service'))
{
    throw new Exception('error load module itbizon.service');
}


/**
 * Class CITBServiceNotifySettings
 */
class CITBServiceNotifySettings extends Simple
{
    public $leads;
    public $filter;
    
    /**
     * @return mixed|void|null
     * @throws Exception
     */
    public function executeComponent()
    {
        try {
            $sources = [];
            $source = [];
            $begin = null;
            $end = null;
            
            $list = \Bitrix\Crm\StatusTable::getList([
                'filter'=>[
                    'ENTITY_ID'=>'SOURCE',
                ],
                'select'=>[
                    'STATUS_ID',
                    'NAME',
                ],
                'order'=>['NAME'=>'ASC'],
            ]);
            while($row = $list->fetch())
                $sources[$row['STATUS_ID']] = $row['NAME'];
            
            $this->filter = [
                [
                    'id' => 'SOURCES',
                    'name' => 'Источники',
                    'type' => 'list',
                    'default' => true,
                    'items' => $sources,
                    'params'=>[
                        'multiple'=>true,
                    ]
                ],
                [
                    'id' => 'DATE_CREATE',
                    'name'=>'Дата создания',
                    'type'=>'date',
                    'default'=>true,
                ]
            ];
    
            $uid = CurrentUser::get()->getId();
            $filterId = 'some_unique_filter_id';
            $filterPreset = CUserOptions::GetList([], [
                'USER_ID'=>$uid,
                'NAME'=>$filterId, // FILTER ID
                'CATEGORY'=>'main.ui.filter',
            ])->Fetch();
    
            if($filterPreset) {
                $filter = unserialize($filterPreset['VALUE'])['filters']['tmp_filter']['fields'];
                $period = [];
                $temp = [];
        
                foreach ($filter as $key => $item) {
                    if(strpos($key, 'DATE_CREATE') !== false) {
                        $temp[$key] = $item;
                    }
                }
        
                // SOURCES
                if(!empty($filter['SOURCES'])) {
                    $source = $filter['SOURCES'];
                }
                // DATE_CREATE
                Options::calcDates('DATE_CREATE', $temp, $period);
                if(!empty($period)) {
                    $begin = new DateTime($period['DATE_CREATE_from']);
                    $end = new DateTime($period['DATE_CREATE_to']);
                }
            }
            
            $filter = [];
            
            if($begin) {
                $filter['>=DATE_CREATE'] = $begin->format('d.m.Y H:i:s');
                $filter['<=DATE_CREATE'] = $end->format('d.m.Y H:i:s');
                
                i_show('Поиск за период: <b>'.$begin->format('d.m.Y H:i:s').' - '.$end->format('d.m.Y H:i:s').'</b>');
            }
            else
                i_show('Поиск за всё время');
            
            if($source) {
                $filter['=SOURCE_ID'] = $source;
                $str = '';
                foreach ($source as $sid) {
                    $str .= '<b>'.$sources[$sid].'</b>; ';
                }
                i_show('По источникам: '.$str);
            }
            else
                i_show('По всем источникам');
            
            $this->leads = \Bitrix\Crm\LeadTable::getList([
                'filter'=>$filter,
                'select'=>['ID','TITLE','SOURCE_ID','SOURCE_BY.NAME', 'DATE_CREATE'],
            ])->fetchAll();
    
        } catch (Exception $e) {
            $this->addError($e->getMessage() . ' ' . $e->getTraceAsString());
        }

        //Include template
        $this->IncludeComponentTemplate();
        return true;
    }
    
    /**
     * @return mixed
     */
    public function getFilter() {
        return $this->filter;
    }
    
    /**
     * @return mixed
     */
    public function getLeads() {
        return $this->leads;
    }
}
