<?php

use Bitrix\Crm\ProductRowTable;


class CITBCRKReportProductList extends CBitrixComponent
{
    public function executeComponent()
    {
        try {
            if (!\Bitrix\Main\Loader::includeModule('crm')) {
                throw new Exception('Error load module crm');
            }

            $gridId = 'itb_crk_report_product_2';
            $gridOptions = new \Bitrix\Main\Grid\Options($gridId);
            $filterOptions = new \Bitrix\Main\UI\Filter\Options($gridId);
            $navigation = new \Bitrix\Main\UI\PageNavigation($gridId);
            $navParams = $gridOptions->GetNavParams();
            $navigation->allowAllRecords(true)
                ->setPageSize($navParams['nPageSize'])
                ->initFromUri();

            $columns = [
                ['id' => 'ID', 'name' => 'ID', 'sort' => false, 'default' => false],
                ['id' => 'TITLE', 'name' => 'Название', 'sort' => false, 'default' => true],
                ['id' => 'QUANTITY', 'name' => 'Кол-во', 'sort' => false, 'default' => true],
            ];
            $filterFields = [
                [
                    'id' => 'DEAL_OWNER.DATE_CREATE',
                    'name' => 'Дата создания',
                    'type' => 'date',
                    'default' => true,
                ],
                [
                    'id' => 'DEAL_OWNER.UF_CRM_1606473009',
                    'name' => 'Дата оплаты',
                    'type' => 'date',
                    'default' => true,
                ],
            ];
            if(!$filterOptions->getFilter([])) {
                $filterOptions->setupDefaultFilter(
                    [
                        'DEAL_OWNER.DATE_CREATE_datesel' => \Bitrix\Main\UI\Filter\DateType::CURRENT_MONTH,
                    ],
                    ['DEAL_OWNER.DATE_CREATE']
                );
            }
            $rawFilter = $filterOptions->getFilter($filterFields);
            echo '<pre>' . print_r($rawFilter, true) . '</pre>';

            $filterData = $filterOptions->getFilterLogic($filterFields);
            echo '<pre>' . print_r($filterData, true) . '</pre>';

            $filterData['=OWNER_TYPE'] = \CCrmOwnerTypeAbbr::Deal;

            $rows = [];
            $result = ProductRowTable::getList([
                'select' => [
                    'ID', 'PRODUCT_ID', 'CP_PRODUCT_NAME', 'QUANTITY', 'PRICE',
                ],
                'filter' => $filterData
            ]);
            while ($row = $result->fetch()) {
                $rows[] = [
                    'data' => [
                        'ID' => $row['ID'],
                        'TITLE' => $row['CP_PRODUCT_NAME'],
                        'QUANTITY' => $row['QUANTITY'],
                    ],
                ];
            }
            $this->arResult = [
                'gridId' => $gridId,
                'columns' => $columns,
                'filter' => $filterFields,
                'rows' => $rows,
                'navigation' => $navigation,
            ];
        } catch (Exception $e) {
            echo '<p class="alert alert-danger">' . $e->getMessage() . '</p>';
        }
	    $this->IncludeComponentTemplate();
    }
}