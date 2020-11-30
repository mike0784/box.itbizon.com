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