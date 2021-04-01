<?
use \Bitrix\Main\Loader;

$requireModules = ['crm', 'im', 'bizproc'];
foreach($requireModules as $moduleId)
    if(!Loader::includeModule($moduleId))
        throw new Exception('For the module to work correctly, the '.$moduleId.' module is required');

$arJsConfig = array(
    'xlsx' => array(
        'js' => '/local/modules/itbizon.ttravel/lib/js/xlsx.core.min.js',
        'css' => '',
        'rel' => array(),
    )
);

foreach ($arJsConfig as $ext => $arExt) {
    \CJSCore::RegisterExt($ext, $arExt);
}