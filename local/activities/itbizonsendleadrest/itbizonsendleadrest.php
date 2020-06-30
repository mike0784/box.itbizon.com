<?php

use Bitrix\Bizproc\FieldType;
use Bitrix\Main\Loader;
use Itbizon\Meleshev\Model\AutoTable;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * Class CBPItBizonSendLeadRest
 * @property mixed|null USER_FIELD
 * @property mixed|null PHONE
 * @property mixed|null EMAIL
 * @property mixed|null TITLE
 * @property mixed|null NAME
 * @property mixed|null LAST_NAME
 * @property mixed|null SECOND_NAME
 * @property mixed|null SOURCE_ID
 * @property mixed|null COMMENT
 * @property mixed|null RESPONSIBLE_ID
 * @property mixed|null REST_PATH
 * @property mixed|null ID
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
            'RESPONSIBLE_ID'=> null,
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
            'RESPONSIBLE_ID' => [
                'Type' => FieldType::INT
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
            if(!is_array($this->PHONE)) {
                $phones = explode(';', $this->PHONE);
                foreach ($phones as $phone){
                    $partPhone = explode(': ', $phone);
                    $phoneData[] = ["VALUE" => isset($partPhone[1]) ? $partPhone[1] : $phone, "VALUE_TYPE" => "WORK"];
                }
            } else {
                if(array_key_exists('PHONE', $this->PHONE))
                    $phoneData = array_values($this->PHONE['PHONE']);
                else
                    $phoneData = array_values($this->PHONE);
            }

            $emailData = [];
            if(!is_array($this->EMAIL)) {
                $emails = explode(';', $this->EMAIL);
                foreach ($emails as $email) {
                    $partEmail = explode(': ', $email);
                    $emailData[] = ["VALUE" => isset($partEmail[1]) ? $partEmail[1] : $email, "VALUE_TYPE" => "WORK"];
                }
            } else {
                if(array_key_exists('EMAIL', $this->EMAIL))
                    $emailData = array_values($this->EMAIL['EMAIL']);
                else
                    $emailData = array_values($this->EMAIL);
            }

            $queryData = http_build_query([
                'fields' => array_merge($uf, [
                    "TITLE" => $this->TITLE,
                    "NAME" => $this->NAME,
                    "LAST_NAME" => $this->LAST_NAME,
                    "SECOND_NAME" => $this->SECOND_NAME,
                    "STATUS_ID" => "NEW",
                    "SOURCE_ID" =>  $this->SOURCE_ID,
                    "COMMENTS" =>  $this->COMMENT,
                    "OPENED" => "Y",
                    "ASSIGNED_BY_ID" => $this->RESPONSIBLE_ID,
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
        } catch (Exception $e) {
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
                'RESPONSIBLE_ID'=> null,
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
            'RESPONSIBLE_ID'=> $arCurrentValues['RESPONSIBLE_ID'],
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
