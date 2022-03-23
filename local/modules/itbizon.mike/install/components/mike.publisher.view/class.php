<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;
use Bitrix\UI\Buttons\JsCode;
use Itbizon\Mike\PublisherTable;
use Bitrix\Main\Type;
use Itbizon\Service\Component\Simple;
use Itbizon\Service\Component\GridHelper;
use Itbizon\Service\Component\RouterHelper;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);

class PublisherView extends Simple
{
    protected $result = array();
    public $gridId = 'mike_publisher';

    public $gridNav;
    public  $grid;
    /**
     * Проверка подключения модуля
     */
    public function _checkModules()
    {
        if(!Loader::includeModule('itbizon.mike')){
            throw new Exception(Loc::getMessage('ITB_MIKE_PUBLISHER_VIEW_ERROR_INCLUDE_MODULE'));
        }
        if (!Loader::includeModule('itbizon.service')) {
            throw new Exception("Модуль itbizon.service не найден");
        }
    }

    /**
     * Точка входа в компонент
     * Должна содержать только последовательность вызовов вспомогательых ф-ий и минимум логики
     * всю логику стараемся разносить по классам и методам
     */
    public function executeComponent() {
        $this -> _checkModules();

        if (!isset($this->arParams['HELPER']) || !is_a($this->arParams['HELPER'], RouterHelper::class)) {
            throw new Exception("Некорректный вход");
        }

        $this->setRoute($this->arParams['HELPER']);

        $grid = new GridHelper($this->gridId);

        $this->setGrid($grid);
        $grid->setColumns([
            ['id' => 'ID', 'name' => 'ID', 'sort' => 'ID', 'default' => true],
            ['id' => 'NAMECOMPANY', 'name' => 'Название организации', 'sort' => 'NAMECOMPANY', 'default' => true],
            ['id' => 'CREATEAT', 'name' => 'Дата создания', 'sort' => 'CREATEAT', 'default' => true],
            ['id' => 'UPDATEAT', 'name' => 'Дата обнавления', 'sort' => 'UPDATEAT', 'default' => true],
        ]);


        $result = PublisherTable::getList([
            'select' => ['*'],
            'filter' => $grid->getFilterData(),
            'limit' => $grid->getNavigation()->getLimit(),
            'offset' => $grid->getNavigation()->getOffset(),
            'order' => $grid->getSort(),
            'count_total' => true,
        ]);

        while($item = $result->fetchObject()) {
            $this->grid->addRow(
                [
                    'data' => [
                        'ID' => $item->getId(),
                        'NAMECOMPANY' => $item->getNamecompany()==nulL? Loc::getMessage('ITB_MIKE_PUBLISHER_VIEW_NONAME'):$item->getNamecompany(),
                        'CREATEAT' => $item->getCreateat(),
                        'UPDATEAT' => $item->getUpdateat(),

                    ],
                    'actions' => [
                        [
                            'text' => Loc::getMessage('ITB_MIKE_PUBLISHER_VIEW_ACTION_READ'),
                            'default' => true,
                            'onclick' => (new JsCode(
                                'BX.SidePanel.Instance.open("' . $this->getRoute()->getUrl('publisher.update', ['ID' => $item->getId()]) . '", {
                                    cacheable: false,
                                    width: 450
                                 });'
                            ))->getCode(),
                        ],

                        [
                            'text' => Loc::getMessage('ITB_MIKE_PUBLISHER_VIEW_ACTION_DELETE'),
                            'default' => false,
                            'onclick' => (new JsCode('deletePublisher('.$item->getId().', "' . $this->grid->getGridId() . '")'))->getCode(),
                        ]
                    ]
                ]
            );
        }
        $grid->getNavigation()->setRecordCount($result->getCount());
        $this->includeComponentTemplate();
    }
}