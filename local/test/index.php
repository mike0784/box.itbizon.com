<?php

use Bitrix\Main\UI\Extension;
use Itbizon\Template\TestClass;

require($_SERVER['DOCUMENT_ROOT'] . "/bitrix/header.php");

Extension::load('ui.bootstrap4');

if (CModule::IncludeModule('itbizon.template')) {
    $test = new TestClass();

    if ($_GET["id"] && !isset($_GET['update'])) {
        $id = $_GET["id"];
        $fine = \Itbizon\Template\SystemFines\Model\FinesTable::getByPrimary($id)->fetchObject();

        if ($fine) {
            $fine->delete();
            header('Location: /local/test/');
            exit;
        }
    }

    if ($_GET["id"] && isset($_GET['update'])) {
        $id = $_GET["id"];
        $updateFine = \Itbizon\Template\SystemFines\Model\FinesTable::getByPrimary($id)->fetch();
    }


    if ($_POST['id'] == 0 && $data = $_POST) {
        unset($data['id']);
        $fine = $test->createFine($data);
        $_POST = [];

        if (is_array($fine)) {
            $errorsBag = $fine;
        }
    }

    if ($_POST['id'] !== 0 && $data = $_POST) {
        unset($data['id']);
        $id = (int)$_POST['id'];
        $fine = $test->updateFine($id, $data);
        $_POST = [];

        if(is_object($fine)){
            header('Location: /local/test/');
            exit;
        }
        if (is_array($fine)) {
            $errorsBag = $fine;
        }
    }
    $errors = isset($errorsBag) ? $errorsBag : null;
    ?>
    <div class="container">
        <br>
        <?= $test->getTableFines(); ?>
        <br>
        <?= $test->getFormFine($errors, $updateFine); ?>
    </div>
    <?php

} else {
    print_r('error');
}
require($_SERVER['DOCUMENT_ROOT'] . "/bitrix/footer.php");
?>