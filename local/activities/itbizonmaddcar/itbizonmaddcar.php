<?php

use Bitrix\Bizproc\FieldType;
use Bitrix\Main\Loader;
use Itbizon\Meleshev\Model\AutoTable;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class CBPItBizonMAddCar extends CBPActivity implements IBPEventActivity, IBPActivityExternalEventListener
{
    const ACTIVITY = 'RequestInformationActivity';
    private $taskId = 0;
    private $shopId = 0;
    private $creatorId = 0;
    private $targetId = 0;

    public function __construct ($name)
    {
        parent::__construct($name);
        $this->arProperties = [
            "AutoId"        => null,
            "SHOP_ID"       => null,
            "TITLE"         => "",
            "MARK"          => "",
            "MODEL"         => "",
            "CREATOR_ID"    => "",
            "VALUE"         => 0,
            "IS_USED"       => 'Y',
            "COMMENT"       => "",
            "TARGET_ID"     => "",
        ];

        $this->SetPropertiesTypes([
            'CREATOR_ID' => [
                'Type' => FieldType::USER
            ],
            'TARGET_ID' => [
                'Type' => FieldType::USER
            ],
            'SHOP_ID' => [
                'Type' => FieldType::INT
            ]
        ]);

    }


    public function Execute ()
    {
        $this->Subscribe($this);

        return CBPActivityExecutionStatus::Executing;
    }

    public function Subscribe(IBPActivityExternalEventListener $eventHandler)
    {
        if ($eventHandler == null)
            throw new Exception("eventHandler");

        try {

            $this->WriteToTrackingService('We are in the task code');

            $rootActivity = $this->GetRootActivity();
            $documentId = $rootActivity->GetDocumentId();

            foreach ($documentId as $key => $value) {
                $this->WriteToTrackingService("KEY:$key  and VALUE: $value ");
            }
            $this->WriteToTrackingService("{$this->arProperties['TARGET_ID']} || {$this->arProperties['CREATOR_ID']} || $documentId");


            $this->targetId = intval(CBPHelper::ExtractUsers($this->arProperties['TARGET_ID'], $documentId, true));
            $this->creatorId = intval(CBPHelper::ExtractUsers($this->arProperties['CREATOR_ID'], $documentId, true));
            $this->shopId = $this->SHOP_ID;

            $runtime = CBPRuntime::GetRuntime();
            $documentService = $runtime->GetService("DocumentService");

            $this->WriteToTrackingService('Task is begining 1. ' . $this->arProperties['CREATOR_ID']);

            $arParameters["DOCUMENT_ID"]        = $documentId;
            $arParameters["DOCUMENT_URL"]       = $documentService->GetDocumentAdminPage($documentId);
            $arParameters["DOCUMENT_TYPE"]      = $this->GetDocumentType();
            $arParameters["FIELD_TYPES"]        = $documentService->GetDocumentFieldTypes($arParameters["DOCUMENT_TYPE"]);
            $arParameters["TaskButtonMessage"]  = "Сохранить";
            $arParameters["ShowComment"]        = "N";
            $arParameters["REQUEST"]            = [];

            $arParameters["REQUEST"][] = [
                "Title"       => "Марка автомобиля",
                "Name"        => "MARK",
                "Description" => "",
                "Type"        => "string",
                "Required"    => "Y",
                "Multiple"    => "N",
                "Options"     => null,
                "Default"     => "",
            ];

            $arParameters["REQUEST"][] = [
                "Title"       => "Модель автомобиля",
                "Name"        => "MODEL",
                "Description" => "",
                "Type"        => "string",
                "Required"    => "Y",
                "Multiple"    => "N",
                "Options"     => null,
                "Default"     => "",
            ];

            $arParameters["REQUEST"][] = [
                "Title"       => "Id магазина",
                "Name"        => "SHOP_ID",
                "Description" => "Если не указано, авто будет добавлено в магазин с ID = 1",
                "Type"        => "string",
                "Required"    => "Y",
                "Multiple"    => "N",
                "Options"     => null,
                "Default"     => 1,
            ];

            $arParameters["REQUEST"][] = [
                "Title"       => "Стоимость в копейках",
                "Name"        => "VALUE",
                "Description" => "",
                "Type"        => "int",
                "Required"    => "Y",
                "Multiple"    => "N",
                "Options"     => null,
                "Default"     => "0",
            ];

            $arParameters["REQUEST"][] = [
                "Title"       => "Комментарий к автомобилю",
                "Name"        => "COMMENT",
                "Description" => "",
                "Type"        => "text",
                "Required"    => "N",
                "Multiple"    => "N",
                "Options"     => null,
                "Default"     => "",
            ];

            $taskService = $this->workflow->GetService("TaskService");
            $this->WriteToTrackingService('Task is begining 2. cid = ' . $this->creatorId . " or {$this->arProperties['TARGET_ID']} and tid = $this->targetId or {$this->arProperties['TARGET_ID']}");
            $this->taskId = $taskService->CreateTask(
                [
                    "USERS"             => 11,
                    "WORKFLOW_ID"       => $this->GetWorkflowInstanceId(),
                    "ACTIVITY"          => static::ACTIVITY,
                    "ACTIVITY_NAME"     => $this->name,
                    "OVERDUE_DATE"      => 0,
                    "NAME"              => "Заполните данные об автомобиле",
                    "DESCRIPTION"       => "",
                    "PARAMETERS"        => $arParameters,
                    'DELEGATION_TYPE'   => (int)$this->DelegationType,
                    'DOCUMENT_NAME'     => $documentService->GetDocumentName($documentId)
                ]
            );
            $this->WriteToTrackingService('Task is begining 3');
            if($this->targetId) {
                if (!CModule::IncludeModule("im"))
                    return CBPActivityExecutionStatus::Closed;

                CIMNotify::Add(array(
                    "FROM_USER_ID" => $this->creatorId,
                    "TO_USER_ID" => $this->targetId,
                    "NOTIFY_TYPE" => 2,
                    "NOTIFY_MESSAGE" => "[URL=/company/personal/bizproc/" . $this->taskId . "/]Создано новое задание[/URL]",
                    "NOTIFY_MESSAGE_OUT" => "",
                    "NOTIFY_MODULE" => "bizproc",
                    "NOTIFY_EVENT" => "activity"
                ));
                $this->WriteToTrackingService('Task is begining 4');
            }

        } catch (Exception $e) {
            $this->WriteToTrackingService($e->getMessage());
        }
        $this->WriteToTrackingService('Task is begining 5. Finally');
        $this->workflow->AddEventHandler($this->name, $eventHandler);
    }

    public function Unsubscribe(IBPActivityExternalEventListener $eventHandler)
    {
        if ($eventHandler == null)
            throw new Exception("eventHandler");

        $taskService = $this->workflow->GetService("TaskService");
        $taskService->DeleteTask($this->taskId);

        $this->workflow->RemoveEventHandler($this->name, $eventHandler);
    }

    public function OnExternalEvent($eventParameters = array())
    {
        try {
            if (!Loader::includeModule('itbizon.meleshev'))
                throw new Exception('Модуль itbizon.meleshev не подключен');

            $shopId = intval($eventParameters['RESPONCE']['SHOP_ID']);

            $data = [
                'SHOP_ID'    => $shopId ? $shopId : 0,
                'MARK'       => $eventParameters['RESPONCE']['MARK'],
                'MODEL'      => $eventParameters['RESPONCE']['MODEL'],
                'VALUE'      => $eventParameters['RESPONCE']['VALUE'],
                'CREATOR_ID' => $this->creatorId,
                'IS_USED'    => $eventParameters['RESPONCE']['IS_USED'],
                'COMMENT'    => $eventParameters['RESPONCE']['COMMENT']
            ];

            $result = AutoTable::add($data);

            if (!$result->isSuccess()) {
                throw new Exception('Ошибка создания автомобиля');
            }

            $id = $result->getId();

            if($this->targetId) {
                // Notify
                if (!CModule::IncludeModule("im"))
                    return CBPActivityExecutionStatus::Closed;

                CIMNotify::Add(array(
                    "FROM_USER_ID" => $this->creatorId,
                    "TO_USER_ID" => $this->targetId,
                    "NOTIFY_TYPE" => 2,
                    "NOTIFY_MESSAGE" => "Автомобиль добавлен. ID = $id",
                    "NOTIFY_MESSAGE_OUT" => "",
                    "NOTIFY_MODULE" => "bizproc",
                    "NOTIFY_EVENT" => "activity"
                ));
            }

            $taskService = $this->workflow->GetService("TaskService");
            $taskService->MarkCompleted($this->taskId, $this->creator_id, CBPTaskUserStatus::Ok);

        } catch (Exception $e) {
            $this->WriteToTrackingService($e->getMessage());
        }
    }

    public static function ValidateProperties($arTestProperties = array(), CBPWorkflowTemplateUser $user = null)
    {
        $arErrors = [];

        if (!array_key_exists("SHOP_ID", $arTestProperties) || empty($arTestProperties["SHOP_ID"])) {
            $arErrors[] = [
                "code" => "Empty",
                "parameter" => "SHOP_ID",
                "message" => 'Поле ID магазина не может быть пустым'
            ];
        }
        return array_merge($arErrors, parent::ValidateProperties($arTestProperties, $user));
    }

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
                "SHOP_ID"    => "",
                "CREATOR_ID"    => "",
                "TARGET_ID"     => "",
            ];

            $arCurrentActivity = &CBPWorkflowTemplateLoader::FindActivityByName(
                $arWorkflowTemplate, $activityName);

            if (is_array($arCurrentActivity['Properties'])) {
                $arCurrentValues = array_merge($arCurrentValues, $arCurrentActivity['Properties']);
            }
        }

        $runtime = CBPRuntime::GetRuntime();
        return $runtime->ExecuteResourceFile(__FILE__, "properties_dialog.php",
            array(
                "arCurrentValues" => $arCurrentValues,
                "formName" => $formName
            ));
    }

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
        $arErrors = self::ValidateProperties(
            $arCurrentValues,
            new CBPWorkflowTemplateUser(CBPWorkflowTemplateUser::CurrentUser)
        );

        if (!empty($arErrors)) {
            return false;
        }

        $arCurrentActivity = &CBPWorkflowTemplateLoader::FindActivityByName(
            $arWorkflowTemplate,
            $activityName);

        $property = [
            "SHOP_ID"    => $arCurrentValues['SHOP_ID'],
            "CREATOR_ID" => CBPHelper::UsersArrayToString($arCurrentValues['CREATOR_ID'], $arWorkflowTemplate, $documentType),
            "TARGET_ID"  => CBPHelper::UsersArrayToString($arCurrentValues['TARGET_ID'], $arWorkflowTemplate, $documentType),
        ];

        $arCurrentActivity['Properties'] = $property;
        return true;
    }







}