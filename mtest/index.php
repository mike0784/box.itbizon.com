<?php

use \Bitrix\Main\Loader;
use \Bitrix\Main\UI\Extension;
use Bitrix\Tasks\Integration\Intranet\Department;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Test");

try {
    if (!Loader::includeModule('bizon.main'))
        throw new Exception('error load module bizon.main');
    
    i_show($_REQUEST);
    
    foreach ($_POST['TEST_SELECT'] as $item)
        i_show($item);
    
    // get Department list
    $list = Department::getCompanyStructure();
    $departments = [];
    foreach ($list as $department)
        $departments[$department['ID']] = $department['NAME'];
    
    // get Group list
    $list = \Bitrix\Main\GroupTable::getList();
    $groups = [];
    while($item = $list->fetch())
        $groups[$item['ID']] = $item['NAME'];
    
    i_show($departments);
    i_show($groups);
    
    $user = \Bitrix\Main\UserTable::getList([
        'filter'=>[
            '=ID'=>14,
        ],
        'select'=>[
            '*',
            'UF_SKILLS',
            'UF_DEPARTMENT',
        ],
    ])->fetch();
    i_show($user);
    
    $test = \Bitrix\Main\UserGroupTable::getList()->fetchAll();
    i_show($test);

} catch (Exception $ex) {
    echo $ex->getMessage();
}

?>
<form method="POST" enctype="multipart/form-data">
    <select name="TEST_SELECT[]" multiple>
        <option value="0">Test1</option>
        <option value="1">Test2</option>
        <option value="2">Test3</option>
    </select>
    <input type="submit">
</form>
<?

Extension::load('ui.bootstrap4');

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
