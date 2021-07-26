<?php


namespace Itbizon\Service\Activities;

use Bitrix\Bizproc\Activity\PropertiesDialog;
use Bitrix\Bizproc\FieldType;
use CBPActivity;
use CBPHelper;
use CBPRuntime;
use CBPWorkflowTemplateLoader;
use CBPWorkflowTemplateUser;

/**
 * Class Activity
 * @package Itbizon\Service\Activities
 */
abstract class Activity extends CBPActivity
{
    /**
     * Activity constructor.
     * @param $name
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->arProperties = static::getDefaultProperties();
        $this->SetPropertiesTypes(static::getDefaultPropertiesType());
    }

    /**
     * @return array
     */
    protected static function getDefaultProperties(): array
    {
        $defaultValues = [];
        foreach(static::getInputFields() as $field) {
            $defaultValues[$field->getId()] = $field->getDefaultValue();
        }
        foreach(static::getOutputFields() as $field) {
            $defaultValues[$field->getId()] = $field->getDefaultValue();
        }
        return $defaultValues;
    }

    /**
     * @return array
     */
    protected static function getDefaultPropertiesType(): array
    {
        $types = [];
        foreach(static::getInputFields() as $field) {
            $types[$field->getId()] = ['Type' => $field->getType()];
        }
        foreach(static::getOutputFields() as $field) {
            $types[$field->getId()] = ['Type' => $field->getType()];
        }
        return $types;
    }

    /**
     * @return array
     */
    public static function getReturnDescription(): array
    {
        $description = [];
        foreach(static::getOutputFields() as $field) {
            $description[$field->getId()] = [
                'NAME' => $field->getName(),
                'TYPE' => $field->getType(),
            ];
        }
        return $description;
    }

    /**
     * @param string $dir
     * @return bool
     */
    public static function includeActivityClass(string $dir)
    {
        $pathParts = explode('/', $dir);
        $fileName = $dir . '/' . array_pop($pathParts) . '.php';
        if(file_exists($fileName)) {
            include_once $fileName;
            return true;
        }
        return false;
    }

    /**
     * @param array $properties
     * @param CBPWorkflowTemplateUser|null $user
     * @return array
     */
    public static function ValidateProperties($properties = [], CBPWorkflowTemplateUser $user = null)
    {
        $errors = [];
        foreach(static::getInputFields() as $field) {
            if($field->isRequired()) {
                if(empty($properties[$field->getId()])) {
                    $errors[] = [
                        'code'    => 'EmptyField' . $field->getId(),
                        'message' => 'Field ' . $field->getId() . ' is empty' //TODO lang
                    ];
                }
            }
        }
        return array_merge($errors, parent::ValidateProperties($properties, $user));
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
     * @param string $siteId
     * @return false|string|null
     */
    public static function GetPropertiesDialog($documentType, $activityName, $arWorkflowTemplate, $arWorkflowParameters, $arWorkflowVariables, $arCurrentValues = null, $formName = '', $popupWindow = null, $siteId = '')
    {
        /*if (!is_array($arCurrentValues)) {
            $arCurrentValues = static::getDefaultProperties();
            $arCurrentActivity = &CBPWorkflowTemplateLoader::FindActivityByName($arWorkflowTemplate, $activityName);
            if(is_array($arCurrentActivity['Properties'])) {
                foreach(static::getInputFields() as $field) {
                    switch($field->getType()) {
                        case FieldType::USER:
                            $value = CBPHelper::UsersArrayToString($arCurrentActivity['Properties'][$field->getId()], $arWorkflowTemplate, $documentType);
                            break;
                        default:
                            $value = $arCurrentActivity['Properties'][$field->getId()];
                            break;
                    }
                    $arCurrentValues[$field->getId()] = $value;
                }
            }
        }
        $runtime = CBPRuntime::GetRuntime();
        return $runtime->ExecuteResourceFile(
            static::getActivityPath(),
            'properties_dialog.php',
            [
                'arCurrentValues' => $arCurrentValues,
                'formName'        => $formName,
            ]
        );*/
        $map = [];
        $dialog = new PropertiesDialog(
            static::getActivityPath(),
            [
                'documentType' => $documentType,
                'activityName' => $activityName,
                'workflowTemplate' => $arWorkflowTemplate,
                'workflowParameters' => $arWorkflowParameters,
                'workflowVariables' => $arWorkflowVariables,
                'currentValues' => $arCurrentValues,
                'formName' => $formName,
                'siteId' => $siteId
            ]
        );
        foreach(static::getInputFields() as $field) {
            $map[$field->getId()] = [
                'Name' => $field->getName(),
                'FieldName' => $field->getId(),
                'Type' => $field->getType(),
                'Options' => $field->getOptions(),
            ];
        }
        $dialog->setMap($map);
        //echo '<pre>'.print_r($dialog->getMap(), true).'</pre>';
        return $dialog;
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
        $properties = [];
        foreach(static::getInputFields() as $field) {
            $textKey = $field->getId() . '_text';
            $rawValue = (isset($arCurrentValues[$textKey]) && !empty($arCurrentValues[$textKey])) ? $arCurrentValues[$textKey] : $arCurrentValues[$field->getId()];
            switch($field->getType()) {
                case FieldType::USER:
                    $value = CBPHelper::UsersStringToArray($rawValue, $documentType, $errors);
                    break;
                default:
                    $value = $rawValue;
                    break;
            }
            $properties[$field->getId()] = $value;
        }
        $errors = static::ValidateProperties($properties, new CBPWorkflowTemplateUser(CBPWorkflowTemplateUser::CurrentUser));
        if(count($errors) > 0) {
            return false;
        }
        $currentActivity = &CBPWorkflowTemplateLoader::FindActivityByName($arWorkflowTemplate, $activityName);
        foreach($properties as $id => $value) {
            $currentActivity['Properties'][$id] = $value;
        }
        return true;
    }

    /**
     * @return Field[]
     */
    abstract protected static function getInputFields(): array;

    /**
     * @return Field[]
     */
    abstract protected static function getOutputFields(): array;

    /**
     * @return string
     */
    abstract protected static function getActivityPath(): string;
}