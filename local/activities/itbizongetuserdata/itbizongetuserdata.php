<?
use \Bitrix\Bizproc\FieldType;
use Bitrix\Main\UserTable;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

/**
 * Class CBPItbizonGetUserData
 */
class CBPItbizonGetUserData extends CBPActivity
{
    /**
     * CBPItbizonGetUserData constructor.
     * @param $name
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->arProperties = self::getDefaultProperties();
    }

    /**
     * @return string[]
     */
    private static function getDefaultProperties()
    {
        return [
            'User' => ''
        ];
    }

    /**
     * @return int
     */
    public function Execute()
    {
        try {
            $userId = CBPHelper::ExtractUsers($this->User, $this->GetDocumentId(), true);

            $user = UserTable::getByPrimary($userId, ['select' => ['*', 'UF_*']])->fetchObject();
            if($user) {
                foreach(self::getFieldMap() as $fieldId => $field) {
                    $this->arProperties[$fieldId] = $user->get($fieldId);
                }
            } else {
                foreach(self::getFieldMap() as $fieldId => $field) {
                    $this->arProperties[$fieldId] = null;
                }
                throw new Exception('Сотрудник с ID ' . $userId . ' не найден');
            }
        } catch(Exception $e) {
            $this->WriteToTrackingService($e->getMessage());
        }
        return CBPActivityExecutionStatus::Closed;
    }

    /**
     * @param $documentType
     * @param $activityName
     * @param $arWorkflowTemplate
     * @param $arWorkflowParameters
     * @param $arWorkflowVariables
     * @param null $arCurrentValues
     * @param string $formName
     * @param null $popupWindow
     * @param null $currentSiteId
     * @return false|null|string
     */
    public static function GetPropertiesDialog($documentType, $activityName, $arWorkflowTemplate, $arWorkflowParameters, $arWorkflowVariables, $arCurrentValues = null, $formName = '', $popupWindow = null, $currentSiteId = null)
    {
        if (!is_array($arCurrentValues)) {
            $arCurrentValues = self::getDefaultProperties();
            $arCurrentActivity = &CBPWorkflowTemplateLoader::FindActivityByName($arWorkflowTemplate, $activityName);
            if(is_array($arCurrentActivity['Properties'])) {
                $arCurrentValues['User'] = CBPHelper::UsersArrayToString($arCurrentActivity['Properties']['User'], $arWorkflowTemplate, $documentType);
            }
        }

        $runtime = CBPRuntime::GetRuntime();
        return $runtime->ExecuteResourceFile(
            __FILE__,
            'properties_dialog.php',
            [
                'arCurrentValues' => $arCurrentValues,
                'formName'        => $formName,
            ]
        );
    }

    /**
     * @param array $arTestProperties
     * @param CBPWorkflowTemplateUser|null $user
     * @return array
     */
    public static function ValidateProperties($arTestProperties = array(), CBPWorkflowTemplateUser $user = null)
    {
        $arErrors = [];
        if(empty($arTestProperties['User'])) {
            $arErrors[] = [
                'code'    => 'InvalidUser',
                'message' => 'Пользователь не задан'
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
     * @param $arCurrentValues
     * @param $errors
     * @return bool
     */
    public static function GetPropertiesDialogValues($documentType, $activityName, &$arWorkflowTemplate, &$arWorkflowParameters, &$arWorkflowVariables, $arCurrentValues, &$errors)
    {
        $errors = [];
        $properties = [
            'User' => CBPHelper::UsersStringToArray($arCurrentValues['User'], $documentType, $arErrorsTmp),
            'InfoData' => self::getFieldMap()
        ];
        $errors = self::ValidateProperties($properties, new CBPWorkflowTemplateUser(CBPWorkflowTemplateUser::CurrentUser));
        if(count($errors) > 0) {
            return false;
        }
        $currentActivity = &CBPWorkflowTemplateLoader::FindActivityByName($arWorkflowTemplate, $activityName);
        $currentActivity['Properties'] = $properties;
        return true;
    }

    /**
     * @param $type
     * @return array
     */
    public static function getFieldMap()
    {
        return [
            'ID' => [
                'Name'  => 'ID',
                'Type'  => FieldType::INT,
            ],
            'NAME' => [
                'Name'  => 'Имя',
                'Type'  => FieldType::STRING,
            ],
            'LAST_NAME' => [
                'Name'  => 'Фамилия',
                'Type'  => FieldType::STRING,
            ],
        ];
    }
}