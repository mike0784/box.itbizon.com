<?php

use Bitrix\Bizproc\FieldType;
use Bitrix\Main\Loader;
use Itbizon\Meleshev\Model\AutoTable;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * Class CBPItBizonSendLeadRest
 */
class CBPItBizonSendLeadRest extends CBPActivity
{
    /**
     * CBPItBizonSendLeadRest constructor.
     * @param $name
     */
    public function __construct ($name)
    {
        parent::__construct($name);
        $this->arProperties = [
            'ID'            => null,
            'REST_PATH'     => null,
            'TITLE'         => null,
            'NAME'          => null,
            'LAST_NAME'     => null,
            'SECOND_NAME'   => null,
            'SOURCE_ID'     => null,
            'PHONE'         => null,
            'EMAIL'         => null,
            'COMMENT'       => null,
            'USER_FIELD'    => "[]",
        ];

        $this->SetPropertiesTypes([
            'ID' => [
                'Type' => FieldType::INT
            ],
            'REST_PATH' => [
                'Type' => FieldType::STRING
            ],
            'TITLE' => [
                'Type' => FieldType::STRING
            ],
            'NAME' => [
                'Type' => FieldType::STRING
            ],
            'LAST_NAME' => [
                'Type' => FieldType::STRING
            ],
            'SECOND_NAME' => [
                'Type' => FieldType::STRING
            ],
            'SOURCE_ID' => [
                'Type' => FieldType::STRING
            ],
            'PHONE' => [
                'Type' => FieldType::STRING
            ],
            'EMAIL' => [
                'Type' => FieldType::STRING
            ],
            'COMMENT' => [
                'Type' => FieldType::STRING
            ],
            'USER_FIELD' => [
                'Type' => FieldType::STRING
            ],
        ]);
    }

    /**
     * @return int
     */
    public function Execute ()
    {
        try {
            $uf = (array)json_decode($this->USER_FIELD);

            $phoneData = [];
            $phones = explode(',', $this->PHONE);
            foreach ($phones as $phone)
                $phoneData[] = ["VALUE" => $phone, "VALUE_TYPE" => "WORK"];

            $emailData = [];
            $emails = explode(',', $this->EMAIL);
            foreach ($emails as $email)
                $emailData[] = ["VALUE" => $email, "VALUE_TYPE" => "WORK"];

            $queryData = http_build_query([
                'fields' => array_merge($uf, [
                    "TITLE" => $this->NAME.' '.$this->LAST_NAME,
                    "NAME" => $this->NAME,
                    "LAST_NAME" => $this->LAST_NAME,
                    "SECOND_NAME" => $this->SECOND_NAME,
                    "STATUS_ID" => "NEW",
                    "SOURCE_ID" =>  $this->SOURCE_ID,
                    "COMMENTS" =>  $this->COMMENT,
                    "OPENED" => "Y",
                    "ASSIGNED_BY_ID" => 9, // change
                    "PHONE" => $phoneData,
                    "EMAIL" => $emailData,
                ]),
                'params' => ["REGISTER_SONET_EVENT" => "Y"]
            ]);

            /**
             * @var stdClass $obj
             */

            $result = file_get_contents($this->REST_PATH . 'crm.lead.add.json?' . $queryData);
            $obj = json_decode($result);

            $this->ID = $obj->result;
        } catch (\Exception $e) {
            $this->WriteToTrackingService($e->getMessage());
        }

        return CBPActivityExecutionStatus::Closed;
    }

    /**
     * @param array $arTestProperties
     * @param CBPWorkflowTemplateUser|null $user
     * @return array
     */
    public static function ValidateProperties($arTestProperties = array(), CBPWorkflowTemplateUser $user = null)
    {
        $arErrors = [];

        $checkField = [
            'REST_PATH',
            'TITLE',
        ];

        foreach ($checkField as $fieldName) {
            if (!array_key_exists($fieldName, $arTestProperties) || empty($arTestProperties[$fieldName])) {
                $arErrors[] = [
                    "code" => "Empty",
                    "parameter" => $fieldName,
                    "message" => 'Поле '.$fieldName.' не может быть пустым'
                ];
            }
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
                'REST_PATH'     => null,
                'TITLE'         => null,
                'NAME'          => null,
                'LAST_NAME'     => null,
                'SECOND_NAME'   => null,
                'SOURCE_ID'     => null,
                'PHONE'         => null,
                'EMAIL'         => null,
                'COMMENT'       => null,
                'USER_FIELD'    => "[]",
            ];

            $arCurrentActivity = &CBPWorkflowTemplateLoader::FindActivityByName(
                $arWorkflowTemplate, $activityName);

            if (is_array($arCurrentActivity['Properties'])) {
                $arCurrentValues = array_merge($arCurrentValues, $arCurrentActivity['Properties']);
            }
        }

        $arCurrentValues['USER_FIELD'] = (array)json_decode($arCurrentValues['USER_FIELD']);

        $runtime = CBPRuntime::GetRuntime();
        return $runtime->ExecuteResourceFile(__FILE__, "properties_dialog.php",
            array(
                "arCurrentValues" => $arCurrentValues,
                "formName" => $formName,
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
            'REST_PATH'     => $arCurrentValues['REST_PATH'],
            'TITLE'         => $arCurrentValues['TITLE'],
            'NAME'          => $arCurrentValues['NAME'],
            'LAST_NAME'     => $arCurrentValues['LAST_NAME'],
            'SECOND_NAME'   => $arCurrentValues['SECOND_NAME'],
            'SOURCE_ID'     => $arCurrentValues['SOURCE_ID'],
            'PHONE'         => $arCurrentValues['PHONE'],
            'EMAIL'         => $arCurrentValues['EMAIL'],
            'COMMENT'       => $arCurrentValues['COMMENT'],
            'USER_FIELD'    => json_encode(array_combine(
                $arCurrentValues['UF_KEYS'],
                $arCurrentValues['UF_VALS']
            )),
        ];

        $arCurrentActivity['Properties'] = $property;
        return true;
    }
}
