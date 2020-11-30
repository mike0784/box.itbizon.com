<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

use Bitrix\Main\UI;
use Itbizon\Basis\Utils\TaskReport;
use Itbizon\Basis\Utils\WeekDay;

/**
 * Class CITBBasisWorkTimeReport
 */
class CITBBasisWorkTimeReport extends CBitrixComponent
{
    /**
     * @return bool|mixed|null
     */
    public function executeComponent()
    {
        $message = '';
        $messageType = 'alert-danger';
        $startFields = [];
        
        try
        {
            // grid
            $gridId = 'work_time_report';
            
            $weeks = WeekDay::getWeekList();
            foreach ($weeks as $id => $item)
                $weeks[$id] = $id;
            
            //Fields for filter
            $filter = [
                [
                    'id'      => 'TAG',
                    'name'    => 'Тэг',
                    'type'    => 'string',
                    'default' => true,
                ],
                [
                    'id'      => 'TASK_ID',
                    'name'    => 'Номер задачи',
                    'type'    => 'string',
                    'default' => true,
                ],
                [
                    'id'      => 'WEEK_ID',
                    'name'    => 'Номер недели',
                    'type'    => 'list',
                    'default' => true,
                    'items'    => $weeks,
                ],
                [
                    'id'      => 'PERIOD',
                    'name'    => 'Период',
                    'type'    => 'date',
                    'default' => true,
                ],
            ];
            
            // Columns for grid
            $columns = [
                [
                    'id'      => 'NAME',
                    'name'    => 'Проект',
                    'sort'    => 'NAME',
                    'default' => true,
                    'shift'   => true,
                ],
                [
                    'id'      => 'STAGE',
                    'name'    => 'Стадия',
                    'sort'    => 'STAGE',
                    'default' => true,
                ],
                [
                    'id'      => 'WEEK_ID',
                    'name'    => 'Номер недели',
                    'sort'    => 'WEEK_ID',
                    'default' => true,
                ],
                [
                    'id'      => 'DEADLINE',
                    'name'    => 'Крайний срок',
                    'sort'    => 'DEADLINE',
                    'default' => true,
                ],
                [
                    'id'      => 'OVERDUE',
                    'name'    => 'Просрочено (дней)',
                    'sort'    => 'OVERDUE',
                    'default' => true,
                ],
                [
                    'id'      => 'WORK_TIME',
                    'name'    => 'Трудоемкость (ЧЧ:ММ)',
                    'sort'    => 'WORK_TIME',
                    'default' => true,
                ],
            ];
            
            // Converting UI filter to D7 filter
            $filterOption = new UI\Filter\Options($gridId);
            $filterData = $this->FilterUI2D7(
                $filterOption->getFilter([]),
                [
                    'search' => ['TAG'],
                    'simple' => [
                        'TASK_ID',
                        'WEEK_ID',
                    ],
                    'date'   => ['PERIOD'],
                ]
            );
            if(empty($filterList))
            {
                $filterOption->setupDefaultFilter([
                    'PERIOD_datesel'=>'CURRENT_MONTH',
                ], [
                    'PERIOD',
                ]);
            }
            
            //Data for grid
            $rows = [];
            
            $start = $filterData['START'] ? new DateTime($filterData['START']) : new DateTime('01.01.1970 00:00:00');
            $end = $filterData['END'] ? new DateTime($filterData['END']) : new DateTime();
            
            unset($filterData['START']);
            unset($filterData['END']);
            
            $result = [];
            $report = new TaskReport($start, $end, $filterData);
            $root = $report->getRoot();
            foreach ($root->getChildren() as $id => $child)
                $result = $child->getReportData($result);
            
            foreach ($result as $index => $item)
            {
                // Actions
                $actions = [];
                
                // Add data
                $rows[] = [
                    'data'      => [
                        'ID'        => strval($item['ID']),
                        'NAME'      => $item['LINK_NAME'],
                        'STAGE'     => $item['STAGE'],
                        'WEEK_ID'   => $item['WEEK_ID'],
                        'DEADLINE'  => $item['DEADLINE'],
                        'OVERDUE'   => $item['OVERDUE'],
                        'WORK_TIME' => $item['WORK_TIME'],
                    ],
                    'actions'   => $actions,
                    'editable'  => false,
                    'has_child' => $item['HAS_CHILD'] == 1,
                    'parent_id' => strval($item['PARENT_ID']),
                ];
            }
        } catch (Exception $ex)
        {
            $message = $ex->getMessage();
        }
        
        //Result
        $this->arResult = [
            'FOLDER'       => $this->arParams['FOLDER'],
            'MESSAGE'      => $message,
            'MESSAGE_TYPE' => $messageType,
            'START_FIELDS' => $startFields,
            
            'GRID_ID' => $gridId,
            'FILTER'  => $filter,
            'COLUMNS' => $columns,
            'ROWS'    => $rows,
        ];
        
        //Include template
        $this->IncludeComponentTemplate();
        return true;
    }
    
