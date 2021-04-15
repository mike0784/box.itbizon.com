<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Bizproc\FieldType;
use Bitrix\Crm\CompanyTable;
use Bitrix\Crm\DealTable;
use Bitrix\Crm\LeadTable;
use Bitrix\Main\Loader;
use Itbizon\Finance\Model\OperationCategoryTable;
use Itbizon\Finance\Model\OperationTable;
use Itbizon\Finance\Model\VaultTable;

/**
 * @property mixed|null MODE_CREATE
 * @property mixed|null NAME
 * @property mixed|null ENTITY_CRM
 * @property mixed|null TYPE
 * @property mixed|null SRC_VAULT_ID
 * @property mixed|null DST_VAULT_ID
 * @property mixed|null CATEGORY_ID
 * @property mixed|null AMOUNT
 * @property mixed|null ENTITY_TYPE_ID
 * @property mixed|null ENTITY_ID
 * @property mixed|null COMMENT
 * @property int|mixed|null ID
 * @property bool|mixed|null CONFIRMED
 * @property bool|mixed|null DECLINE
 * @property bool|mixed|null DELETED
 * @property mixed|null RESPONSIBLE_ID
 */
class CBPItBizonFinanceOperation extends CBPActivity implements IBPEventActivity, IBPActivityExternalEventListener
{
    private $userFields = [];
    private $manager = null;
    private $userFieldsEntity = null;

    /**
     * CBPItBizonFinanceOperation constructor.
     * @param $name
     * @throws \Bitrix\Main\LoaderException
     */
    public function __construct($name)
    {
        if (!Loader::includeModule('itbizon.finance'))
            throw new Exception(GetMessage('ACTIVITY.FINANCE.OPERATION.ERROR.LOAD_FINANCE'));
        if (!Loader::IncludeModule('crm'))
            throw new Exception(GetMessage('ACTIVITY.FINANCE.OPERATION.ERROR.LOAD_CRM'));

        $lang = Bitrix\Main\Application::getInstance()->getContext()->getLanguage();
        $this->userFieldsEntity = \ItBizon\Finance\Model\OperationTable::getUfId();
        $this->manager = new \CUserTypeManager();
        $this->userFields = $this->manager->GetUserFields($this->userFieldsEntity, 0, $lang);

        parent::__construct($name);
        $this->arProperties = [
            "ID" => null,
            "CONFIRMED" => null,
            "DECLINE" => null,
            "DELETED" => null,
            "NAME" => '',
            "TYPE" => 0,
            "SRC_VAULT_ID" => 0,
            "DST_VAULT_ID" => 0,
            "CATEGORY_ID" => 0,
            "RESPONSIBLE_ID" => null,
            "AMOUNT" => null,
            "ENTITY_CRM" => '',
            "ENTITY_TYPE_ID" => 0,
            "ENTITY_ID" => 0,
            "COMMENT" => '',
            "MODE_CREATE" => 'C',
        ];

        $this->arProperties = array_merge(
            $this->arProperties,
            array_combine(array_keys($this->userFields), array_fill(0, count($this->userFields), null))
        );

        $propertyTypes = [
            'NAME' => [
                'Type' => FieldType::STRING
            ],
            'TYPE' => [
                'Type' => FieldType::INT
            ],
            'SRC_VAULT_ID' => [
                'Type' => FieldType::INT
            ],
            'DST_VAULT_ID' => [
                'Type' => FieldType::INT
            ],
            'CATEGORY_ID' => [
                'Type' => FieldType::INT
            ],
            'RESPONSIBLE_ID' => [
                'Type' => FieldType::USER
            ],
            'AMOUNT' => [
                'Type' => FieldType::DOUBLE
            ],
            'ENTITY_CRM' => [
                'Type' => FieldType::STRING
            ],
            'ENTITY_TYPE_ID' => [
                'Type' => FieldType::INT
            ],
            'ENTITY_ID' => [
                'Type' => FieldType::INT
            ],
            'COMMENT' => [
                'Type' => FieldType::TEXT
            ],
            'MODE_CREATE' => [
                'Type' => FieldType::INT
            ],
        ];

        $propertyTypes = array_merge(
            $propertyTypes,
            array_combine(array_keys($this->userFields), array_fill(0, count($this->userFields), [
                'Type' => FieldType::STRING
            ]))
        );

        $this->SetPropertiesTypes($propertyTypes);

    }

