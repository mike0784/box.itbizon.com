<?php


namespace Itbizon\Finance;


use Bitrix\Main\ArgumentException;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\ObjectNotFoundException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use CUser;

/**
 * Class Permission
 * @package Itbizon\Finance
 */
class Permission
{
    protected static $instance;
    protected $user;

    /**
     * Permission constructor.
     */
    protected function __construct()
    {
        global $USER;
        if (isset($USER) && is_a($USER, CUser::class)) {
            $this->user = CurrentUser::get();
        }
    }

    /**
     * @return static
     */
    public static function getInstance(): self
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @return CurrentUser|null
     */
    public function get(): ?CurrentUser
    {
        return $this->user;
    }

    /**
     * @return bool
     * @throws ArgumentException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function isAllowCategoryAdd(): bool
    {
        return ($this->get() && ($this->get()->isAdmin() ||
                AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_CATEGORY, Model\AccessRightTable::ENTITY_ID_ALL, Model\AccessRightTable::ACTION_ADD)
            ));
    }

    /**
     * @param OperationCategory|null $category
     * @return bool
     * @throws ArgumentException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function isAllowCategoryView(OperationCategory $category = null): bool
    {
        return ($this->get() && ($this->get()->isAdmin() ||
                ($category && AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_CATEGORY, $category->getId(), Model\AccessRightTable::ACTION_VIEW)) ||
                AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_CATEGORY, Model\AccessRightTable::ENTITY_ID_ALL, Model\AccessRightTable::ACTION_VIEW)
            ));
    }

    /**
     * @param OperationCategory|null $category
     * @return bool
     * @throws ArgumentException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function isAllowCategoryEdit(OperationCategory $category = null): bool
    {
        return ($this->get() && ($this->get()->isAdmin() ||
                ($category && AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_CATEGORY, $category->getId(), Model\AccessRightTable::ACTION_EDIT)) ||
                AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_CATEGORY, Model\AccessRightTable::ENTITY_ID_ALL, Model\AccessRightTable::ACTION_EDIT)
            ));
    }

    /**
     * @param OperationCategory|null $category
     * @return bool
     * @throws ArgumentException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function isAllowCategoryDelete(OperationCategory $category = null): bool
    {
        return ($this->get() && ($this->get()->isAdmin() ||
                ($category && AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_CATEGORY, $category->getId(), Model\AccessRightTable::ACTION_DELETE)) ||
                AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_CATEGORY, Model\AccessRightTable::ENTITY_ID_ALL, Model\AccessRightTable::ACTION_DELETE)
            ));
    }

    /**
     * @return bool
     * @throws ArgumentException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function isAllowVaultAdd(): bool
    {
        return ($this->get() && ($this->get()->isAdmin() ||
                AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_VAULT, Model\AccessRightTable::ENTITY_ID_ALL, Model\AccessRightTable::ACTION_ADD)
            ));
    }

    /**
     * @param Vault|null $vault
     * @return bool
     * @throws ArgumentException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function isAllowVaultView(Vault $vault = null): bool
    {
        return ($this->get() && ($this->get()->isAdmin() ||
                ($vault && ($this->get()->getId() == $vault->getResponsibleId() || AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_VAULT, $vault->getId(), Model\AccessRightTable::ACTION_VIEW))) ||
                AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_VAULT, Model\AccessRightTable::ENTITY_ID_ALL, Model\AccessRightTable::ACTION_VIEW)
            ));
    }

    /**
     * @param Vault|null $vault
     * @return bool
     * @throws ArgumentException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function isAllowVaultEdit(Vault $vault = null): bool
    {
        return ($this->get() && ($this->get()->isAdmin() ||
                ($vault && ($this->get()->getId() == $vault->getResponsibleId() || AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_VAULT, $vault->getId(), Model\AccessRightTable::ACTION_EDIT))) ||
                AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_VAULT, Model\AccessRightTable::ENTITY_ID_ALL, Model\AccessRightTable::ACTION_EDIT)
            ));
    }

    /**
     * @param Vault|null $vault
     * @return bool
     * @throws ArgumentException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function isAllowVaultDelete(Vault $vault = null): bool
    {
        return ($this->get() && ($this->get()->isAdmin() ||
                ($vault && ($this->get()->getId() == $vault->getResponsibleId() || AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_VAULT, $vault->getId(), Model\AccessRightTable::ACTION_DELETE))) ||
                AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_VAULT, Model\AccessRightTable::ENTITY_ID_ALL, Model\AccessRightTable::ACTION_DELETE)
            ));
    }

    /**
     * @return bool
     * @throws ArgumentException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function isAllowVaultGroupAdd(): bool
    {
        return ($this->get() && ($this->get()->isAdmin() ||
                AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_VAULT_GROUP, Model\AccessRightTable::ENTITY_ID_ALL, Model\AccessRightTable::ACTION_ADD)
            ));
    }

    /**
     * @param VaultGroup|null $group
     * @return bool
     * @throws ArgumentException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function isAllowVaultGroupView(VaultGroup $group = null): bool
    {
        return ($this->get() && ($this->get()->isAdmin() ||
                ($group && AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_VAULT_GROUP, $group->getId(), Model\AccessRightTable::ACTION_VIEW)) ||
                AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_VAULT_GROUP, Model\AccessRightTable::ENTITY_ID_ALL, Model\AccessRightTable::ACTION_VIEW)
            ));
    }

    /**
     * @param VaultGroup|null $group
     * @return bool
     * @throws ArgumentException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function isAllowVaultGroupEdit(VaultGroup $group = null): bool
    {
        return ($this->get() && ($this->get()->isAdmin() ||
                ($group && AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_VAULT_GROUP, $group->getId(), Model\AccessRightTable::ACTION_EDIT)) ||
                AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_VAULT_GROUP, Model\AccessRightTable::ENTITY_ID_ALL, Model\AccessRightTable::ACTION_EDIT)
            ));
    }

    /**
     * @param VaultGroup|null $group
     * @return bool
     * @throws ArgumentException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function isAllowVaultGroupDelete(VaultGroup $group = null): bool
    {
        return ($this->get() && ($this->get()->isAdmin() ||
                ($group && AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_VAULT_GROUP, $group->getId(), Model\AccessRightTable::ACTION_DELETE)) ||
                AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_VAULT_GROUP, Model\AccessRightTable::ENTITY_ID_ALL, Model\AccessRightTable::ACTION_DELETE)
            ));
    }

    /**
     * @return bool
     * @throws ArgumentException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function isAllowOperationAdd(): bool
    {
        return ($this->get() && ($this->get()->isAdmin() ||
                AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_OPERATION, Model\AccessRightTable::ENTITY_ID_ALL, Model\AccessRightTable::ACTION_ADD)
            ));
    }

    /**
     * @param Operation|null $operation
     * @return bool
     * @throws ArgumentException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function isAllowOperationView(Operation $operation = null): bool
    {
        $allowIds = [];
        if ($operation) {
            $allowIds[] = $operation->getResponsibleId();
            if ($operation->getSrcVault()) {
                $allowIds[] = $operation->getSrcVault()->getResponsibleId();
            }
            if ($operation->getDstVault()) {
                $allowIds[] = $operation->getDstVault()->getResponsibleId();
            }
        }
        return ($this->get() && ($this->get()->isAdmin() ||
                ($operation && (in_array($this->get()->getId(), $allowIds) || AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_OPERATION, $operation->getId(), Model\AccessRightTable::ACTION_VIEW))) ||
                AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_OPERATION, Model\AccessRightTable::ENTITY_ID_ALL, Model\AccessRightTable::ACTION_VIEW)
            ));
    }

    /**
     * @param Operation|null $operation
     * @return bool
     * @throws ArgumentException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function isAllowOperationEdit(Operation $operation = null): bool
    {
        return ($this->get() && ($this->get()->isAdmin() ||
                ($operation && AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_OPERATION, $operation->getId(), Model\AccessRightTable::ACTION_EDIT)) ||
                AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_OPERATION, Model\AccessRightTable::ENTITY_ID_ALL, Model\AccessRightTable::ACTION_EDIT)
            ));
    }

    /**
     * @param Operation|null $operation
     * @return bool
     * @throws ArgumentException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function isAllowOperationDelete(Operation $operation = null): bool
    {
        return ($this->get() && ($this->get()->isAdmin() ||
                ($operation && AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_OPERATION, $operation->getId(), Model\AccessRightTable::ACTION_DELETE)) ||
                AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_OPERATION, Model\AccessRightTable::ENTITY_ID_ALL, Model\AccessRightTable::ACTION_DELETE)
            ));
    }

    /**
     * @return bool
     * @throws ArgumentException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function isAllowPeriodAdd(): bool
    {
        return ($this->get() && ($this->get()->isAdmin() ||
                AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_PERIOD, Model\AccessRightTable::ENTITY_ID_ALL, Model\AccessRightTable::ACTION_ADD)
            ));
    }

    /**
     * @return bool
     * @throws ArgumentException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function isAllowPeriodEdit(): bool
    {
        return ($this->get() && ($this->get()->isAdmin() ||
                AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_PERIOD, Model\AccessRightTable::ENTITY_ID_ALL, Model\AccessRightTable::ACTION_EDIT)
            ));
    }

    /**
     * @return bool
     * @throws ArgumentException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function isAllowPeriodDelete(): bool
    {
        return ($this->get() && ($this->get()->isAdmin() ||
                AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_PERIOD, Model\AccessRightTable::ENTITY_ID_ALL, Model\AccessRightTable::ACTION_DELETE)
            ));
    }

    /**
     * @return bool
     * @throws ArgumentException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function isAllowConfigSave(): bool
    {
        return ($this->get() && ($this->get()->isAdmin() ||
                AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_CONFIG, Model\AccessRightTable::ENTITY_ID_ALL, Model\AccessRightTable::ACTION_EDIT)
            ));
    }

    /**
     * @return bool
     * @throws ArgumentException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function isAllowRequestAdd(): bool
    {
        return ($this->get() && ($this->get()->isAdmin() ||
                ($this->get()->getId() > 0) || //TODO
                AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_REQUEST, Model\AccessRightTable::ENTITY_ID_ALL, Model\AccessRightTable::ACTION_ADD)
            ));
    }

    /**
     * @param Request|null $request
     * @return bool
     * @throws ArgumentException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function isAllowRequestView(Request $request = null): bool
    {
        return ($this->get() && ($this->get()->isAdmin() ||
                ($request && $request->getAuthorId() == $this->get()->getId()) ||
                AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_REQUEST, Model\AccessRightTable::ENTITY_ID_ALL, Model\AccessRightTable::ACTION_VIEW)
            ));
    }

    /**
     * @param Request|null $request
     * @return bool
     * @throws ArgumentException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function isAllowRequestEdit(Request $request = null): bool
    {
        return ($this->get() && ($this->get()->isAdmin() ||
                ($request && $request->getAuthorId() == $this->get()->getId()) ||
                AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_REQUEST, Model\AccessRightTable::ENTITY_ID_ALL, Model\AccessRightTable::ACTION_EDIT)
            ));
    }

    /**
     * @return bool
     * @throws ArgumentException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function isAllowRequestDelete(): bool
    {
        return ($this->get() && ($this->get()->isAdmin() ||
                AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_REQUEST, Model\AccessRightTable::ENTITY_ID_ALL, Model\AccessRightTable::ACTION_DELETE)
            ));
    }

    /**
     * @return bool
     * @throws ArgumentException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function isAllowCategoryReportShow(): bool
    {
        return ($this->get() && ($this->get()->isAdmin() ||
                AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_CATEGORY_REPORT, Model\AccessRightTable::ENTITY_ID_ALL, Model\AccessRightTable::ACTION_VIEW)
            ));
    }

    /**
     * @return bool
     * @throws ArgumentException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function isAllowRequestTemplateAdd(): bool
    {
        return ($this->get() && ($this->get()->isAdmin() ||
                AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_REQUEST_TEMPLATE, Model\AccessRightTable::ENTITY_ID_ALL, Model\AccessRightTable::ACTION_ADD)
            ));
    }

    /**
     * @param RequestTemplate $template
     * @return bool
     * @throws ArgumentException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function isAllowRequestTemplateEdit(RequestTemplate $template = null): bool
    {
        return ($this->get() && ($this->get()->isAdmin() ||
                ($template && AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_REQUEST_TEMPLATE, $template->getId(), Model\AccessRightTable::ACTION_EDIT)) ||
                AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_REQUEST_TEMPLATE, Model\AccessRightTable::ENTITY_ID_ALL, Model\AccessRightTable::ACTION_EDIT)
            ));
    }

    /**
     * @param RequestTemplate $template
     * @return bool
     * @throws ArgumentException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function isAllowRequestTemplateDelete(RequestTemplate $template = null): bool
    {
        return ($this->get() && ($this->get()->isAdmin() ||
                ($template && AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_REQUEST_TEMPLATE, $template->getId(), Model\AccessRightTable::ACTION_DELETE)) ||
                AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_REQUEST_TEMPLATE, Model\AccessRightTable::ENTITY_ID_ALL, Model\AccessRightTable::ACTION_DELETE)
            ));
    }

    /**
     * @return bool
     * @throws ArgumentException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function isAllowStockAdd(): bool
    {
        return ($this->get() && ($this->get()->isAdmin() ||
                AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_STOCK, Model\AccessRightTable::ENTITY_ID_ALL, Model\AccessRightTable::ACTION_ADD)
            ));
    }

    /**
     * @param Stock|null $stock
     * @return bool
     * @throws ArgumentException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function isAllowStockView(Stock $stock = null): bool
    {
        return ($this->get() && ($this->get()->isAdmin() ||
                ($stock && ($this->get()->getId() == $stock->getResponsibleId() || AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_STOCK, $stock->getId(), Model\AccessRightTable::ACTION_VIEW))) ||
                AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_STOCK, Model\AccessRightTable::ENTITY_ID_ALL, Model\AccessRightTable::ACTION_VIEW)
            ));
    }

    /**
     * @param Stock|null $stock
     * @return bool
     * @throws ArgumentException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function isAllowStockEdit(Stock $stock = null): bool
    {
        return ($this->get() && ($this->get()->isAdmin() ||
                ($stock && ($this->get()->getId() == $stock->getResponsibleId() || AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_STOCK, $stock->getId(), Model\AccessRightTable::ACTION_EDIT))) ||
                AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_STOCK, Model\AccessRightTable::ENTITY_ID_ALL, Model\AccessRightTable::ACTION_EDIT)
            ));
    }

    /**
     * @param Stock|null $stock
     * @return bool
     * @throws ArgumentException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function isAllowStockDelete(Stock $stock = null): bool
    {
        return ($this->get() && ($this->get()->isAdmin() ||
                ($stock && ($this->get()->getId() == $stock->getResponsibleId() || AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_STOCK, $stock->getId(), Model\AccessRightTable::ACTION_DELETE))) ||
                AccessRight::checkPermission($this->get()->getId(), Model\AccessRightTable::ENTITY_STOCK, Model\AccessRightTable::ENTITY_ID_ALL, Model\AccessRightTable::ACTION_DELETE)
            ));
    }
}