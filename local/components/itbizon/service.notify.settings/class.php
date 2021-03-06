<?php

use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UserTable;

use Itbizon\Service\Component\Simple;

Loc::loadMessages(__FILE__);

if (!Loader::includeModule('itbizon.service'))
{
    throw new Exception('error load module itbizon.service');
}


/**
 * Class CITBServiceNotifySettings
 */
class CITBServiceNotifySettings extends Simple
{
    private $arTable;
    
    public $usersList;
    public $fromUser;
    public $toUsers;
    
    /**
     * @return mixed|void|null
     * @throws Exception
     */
    public function executeComponent()
    {
        try {
            $userId = CurrentUser::get()->getId();
            
            if (!Loader::includeModule('itbizon.service')) {
                throw new Exception(Loc::getMessage('ITB_SERVICE.NOTIFY.SETTINGS.ERROR.INCLUDE_SERVICE'));
            }

            if(!CurrentUser::get()->isAdmin()) {
                throw new Exception(Loc::getMessage('ITB_SERVICE.NOTIFY.SETTINGS.ERROR.ACCESS_DENY'));
            }
    
            // select users
            $this->usersList = UserTable::getList([
                    'select' => [
                        'ID',
                        'NAME',
                        'SECOND_NAME',
                        'LAST_NAME'
                    ],
                    'filter' => [
                        '=ACTIVE' => 'Y',
                        '=IS_REAL_USER' => 'Y'
                    ],
                    'order' => [
                        'LAST_NAME' => 'ASC',
                        'NAME' => 'ASC',
                    ],
                ]
            )->fetchAll();
    
            // select source user id
            if ($_POST['select_from_user']) {
                $this->fromUser = intval($_POST['FROM_USER_ID']);
            }
    
            if ($_POST['select_to_user'] && is_array($_POST['TO_USER_ID'])) {
                foreach ($_POST['TO_USER_ID'] as $to_user_id) {
                    $this->toUsers[] = intval($to_user_id);
                }
            }
            
            // POST - save data
            if ($_POST['form_notify_table']) {
                $optList = $this->parseOptList();
        
                // save settings to DB
                foreach ($this->toUsers as $itemUserId)
                    $this->setNotifySettingsUser($optList, $itemUserId);
            } // POST
    
            // Read current user settings
            if (!isset($this->fromUser)) $this->fromUser = $userId;
    
            $arSettings = CIMSettings::Get($this->fromUser); // re-read
            $arNotifyNames = CIMSettings::GetNotifyNames();
            $this->arTable = $this->parseNotifyArray($arSettings, $arNotifyNames);
    
        } catch (Exception $e) {
            $this->addError($e->getMessage() . ' ' . $e->getTraceAsString());
        }

        //Include template
        $this->IncludeComponentTemplate();
        return true;
    }
    
    /**
     * @return array
     */
    public function getTable(): array
    {
        return $this->arTable;
    }
    
    /**
     * @param $arSettings
     * @param $arNames
     * @return array
     */
    private function parseNotifyArray($arSettings, $arNames): array
    {
        $arNotifyValues = $arSettings['notify'];
        $rows_list = [];
        foreach ($arNames as $block_name => $arNameVal) {
            
            $name_lang = $arNameVal['NAME'];
            $items = $arNameVal['NOTIFY'];
            
            $rows_block = [];
            $rows_block[] = ['title' => $name_lang, 'type' => 'header'];
            
            foreach ($items as $item_name => $val) {
                $full = $block_name . '|' . $item_name;
                
                $row = [];
                $row['title'] = $val;
                $row['type'] = 'data';
                
                foreach (['site', 'email', 'push'] as $column) {
                    $col_val = isset($arNotifyValues[$column . '|' . $full]) & ($arNotifyValues[$column . '|' . $full]);
                    $col_dis = isset($arNotifyValues["disabled|" . $column . '|' . $full]) & ($arNotifyValues["disabled|" . $column . '|' . $full]);
                    $row[$column] = "<input type='checkbox' name='" . "notify|" . $column . "|" . $full . "' " . ($col_val ? "checked='true'" : "") . " " . ($col_dis ? "disabled='true'" : "") . " value='yes' >";
                }
                $rows_block[] = $row;
            }
            
            if ($block_name == 'im') {
                $rows_list = array_merge($rows_block, $rows_list);
            } else {
                $rows_list = array_merge($rows_list, $rows_block);
            }
        }
        return $rows_list;
    }
    
    /**
     * @param $optList
     * @param $userId
     */
    private function setNotifySettingsUser($optList, $userId)
    {
        $arNotify = CUserOptions::GetOption('im', CIMSettings::NOTIFY, array(), $userId);  // , $userId
        $arNotify = CIMSettings::checkValues(CIMSettings::NOTIFY, $arNotify);
    
        $this->setNotifyScheme('expert', $userId);
        
        foreach ($arNotify as $optName => $optVal) {
            if ((substr($optName, 0, 9) == 'disabled|') || (substr($optName, 0, 10) == 'important|')) {
                continue;
            }
            // set values
            if ((!$optVal) && isset($optList[$optName]) && !($arNotify['disabled|' . $optName])) {
                $arNotify[$optName] = True;
            } // clear values
            elseif (($optVal) && !isset($optList[$optName]) && !($arNotify['disabled|' . $optName])) {
                $arNotify[$optName] = False;
            }
        } // */
        $res = CIMSettings::Set('notify', $arNotify, $userId); // $userId
        
    }
    
    /**
     * @return array
     */
    private function parseOptList()
    {
        $optList = [];
        foreach ($_POST as $postName => $postVal) {
            if (substr($postName, 0, 7) == 'notify|') {
                $optName = substr($postName, 7);
                $optList[$optName] = ($postVal == 'yes');
            }
        }
        return $optList;
    }
    
    /**
     * @param string $scheme
     * @param int|null $userId
     * @throws Exception
     */
    public function setNotifyScheme(string $scheme, int $userId = null)
    {
        if (!$userId) {
            $userId = CurrentUser::get()->getId();
        }
    
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
