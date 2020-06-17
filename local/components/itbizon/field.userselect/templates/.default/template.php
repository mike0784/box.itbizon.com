<? if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main;

Main\UI\Extension::load("crm.entity-editor");

/**@var CBitrixComponentTemplate $this**/
/**@var CITBFinanceVaultEdit $component **/
$component = $this->getComponent();

if (Main\Loader::includeModule('socialnetwork'))
{
    \CJSCore::init(['socnetlogdest']);

    $destSort = CSocNetLogDestination::GetDestinationSort(
        ['DEST_CONTEXT' => \Bitrix\Crm\Entity\EntityEditor::getUserSelectorContext()]
    );
    $last = [];
    CSocNetLogDestination::fillLastDestination($destSort, $last);

    $destUserIDs = [];
    if (isset($last['USERS'])) {
        foreach ($last['USERS'] as $code) {
            $destUserIDs[] = str_replace('U', '', $code);
        }
    }

    $dstUsers = CSocNetLogDestination::GetUsers();
    $structure = CSocNetLogDestination::GetStucture(['LAZY_LOAD' => true]);

    $currentUser = [];

    if(isset($dstUsers['U'.$component->currentUser]))
    {
        $currentUser = $dstUsers['U'.$component->currentUser];
    }

    $department = $structure['department'];
    $departmentRelation = $structure['department_relation'];
    $departmentRelationHead = $structure['department_relation_head'];
    var_dump($component->status);
    ?>
    <script type="text/javascript">

        window.user = <?=CUtil::PhpToJSObject($currentUser)?>;
        var fieldname = <?=CUtil::PhpToJSObject($component->fieldName)?>;
        var fieldId = <?=CUtil::PhpToJSObject($component->fieldId)?>;
        window.changestatus = <?=CUtil::PhpToJSObject($component->status)?>;

        BX.ready(
            function () {
                BX.Crm.EntityEditorUserSelector.users =  <?=CUtil::PhpToJSObject($dstUsers)?>;
                BX.Crm.EntityEditorUserSelector.department = <?=CUtil::PhpToJSObject($department)?>;
                BX.Crm.EntityEditorUserSelector.departmentRelation = <?=CUtil::PhpToJSObject($departmentRelation)?>;
                BX.Crm.EntityEditorUserSelector.last = <?=CUtil::PhpToJSObject(array_change_key_case($last, CASE_LOWER))?>;

                BX.Crm.EntityEditorCrmSelector.contacts = {};
                BX.Crm.EntityEditorCrmSelector.contactsLast = {};

                BX.Crm.EntityEditorCrmSelector.companies = {};
                BX.Crm.EntityEditorCrmSelector.companiesLast = {};

                BX.Crm.EntityEditorCrmSelector.leads = {};
                BX.Crm.EntityEditorCrmSelector.leadsLast = {};

                BX.Crm.EntityEditorCrmSelector.deals = {};
                BX.Crm.EntityEditorCrmSelector.dealsLast = {};

                window.createField();
                window.changeUser(null, window.user);

            }
        );
    </script>
<? } ?>

<div id="<?= $component->fieldId; ?>" class="form-group">
    <label for="vault-responsible"><?= $component->title; ?></label>
</div>
