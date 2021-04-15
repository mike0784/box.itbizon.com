<?php

use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Itbizon\Finance\AccessRight;
use Itbizon\Finance\Model\AccessRightTable;

Loc::loadMessages(__FILE__);

/**
 * Class CITBFinancePermissionAdd
 */
class CITBFinancePermissionAdd extends CBitrixComponent
{
    public $error = null;

    /**
     * @return bool|mixed
     */
    public function executeComponent()
    {
        $this->error = null;

        try {
            if(!CurrentUser::get()->isAdmin())
                throw new Exception(Loc::getMessage("ITB_FINANCE.PERMISSION.ADD.ERROR.ACCESS_DENIED"));

            if(!Loader::includeModule('itbizon.finance'))
                throw new Exception(Loc::getMessage('ITB_FINANCE.PERMISSION.ADD.ERROR.INCLUDE_FINANCE'));

            $this->appendToJSActionsId();
            $this->appendToJSEntityTypeIds();
            $this->appendToJSEntityIds();

            if(isset($_REQUEST['DATA'])) {

                $obj = new AccessRight();

                $arrActions = $this->getActions();
                $type = intval($_REQUEST['DATA']['ENTITY_TYPE_ID']);
                $entityId = intval($_REQUEST['DATA']['ENTITY_ID']);
                $action = intval($_REQUEST['DATA']['ACTION']);

                if(!isset($arrActions[$action]))
                    throw new Exception(Loc::getMessage("ITB_FINANCE.PERMISSION.ADD.INVALID_ENTITY_TYPE"));

                if($type == AccessRightTable::ENTITY_VAULT) {
                    if(
                        $action != AccessRightTable::ACTION_VIEW &&
                        $action != AccessRightTable::ACTION_EDIT &&
                        $action != AccessRightTable::ACTION_REQUEST_INCOME
                    ) {
                        $entityId = AccessRightTable::ENTITY_ID_ALL;
                    }

                } elseif(
                    $type == AccessRightTable::ENTITY_VAULT_GROUP ||
                    $type == AccessRightTable::ENTITY_CATEGORY
                ) {
                    if(
                        $action != AccessRightTable::ACTION_VIEW &&
                        $action != AccessRightTable::ACTION_EDIT
                    ) {
                        $entityId = AccessRightTable::ENTITY_ID_ALL;
                    }
                } else {
                    $entityId = AccessRightTable::ENTITY_ID_ALL;
                }

                $obj->setEntityTypeId($type);
                $obj->setEntityId($entityId);
                $obj->setAction($action);

                $matches = [];
                if(preg_match('#(' . implode('|', ['U', 'DR']) . ')([0-9]+)#', $_REQUEST['DATA']['USER'], $matches) === 1
                    && !empty($matches[1]) && !empty($matches[2])
                ) {
                    $symbol = ($matches[1] == 'U' ? AccessRightTable::USER : AccessRightTable::DEPARTMENT);
                    $id = $matches[2];

                    $obj->setUserType($symbol);
                    $obj->setUserId($id);
                } else {
                    throw new Exception(Loc::getMessage("ITB_FINANCE.PERMISSION.ADD.INVALID_USER"));
                }

                $result = $obj->save();

                if(isset($result)) {
                    if(!empty($result->getId())) {
                        if($_REQUEST['IFRAME'] == 'Y') {
                            $this->actionSuccessAjax();
                            die();
                        }
                        $protocol = ($_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https' : 'http';
                        $pathToRedirect = $protocol . '://'
                            . $_SERVER["SERVER_NAME"]
                            . $this->arParams['FOLDER']
                            . str_replace("#ID#", $result->getId(), $this->arParams['TEMPLATE_EDIT']);
                        header('Location: ' . $pathToRedirect);
                        die();

                    } else {
                        throw new Exception(implode("", $result->getErrorMessages()));
                    }
                } else {
                    throw new Exception(Loc::getMessage('ITB_FINANCE.PERMISSION.ADD.ERROR.CREATE_FAILED'));
                }
            }

        } catch (Exception $e) {
            $this->error = $e->getMessage();
        }

        //Include template
        $this->IncludeComponentTemplate();
        return true;
    }

    /**
     * Метод добавляет id типов сущности в js
     */
    private function appendToJSActionsId()
    {
        global $APPLICATION;
        $mess = array(
            'ITB_FINANCE.PERMISSION.ADD.ACTIONS.REQUEST_INCOME' => AccessRightTable::ACTION_REQUEST_INCOME,
            'ITB_FINANCE.PERMISSION.ADD.ACTIONS.ADD' => AccessRightTable::ACTION_ADD,
            'ITB_FINANCE.PERMISSION.ADD.ACTIONS.DELETE' => AccessRightTable::ACTION_DELETE,
            'ITB_FINANCE.PERMISSION.ADD.ACTIONS.EDIT' => AccessRightTable::ACTION_EDIT,
            'ITB_FINANCE.PERMISSION.ADD.ACTIONS.VIEW' => AccessRightTable::ACTION_VIEW,
        );
        $APPLICATION->addheadstring('<script type="text/javascript">BX.message(' . json_encode($mess) . ')</script>');
    }

    /**
     * Метод добавляет id типов сущности в js
     */
    private function appendToJSEntityTypeIds()
    {
        global $APPLICATION;
        $mess = array(
            'ITB_FINANCE.PERMISSION.ADD.ENTITY.VAULT' => AccessRightTable::ENTITY_VAULT,
            'ITB_FINANCE.PERMISSION.ADD.ENTITY.VAULT_GROUP' => AccessRightTable::ENTITY_VAULT_GROUP,
            'ITB_FINANCE.PERMISSION.ADD.ENTITY.OPERATION' => AccessRightTable::ENTITY_OPERATION,
            'ITB_FINANCE.PERMISSION.ADD.ENTITY.CATEGORY' => AccessRightTable::ENTITY_CATEGORY,
            'ITB_FINANCE.PERMISSION.ADD.ENTITY.PERIOD' => AccessRightTable::ENTITY_PERIOD,
            'ITB_FINANCE.PERMISSION.ADD.ENTITY.REQUEST_TEMPLATE' => AccessRightTable::ENTITY_REQUEST_TEMPLATE,
            'ITB_FINANCE.PERMISSION.ADD.ENTITY.CONFIG' => AccessRightTable::ENTITY_CONFIG,
        );
        $APPLICATION->addheadstring('<script type="text/javascript">BX.message(' . json_encode($mess) . ')</script>');
    }

    /**
     * Метод добавляет id типов сущности в js
     */
    private function appendToJSEntityIds()
    {
        global $APPLICATION;
        $mess = array(
            'ITB_FINANCE.PERMISSION.ADD.ENTITY_ID.ALL' => AccessRightTable::ENTITY_ID_ALL,
        );
        $APPLICATION->addheadstring('<script type="text/javascript">BX.message(' . json_encode($mess) . ')</script>');
    }

    /**
     *
     */
    private function actionSuccessAjax()
    {
        echo "<script>setTimeout(function(){BX.SidePanel.Instance.getTopSlider().data.set('close', true)}, 100);</script>";
        echo "<script>setTimeout(function(){BX.SidePanel.Instance.close()}, 200);</script>";
    }

    /**
     * @param int|null $id
     * @return array|string
     */
    public function getEntityTypes(int $id = null)
    {
        return AccessRightTable::getEntityTypes($id);
    }

    /**
     * @return array
     */
    public function getEntity(): array
    {
        $arrEntity = [];

        try {
            $arrEntity[] = [
                'ID' => 0,
                'TYPE' => AccessRightTable::ENTITY_VAULT,
                'NAME' => Loc::getMessage("ITB_FINANCE.PERMISSION.ADD.FIELD.ENTITY_ALL")
            ];
            $arrEntity[] = [
                'ID' => 0,
                'TYPE' => AccessRightTable::ENTITY_VAULT_GROUP,
                'NAME' => Loc::getMessage("ITB_FINANCE.PERMISSION.ADD.FIELD.ENTITY_ALL")
            ];
            $arrEntity[] = [
                'ID' => 0,
                'TYPE' => AccessRightTable::ENTITY_CATEGORY,
                'NAME' => Loc::getMessage("ITB_FINANCE.PERMISSION.ADD.FIELD.ENTITY_ALL")
            ];

            $listVault = \Itbizon\Finance\Model\VaultTable::getList();
            while ($objVault = $listVault->fetchObject()) {
                $arrEntity[] = [
                    'ID' => $objVault->getId(),
                    'TYPE' => AccessRightTable::ENTITY_VAULT,
                    'NAME' => $objVault->getName()
                ];
            }

            $listVaultGroup = \Itbizon\Finance\Model\VaultGroupTable::getList();
            while ($objVaultGroup = $listVaultGroup->fetchObject()) {
                $arrEntity[] = [
                    'ID' => $objVaultGroup->getId(),
                    'TYPE' => AccessRightTable::ENTITY_VAULT_GROUP,
                    'NAME' => $objVaultGroup->getName()
                ];
            }

            $listOperationCategory = \Itbizon\Finance\Model\OperationCategoryTable::getList();
            while ($objOperationCategory = $listOperationCategory->fetchObject()) {
                $arrEntity[] = [
                    'ID' => $objOperationCategory->getId(),
                    'TYPE' => AccessRightTable::ENTITY_CATEGORY,
                    'NAME' => $objOperationCategory->getName()
                ];
            }

        } catch (\Exception $e) {}

        return $arrEntity;
    }

    /**
     * @param int|null $id
     * @return array|string
     */
    public function getActions(int $id = null)
    {
        return AccessRightTable::getActions($id);
    }
}
