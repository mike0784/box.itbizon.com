<?

$arJsConfig = array(
    'xlsx' => array(
        'js' => '/local/modules/itbizon.basis/lib/js/xlsx.core.min.js',
        'css' => '',
        'rel' => array(),
    )
);

foreach ($arJsConfig as $ext => $arExt) {
    \CJSCore::RegisterExt($ext, $arExt);
}

CJSCore::RegisterExt('ext_bootstrap4', [
    'js' => [
        '/local/modules/itbizon.basis/extension/bootstrap4/js/bootstrap.min.js',
    ],
    'css' => [
        '/local/modules/itbizon.basis/extension/bootstrap4/css/bootstrap.min.css',
    ],
    'rel'  => ["jquery2"],
    "skip_core" => true,
]);