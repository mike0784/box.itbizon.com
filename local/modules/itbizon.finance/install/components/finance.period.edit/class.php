<?php

use Bitrix\Main\Application;
use Bitrix\Main\Grid;
use Bitrix\Main\Grid\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Filter\Options;
use Bitrix\Main\UI\PageNavigation;
use Itbizon\Finance\Model\PeriodTable;
use Itbizon\Finance\Model\RequestTable;
use Itbizon\Finance\Model\StockTable;
use Itbizon\Finance\Model\VaultTable;
use ItBizon\Finance\Period;
use Itbizon\Finance\Permission;
use Itbizon\Finance\Request;
use Itbizon\Finance\Utils\Money;
use Itbizon\Service\Component\GridHelper;
use Itbizon\Service\Component\Simple;

Loc::loadMessages(__FILE__);

if(!Loader::includeModule('itbizon.service')) {
    throw new Exception(Loc::getMessage('Error load module itbizon.service'));
}

/**
 * Class CITBFinancePeriodEdit
 */
class CITBFinancePeriodEdit extends Simple
{
    protected $period;

    /**
     * @return bool|mixed
     */
    public function executeComponent()
    {
        try {
            if (!Loader::includeModule('itbizon.finance'))
                throw new Exception(Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.EDIT.ERROR.INCLUDE_FINANCE'));

            $this->setRoute($this->arParams['HELPER']);

            if(!Permission::getInstance()->isAllowPeriodEdit()) {
                throw new Exception(Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.EDIT.ERROR.ACCESS_DENY'));
            }

            $id = intval($this->getRoute()->getVariable('ID'));
            $this->period = PeriodTable::getById($id)->fetchObject();
            if(!$this->period) {
                throw new Exception(Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.EDIT.ERROR.PERIOD_NOT_FOUNT'));
            }

            $grid = new GridHelper('itb_finance_period_edit', 'itb_finance_period_edit');
            $grid->setFilter([
                [
                    'id'      => 'ID',
                    'name'    => Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.EDIT.FIELDS.ID'),
                    'type'    => 'number',
                    'default' => true
                ],
                [
                    'id'     => 'STATUS',
                    'name'   => Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.EDIT.FIELDS.STATUS'),
                    'type'   => 'list',
                    'items'  => RequestTable::getStatuses(),
                    'params' => [
                        'multiple' => 'Y'
                    ],
                    'default' => true
                ],
            ])
                ->setColumns([
                    ['id' => 'ID',            'name' => Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.EDIT.FIELDS.ID'),            'sort' => 'ID',            'default' => false],
                    ['id' => 'NAME',          'name' => Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.EDIT.FIELDS.NAME'),          'sort' => 'NAME',          'default' => true, 'shift' => true],
                    ['id' => 'STATUS',        'name' => Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.EDIT.FIELDS.STATUS'),        'sort' => 'STATUS',        'default' => true],
                    ['id' => 'DATE_CREATE',   'name' => Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.EDIT.FIELDS.DATE_CREATE'),   'sort' => 'DATE_CREATE',   'default' => true],
                    ['id' => 'AUTHOR',        'name' => Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.EDIT.FIELDS.AUTHOR'),        'sort' => 'AUTHOR.NAME',   'default' => true],
                    ['id' => 'CATEGORY',      'name' => Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.EDIT.FIELDS.CATEGORY'),      'sort' => 'CATEGORY.NAME', 'default' => true],
                    ['id' => 'FILE',          'name' => Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.EDIT.FIELDS.FILE'),          'sort' => 'FILE_ID',       'default' => true],
                    ['id' => 'COMMENT',       'name' => Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.EDIT.FIELDS.COMMENT'),       'sort' => false,           'default' => true],
                    ['id' => 'ENTITY',        'name' => Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.EDIT.FIELDS.ENTITY'),        'sort' => false,           'default' => true],
                    ['id' => 'AMOUNT',        'name' => Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.EDIT.FIELDS.AMOUNT'),        'sort' => 'AMOUNT',        'default' => true],
                    ['id' => 'BALANCE',       'name' => Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.EDIT.FIELDS.BALANCE'),       'sort' => false,           'default' => true],
                    ['id' => 'LOCK_BALANCE',  'name' => Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.EDIT.FIELDS.LOCK_BALANCE'),  'sort' => false,           'default' => true],
                    ['id' => 'ALLOW_BALANCE', 'name' => Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.EDIT.FIELDS.ALLOW_BALANCE'), 'sort' => false,           'default' => true],
                ]);
            $filterData = [
                $grid->getFilterData(),
                '>=DATE_CREATE' => $this->period->getDateStart()->format('d.m.Y H:i:s'),
                '<=DATE_CREATE' => $this->period->getDateEnd()->format('d.m.Y H:i:s'),
                '!=VAULT.HIDE_ON_PLANNING' => true,
            ];
            $requests = RequestTable::getList(
                [
                    'select' => [
                        '*',
                        'AUTHOR.NAME',
                        'AUTHOR.LAST_NAME',
                        'CATEGORY.NAME',
                        'APPROVER.NAME',
                        'APPROVER.LAST_NAME',
                        'COMPANY.TITLE'
                    ],
                    'order'  => $grid->getSort(),
                    'filter' => $filterData,
                ]
            );
            $rows = [];
            $totalAmount = 0;
            while ($request = $requests->fetchObject()) {
                $actions = [];
                if($request->isAllowChange()) {
                    $actions[] = [
                        'text'    => Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.EDIT.ACTION.CHANGE'),
                        'default' => true,
                        'onclick' => 'showChangePopup("'.$this->getAjaxPath().'", "'.$grid->getGridId().'", '.$request->getId().');',
                    ];
                }
                if($request->isAllowRenew()) {
                    $actions[] = [
                        'text'    => Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.EDIT.ACTION.RENEW'),
                        'default' => false,
                        'onclick' => 'renewRequest("'.$this->getAjaxPath().'", "'.$grid->getGridId().'", '.$request->getId().');',
                    ];
                }
                if($request->isAllowDecline()) {
                    $actions[] = [
                        'text'    => Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.EDIT.ACTION.DECLINE'),
                        'default' => false,
                        'onclick' => 'showDeclinePopup("'.$this->getAjaxPath().'", "'.$grid->getGridId().'", '.$request->getId().');',
                    ];
                }
                $rows[] = [
                    'data' => [
                        'ID'          => strval($request->getId()),
                        'NAME'        => $request->getName(),
                        'STATUS'      => $this->getStatusHtml($request),
                        'DATE_CREATE' => $request->getDateCreate()->format('d.m.Y'),
                        'AUTHOR'      => '<a href="/company/personal/user/'.$request->getAuthorId().'/">'.$request->getAuthor()->getName().' '.$request->getAuthor()->getLastName().'</a>',
                        'CATEGORY'    => ($request->getCategory()) ? $request->getCategory()->getName() : '-',
                        'FILE'        => ($request->getFileId() > 0) ? '<a href="'.$request->getFileUrl().'" target="_blank" download>Скачать</a>' : '',
                        'COMMENT'     => '<b>'.Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.EDIT.FIELDS.COMMENT_SITUATION').': </b>'.$request->getCommentSituation().
                            '<br><b>'.Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.EDIT.FIELDS.COMMENT_DATA').': </b>'.$request->getCommentData(),
                        'ENTITY'      => ($request->getEntityType() == 4 && $request->getCompany()) ? '<a href="/crm/company/details/'.$request->getEntityId().'/">'.$request->getCompany()->getTitle().'</a>' : '',
                        'AMOUNT'      => Money::formatfromBase($request->getAmount()),
                    ],
                    'actions'   => $actions,
                ];

                //Collect stock data
                /*$stockId = ($request->getStockId()) ? 'V'.$request->getStockId() : 'V0';
                if(!isset($stockData[$stockId])) {
                    $stockData[$stockId] = ['count' => 0, 'amount' => 0, 'lock' => 0];
                }
                $stockData[$stockId]['count']++;
                $stockData[$stockId]['amount'] += $request->getAmount();*/
                $totalAmount += $request->getAmount();
            }

            //Total row
            $tree = StockTable::getTree();
            $totalBalance = $tree->getBalance(true);
            $totalLock = $tree->getLockBalance(true);
            $totalRow = [
                'ID'            => 'T',
                'NAME'          => Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.EDIT.TOTAL'),
                'AMOUNT'        => $totalAmount,
                'BALANCE'       => $totalBalance,
                'LOCK_BALANCE'  => $totalLock,
                'ALLOW_BALANCE' => ($totalBalance - $totalLock),
            ];

            $grid->setRows(array_merge([
                [
                    'data' => [
                        'ID'            => $totalRow['ID'],
                        'NAME'          => '<b>'.$totalRow['NAME'].'</b>',
                        'AMOUNT'        => Money::formatFromBase($totalRow['AMOUNT']),
                        'BALANCE'       => $this->getMoneyHtml($totalRow['BALANCE']),
                        'LOCK_BALANCE'  => $this->getMoneyHtml($totalRow['LOCK_BALANCE'], false),
                        'ALLOW_BALANCE' => $this->getMoneyHtml($totalRow['ALLOW_BALANCE']),
                    ],
                ]
            ], $rows));
            $grid->getNavigation()->setRecordCount(RequestTable::getCount($filterData));
            $this->setGrid($grid);
        } catch (Exception $e) {
            $this->addError($e->getMessage());
        }
        $this->IncludeComponentTemplate();
        return true;
    }

    /**
     * @return mixed
     */
    public function getPeriod(): ?Period
    {
        return $this->period;
    }

    /**
     * @param Request $request
     * @return string
     */
    public function getStatusHtml(Request $request): string
    {
        static $classes = [
            RequestTable::STATUS_NEW     => 'primary',
            RequestTable::STATUS_APPROVE => 'info',
            RequestTable::STATUS_DECLINE => 'danger',
            RequestTable::STATUS_CONFIRM => 'success',
            RequestTable::STATUS_CANCEL  => 'secondary',
            RequestTable::STATUS_FIX     => 'light',
            RequestTable::STATUS_ERROR   => 'dark',
        ];
        $class = isset($classes[$request->getStatus()]) ? $classes[$request->getStatus()] : 'secondary';
        return '<h6><span class="badge badge-'.$class.'">'.$request->getStatusName().'</span></h6>';
    }

    /**
     * @param int $value
     * @param bool $color
     * @return string
     */
    public function getMoneyHtml(int $value, bool $color = true): string
    {
        if($color) {
            if($value < 0)
                $class = 'danger';
            else if($value > 0)
                $class = 'success';
            else
                $class = 'dark';
        } else {
            $class = 'dark';
        }
        return '<span class="font-weight-bold text-'.$class.'">'.Money::formatFromBase($value).'</span>';
    }

    /**
     * @return string
     */
    public function getAjaxPath()
    {
        return $this->getPath().'/templates/.default/ajax.php';
    }
}