    /**
     * @return int
     */
    public function Execute()
    {
        try {
            if (!Loader::includeModule('itbizon.finance'))
                throw new Exception(GetMessage('ACTIVITY.FINANCE.OPERATION.ERROR.LOAD_FINANCE'));
            if (!Loader::IncludeModule('crm'))
                throw new Exception(GetMessage('ACTIVITY.FINANCE.OPERATION.ERROR.LOAD_CRM'));

            $this->ID = null;
            $this->CONFIRMED = false;
            $this->DECLINE = false;
            $this->DELETED = false;

            $entityId = 0;
            $entityTypeId = $this->ENTITY_TYPE_ID;
            $entity = explode('_', $this->ENTITY_CRM);

            if (count($entity) < 2) {
                $entityId = $this->ENTITY_ID;
            } else {
                $entityId = intval($entity[1]);
                switch ($entity[0]) {
                    case 'L':
                        $entityTypeId = CCrmOwnerType::Lead;
                        break;
                    case 'D':
                        $entityTypeId = CCrmOwnerType::Deal;
                        break;
                    case 'C':
                        $entityTypeId = CCrmOwnerType::Contact;
                        break;
                    case 'CO':
                        $entityTypeId = CCrmOwnerType::Company;
                        break;
                    default:
                        throw new Exception(GetMessage('ACTIVITY.FINANCE.OPERATION.ERROR.INVALID_CRM_TYPE'));
                }
            }

            $data = [
                "NAME" => $this->NAME,
                "TYPE" => $this->TYPE,
                "SRC_VAULT_ID" => $this->SRC_VAULT_ID,
                "DST_VAULT_ID" => $this->DST_VAULT_ID,
                "CATEGORY_ID" => $this->CATEGORY_ID,
                "RESPONSIBLE_ID" => intval(CBPHelper::ExtractUsers($this->RESPONSIBLE_ID, $this->GetDocumentId(), true)),
                "AMOUNT" => $this->AMOUNT * 100,
                "ENTITY_TYPE_ID" => $entityTypeId,
                "ENTITY_ID" => $entityId,
                "COMMENT" => $this->COMMENT,
            ];

            switch (intval($this->TYPE)) {
                case OperationTable::TYPE_INCOME:
                    $operation = Itbizon\Finance\Operation::createIncome($data);
                    break;
                case OperationTable::TYPE_OUTGO:
                    $operation = Itbizon\Finance\Operation::createOutgo($data);
                    break;
                case OperationTable::TYPE_TRANSFER:
                    $operation = Itbizon\Finance\Operation::createTransfer($data);
                    break;
                default:
                    throw new Exception(GetMessage('ACTIVITY.FINANCE.OPERATION.ERROR.TYPE_UNDEFINED'));
            }

            if (!isset($operation) || empty($operation->getId()))
                throw new Exception(GetMessage('ACTIVITY.FINANCE.OPERATION.ERROR.OPERATION_CREATE'));

            $this->ID = $operation->getId();

            $userFieldsData = [];
            foreach ($this->userFields as $userFieldKey => $userFieldVal)
                $userFieldsData[$userFieldKey] = $this->{$userFieldKey};

            if (count($this->userFields)) {
                $this->manager->Update($this->userFieldsEntity, $this->ID, $userFieldsData);
            }

            $this->CONFIRMED = false;
            $this->DECLINE = false;
            $this->DELETED = false;

            if ($this->MODE_CREATE == 'CP') {
                $operation->confirm(0);
                $this->CONFIRMED = OperationTable::STATUS_COMMIT == $operation->getStatus();
            } elseif ($this->MODE_CREATE == 'CW') {
                $this->Subscribe($this);
                return CBPActivityExecutionStatus::Executing;
            }

        } catch (Exception $e) {
            $this->WriteToTrackingService($e->getMessage());
        }

        return CBPActivityExecutionStatus::Closed;
    }

