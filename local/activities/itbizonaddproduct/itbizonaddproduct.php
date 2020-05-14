<?php

use Bitrix\Bizproc\FieldType;
use Bitrix\Main\Loader;
use Itbizon\Template\SystemFines\Model\FinesTable;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class CBPItbizonAddProduct extends CBPActivity implements IBPEventActivity, IBPActivityExternalEventListener
{
    const ACTIVITY = 'RequestInformationActivity';
    private $taskId = 0;
    private $creator_id = 0;
    private $target_id = 0;
    private $invoice_id = 0;


    public function __construct($name)
    {
        parent::__construct($name);
        $this->arProperties = array(
            "TITLE"         => "",
            "CREATOR_ID"    => "",
            "TARGET_ID"     => "",
            "COMMENT"       => "",
            "COUNT"         => 0,
            "VALUE"         => 0,
            "INVOICE_ID"    => null,
            "ProductID"     => null,

        );

        $this->SetPropertiesTypes(
            array(
                'CREATOR_ID' => array(
                    'Type' => FieldType::USER
                ),
                'INVOICE_ID' => array(
                    'Type' => FieldType::INT
                ),
                'TARGET_ID' => array(
                    'Type' => FieldType::USER
                ),
            ));


    }

    public function Execute()
    {
        $this->Subscribe($this);

        return CBPActivityExecutionStatus::Executing;

    }

    public function Subscribe(IBPActivityExternalEventListener $eventHandler)
    {
        if ($eventHandler == null)
            throw new Exception("eventHandler");

        try {

            $rootActivity = $this->GetRootActivity();
            $documentId = $rootActivity->GetDocumentId();

            $this->target_id = intval(CBPHelper::ExtractUsers($this->arProperties['TARGET_ID'], $documentId, true));
            $this->creator_id = intval(CBPHelper::ExtractUsers($this->arProperties['CREATOR_ID'], $documentId, true));
            $this->invoice_id = $this->INVOICE_ID;

            $runtime = CBPRuntime::GetRuntime();
            $documentService = $runtime->GetService("DocumentService");

            $arParameters["DOCUMENT_ID"]        = $documentId;
            $arParameters["DOCUMENT_URL"]       = $documentService->GetDocumentAdminPage($documentId);
            $arParameters["DOCUMENT_TYPE"]      = $this->GetDocumentType();
            $arParameters["FIELD_TYPES"]        = $documentService->GetDocumentFieldTypes($arParameters["DOCUMENT_TYPE"]);
            $arParameters["TaskButtonMessage"]  = "Сохранить";
            $arParameters["ShowComment"]        = "N";
            $arParameters["REQUEST"]            = array();

            $arParameters["REQUEST"][] = [
                "Title" => "ID Накладной",
                "Name" => "ID_INVOICE",
                "Description" => "Если поле не заполнено, будет использовано значение по умолчанию.",
                "Type" => "int",
                "Required" => "N",
                "Multiple" => "N",
                "Options" => null,
                "Default" => $this->invoice_id,
            ];

            $arParameters["REQUEST"][] = [
                "Title" => "Название товара",
                "Name" => "TITLE",
                "Description" => "",
                "Type" => "string",
                "Required" => "Y",
                "Multiple" => "N",
                "Options" => null,
                "Default" => "",
            ];

            $arParameters["REQUEST"][] = [
                "Title" => "Стоимость",
                "Name" => "VALUE",
                "Description" => "",
                "Type" => "int",
                "Required" => "Y",
                "Multiple" => "N",
                "Options" => null,
                "Default" => "0",
            ];

            $arParameters["REQUEST"][] = [
                "Title" => "Количество",
                "Name" => "COUNT",
                "Description" => "",
                "Type" => "int",
                "Required" => "Y",
                "Multiple" => "N",
                "Options" => null,
                "Default" => "0",
            ];

            $arParameters["REQUEST"][] = [
                "Title" => "Комментарий к товару",
                "Name" => "COMMENT",
                "Description" => "",
                "Type" => "text",
                "Required" => "N",
                "Multiple" => "N",
                "Options" => null,
                "Default" => "",
            ];

            $taskService = $this->workflow->GetService("TaskService");
            $this->taskId = $taskService->CreateTask(
                array(
                    "USERS"             => $this->creator_id,
                    "WORKFLOW_ID"       => $this->GetWorkflowInstanceId(),
                    "ACTIVITY"          => static::ACTIVITY,
                    "ACTIVITY_NAME"     => $this->name,
                    "OVERDUE_DATE"      => 0,
                    "NAME"              => "Заполните данные о товаре",
                    "DESCRIPTION"       => "",
                    "PARAMETERS"        => $arParameters,
                    'DELEGATION_TYPE'   => (int)$this->DelegationType,
                    'DOCUMENT_NAME'     => $documentService->GetDocumentName($documentId)
                )
            );

            if($this->target_id) {
                if (!CModule::IncludeModule("im"))
                    return CBPActivityExecutionStatus::Closed;

                CIMNotify::Add(array(
                    "FROM_USER_ID" => $this->creator_id,
                    "TO_USER_ID" => $this->target_id,
                    "NOTIFY_TYPE" => 2,
                    "NOTIFY_MESSAGE" => "[URL=/company/personal/bizproc/" . $this->taskId . "/]Создано новое задание[/URL]",
                    "NOTIFY_MESSAGE_OUT" => "",
                    "NOTIFY_MODULE" => "bizproc",
                    "NOTIFY_EVENT" => "activity"
                ));
            }

        } catch (Exception $e) {
            $this->WriteToTrackingService($e->getMessage());
        }
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
            if (!Loader::includeModule('itbizon.kulakov'))
                throw new Exception('Модуль itbizon.kulakov не подключен');

            $invoice_id_ = intval($eventParameters['RESPONCE']['ID_INVOICE']);

            $product = Itbizon\Kulakov\Orm\Manager::addProduct([
                'invoice_id'    => $invoice_id_ ? $invoice_id_ : $this->invoice_id,
                'creator_id'    => $this->creator_id,
                'title'         => $eventParameters['RESPONCE']['TITLE'],
                'value'         => intval($eventParameters['RESPONCE']['VALUE']),
                'count'         => intval($eventParameters['RESPONCE']['COUNT']),
                'comment'       => $eventParameters['RESPONCE']['COMMENT'],
            ]);

            $this->ProductID = $product->get("ID");

            if($this->target_id) {
                // Notify
                if (!CModule::IncludeModule("im"))
                    return CBPActivityExecutionStatus::Closed;

                CIMNotify::Add(array(
                    "FROM_USER_ID" => $this->creator_id,
                    "TO_USER_ID" => $this->target_id,
                    "NOTIFY_TYPE" => 2,
                    "NOTIFY_MESSAGE" => "Товар добавлен ID " . $this->ProductID,
                    "NOTIFY_MESSAGE_OUT" => "",
                    "NOTIFY_MODULE" => "bizproc",
                    "NOTIFY_EVENT" => "activity"
                ));
            }

            if ($this->ProductID == null) {
                throw new Exception('Ошибка создания накладной');
            }

            $taskService = $this->workflow->GetService("TaskService");
            $taskService->MarkCompleted($this->taskId, $this->creator_id, CBPTaskUserStatus::Ok);

        } catch (Exception $e) {
            $this->WriteToTrackingService($e->getMessage());
        }

    }

    public static function ValidateProperties($arTestProperties = array(), CBPWorkflowTemplateUser $user = null)
    {
        $arErrors = array();

        if (!array_key_exists("INVOICE_ID", $arTestProperties) || empty($arTestProperties["INVOICE_ID"])) {
            $arErrors[] = array(
                "code" => "NotExist",
                "parameter" => "INVOICE_ID",
                "message" => 'Поле ID накладной не может быть пустым'
            );
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
            $arCurrentValues = array(
                "CREATOR_ID"    => "",
                "TARGET_ID"     => "",
                "INVOICE_ID"    => "",
            );

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

        $property = array(
            "INVOICE_ID"    => $arCurrentValues['INVOICE_ID'],
            "TARGET_ID"     => CBPHelper::UsersArrayToString($arCurrentValues['TARGET_ID'], $arWorkflowTemplate, $documentType),
            "CREATOR_ID"    => CBPHelper::UsersArrayToString($arCurrentValues['CREATOR_ID'], $arWorkflowTemplate, $documentType),
        );

        $arCurrentActivity['Properties'] = $property;
        return true;
    }
}