    /**
     * @param array $uiFilter
     * @param array $fields
     * @return array
     */
    function FilterUI2D7(array $uiFilter, array $fields = [])
    {
        $filter = [];
        if (!isset($fields['search']))
            $fields['search'] = [];
        if (!isset($fields['simple']))
            $fields['simple'] = [];
        if (!isset($fields['date']))
            $fields['date'] = [];
        if (!isset($fields['number']))
            $fields['number'] = [];
        if (!isset($fields['list']))
            $fields['list'] = [];
        
        if (isset($uiFilter['FIND']) && !empty($uiFilter['FIND']) && !empty($fields['search']))
        {
            $search_filter = ['LOGIC' => 'OR'];
            foreach ($fields['search'] as $field)
                $search_filter[$field] = '%' . $uiFilter['FIND'] . '%';
            $filter[] = $search_filter;
        }
        else
        {
            foreach ($fields['search'] as $field)
                if (isset($uiFilter[$field]))
                    $filter[$field] = '%' . $uiFilter[$field] . '%';
        }
        //Simple fields
        foreach ($fields['simple'] as $field)
        {
            if (isset($uiFilter[$field]))
                $filter[$field] = $uiFilter[$field];
        }
        //Date fields
        foreach ($fields['date'] as $field)
        {
            if (isset($uiFilter[$field . '_from']) && isset($uiFilter[$field . '_to']))
            {
                $filter['START'] = $uiFilter[$field . '_from'];
                $filter['END'] = $uiFilter[$field . '_to'];
            }
        }
        //Number fields
        foreach ($fields['number'] as $field => $value)
        {
            if (isset($uiFilter[$field . '_numsel']))
            {
                if ($uiFilter[$field . '_numsel'] == 'exact')
                    $filter['=' . $field] = $uiFilter[$field . '_from'] * $value;
                else if ($uiFilter[$field . '_numsel'] == 'range')
                    $filter[] = [
                        '>=' . $field => $uiFilter[$field . '_from'] * $value,
                        '<=' . $field => $uiFilter[$field . '_to'] * $value,
                    ];
                else if ($uiFilter[$field . '_numsel'] == 'more')
                    $filter['>' . $field] = $uiFilter[$field . '_from'] * $value;
                else if ($uiFilter[$field . '_numsel'] == 'less')
                    $filter['<' . $field] = $uiFilter[$field . '_to'] * $value;
            }
        }
        
        // List fields
        foreach ($fields['list'] as $field => $value)
        {
            if (isset($uiFilter[$value]) && is_array($uiFilter[$value]))
            {
                if (count($uiFilter[$value]) > 1)
                {
                    $listFilter = [];
                    $listFilter['LOGIC'] = 'OR';
                    foreach ($uiFilter[$value] as $item)
                        $listFilter[] = ['=' . $value => $item];
                    
                    if (!empty($filter))
                        $filter = [
                            'LOGIC' => 'AND',
                            $filter,
                            $listFilter,
                        ];
                    else
                        $filter = $listFilter;
                }
                else
                {
                    foreach ($uiFilter[$value] as $item)
                        $filter['=' . $value] = $item;
                }
            }
        }
        return $filter;
    }
}