    /**
     * @param IBPActivityExternalEventListener $eventHandler
     * @throws Exception
     */
    public function Subscribe(IBPActivityExternalEventListener $eventHandler)
    {
        if ($eventHandler == null)
            throw new Exception("eventHandler");

        $schedulerService = $this->workflow->GetService('SchedulerService');
        $events = [
            'onAfterOperationCommit',
            'onAfterOperationDecline',
            'onAfterOperationDelete',
        ];

        foreach ($events as $eventName) {
            $schedulerService->SubscribeOnEvent(
                $this->workflow->GetInstanceId(),
                $this->name,
                'itbizon.finance',
                $eventName
            );
        }

        $this->workflow->AddEventHandler($this->name, $eventHandler);
    }

    /**
     * @param IBPActivityExternalEventListener $eventHandler
     * @throws Exception
     */
    public function Unsubscribe(IBPActivityExternalEventListener $eventHandler)
    {
        if ($eventHandler == null)
            throw new Exception("eventHandler");

        $schedulerService = $this->workflow->GetService('SchedulerService');
        $events = [
            'onAfterOperationCommit',
            'onAfterOperationDecline',
            'onAfterOperationDelete',
        ];

        foreach ($events as $eventName) {
            $schedulerService->SubscribeOnEvent(
                $this->workflow->GetInstanceId(),
                $this->name,
                'itbizon.finance',
                $eventName
            );
        }
        $this->workflow->RemoveEventHandler($this->name, $eventHandler);
    }

    /**
     * @param array $arEventParameters
     * @return bool
     * @throws Exception
     */
    public function OnExternalEvent($arEventParameters = [])
    {
        if (intval($arEventParameters[0]) != $this->ID) return false;

        switch ($arEventParameters['eventName']) {
            case 'onAfterOperationCommit':
                $this->CONFIRMED = true;
                break;
            case 'onAfterOperationDecline':
                $this->DECLINE = true;
                break;
            case 'onAfterOperationDelete':
                $this->DELETED = true;
                break;
            default:
                return false;
        }

        $this->Unsubscribe($this);
        $this->workflow->CloseActivity($this);

        return true;
    }

    /**
     * @param array $arTestProperties
     * @param CBPWorkflowTemplateUser|null $user
     * @return array
     * @throws Exception
     */
    public static function ValidateProperties($arTestProperties = [], CBPWorkflowTemplateUser $user = null)
    {
        if (!Loader::includeModule('itbizon.finance'))
            throw new Exception(GetMessage('ACTIVITY.FINANCE.OPERATION.ERROR.LOAD_FINANCE'));

        $arErrors = [];
        $checkField = [
            "NAME",
            "TYPE",
//            "SRC_VAULT_ID",
//            "DST_VAULT_ID",
            "CATEGORY_ID",
            "RESPONSIBLE_ID",
            "AMOUNT",
            "MODE_CREATE",
        ];

        foreach ($checkField as $fieldName) {
            if (!array_key_exists($fieldName, $arTestProperties) || empty($arTestProperties[$fieldName])) {
                $mess = str_replace(
                    '#FIELDNAME#',
                    $fieldName,
                    GetMessage('ACTIVITY.FINANCE.OPERATION.ERROR.INVALID_FIELD')
                );
                $arErrors[] = [
                    "code" => "Empty",
                    "parameter" => $fieldName,
                    "message" => $mess
                ];
            }
        }

        if (
            ($arTestProperties['TYPE'] == OperationTable::TYPE_OUTGO || $arTestProperties['TYPE'] == OperationTable::TYPE_TRANSFER) &&
            (!array_key_exists("SRC_VAULT_ID", $arTestProperties) || empty($arTestProperties["SRC_VAULT_ID"]))
        ) {
            $mess = str_replace(
                '#FIELDNAME#',
                "SRC_VAULT_ID",
                GetMessage('ACTIVITY.FINANCE.OPERATION.ERROR.INVALID_FIELD')
            );
            $arErrors[] = [
                "code" => "Empty",
                "parameter" => "SRC_VAULT_ID",
                "message" => $mess
            ];
        }

        if (
            ($arTestProperties['TYPE'] == OperationTable::TYPE_INCOME || $arTestProperties['TYPE'] == OperationTable::TYPE_TRANSFER) &&
            (!array_key_exists("DST_VAULT_ID", $arTestProperties) || empty($arTestProperties["DST_VAULT_ID"]))
        ) {
            $mess = str_replace(
                '#FIELDNAME#',
                "DST_VAULT_ID",
                GetMessage('ACTIVITY.FINANCE.OPERATION.ERROR.INVALID_FIELD')
            );
            $arErrors[] = [
                "code" => "Empty",
                "parameter" => "DST_VAULT_ID",
                "message" => $mess
            ];
        }

        return array_merge($arErrors, parent::ValidateProperties($arTestProperties, $user));
    }

