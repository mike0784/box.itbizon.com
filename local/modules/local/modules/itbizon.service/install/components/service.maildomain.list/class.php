<?php

use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Itbizon\Service\Component\GridHelper;
use Itbizon\Service\Component\RouterHelper;
use Itbizon\Service\Mail\Model\MailDomainTable;

Loc::loadMessages(__FILE__);

/**
 * Class CITBServiceMailDomainList
 */
class CITBServiceMailDomainList extends CBitrixComponent
{
    protected $helper;
    protected $gridHelper;
    protected $error;

    /**
     * @return mixed|void|null
     */
    public function executeComponent()
    {
        try {
            if (!Loader::includeModule('itbizon.service')) {
                throw new Exception(Loc::getMessage('ITB_SERVICE.MAILDOMAIN.LIST.ERROR.INCLUDE_SERVICE'));
            }
            if (!CurrentUser::get()->isAdmin())
                throw new Exception(Loc::getMessage('ITB_SERVICE.MAILDOMAIN.LIST.ERROR.ACCESS_DENY'));

            $this->setHelper(new RouterHelper(
                $this,
                [
                    'list' => 'list/',
                    'add' => 'add/',
                    'edit' => 'edit/#ID#/',
                ],
                'list'
            ));
            $this->getHelper()->run();

            $this->gridHelper = $gridHelper = (new GridHelper('itb_service_maildomain', 'itb_service_maildomain'))
                ->setFilter([
                    [
                        'id' => 'ID',
                        'name' => Loc::getMessage('ITB_SERVICE.MAILDOMAIN.LIST.ID'),
                        'type' => 'number',
                        'default' => false
                    ],
                    [
                        'id' => 'ACTIVE',
                        'name' => Loc::getMessage('ITB_SERVICE.MAILDOMAIN.LIST.ACTIVE'),
                        'type' => 'list',
                        'items' => [
                            'Y' => Loc::getMessage('ITB_SERVICE.MAILDOMAIN.LIST.ACTIVE.YES'),
                            'N' => Loc::getMessage('ITB_SERVICE.MAILDOMAIN.LIST.ACTIVE.NO'),
                        ],
                        'default' => true
                    ],
                    [
                        'id' => 'DOMAIN',
                        'name' => Loc::getMessage('ITB_SERVICE.MAILDOMAIN.LIST.DOMAIN'),
                        'type' => 'string',
                        'default' => true
                    ],
                    [
                        'id' => 'SERVER',
                        'name' => Loc::getMessage('ITB_SERVICE.MAILDOMAIN.LIST.SERVER'),
                        'type' => 'string',
                        'default' => true
                    ],
                    [
                        'id' => 'PORT',
                        'name' => Loc::getMessage('ITB_SERVICE.MAILDOMAIN.LIST.PORT'),
                        'type' => 'number',
                        'default' => true
                    ],
                ])
                ->setColumns([
                    ['id' => 'ID', 'name' => Loc::getMessage('ITB_SERVICE.MAILDOMAIN.LIST.ID'), 'sort' => 'ID', 'default' => true],
                    ['id' => 'ACTIVE', 'name' => Loc::getMessage('ITB_SERVICE.MAILDOMAIN.LIST.ACTIVE'), 'sort' => 'ACTIVE', 'default' => true],
                    ['id' => 'DOMAIN', 'name' => Loc::getMessage('ITB_SERVICE.MAILDOMAIN.LIST.DOMAIN'), 'sort' => 'DOMAIN', 'default' => true],
                    ['id' => 'SERVER', 'name' => Loc::getMessage('ITB_SERVICE.MAILDOMAIN.LIST.SERVER'), 'sort' => 'SERVER', 'default' => true],
                    ['id' => 'PORT', 'name' => Loc::getMessage('ITB_SERVICE.MAILDOMAIN.LIST.PORT'), 'sort' => 'PORT', 'default' => true],
                ]);

            $result = MailDomainTable::getList([
                'filter' => $gridHelper->getFilterData(),
                'limit' => $gridHelper->getNavigation()->getLimit(),
                'offset' => $gridHelper->getNavigation()->getOffset(),
                'order' => $gridHelper->getSort(),
            ]);
            while ($item = $result->fetchObject()) {
                $gridHelper->addRow([
                    'data' => [
                        'ID' => $item->getId(),
                        'ACTIVE' => ($item->getActive()) ? Loc::getMessage('ITB_SERVICE.MAILDOMAIN.LIST.ACTIVE.YES') : Loc::getMessage('ITB_SERVICE.MAILDOMAIN.LIST.ACTIVE.NO'),
                        'DOMAIN' => $item->getDomain(),
                        'SERVER' => $item->getServer(),
                        'PORT' => $item->getPort(),
                    ],
                    'actions' => [
                        [
                            'text' => Loc::getMessage('ITB_SERVICE.MAILDOMAIN.LIST.ACTION.EDIT'),
                            'default' => true,
                            'onclick' => 'BX.SidePanel.Instance.open("' . $this->getHelper()->getUrl('edit', ['ID' => $item->getId()]) . '", {
                                cacheable: false,
                                width: 450
                            });',
                        ],
                        [
                            'text' => Loc::getMessage('ITB_SERVICE.MAILDOMAIN.LIST.ACTION.DELETE'),
                            'default' => false,
                            'onclick' => 'alert("Yes my lord")',
                        ],
                    ]
                ]);
            }
        } catch (Exception $e) {
            $this->setError($e->getMessage());
        }
        // Include template
        $this->IncludeComponentTemplate();
    }

    /**
     * @return RouterHelper|null
     */
    public function getHelper(): ?RouterHelper
    {
        return $this->helper;
    }

    /**
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * @param mixed $helper
     */
    protected function setHelper(RouterHelper $helper): void
    {
        $this->helper = $helper;
    }

    /**
     * @param mixed $error
     */
    protected function setError($error): void
    {
        $this->error = $error;
    }

    /**
     * @return mixed
     */
    public function getGridHelper()
    {
        return $this->gridHelper;
    }
}
