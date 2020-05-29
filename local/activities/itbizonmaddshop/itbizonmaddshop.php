<?php
defined('B_PROLOG_INCLUDED') || die();

use Bitrix\Bizproc\FieldType;
use Bitrix\Main\Loader;
use Itbizon\Meleshev\Model\ShopTable;

    /**
 * Действие "Звонок клиенту с анкетой".
 */
class CBPItBizonMAddShop extends CBPActivity
{

    /**
     * Инициализирует действие.
     *
     * @param
     *          $name
     */
    public function __construct ($name)
    {
        parent::__construct($name);
        $this->arProperties = [
            "CREATOR_ID"    => "",
            "TARGET_ID"     => "",
        ];

        $this->SetPropertiesTypes([
            'CREATOR_ID' => [
                'Type' => FieldType::USER
            ],
            'TARGET_ID' => [
                'Type' => FieldType::USER
            ]
        ]);
    }

    /**
     * Начинает выполнение действия.
     *
     * @return int Константа CBPActivityExecutionStatus::*.
     * @throws Exception
     */
    public function Execute ()
    {
        $this->WriteToTrackingService("Начало трудоемкой работы");

        try {
            if (!Loader::includeModule('itbizon.meleshev')) {
                throw new Exception('Модуль itbizon.meleshev не подключен');
            }

            $data = [
                'TITLE' => 'New shop',
                'CREATOR_ID' => '',
            ];

            $this->WriteToTrackingService("Начало безумного сохранения");

            ShopTable::add($data);
        } catch (Exception $e) {
            $this->WriteToTrackingService($e->getMessage());
        }
        return CBPActivityExecutionStatus::Closed;
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
                "CREATOR_ID" => "",
                "TARGET_ID"  => "",
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
        if (!empty($arErrors)) {
            return false;
        }

        $arCurrentActivity = &CBPWorkflowTemplateLoader::FindActivityByName(
            $arWorkflowTemplate,
            $activityName);

        $property = [
            "CREATOR_ID" => CBPHelper::UsersArrayToString($arCurrentValues['CREATOR_ID'], $arWorkflowTemplate, $documentType),
            "TARGET_ID"  => CBPHelper::UsersArrayToString($arCurrentValues['TARGET_ID'], $arWorkflowTemplate, $documentType),
        ];

        $arCurrentActivity['Properties'] = $property;
        return true;
    }
}