<?php

use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UserTable;

use Itbizon\Service\Component\GridHelper;
use Itbizon\Service\Component\RouterHelper;
use Itbizon\Service\Log;


Loc::loadMessages(__FILE__);

/**
 * Class CITBServiceNotifySettings
 */
class CITBServiceNotifySettings extends CBitrixComponent // fixme extends Simple !!!
{
    private $error;
    private $helper;
    private $gridHelper;
    
    private $arTable;
    
    public $users; // fixme
    public $fromUser;
    public $toUsers;
    
    /**
     * @return mixed|void|null
     * @throws Exception
     */
    public function executeComponent()
    {
        try {
            global $USER;
            $userId = $USER->GetId();
            
            
            if (!Loader::includeModule('itbizon.service')) {
                throw new Exception(Loc::getMessage('ITB_SERVICE.NOTIFY.SETTINGS.ERROR.INCLUDE_SERVICE'));
            }
            
            $this->setHelper(new RouterHelper(
                $this,
                [
                    'list' => 'list/',
                    //'view' => 'view/#FILE_NAME#/',
                ],
                'list'
            ));
            
            $this->getHelper()->run();

            if(!CurrentUser::get()->isAdmin()) {
                throw new Exception(Loc::getMessage('ITB_SERVICE.NOTIFY.SETTINGS.ERROR.ACCESS_DENY'));
            }
    
            // select users
            $this->users = UserTable::getList([
                'filter' => ['=ACTIVE' => 'Y'],
                'order' => ['LAST_NAME' => 'DESC',  'NAME' => 'DESC'],
                ]
            )->fetchAll();
            
            
            //echo "\n<br>DEBUG post = <pre>".print_r($_POST, True)."</pre>"; // fixme
    
            //foreach ($this->users as $key => $user) echo "\n<br>DEBUG $key => $user ".$user['ID']." ".$user['NAME']." ".$user['LAST_NAME']; // fixme
            //print_r($user);

            // select source user id
            if ($_POST['select_from_user']){
                echo "\n<br>Selected _from_ user id = ".$_POST['FROM_USER_ID']."<br>"; // fixme
                $this->fromUser = intval($_POST['FROM_USER_ID']);
                
            }
    
            // save data to selected user
            if ($_POST['select_to_user'] && is_array($_POST['TO_USER_ID'])){
                echo "\n<br>Selected _to_ user id = ".print_r($_POST['TO_USER_ID'], True)."<br>"; // fixme
                //$this->toUsers = intval($_POST['TO_USER_ID']);
                foreach($_POST['TO_USER_ID'] as $to_user_id)
                {
                    $this->toUsers[] = intval($to_user_id);
                }
                //$this->toUsers[] = intval($_POST['TO_USER_ID']);
                // save data
        
            }
            
            // POST - save data
            if ($_POST['form_notify_table']){
    
    
                /*
                $optList = [];
    
                foreach($_POST as $postName => $postVal) {
                    if (substr($postName, 0, 7) == 'notify|')
                    {
                        $optName = substr($postName, 7);
                        $optList[$optName] = ($postVal == 'yes') ;
                    }
                }
                // */

                $optList = $this->parseOptList();

                /*
                //echo "\n<br>====================<br>";
                //echo "optlist = "; print_r($optList); // fixme
                //echo "\n<br>====================<br>";
                $arNotify = CUserOptions::GetOption('im', CIMSettings::NOTIFY);  // , $userId
                // echo "***** arNotify = "; print_r($arNotify); // fixme
                $arNotify = CIMSettings::checkValues(CIMSettings::NOTIFY, $arNotify);
                //echo "<br>\n&&&&& arNotify = "; print_r($arNotify); // fixme
                //echo "\n<br>====================<br>"; // fixme
                foreach($arNotify as $optName => $optVal)
                {
                    // echo "\nOPT $optName = $optVal [ ".($optList[$optName])." ~ ".($arNotify['disabled|'.$optName])." ]   (@)".( isset($optList[$optName]) )."  (#)".( !($arNotify['disabled|'.$optName]) )."<br>";
        
                    if ((substr($optName, 0, 9) == 'disabled|') || (substr($optName, 0, 10) == 'important|')){
                        continue;
                    }
        
                    // set values
                    if ((!$optVal) && isset($optList[$optName]) && !($arNotify['disabled|'.$optName]) )
                    {
                        //echo "\nDEBUG (1) optName = $optName;   ".($optList[$optName])."  <<<===  )$optVal( <br>"; // fixme
                        $arNotify[$optName] = True;
                    }
        
                    // clear values
                    elseif (($optVal) && !isset($optList[$optName]) && !($arNotify['disabled|'.$optName]) )
                    {
                        //echo "\nDEBUG (2) optName = $optName;   ".($optList[$optName])."  <<<===  )$optVal( <br>"; // fixme
                        $arNotify[$optName] = False;
                        //echo "\nDEBUG (3) optName = $optName;   ".($arNotify[$optName])."<br>"; // fixme
                    }
        
                }
                $res = CIMSettings::Set('notify', $arNotify); // $userId
                // */
    
    
                // save options to DB
                foreach($this->toUsers as $userId)
                    $this->setNotifySettingsUser($optList, $userId);
                
            
            } // POST
            
            // Read current user settings
            // fixme - current user id
            if (!isset($this->fromUser)) $this->fromUser = $userId;
            
            
            $arSettings = CIMSettings::Get($this->fromUser); // re-read!!!
            $arNotifyNames = CIMSettings::GetNotifyNames();
            $this->arTable = $this->parseNotifyArray($arSettings, $arNotifyNames);

 
        } catch (Exception $e) {
            echo $e->getMessage().' '.$e->getTraceAsString();
        }

        //Include template
        $this->IncludeComponentTemplate();
        return true;
    }

    
    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param mixed $error
     */
    public function setError($error): void
    {
        $this->error = $error;
    }

