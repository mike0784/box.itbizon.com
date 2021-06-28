<?
use Bitrix\Bizproc\FieldType;
use Bitrix\Main\Loader;

\CModule::IncludeModule('im'); // for runtime

use Itbizon\Service\Activities\Activity;
use Itbizon\Service\Activities\Field;

use Itbizon\Service\Log; // fixme

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true || !Loader::includeModule('itbizon.service')) {
    die();
}

/**
 * Class CBPItbizonSetNotify
 */
class CBPItbizonSetNotify extends Activity
{
    private $log; //fixme
    
    public function __construct($name) // fixme
    {
        parent::__construct($name);
        
        $this->log = new Log('bizproc'); // fixme
        //$this->log->add("CBPItbizonSetNotify.__construct($name) finished", Log::LEVEL_INFO); // fixme
    }
    
    
    /**
     * @return string
     */
    protected static function getActivityPath(): string
    {
        $log = new Log('bizproc'); // fixme
        //$log->add("CBPItbizonSetNotify.getActivityPath()", Log::LEVEL_INFO); // fixme
        
        return __FILE__;
    }

    /**
     * @return Field[]
     */
    protected static function getInputFields(): array
    {
        $log = new Log('bizproc'); // fixme
        //$log->add("CBPItbizonSetNotify.getInputFields()", Log::LEVEL_INFO); // fixme
        
        $fields = [];
        $fields[] = new Field(
            'InputUser',
            'Пользователь',
            FieldType::USER,
            true,
            );
    
        // read current values
        $arSettings = CIMSettings::Get();
        $arNotifyNames = CIMSettings::GetNotifyNames();
        $arTable = CBPItbizonSetNotify::parseNotifyArray($arSettings, $arNotifyNames);
    
        foreach($arTable as $block => $arBlock)
        {
            foreach($arBlock as $rowId => $rowValue)
            {
                $fields[] = new Field(
                    $rowValue['FieldName'],
                    $rowValue['Name'],
                    FieldType::BOOL,
                    false,
                    null,
                    $rowValue['Value'] // Y N
                );

            }
            
        }
    
        //$log->add("CBPItbizonSetNotify.getInputFields() filds = ".print_r($fields, True), Log::LEVEL_INFO); // fixme
        
        return $fields;
        
        /*
        return [
            new Field(
                'InputUser',
                'Пользователь',
                FieldType::USER,
                true,
            ),
            new Field(
                'notify|email|chat',
                'E-mail уведомление',
                FieldType::BOOL,
                true,
                null,
                'N' // Y N
            ),
            
            
        ]; // */
    }

    /**
     * @return Field[]
     */
    protected static function getOutputFields(): array
    {
        $log = new Log('bizproc'); // fixme
        //$log->add("CBPItbizonSetNotify.getOutputFields()", Log::LEVEL_INFO); // fixme
        
        return [
            new Field(
                'OutputChangeNotifyResult',
                'Результат операции',
                FieldType::BOOL,
                true,
            ),
        ];
    }

    /**
     * @return int
     */
    public function Execute()
    {
        try {
            //$this->log->add("CBPItbizonSetNotify.Execute()", Log::LEVEL_INFO); // fixme
    
            $userId = CBPHelper::ExtractUsers($this->InputUser, $this->GetDocumentId(), true);
            
            if (!$userId)
                throw new Exception('Wrong user id');
            
            //$this->log->add("CBPItbizonSetNotify.Execute() userId = ".print_r($userId, True), Log::LEVEL_INFO); // fixme
    
            $this->setNotifyScheme('expert', $userId);
            
            $arNotifySettings = [];
    
            foreach(self::getInputFields() as $field) {
    
                //$this->log->add("CBPItbizonSetNotify.Execute() field = ".print_r($field, True), Log::LEVEL_INFO); // fixme
    
                $inFieldId = $field->getId();
                //$this->log->add("CBPItbizonSetNotify.Execute() field_value = ".print_r($this->$inFieldId, True), Log::LEVEL_INFO); // fixme
                
                if ($field->getType() == 'bool')
                {
                    $fieldId = $field->getId();
                    $arNotifySettings[$fieldId] = ($this->$fieldId == 'Y');
                }
                
            }

            $res = false;
            if (count($arNotifySettings))
                $res = CIMSettings::Set('notify', $arNotifySettings, $userId);

            if ($res)
                $this->OutputChangeNotifyResult = 'Y';

            //Просто переписываю входные параметры в выходные
            //foreach(self::getInputFields() as $field) {
            //    $inFieldId = $field->getId();
            //    $outFieldId = str_replace('Input', 'Output', $inFieldId);
            //    $this->$outFieldId = $this->$inFieldId;
            //    $this->WriteToTrackingService($inFieldId . ' -> ' . $outFieldId . ' / ' . $this->$inFieldId . ' -> ' . $this->$outFieldId);
            //}

        } catch(Exception $e) {
            $this->WriteToTrackingService($e->getMessage(). ' ' . $e->getTraceAsString());
        }
        return CBPActivityExecutionStatus::Closed;
    }
    
    public static function parseNotifyArray($arSettings, $arNames): array
    {
        $arNotifyValues = $arSettings['notify'];
        $rowsList = [];
        foreach ($arNames as $blockName => $arNameVal) {
            
            $nameLang = $arNameVal['NAME'];
            $items = $arNameVal['NOTIFY'];
            
            $rowsBlock = [];
            //$rowsBlock[] = ['title' => $nameLang, 'type' => 'header'];
            
            foreach ($items as $itemName => $itemVal) {
                $full = $blockName . '|' . $itemName;
                
                $row = [];
                
                foreach (['site' => 'Сайт', 'email' => 'E-mail', 'push' => 'Push'] as $column => $columnSuffix) {
                    $colVal = isset($arNotifyValues[$column . '|' . $full]) & ($arNotifyValues[$column . '|' . $full]);
                    $colDis = isset($arNotifyValues["disabled|" . $column . '|' . $full]) & ($arNotifyValues["disabled|" . $column . '|' . $full]);

                    if ($colDis) continue;
                    
                    $colVal = ($colVal) ? 'Y' : 'N';
                    $row[] = [
                        'FieldName'=> $column."|".$full,
                        'Value' => $colVal,
                        'Name' => $columnSuffix.' - '.$itemVal." (".$nameLang.')',
                    ];
                }
                $rowsBlock[] = $row;
            }
            
            if ($blockName == 'im') {
                $rowsList = array_merge($rowsBlock, $rowsList);
            } else {
                $rowsList = array_merge($rowsList, $rowsBlock);
            }
        }
        return $rowsList;
    }
    
    public static function setNotifyScheme(string $scheme, int $userId)
    {
        if (!in_array($scheme, ['simple', 'expert'])) {
            throw new Exception('Wrong scheme name');
        }
        
        $arSettings = CIMSettings::Get($userId);
        if ($arSettings[CIMSettings::SETTINGS]['notifyScheme'] != $scheme) {
            $arSettings[CIMSettings::SETTINGS]['notifyScheme'] = $scheme;
            CIMSettings::Set(CIMSettings::SETTINGS,  $arSettings[CIMSettings::SETTINGS], $userId);
        }
    }
    
}