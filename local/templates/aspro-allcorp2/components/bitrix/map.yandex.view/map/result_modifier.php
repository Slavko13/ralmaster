<?
global $arRegion, $arTheme;
if($arTheme['USE_REGIONALITY']['DEPENDENT_PARAMS']['SHOW_REGION_CONTACT']['VALUE'] == 'Y' 
&& $arRegion && $arRegion["PROPERTY_REGION_TAG_YANDEX_MAP_VALUE"] 
&& $arParams['USE_REGION_DATA'] !== 'N')
{
	$arCoord = explode(",", $arRegion["PROPERTY_REGION_TAG_YANDEX_MAP_VALUE"]);
	$arResult['POSITION']['yandex_lat'] = $arCoord[0];
	$arResult['POSITION']['yandex_lon'] = $arCoord[1];
	$arTmpMark = array(
		"LON" => $arResult['POSITION']['yandex_lon'],
		"LAT" => $arResult['POSITION']['yandex_lat'],
		"TEXT" => $arResult['POSITION']['PLACEMARKS'][0]['TEXT'],
	);
	$arResult['POSITION']['PLACEMARKS'] = array();
	$arResult['POSITION']['PLACEMARKS'][] = $arTmpMark;
}
?>