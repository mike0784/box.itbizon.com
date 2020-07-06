<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

use \Bitrix\Bizproc\FieldType;
use \Bitrix\Main\Loader;

class CBPItbAddEmployee extends CBPActivity
{
    protected static $data = [
        'LAST_NAME'         => ['TYPE' => FieldType::STRING, 'NAME'=>'Фамилия'],
        'NAME'              => ['TYPE' => FieldType::STRING, 'NAME'=>'Имя'],
        'SECOND_NAME'       => ['TYPE' => FieldType::STRING, 'NAME'=>'Отчество'],
        'PERSONAL_GENDER'   => ['TYPE' => FieldType::STRING, 'NAME'=>'Пол'],
        'PERSONAL_BIRTHDAY' => ['TYPE' => FieldType::DATE, 'NAME'=>'День рождения'],
        'LOGIN'             => ['TYPE' => FieldType::STRING, 'NAME'=>'Логин', 'REQUIRED'=>true],
        'EMAIL'             => ['TYPE' => FieldType::STRING, 'NAME'=>'Email', 'REQUIRED'=>true],
        'PASSWORD'          => ['TYPE' => FieldType::STRING, 'NAME'=>'Пароль', 'REQUIRED'=>true],
        'CONFIRM_PASSWORD'  => ['TYPE' => FieldType::STRING, 'NAME'=>'Подтверждение пароля', 'REQUIRED'=>true],
        'ACTIVE'            => ['TYPE' => FieldType::BOOL, 'NAME'=>'Активный'],
        'PHONE_NUMBER'      => ['TYPE' => FieldType::STRING, 'NAME'=>'Рабочий телефон'],
        'PERSONAL_PHONE'    => ['TYPE' => FieldType::STRING, 'NAME'=>'Личный телефон'],
        'PERSONAL_COUNTRY'  => ['TYPE' => FieldType::STRING, 'NAME'=>'Страна'],
        'PERSONAL_STATE'    => ['TYPE' => FieldType::STRING, 'NAME'=>'Область'],
        'PERSONAL_CITY'     => ['TYPE' => FieldType::STRING, 'NAME'=>'Город'],
        'PERSONAL_STREET'   => ['TYPE' => FieldType::STRING, 'NAME'=>'Улица'],
        'PERSONAL_MAILBOX'  => ['TYPE' => FieldType::STRING, 'NAME'=>'Почтовый адрес'],
        'PERSONAL_ZIP'      => ['TYPE' => FieldType::STRING, 'NAME'=>'Индекс'],
        'GROUP_ID'          => ['TYPE' => FieldType::TEXT, 'NAME'=>'Группы'],
        'UF_DEPARTMENT'     => ['TYPE' => FieldType::TEXT, 'NAME'=>'Подразделения'],
        'WORK_POSITION'     => ['TYPE' => FieldType::STRING, 'NAME'=>'Должность'],
    ];
    
    /**
     * CBPItbizonRatingActivity constructor.
     * @param $name
     * @throws Exception
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->arProperties = self::getDefaultProperties();
        $this->SetPropertiesTypes($this->arProperties);
    }
    
    
    /**
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws Exception
     */
    public static function getDefaultProperties()
    {
        $property = [];
        foreach (self::$data as $key => $field)
            $property[$key]['TYPE'] = $field['TYPE'];
    
        if (!Loader::includeModule('bizon.main'))
            throw new Exception('Error load module bizon.main');
        
        $utsUser = \Bizon\Main\UserFieldHelper::getUtsList('USER');
        foreach ($utsUser as $key => $item)
        {
            if($item['NAME'] == 'UF_DEPARTMENT')
                continue;
            if($item['NAME'])
                $property[$item['FIELD_NAME']]['TYPE'] = FieldType::STRING;
        }
        return $property;
    }
    
    /**
     * @return array
     */
    private function getFields()
    {
        $result = [];
        
        foreach ($this->arProperties as $key => $value)
        {
            if(isset(self::$data[$key]) && self::$data[$key]['TYPE'] == FieldType::STRING)
                $result[$key] = strval($this->$key);
            else
                $result[$key] = $this->$key;
        }
        return $result;
    }
    
