<?php

use Bitrix\Bizproc\FieldType;
use Bitrix\Main\Loader;
use Itbizon\Template\SystemFines\Model\FinesTable;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class CBPItbizonAddInvoice extends CBPActivity
{
    public function __construct($name)
    {
        parent::__construct($name);
        $this->arProperties = array(
            "TITLE"         => "",
            "CREATOR_ID"    => "",
            "TARGET_ID"     => "",
            "COMMENT"       => "",
            'InvoiceID'     => null
        );

        $this->SetPropertiesTypes(
            array(
                'TITLE' => array(
                    'Type' => FieldType::STRING
                ),
                'CREATOR_ID' => array(
                    'Type' => FieldType::USER
                ),
                'TARGET_ID' => array(
                    'Type' => FieldType::USER
                ),
                'COMMENT' => array(
                    'Type' => FieldType::TEXT
                ),
            ));
    }

    public function Execute()
    {
        try {
            if (!Loader::includeModule('itbizon.kulakov'))
                throw new Exception('Модуль itbizon.kulakov не подключен');

            $target_id = intval(CBPHelper::ExtractUsers($this->arProperties['TARGET_ID'], $this->GetDocumentId(), true));
            $create_id = intval(CBPHelper::ExtractUsers($this->arProperties['CREATOR_ID'], $this->GetDocumentId(), true));

            $invoice = Itbizon\Kulakov\Orm\Manager::addInvoice(
                $this->arProperties['TITLE'],
                $this->arProperties['COMMENT'],
                $create_id
            );

            $this->InvoiceID = $invoice->get("ID");

            // Notify
            if (!CModule::IncludeModule("im"))
                return CBPActivityExecutionStatus::Closed;

            CIMNotify::Add(array(
                "FROM_USER_ID"          => $create_id,
                "TO_USER_ID"            => $target_id,
                "NOTIFY_TYPE"           => 2,
                "NOTIFY_MESSAGE"        => "Накладная добавлена ID " . $this->InvoiceID,
                "NOTIFY_MESSAGE_OUT"    => "",
                "NOTIFY_MODULE"         => "bizproc",
                "NOTIFY_EVENT"          => "activity"
            ));

            if ($this->InvoiceID == null) {
                throw new Exception('Ошибка создания накладной');
            }

        } catch (Exception $e) {
            $this->WriteToTrackingService($e->getMessage());
        }
        return CBPActivityExecutionStatus::Closed;
    }

    public static function ValidateProperties($arTestProperties = array(), CBPWorkflowTemplateUser $user = null)
    {
        $arErrors = array();

        if (!array_key_exists("TITLE", $arTestProperties) || empty($arTestProperties["TITLE"])) {
            $arErrors[] = array(
                "code" => "Empty",
                "parameter" => "TITLE",
                "message" => 'Поле название не может быть пустым'
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
                "TITLE"         => "",
                "CREATOR_ID"    => "",
                "TARGET_ID"     => "",
                "COMMENT"       => ""
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
            "TITLE" => $arCurrentValues['TITLE'],
            "TARGET_ID" => CBPHelper::UsersArrayToString($arCurrentValues['TARGET_ID'], $arWorkflowTemplate, $documentType),
            "CREATOR_ID" => CBPHelper::UsersArrayToString($arCurrentValues['CREATOR_ID'], $arWorkflowTemplate, $documentType),
            "COMMENT" => $arCurrentValues['COMMENT']
        );

        $arCurrentActivity['Properties'] = $property;
        return true;
    }
}