    /**
     * @param $documentType
     * @param $activityName
     * @param $arWorkflowTemplate
     * @param $arWorkflowParameters
     * @param $arWorkflowVariables
     * @param null $arCurrentValues
     * @param string $formName
     * @param null $form
     * @param string $siteId
     * @return false|string|null
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function GetPropertiesDialog(
        $documentType,
        $activityName,
        $arWorkflowTemplate,
        $arWorkflowParameters,
        $arWorkflowVariables,
        $arCurrentValues = null,
        $formName = "",
        $form = null,
        $siteId = ""
    )
    {
        if (!is_array($arCurrentValues)) {
            $arCurrentValues = [
                "ID" => null,
                "CONFIRMED" => null,
                "CANCELED" => null,
                "DELETED" => null,
                "NAME" => null,
                "TYPE" => null,
                "SRC_VAULT_ID" => null,
                "DST_VAULT_ID" => null,
                "CATEGORY_ID" => null,
                "RESPONSIBLE_ID" => null,
                "AMOUNT" => null,
                "ENTITY_CRM" => null,
                "ENTITY_TYPE_ID" => null,
                "ENTITY_ID" => null,
                "COMMENT" => null,
                "MODE_CREATE" => null,
            ];

            $arCurrentActivity = &CBPWorkflowTemplateLoader::FindActivityByName(
                $arWorkflowTemplate, $activityName);

            if (is_array($arCurrentActivity['Properties'])) {
                $arCurrentValues = array_merge($arCurrentValues, $arCurrentActivity['Properties']);
            }

            if (is_array($arCurrentValues['RESPONSIBLE_ID']))
                $arCurrentValues['RESPONSIBLE_ID'] = CBPHelper::UsersArrayToString($arCurrentValues['RESPONSIBLE_ID'], $arWorkflowTemplate, $documentType);
        }

        if (!Loader::includeModule('itbizon.finance'))
            throw new Exception(GetMessage('ACTIVITY.FINANCE.OPERATION.ERROR.LOAD_FINANCE'));
        if (!Loader::IncludeModule('crm'))
            throw new Exception(GetMessage('ACTIVITY.FINANCE.OPERATION.ERROR.LOAD_CRM'));

        $crmList = [
            CCrmOwnerType::Lead => [
                'NAME' => CCrmOwnerType::LeadName,
                'DESCRIPTION' => CCrmOwnerType::GetDescription(CCrmOwnerType::Lead),
            ],
            CCrmOwnerType::Deal => [
                'NAME' => CCrmOwnerType::DealName,
                'DESCRIPTION' => CCrmOwnerType::GetDescription(CCrmOwnerType::Deal),
            ],
            CCrmOwnerType::Company => [
                'NAME' => CCrmOwnerType::CompanyName,
                'DESCRIPTION' => CCrmOwnerType::GetDescription(CCrmOwnerType::Company),
            ],
            CCrmOwnerType::Contact => [
                'NAME' => CCrmOwnerType::ContactName,
                'DESCRIPTION' => CCrmOwnerType::GetDescription(CCrmOwnerType::Contact),
            ],
        ];

        $type = OperationTable::getType();

        $vaults = [];
        $vaultList = VaultTable::getList([
            'select' => ['ID', 'NAME']
        ]);
        while ($vault = $vaultList->fetchObject())
            $vaults[$vault->getId()] = $vault->getName();

        $categories = [];
        $categoryList = OperationCategoryTable::getList([
            'select' => ['*'],
        ]);
        while ($category = $categoryList->fetchObject()) {
            if ($category->getAllowTransfer())
                $categories['ALLOW_TRANSFER'][$category->getId()] = $category->getName();
            if ($category->getAllowOutgo())
                $categories['ALLOW_OUTGO'][$category->getId()] = $category->getName();
            if ($category->getAllowIncome())
                $categories['ALLOW_INCOME'][$category->getId()] = $category->getName();
        }

        $mode = [
            'C' => GetMessage('ACTIVITY.FINANCE.OPERATION.MODE.C'),
            'CW' => GetMessage('ACTIVITY.FINANCE.OPERATION.MODE.CW'),
            'CP' => GetMessage('ACTIVITY.FINANCE.OPERATION.MODE.CP'),
        ];

        $lang = Bitrix\Main\Application::getInstance()->getContext()->getLanguage();
        $entityId = \ItBizon\Finance\Model\OperationTable::getUfId();
        $manager = new \CUserTypeManager();
        $userFields = $manager->GetUserFields($entityId, 0, $lang);

        $runtime = CBPRuntime::GetRuntime();
        return $runtime->ExecuteResourceFile(__FILE__, "properties_dialog.php",
            array(
                "arCurrentValues" => $arCurrentValues,
                "formName" => $formName,
                "formFieldsData" => [
                    'VAULT_LIST' => $vaults,
                    'TYPE_LIST' => $type,
                    'CATEGORY_LIST' => $categories,
                    'MODE_CREATE' => $mode,
                    'ENTITY_TYPE_LIST' => $crmList,
                    'USER_FIELDS' => $userFields,
                ]
            ));
    }

    /**
     * @param $documentType
     * @param $activityName
     * @param $arWorkflowTemplate
     * @param $arWorkflowParameters
     * @param $arWorkflowVariables
     * @param $arCurrentValues
     * @param $arErrors
     * @return bool
     * @throws \Bitrix\Main\LoaderException
     */
    public static function GetPropertiesDialogValues(
        $documentType,
        $activityName,
        &$arWorkflowTemplate,
        &$arWorkflowParameters,
        &$arWorkflowVariables,
        $arCurrentValues,
        &$arErrors
    )
    {
        if (!Loader::includeModule('itbizon.finance'))
            throw new Exception(GetMessage('ACTIVITY.FINANCE.OPERATION.ERROR.LOAD_FINANCE'));
        if (!Loader::IncludeModule('crm'))
            throw new Exception(GetMessage('ACTIVITY.FINANCE.OPERATION.ERROR.LOAD_CRM'));

        $entityId = \ItBizon\Finance\Model\OperationTable::getUfId();
        $manager = new \CUserTypeManager();
        $userFields = $manager->GetUserFields($entityId, 0);

        $fieldsSelect = [
            'TYPE',
            'SRC_VAULT_ID',
            'DST_VAULT_ID',
            'CATEGORY_ID',
            'ENTITY_TYPE_ID',
            'MODE_CREATE',
        ];

        $arProperties = [
            "NAME" => $arCurrentValues['NAME'],
            "AMOUNT" => $arCurrentValues['AMOUNT'],
            "ENTITY_ID" => $arCurrentValues['ENTITY_ID'],
            "ENTITY_CRM" => $arCurrentValues['ENTITY_CRM'],
            "COMMENT" => $arCurrentValues['COMMENT'],
            "RESPONSIBLE_ID" => CBPHelper::UsersStringToArray($arCurrentValues['RESPONSIBLE_ID'], $documentType, $arErrors),
        ];

        $fieldsSelect = array_merge($fieldsSelect, array_keys($userFields));

        foreach ($fieldsSelect as $field) {
            $arProperties[$field] = !empty($arCurrentValues[$field]) ? $arCurrentValues[$field] : $arCurrentValues[$field . "_TEXT"];

            if (isset($arCurrentValues[$field . "_TEXT"]))
                $arProperties[$field . "_TEXT"] = $arCurrentValues[$field . "_TEXT"];
        }

        $arErrors = self::ValidateProperties($arProperties, new CBPWorkflowTemplateUser(CBPWorkflowTemplateUser::CurrentUser));
        if (count($arErrors) > 0)
            return false;

        $arCurrentActivity = &CBPWorkflowTemplateLoader::FindActivityByName($arWorkflowTemplate, $activityName);
        $arCurrentActivity['Properties'] = $arProperties;

        return true;
    }
}
