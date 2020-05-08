<?php

\Bitrix\Main\Loader::registerAutoloadClasses(
    'itbizon.kulakov',
    array(
        'TestModule\Tables\ItbInvoiceTable'     => 'orm/itbinvoicetable.php',
        'TestModule\Tables\ItbProductTable'     => 'orm/itbproducttable.php',
        'Manager'                               => 'orm/manager.php',
        'TestModule\Log'                        => 'helper/log.php'
    )
);