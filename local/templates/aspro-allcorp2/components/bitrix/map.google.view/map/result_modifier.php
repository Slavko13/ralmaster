<?
global $arRegion, $arTheme;
if($arTheme['USE_REGIONALITY']['DEPENDENT_PARAMS']['SHOW_REGION_CONTACT']['VALUE'] == 'Y' && $arRegion && $arRegion["PROPERTY_REGION_TAG_YANDEX_MAP_VALUE"])
{
	$arCoord = explode(",", $arRegion["PROPERTY_REGION_TAG_YANDEX_MAP_VALUE"]);
	$arResult['POSITION']['google_lat'] = $arCoord[0];
	$arResult['POSITION']['google_lon'] = $arCoord[1];
	$arTmpMark = array(
		"LON" => $arResult['POSITION']['google_lon'],
		"LAT" => $arResult['POSITION']['google_lat'],
		"TEXT" => $arResult['POSITION']['PLACEMARKS'][0]['TEXT'],
	);
	$arResult['POSITION']['PLACEMARKS'] = array();
	$arResult['POSITION']['PLACEMARKS'][] = $arTmpMark;
}
?>