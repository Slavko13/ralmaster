<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?$bPostSection = (isset($_POST['ID']) && $_POST['ID']);?>
<?
$bUseMap = CAllcorp2::GetFrontParametrValue('CONTACTS_USE_MAP', SITE_ID) != 'N';
$mapLAT = $mapLON = $iCountShops =0;
$arPlacemarks = array();
if($bPostSection)
{
	$arItems = CCache::CIblockElement_GetList(array("CACHE" => array("TAG" => CCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array_merge($arItemFilter, array('SECTION_ID' => $_POST['ID'])), false, false, $arItemSelect);
	$GLOBALS[$arParams['FILTER_NAME']]['SECTION_ID'] = $_POST['ID'];
}

foreach($arItems as $arItem)
{
	if($arItem['PROPERTY_MAP_VALUE']){
		$arCoords = explode(',', $arItem['PROPERTY_MAP_VALUE']);
		$mapLAT += $arCoords[0];
		$mapLON += $arCoords[1];
		$html = '<div class="pane_info_wrapper"><div class="pane_info">';
				
		$html .= '<div class="title">'.'<div class="name">'.$arItem['NAME'].'</div></div>';
		if($arItem['PROPERTY_METRO_VALUE'])
		{
			$html .= '<div class="value-block metro"><div>'.CAllcorp2::showIconSvg("metro colored", SITE_TEMPLATE_PATH."/images/svg/Metro.svg").'<div>'.$arItem['PROPERTY_METRO_VALUE'].'</div></div></div>';
		}
		if($arItem['PROPERTY_SCHEDULE_VALUE']['TEXT'])
		{
			$html .= '<div class="value-block schedule"><div>'.CAllcorp2::showIconSvg("clock colored", SITE_TEMPLATE_PATH."/images/svg/WorkingHours.svg").'<div>'.$arItem['PROPERTY_SCHEDULE_VALUE']['TEXT'].'</div></div></div>';
		}
		if($arItem['PROPERTY_PHONE_VALUE'])
		{
			if(!is_array($arItem['PROPERTY_PHONE_VALUE']))
				$arItem['PROPERTY_PHONE_VALUE'] = (array)$arItem['PROPERTY_PHONE_VALUE'];
			$html .= '<div class="value-block phones"><div>'.CAllcorp2::showIconSvg("phone colored", SITE_TEMPLATE_PATH."/images/svg/Phone_black.svg");
			foreach($arItem['PROPERTY_PHONE_VALUE'] as $phone)
			{
				$html .= '<div class="phone"><a rel="nofollow" href="tel:'.str_replace(array(' ', ',', '-', '(', ')'), '', $phone).'">'.$phone.'</a></div>';
			}
			$html .= '</div></div>';
		}
		if($arItem['PROPERTY_EMAIL_VALUE'])
		{
			$html .= '<div class="value-block email"><div>'.CAllcorp2::showIconSvg("email colored", SITE_TEMPLATE_PATH."/images/svg/Email.svg").'<div>'.$arItem['PROPERTY_EMAIL_VALUE'].'</div></div></div>';
		}

		$html .= '</div></div>';
		$arPlacemarks[] = array(
			"ID" => $arItem["ID"],
			"LAT" => $arCoords[0],
			"LON" => $arCoords[1],
			"TEXT" => $arItem['NAME'],
			"HTML" => $html
		);
		++$iCountShops;
	}
}
if($iCountShops && $bUseMap)
{
	$mapLAT = floatval($mapLAT / $iCountShops);
	$mapLON = floatval($mapLON / $iCountShops);?>
	<?if($arParams['SHOW_TOP_MAP'] == 'Y'):?>
		<?$this->SetViewTarget('yandex_map');?>
	<?endif;?>
	<div class="contacts-page-map">
		<?if(CAllcorp2::GetFrontParametrValue('CONTACTS_TYPE_MAP') == 'YANDEX') { ?>
			<?$APPLICATION->IncludeComponent(
				"bitrix:map.yandex.view",
				"map",
				array(
					"INIT_MAP_TYPE" => "MAP",
					"MAP_DATA" => serialize(array("yandex_lat" => $mapLAT, "yandex_lon" => $mapLON, "yandex_scale" => 19, "PLACEMARKS" => $arPlacemarks)),
					"MAP_WIDTH" => "100%",
					"MAP_HEIGHT" => "420",
					"CONTROLS" => array(
						0 => "ZOOM",
						1 => "TYPECONTROL",
						2 => "SCALELINE",
					),
					"OPTIONS" => array(
						0 => "ENABLE_DBLCLICK_ZOOM",
						1 => "ENABLE_DRAGGING",
					),
					"MAP_ID" => "MAP_v33",
					"API_KEY" => \Bitrix\Main\Config\Option::get("fileman","yandex_map_api_key",""),
					"COMPONENT_TEMPLATE" => "map",
					"USE_REGION_DATA" => "N",
				),
				false
			);
		} ?>
		<?if(CAllcorp2::GetFrontParametrValue('CONTACTS_TYPE_MAP') == 'GOOGLE') { ?>
			<?$APPLICATION->IncludeComponent(
				"bitrix:map.google.view",
				"map",
				array(
					"INIT_MAP_TYPE" => "ROADMAP",
					"MAP_DATA" => serialize(array("google_lat" => $mapLAT, "google_lon" => $mapLON, "google_scale" => 19, "PLACEMARKS" => $arPlacemarks)),
					"MAP_WIDTH" => "100%",
					"MAP_HEIGHT" => "420",
					"CONTROLS" => array(
						0 => "SMALL_ZOOM_CONTROL",
						1 => "TYPECONTROL",
						2 => "SCALELINE",
					),
					"OPTIONS" => array(
						0 => "ENABLE_DBLCLICK_ZOOM",
						1 => "ENABLE_DRAGGING",
					),
					"MAP_ID" => "",
					"USE_REGION_DATA" => "Y",
					"COMPONENT_TEMPLATE" => "map",
					"API_KEY" => \Bitrix\Main\Config\Option::get("fileman","google_map_api_key",""),
					"USE_REGION_DATA" => "N",
				),
				false
			);
		} ?>
	</div>
	<?if($arParams['SHOW_TOP_MAP'] == 'Y'):?>
		<?$this->EndViewTarget();?>
	<?endif;?>
<?}?>
<?$APPLICATION->IncludeComponent(
	"bitrix:news.list",
	"contacts",
	Array(
		"COUNT_IN_LINE" => $arParams["COUNT_IN_LINE"],
		"SHOW_SECTION_PREVIEW_DESCRIPTION" => $arParams["SHOW_SECTION_PREVIEW_DESCRIPTION"],
		"VIEW_TYPE" => $arParams["VIEW_TYPE"],
		"SHOW_TABS" => $arParams["SHOW_TABS"],
		"IMAGE_POSITION" => $arParams["IMAGE_POSITION"],
		"IBLOCK_TYPE"	=>	$arParams["IBLOCK_TYPE"],
		"IBLOCK_ID"	=>	$arParams["IBLOCK_ID"],
		"NEWS_COUNT"	=>	$arParams["NEWS_COUNT"],
		"SORT_BY1"	=>	$arParams["SORT_BY1"],
		"SORT_ORDER1"	=>	$arParams["SORT_ORDER1"],
		"SORT_BY2"	=>	$arParams["SORT_BY2"],
		"SORT_ORDER2"	=>	$arParams["SORT_ORDER2"],
		"FIELD_CODE"	=>	$arParams["LIST_FIELD_CODE"],
		"PROPERTY_CODE"	=>	$arParams["LIST_PROPERTY_CODE"],
		"DISPLAY_PANEL"	=>	$arParams["DISPLAY_PANEL"],
		"SET_TITLE"	=>	$arParams["SET_TITLE"],
		"SET_STATUS_404" => $arParams["SET_STATUS_404"],
		"INCLUDE_IBLOCK_INTO_CHAIN"	=>	$arParams["INCLUDE_IBLOCK_INTO_CHAIN"],
		"ADD_SECTIONS_CHAIN"	=>	$arParams["ADD_SECTIONS_CHAIN"],
		"CACHE_TYPE"	=>	$arParams["CACHE_TYPE"],
		"CACHE_TIME"	=>	$arParams["CACHE_TIME"],
		"CACHE_FILTER"	=>	"Y",
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"DISPLAY_TOP_PAGER"	=>	$arParams["DISPLAY_TOP_PAGER"],
		"DISPLAY_BOTTOM_PAGER"	=>	$arParams["DISPLAY_BOTTOM_PAGER"],
		"PAGER_TITLE"	=>	$arParams["PAGER_TITLE"],
		"PAGER_TEMPLATE"	=>	$arParams["PAGER_TEMPLATE"],
		"PAGER_SHOW_ALWAYS"	=>	$arParams["PAGER_SHOW_ALWAYS"],
		"PAGER_DESC_NUMBERING"	=>	$arParams["PAGER_DESC_NUMBERING"],
		"PAGER_DESC_NUMBERING_CACHE_TIME"	=>	$arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
		"PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
		"DISPLAY_DATE"	=>	$arParams["DISPLAY_DATE"],
		"DISPLAY_NAME"	=>	$arParams["DISPLAY_NAME"],
		"DISPLAY_PICTURE"	=>	$arParams["DISPLAY_PICTURE"],
		"DISPLAY_PREVIEW_TEXT"	=>	$arParams["DISPLAY_PREVIEW_TEXT"],
		"PREVIEW_TRUNCATE_LEN"	=>	$arParams["PREVIEW_TRUNCATE_LEN"],
		"ACTIVE_DATE_FORMAT"	=>	$arParams["LIST_ACTIVE_DATE_FORMAT"],
		"USE_PERMISSIONS"	=>	$arParams["USE_PERMISSIONS"],
		"GROUP_PERMISSIONS"	=>	$arParams["GROUP_PERMISSIONS"],
		"FILTER_NAME"	=>	$arParams["FILTER_NAME"],
		"HIDE_LINK_WHEN_NO_DETAIL"	=>	$arParams["HIDE_LINK_WHEN_NO_DETAIL"],
		"CHECK_DATES"	=>	$arParams["CHECK_DATES"],
		"PARENT_SECTION"	=>	$arResult["VARIABLES"]["SECTION_ID"],
		"PARENT_SECTION_CODE"	=>	$arResult["VARIABLES"]["SECTION_CODE"],
		"DETAIL_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["detail"],
		"SECTION_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		"IBLOCK_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["news"],
		"INCLUDE_SUBSECTIONS" => "Y",
		"SHOW_DETAIL_LINK" => $arParams["SHOW_DETAIL_LINK"],
	),
	$component
);?>