    /**
     * @return int
     */
    public function Execute()
    {
        try
        {
            if (!Loader::includeModule('bizon.main'))
                throw new Exception('Error load module bizon.main');
            if (!Loader::includeModule('itbizon.kulakov'))
                throw new Exception('Error load module itbizon.kulakov');
            
            $fields = $this->getFields();
            $user = new CUser();
            $result = $user->Add($fields);
            
            if(!$result)
                $result = 0;
            
            $this->arProperties['EmployeeId'] = $result;
            
        } catch (Exception $e)
        {
            $this->WriteToTrackingService($e->getMessage());
        }
        return CBPActivityExecutionStatus::Closed;
    }
    
    
    /**
     * @param        $documentType
     * @param        $activityName
     * @param        $arWorkflowTemplate
     * @param        $arWorkflowParameters
     * @param        $arWorkflowVariables
     * @param null   $arCurrentValues
     * @param string $formName
     * @return false|string|null
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function GetPropertiesDialog($documentType, $activityName, $arWorkflowTemplate, $arWorkflowParameters, $arWorkflowVariables, $arCurrentValues = null, $formName = '')
    {
        $dialog = new \Bitrix\Bizproc\Activity\PropertiesDialog(__FILE__, array(
            'documentType'       => $documentType,
            'activityName'       => $activityName,
            'workflowTemplate'   => $arWorkflowTemplate,
            'workflowParameters' => $arWorkflowParameters,
            'workflowVariables'  => $arWorkflowVariables,
            'currentValues'      => $arCurrentValues,
            'formName'           => $formName,
            'siteId'             => 1,
        ));
    
        $arCurrentActivity = &CBPWorkflowTemplateLoader::FindActivityByName($arWorkflowTemplate, $activityName);

        if (is_array($arCurrentActivity['Properties']))
        {
            $arCurrentValues = self::getDefaultProperties();
            $arCurrentValues = array_merge($arCurrentValues, $arCurrentActivity['Properties']);
        }
        
        $runtime = CBPRuntime::GetRuntime();
        return $runtime->ExecuteResourceFile(__FILE__, "properties_dialog.php",
            array(
                "arCurrentValues" => $arCurrentValues,
                "formName" => $formName
            ));
    }
    
    /**
     * @param array                        $arTestProperties
     * @param CBPWorkflowTemplateUser|null $user
     * @return array
     */
    public static function ValidateProperties($arTestProperties = [], CBPWorkflowTemplateUser $user = null)
    {
        $arErrors = [];
        
        foreach (self::$data as $key => $field)
        {
            if(empty($arTestProperties[$key]) && $field['REQUIRED'])
                $arErrors[] = ['code'=>'InvalidName','message'=>'Некорректное поле - '.$field['NAME'] ?? $key];
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
     * @throws \Bitrix\Main\LoaderException
     */
    public static function GetPropertiesDialogValues($documentType, $activityName, &$arWorkflowTemplate, &$arWorkflowParameters, &$arWorkflowVariables, $arCurrentValues, &$errors)
    {
        $properties = self::getBaseValues($arCurrentValues);
        $properties = self::getUtsValues($arCurrentValues, $properties);
        $errors = [];
        $errors = self::ValidateProperties($properties, new CBPWorkflowTemplateUser(CBPWorkflowTemplateUser::CurrentUser));
        
        if (count($errors) > 0)
            return false;
        
        $arCurrentActivity = &CBPWorkflowTemplateLoader::FindActivityByName($arWorkflowTemplate, $activityName);
        $arCurrentActivity['Properties'] = $properties;
        
        return true;
    }
    
    /**
     * @param $arCurrentValues
     * @return array
     */
    private static function getBaseValues($arCurrentValues)
    {
        $properties = [];
        foreach (self::$data as $key => $field)
        {
            if($key == 'GROUP_ID' || $key == 'UF_DEPARTMENT')
            {
                if(!$arCurrentValues[$key] || !$arCurrentValues[$key][0])
                {
                    $properties[$key] = $arCurrentValues[$key.'_X'];
                    $properties[$key.'_X'] = $arCurrentValues[$key.'_X'];
                }
                else
                    $properties[$key] = $arCurrentValues[$key];
            }
            else
            {
                if(!$arCurrentValues[$key])
                {
                    $properties[$key] = $arCurrentValues[$key.'_X'];
                    $properties[$key.'_X'] = $arCurrentValues[$key.'_X'];
                }
                else
                    $properties[$key] = $arCurrentValues[$key];
            }
        }
        return $properties;
    }
    
    /**
     * @param $arCurrentValues
     * @param $properties
     * @return mixed
     */
    private static function getUtsValues($arCurrentValues, $properties)
    {
        $utsCheck = false;
        foreach ($arCurrentValues as $key => $value)
        {
            if($key == 'save')
                break;
            
            if($utsCheck)
                $properties[$key] = $arCurrentValues[$key];
            
            if($key == array_search(end(self::$data), self::$data))
                $utsCheck = true;
        }
        return $properties;
    }
}