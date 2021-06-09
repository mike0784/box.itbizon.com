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
		// ID, ACTIVE, NAME, LAST_NAME, SECOND_NAME, EMAIL, PERSONAL_*, WORK_*, + все пользовательские поля // fixme

	    $res = [
            'ID' => [
                'Name'  => 'ID',
                'Type'  => FieldType::INT,
            ],
		    'ACTIVE' =>[
			    'Name' => 'Активность',
			    'Type' => 'bool',
		    ],
            'NAME' => [
                'Name'  => 'Имя',
                'Type'  => FieldType::STRING,
            ],
		    'LAST_NAME' => [
			    'Name'  => 'Фамилия',
			    'Type'  => FieldType::STRING,
		    ],
		    'SECOND_NAME' => [
			    'Name' => 'Отчество',
			    'Type' => 'string',
		    ],
		    'PERSONAL_PROFESSION' => [
			    'Name' => 'Профессия PERSONAL_PROFESSION',
			    'Type' => FieldType::STRING,
		    ],
		    'PERSONAL_PHONE' => [
			    'Name' => 'Личный телефон PERSONAL_PHONE',
			    'Type' => FieldType::STRING,
		    ],
		    'PERSONAL_MOBILE' => [
			    'Name' => 'Личный мобильный PERSONAL_MOBILE',
			    'Type' => FieldType::STRING,
		    ],
		    'PERSONAL_WWW' => [
			    'Name' => 'Личный сайт PERSONAL_WWW',
			    'Type' => FieldType::STRING,
		    ],
		    'PERSONAL_ICQ' => [
			    'Name' => 'Личный ICQ PERSONAL_ICQ',
			    'Type' => FieldType::STRING,
		    ],
		    'PERSONAL_FAX' => [
			    'Name' => 'Личный факс PERSONAL_FAX',
			    'Type' => FieldType::STRING,
		    ],
		    'PERSONAL_PAGER' => [
			    'Name' => 'Личный пейджер PERSONAL_PAGER',
			    'Type' => FieldType::STRING,
		    ],
		    'PERSONAL_STREET' => [
			    'Name' => 'Личный адрес, улица,дом PERSONAL_STREET',
			    'Type' => FieldType::TEXT,
		    ],
		    'PERSONAL_MAILBOX' => [
			    'Name' => 'Личный адрес, почтовый ящик PERSONAL_MAILBOX',
			    'Type' => FieldType::STRING,
		    ],
		    'PERSONAL_CITY' => [
			    'Name' => 'Личный адрес, город PERSONAL_CITY',
			    'Type' => FieldType::STRING,
		    ],
		    'PERSONAL_STATE' => [
			    'Name' => 'Личный адрес, область,край PERSONAL_STATE',
			    'Type' => FieldType::STRING,
		    ],
		    'PERSONAL_ZIP' => [
			    'Name' => 'Личный адрес, индекс PERSONAL_ZIP',
			    'Type' => FieldType::STRING,
		    ],
		    'PERSONAL_COUNTRY' => [
			    'Name' => 'Личный адрес, страна PERSONAL_COUNTRY',
			    'Type' => FieldType::STRING,
		    ],
		    'PERSONAL_BIRTHDAY' => [
			    'Name' => 'День рождения PERSONAL_BIRTHDAY',
			    'Type' => FieldType::DATE,
		    ],
		    'PERSONAL_GENDER' => [
			    'Name' => 'Пол PERSONAL_GENDER',
			    'Type' => FieldType::STRING,
		    ],
		    'PERSONAL_PHOTO' => [
			    'Name' => 'Личное фото PERSONAL_PHOTO',
			    'Type' => FieldType::INT,
		    ],
		    'PERSONAL_NOTES' => [
			    'Name' => 'Личное, заметки PERSONAL_NOTES',
			    'Type' => FieldType::TEXT,
		    ],
		    'WORK_COMPANY' => [
			    'Name' => 'Название компании WORK_COMPANY',
			    'Type' => FieldType::STRING,
		    ],
		    'WORK_DEPARTMENT' => [
			    'Name' => 'Отдел WORK_DEPARTMENT',
			    'Type' => FieldType::STRING,
		    ],
		    'WORK_PHONE' => [
			    'Name' => 'Рабочий телефон WORK_PHONE',
			    'Type' => FieldType::STRING,
		    ],
		    'WORK_POSITION' => [
			    'Name' => 'Должность WORK_POSITION',
			    'Type' => FieldType::STRING,
		    ],
		    'WORK_WWW' => [
			    'Name' => 'Рабочийй сайт WORK_WWW',
			    'Type' => FieldType::STRING,
		    ],
		    'WORK_FAX' => [
			    'Name' => 'Рабочий факс WORK_FAX',
			    'Type' => FieldType::STRING,
		    ],
		    'WORK_PAGER' => [
			    'Name' => 'Рабочий пейджер WORK_PAGER',
			    'Type' => FieldType::STRING,
		    ],
		    'WORK_STREET' => [
			    'Name' => 'Рабочий адрес, улица,дом WORK_STREET',
			    'Type' => FieldType::TEXT,
		    ],
		    'WORK_MAILBOX' => [
			    'Name' => 'Рабочий адрес, почтовый ящик WORK_MAILBOX',
			    'Type' => FieldType::STRING,
		    ],
		    'WORK_CITY' => [
			    'Name' => 'Рабочий адрес, город WORK_CITY',
			    'Type' => FieldType::STRING,
		    ],
		    'WORK_STATE' => [
			    'Name' => 'Рабочий адрес, область,край WORK_STATE',
			    'Type' => FieldType::STRING,
		    ],
		    'WORK_ZIP' => [
			    'Name' => 'Рабочий адрес, индекс WORK_ZIP',
			    'Type' => FieldType::STRING,
		    ],
		    'WORK_COUNTRY' => [
			    'Name' => 'Рабочий адрес, страна WORK_COUNTRY',
			    'Type' => FieldType::STRING,
		    ],
		    'WORK_PROFILE' => [
			    'Name' => 'Направление деятельности WORK_PROFILE',
			    'Type' => FieldType::TEXT,
		    ],
		    'WORK_LOGO' => [
			    'Name' => 'Логотип WORK_LOGO',
			    'Type' => FieldType::INT,
		    ],
		    'WORK_NOTES' => [
			    'Name' => 'Работа, заметки WORK_NOTES',
			    'Type' => FieldType::TEXT,
		    ],

	    ];

	    // User Fields
	    $fields = (new \CUserTypeManager())->GetUserFields('USER', 0, Bitrix\Main\Application::getInstance()->getContext()->getLanguage());

	    foreach($fields as $name => $item)
	    {
		    $type = FieldType::INT; // fixme default value

		    if ($item['USER_TYPE_ID'] == 'string'
			    || $item['USER_TYPE_ID'] == 'string_formatted'
			    || $item['USER_TYPE_ID'] == 'url'
			    || $item['USER_TYPE_ID'] == 'crm') {
			    $type = FieldType::STRING;
		    } elseif ($item['USER_TYPE_ID'] == 'int'
			    || $item['USER_TYPE_ID'] == 'enumeration') {
			    $type = FieldType::INT;
		    } elseif ($item['USER_TYPE_ID'] == 'date') {
			    $type = FieldType::DATE;
		    } elseif ($item['USER_TYPE_ID'] == 'datetime') {
			    $type = FieldType::DATETIME;
		    } elseif ($item['USER_TYPE_ID'] == 'double') {
			    $type = FieldType::DOUBLE;
		    } elseif ($item['USER_TYPE_ID'] == 'boolean') {
			    $type = FieldType::BOOL;
		    };

		    $res[$name] = ['Name' => $item['EDIT_FORM_LABEL'] .' '.$name, 'Type' => $type] ;
	    }

	    return $res;
    }
}