    /**
     * @return RouterHelper|null
     */
    public function getHelper(): ?RouterHelper
    {
        return $this->helper;
    }

    /**
     * @param mixed $helper
     */
    public function setHelper($helper): void
    {
        $this->helper = $helper;
    }

    /**
     * @return GridHelper|null
     */
    public function getGridHelper(): ?GridHelper
    {
        return $this->gridHelper;
    }

    /**
     * @param mixed $gridHelper
     */
    public function setGridHelper($gridHelper): void
    {
        $this->gridHelper = $gridHelper;
    }
    
    public function getTable(): array
    {
        return $this->arTable;
    }
    
    private function parseNotifyArray($arSettings, $arNames ): array
    {
        //echo "\n<br>====================<br>".count($arSettings)." ".count($arNames)."<br>"; // fixme
    
        //$temp=[];
        //foreach($arSettings as $key => $val){
        //}
        $arNotifyValues = $arSettings['notify'];
        //echo "\n<br>====================<br>".count($arNotifyValues)."<br>"; // fixme
    
        $rows_list = [];
        foreach ($arNames as $block_name => $arNameVal){
        
            //echo "DEBUG $block_name => $arNameVal<br>"; // fixme
            $name_lang = $arNameVal['NAME'];
            $items = $arNameVal['NOTIFY'];
        
            $rows_block=[];
            $rows_block[] = ['title' => $name_lang, 'type' => 'header'];
        
            foreach($items as $item_name => $val){
                $full = $block_name.'|'.$item_name;
                //echo "DEBUG +++ item = $item_name => $val ............... full = ".$full." %%%%% $name_lang - $val  <br>"; // fixme
            
                $row=[];
                $row['title']=$val;
                $row['type']='data';
            
                foreach(['site','email','push'] as $column )
                {
                    $col_val = isset($arNotifyValues[$column.'|'.$full]) & ($arNotifyValues[$column.'|'.$full]);
                    $col_dis = isset($arNotifyValues["disabled|".$column.'|'.$full]) & ($arNotifyValues["disabled|".$column.'|'.$full]);
                
                    //echo "DEBUG ******** col_val = $col_val _____ col_dis = $col_dis ~~~~~~~~~~  $column|$full <br>"; // fixme
                
                    //$row[$column] = "<td><div style="white-space: nowrap;"><input type="checkbox" name="notify|push|blog|broadcast_post" checked="true" data-save="1"></div></td>"
                    //$row[$column] = "$column|$full <input type='checkbox' name='"."notify|".$column."|".$full."' ".($col_val?"checked='true'":"")." ".($col_dis?"disabled='true'":"")." data-save='".($col_val?"1":"0")."'>";
                
                    $row[$column] = "<input type='checkbox' name='"."notify|".$column."|".$full."' ".($col_val?"checked='true'":"")." ".($col_dis?"disabled='true'":"")." value='yes' >";
                
                }
            
                $rows_block[] = $row;
            
                /*
                foreach($arNotifyValues as $k => $v){
                    //echo "@ $k => $v  $full<br>";
                    if (strstr($k, $full)){
                        echo "VALUES $k => $v ___ ".print_r($v,True)."<br>";
                    }
                    
                } // */
            
            }
        
            if ($block_name == 'im')
            {
                $rows_list = array_merge($rows_block, $rows_list);
                //array_unshift($rows_list, $rows_block);
                //array_unshift($rows_list, ['title' => $name_lang, 'type' => 'header']);
            
            } else
            {
                //$rows_list[] = ['title' => $name_lang, 'type' => 'header'];
                //$rows_list[]=$rows_block;
                $rows_list = array_merge($rows_list, $rows_block);
            }
        
        
        
        }
    
    
        return $rows_list;
    } // private function parseNotifyArray($arSettings, $arNames)
    
    
    
    
    private function setNotifySettingsUser($optList, $userId)
    {
        $arNotify = CUserOptions::GetOption('im', CIMSettings::NOTIFY, Array(),     $userId);  // , $userId
        $arNotify = CIMSettings::checkValues(CIMSettings::NOTIFY, $arNotify);
        
        foreach($arNotify as $optName => $optVal)
        {
            // echo "\nOPT $optName = $optVal [ ".($optList[$optName])." ~ ".($arNotify['disabled|'.$optName])." ]   (@)".( isset($optList[$optName]) )."  (#)".( !($arNotify['disabled|'.$optName]) )."<br>";
            
            if ((substr($optName, 0, 9) == 'disabled|') || (substr($optName, 0, 10) == 'important|')){
                continue;
            }
            
            // set values
            if ((!$optVal) && isset($optList[$optName]) && !($arNotify['disabled|'.$optName]) )
            {
                //echo "\nDEBUG (1) optName = $optName;   ".($optList[$optName])."  <<<===  )$optVal( <br>"; // fixme
                $arNotify[$optName] = True;
            }
            
            // clear values
            elseif (($optVal) && !isset($optList[$optName]) && !($arNotify['disabled|'.$optName]) )
            {
                //echo "\nDEBUG (2) optName = $optName;   ".($optList[$optName])."  <<<===  )$optVal( <br>"; // fixme
                $arNotify[$optName] = False;
                //echo "\nDEBUG (3) optName = $optName;   ".($arNotify[$optName])."<br>"; // fixme
            }
            
        } // */
        $res = CIMSettings::Set('notify', $arNotify, $userId); // $userId
        
    }
    
    private function parseOptList()
    {
        $optList = [];
        
        foreach($_POST as $postName => $postVal) {
            if (substr($postName, 0, 7) == 'notify|')
            {
                $optName = substr($postName, 7);
                $optList[$optName] = ($postVal == 'yes') ;
            }
        }
        
        return $optList;
        
    }
    
    
    
}
