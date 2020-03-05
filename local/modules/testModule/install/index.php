<?

Class testModule extends CModule
{
    var $MODULE_ID = "testModule";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;

    function testModule()
    {
        $moduleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");

        if (is_array($moduleVersion) && array_key_exists("VERSION", $moduleVersion))
        {
            $this->MODULE_VERSION = $moduleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $moduleVersion["VERSION_DATE"];
        }

        $this->MODULE_NAME = "testModule name";
        $this->MODULE_DESCRIPTION = "testModule description";
    }

    function InstallFiles()
    {
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/testModule/install/components",
            $_SERVER["DOCUMENT_ROOT"]."/local/components", true, true);
        return true;
    }

    function UnInstallFiles()
    {
        DeleteDirFilesEx("/local/components/testComponent");
        return true;
    }

    function DoInstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;
        $this->InstallFiles();
        RegisterModule("testModule");
        $APPLICATION->IncludeAdminFile("Install testModule", $DOCUMENT_ROOT."/local/modules/testModule/install/step.php");
    }

    function DoUninstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;
        $this->UnInstallFiles();
        UnRegisterModule("testModule");
        $APPLICATION->IncludeAdminFile("Uninstall testModule", $DOCUMENT_ROOT."/local/modules/testModule/install/unstep.php");
    }
}
?>