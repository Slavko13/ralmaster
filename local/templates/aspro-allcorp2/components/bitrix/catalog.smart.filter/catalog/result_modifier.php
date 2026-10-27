<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(count($arResult['COMBO']) == 1) {
	$hidden = true;
	foreach ($arResult['COMBO'][0] as $value) {
		if($value){
			$hidden = false;
		}
	}
}

$GLOBALS['SHOW_SMART_FILTER'] = !$hidden;

if($hidden) {
	$arResult['ITEMS'] = array();
}

global $sotbitFilterResult;
$sotbitFilterResult = $arResult;