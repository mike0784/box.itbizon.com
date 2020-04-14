<?

use Bitrix\Bizproc\FieldType;
use Bitrix\Main\Loader;
use Itbizon\Template\SystemFines\Model\FinesTable;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class CBPItbizonaddfine extends CBPActivity
{
    public function __construct($name)
    {
        parent::__construct($name);
        $this->arProperties = array(
            "TITLE" => "",
            "VALUE" => "",
            "TARGET_ID" => "",
            "CREATOR_ID" => "",
            "COMMENT" => "",
            'ID' => null
        );

        $this->SetPropertiesTypes(
            array(
                'TITLE' => array(
                    'Type' => FieldType::STRING
                ),
                'VALUE' => array(
                    'Type' => FieldType::STRING
                ),
                'TARGET_ID' => array(
                    'Type' => FieldType::USER
                ),
                'CREATOR_ID' => array(
                    'Type' => FieldType::USER
                ),
                'COMMENT' => array(
                    'Type' => FieldType::TEXT
                )
            ));
    }

    public function Execute()
    {
        try {
            if (!Loader::includeModule('itbizon.template')) {
                throw new Exception('Модуль itbizon.template не подулючен');
            }

            $properties = [];
            foreach ($this->arProperties as $key => $val) {
                if ($key !== 'USER_ID') {
                    if ($key === 'TARGET_ID' || $key === 'CREATOR_ID') {
                        $properties[$key] = intval(CBPHelper::ExtractUsers($this->$key, $this->GetDocumentId(), true));
                    } else {
                        $properties[$key] = $this->$key;
                    }
                }
            }

            $fine = FinesTable::add($properties);
            if (!$fine->isSuccess()) {
                $this->WriteToTrackingService($fine->getErrorMessages());
                throw new Exception('Ошибка создания штрафа');
            }
            $this->ID = $fine->getObject()->getId();

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
                "message" => 'Поле тайтл не может быть пустым'
            );
        }

        if (!array_key_exists("VALUE", $arTestProperties) || empty($arTestProperties["VALUE"])) {
            $arErrors[] = array(
                "code" => "Empty",
                "parameter" => "TITLE",
                "message" => 'Поле VALUE не может быть пустым'
            );
        }

        if (!array_key_exists("TARGET_ID", $arTestProperties) || empty($arTestProperties["TARGET_ID"])) {
            $arErrors[] = array(
                "code" => "Empty",
                "parameter" => "TARGET_ID",
                "message" => 'Выбирите пользователя кому вы хотите создать штраф/бонус'
            );
        }

        if (!array_key_exists("CREATOR_ID", $arTestProperties) || empty($arTestProperties["CREATOR_ID"])) {
            $arErrors[] = array(
                "code" => "Empty",
                "parameter" => "CREATOR_ID",
                "message" => 'Поле тайтл не может быть пустым'
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
                "TITLE" => "",
                "VALUE" => "",
                "TARGET_ID" => "",
                "CREATOR_ID" => "",
                "COMMENT" => ""
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
            "VALUE" => $arCurrentValues['VALUE'],
            "TARGET_ID" => CBPHelper::UsersArrayToString($arCurrentValues['TARGET_ID'], $arWorkflowTemplate, $documentType),
            "CREATOR_ID" => CBPHelper::UsersArrayToString($arCurrentValues['CREATOR_ID'], $arWorkflowTemplate, $documentType),
            "COMMENT" => $arCurrentValues['COMMENT']
        );

        $arCurrentActivity['Properties'] = $property;
        return true;
    }
}

?>