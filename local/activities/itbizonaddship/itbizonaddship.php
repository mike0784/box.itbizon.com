<?php

use Bitrix\Bizproc\FieldType;
use Bitrix\Main\Loader;
use Itbizon\Kalinin\Model\Manager;
use Itbizon\Kalinin\Model\StationTable;

defined('B_PROLOG_INCLUDED') || die;

class CBPItbizonAddShip extends CBPActivity
{
    public function __construct($name)
    {
        parent::__construct($name);

        $this->arProperties = array(
            'NAME' => "",
            'CREATOR_ID' => "",
            'TARGET_ID' => "",
            'STATION_ID' => "",
            'MATERIALS' => "",
            'VALUE' => "",
            'IS_RELEASED' => "",
            'COMMENT' => "",
            'ShipId' => null
        );

        $this->SetPropertiesTypes(array(
            'NAME' => array(
                'Type' => FieldType::STRING
            ),
            'CREATOR_ID' => array(
                'Type' => FieldType::USER
            ),
            'TARGET_ID' => array(
                'Type' => FieldType::USER
            ),
            'STATION_ID' => array(
                'Type' => FieldType::INT
            ),
            'MATERIALS' => array(
                'Type' => FieldType::STRING
            ),
            'VALUE' => array(
                'Type' => FieldType::INT
            ),
            'IS_RELEASED' => array(
                'Type' => FieldType::BOOL
            ),
            'COMMENT' => array(
                'Type' => FieldType::TEXT
            ),
        ));
    }

    public function Execute()
    {
        try {
            if (!Loader::includeModule('itbizon.kalinin'))
                throw new Exception('Модуль itbizon.kalinin не подключен');

            $creator_id = intval(CBPHelper::ExtractUsers($this->arProperties['CREATOR_ID'], $this->GetDocumentId(), true));
            $target_id = intval(CBPHelper::ExtractUsers($this->arProperties['TARGET_ID'], $this->GetDocumentId(), true));
            $ship = Manager::createShip(
                $this->NAME,
                $this->MATERIALS,
                $this->VALUE,
                $this->STATION_ID,
                $creator_id,
                $this->IS_RELEASED,
                $this->COMMENT
            );

            $this->ShipId = $ship->get("ID");

            if (!CModule::IncludeModule("im"))
                return CBPActivityExecutionStatus::Closed;

            CIMNotify::Add(array(
                "FROM_USER_ID"          => $creator_id,
                "TO_USER_ID"            => $target_id,
                "NOTIFY_TYPE"           => 2,
                "NOTIFY_MESSAGE"        => "Создан корабль ID: " . $this->ShipId,
                "NOTIFY_MESSAGE_OUT"    => "",
                "NOTIFY_MODULE"         => "bizproc",
                "NOTIFY_EVENT"          => "activity"
            ));

            if ($this->ShipId == null) {
                throw new Exception('Ошибка создания станции');
            }

        } catch (Exception $e) {
            $this->WriteToTrackingService($e->getMessage());
        }
        return CBPActivityExecutionStatus::Closed;
    }

    public static function ValidateProperties($arTestProperties=array(), CBPWorkflowTemplateUser $user=null)
    {
        $arErrors = array();

        if (!array_key_exists("NAME", $arTestProperties) || empty($arTestProperties["NAME"])) {
            $arErrors[] = array(
                "code" => "Empty",
                "parameter" => "NAME",
                "message" => 'Поле название не может быть пустым'
            );
        }

        if (!array_key_exists("STATION_ID", $arTestProperties) || empty($arTestProperties["STATION_ID"]))
        {
            $arErrors[] = array(
                "code" => "Empty",
                "parameter" => "STATION_ID",
                "message" => 'Корабль должен быть приписан к какой-нибудь станции'
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

            $arCurrentActivity = &CBPWorkflowTemplateLoader::FindActivityByName(
                $arWorkflowTemplate, $activityName);

            $arCurrentValues = array(
                'NAME' => "",
                'CREATOR_ID' => "",
                'TARGET_ID' => "",
                'STATION_ID' => "",
                'MATERIALS' => "",
                'VALUE' => "",
                'IS_RELEASED' => "",
                'COMMENT' => "",
            );

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
            'NAME' => $arCurrentValues['NAME'],
            'CREATOR_ID' => CBPHelper::UsersArrayToString($arCurrentValues['CREATOR_ID'], $arWorkflowTemplate, $documentType),
            'TARGET_ID' => CBPHelper::UsersArrayToString($arCurrentValues['TARGET_ID'], $arWorkflowTemplate, $documentType),
            'STATION_ID' => $arCurrentValues['STATION_ID'],
            'MATERIALS' => $arCurrentValues['MATERIALS'],
            'VALUE' => $arCurrentValues['VALUE'],
            'IS_RELEASED' => $arCurrentValues['IS_RELEASED'],
            "COMMENT" => $arCurrentValues['COMMENT']
        );

        $arCurrentActivity['Properties'] = $property;
        return true;
    }
}
