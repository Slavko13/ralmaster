<?
if($arParams['DISPLAY_PICTURE'] != 'N'){
	if(is_array($arResult['DETAIL_PICTURE'])){
		CAllcorp2::getFieldImageData($arResult, array('DETAIL_PICTURE'));
		$arResult['GALLERY'][] = array(
			'DETAIL' => $arResult['DETAIL_PICTURE'],
			'PREVIEW' => CFile::ResizeImageGet($arResult['DETAIL_PICTURE'] , array('width' => 800, 'height' => 800), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true),
			'THUMB' => CFile::ResizeImageGet($arResult['DETAIL_PICTURE'] , array('width' => 52, 'height' => 52), BX_RESIZE_IMAGE_EXACT, true),
			'TITLE' => (strlen($arResult['DETAIL_PICTURE']['DESCRIPTION']) ? $arResult['DETAIL_PICTURE']['DESCRIPTION'] : (strlen($arResult['DETAIL_PICTURE']['TITLE']) ? $arResult['DETAIL_PICTURE']['TITLE'] : $arResult['NAME'])),
			'ALT' => (strlen($arResult['DETAIL_PICTURE']['DESCRIPTION']) ? $arResult['DETAIL_PICTURE']['DESCRIPTION'] : (strlen($arResult['DETAIL_PICTURE']['ALT']) ? $arResult['DETAIL_PICTURE']['ALT'] : $arResult['NAME'])),
		);
	}
	
	if(!empty($arResult['PROPERTIES']['PHOTOS']['VALUE'])){
		foreach($arResult['PROPERTIES']['PHOTOS']['VALUE'] as $img){
			$arResult['GALLERY'][] = array(
				'DETAIL' => ($arPhoto = CFile::GetFileArray($img)),
				'PREVIEW' => CFile::ResizeImageGet($img, array('width' => 800, 'height' => 800), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true),
				'THUMB' => CFile::ResizeImageGet($img , array('width' => 52, 'height' => 52), BX_RESIZE_IMAGE_EXACT, true),
				'TITLE' => (strlen($arPhoto['DESCRIPTION']) ? $arPhoto['DESCRIPTION'] : (strlen($arResult['DETAIL_PICTURE']['TITLE']) ? $arResult['DETAIL_PICTURE']['TITLE']  :(strlen($arPhoto['TITLE']) ? $arPhoto['TITLE'] : $arResult['NAME']))),
				'ALT' => (strlen($arPhoto['DESCRIPTION']) ? $arPhoto['DESCRIPTION'] : (strlen($arResult['DETAIL_PICTURE']['ALT']) ? $arResult['DETAIL_PICTURE']['ALT']  : (strlen($arPhoto['ALT']) ? $arPhoto['ALT'] : $arResult['NAME']))),
			);
		}
	}
}

if(!empty($arResult['PROPERTIES']['GALLEY_BIG']['VALUE'])){
	foreach($arResult['PROPERTIES']['GALLEY_BIG']['VALUE'] as $img){
		$arResult['GALLERY_BIG'][] = array(
			'DETAIL' => ($arPhoto = CFile::GetFileArray($img)),
			'PREVIEW' => CFile::ResizeImageGet($img, array('width' => 1500, 'height' => 1500), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true),
			'THUMB' => CFile::ResizeImageGet($img , array('width' => 60, 'height' => 60), BX_RESIZE_IMAGE_EXACT, true),
			'TITLE' => (strlen($arPhoto['DESCRIPTION']) ? $arPhoto['DESCRIPTION'] : (strlen($arResult['DETAIL_PICTURE']['TITLE']) ? $arResult['DETAIL_PICTURE']['TITLE']  :(strlen($arPhoto['TITLE']) ? $arPhoto['TITLE'] : $arResult['NAME']))),
			'ALT' => (strlen($arPhoto['DESCRIPTION']) ? $arPhoto['DESCRIPTION'] : (strlen($arResult['DETAIL_PICTURE']['ALT']) ? $arResult['DETAIL_PICTURE']['ALT']  : (strlen($arPhoto['ALT']) ? $arPhoto['ALT'] : $arResult['NAME']))),
		);
	}
}

if($arResult['DISPLAY_PROPERTIES']){
	$arResult['CHARACTERISTICS'] = array();
	$arResult['VIDEO'] = array();
	$arResult['VIDEO_IFRAME'] = array();
	foreach($arResult['DISPLAY_PROPERTIES'] as $PCODE => $arProp){
		if(!in_array($arProp['CODE'], array('PERIOD', 'PHOTOS', 'PRICE', 'PRICEOLD', 'ARTICLE', 'STATUS', 'DOCUMENTS', 'LINK_GOODS', 'LINK_STAFF', 'LINK_REVIEWS', 'LINK_PROJECTS', 'LINK_SERVICES', 'FORM_ORDER', 'FORM_QUESTION', 'PHOTOPOS', 'POPUP_VIDEO')) && ($arProp['PROPERTY_TYPE'] != 'E' && $arProp['PROPERTY_TYPE'] != 'G')){
			if($arProp["VALUE"] || strlen($arProp["VALUE"])){
				if ($arProp['USER_TYPE'] == 'video') {
					if (count($arProp['PROPERTY_VALUE_ID']) >= 1) {
						foreach($arProp['VALUE'] as $val){
							if($val['path']){
								$arResult['VIDEO'][] = $val;
							}
						}
					}
					elseif($arProp['VALUE']['path']){
						$arResult['VIDEO'][] = $arProp['VALUE'];
					}
				}
				elseif($arProp['CODE'] == 'VIDEO_IFRAME'){
					$arResult['VIDEO_IFRAME'] = $arProp["~VALUE"];
				}
				else{
					$arResult['CHARACTERISTICS'][$PCODE] = $arProp;
				}
			}
		}
	}
	$arResult['CHARACTERISTICS'] = CAllcorp2::PrepareItemProps($arResult['CHARACTERISTICS']);
}

/*brand item*/
$arBrand = array();
if(strlen($arResult["DISPLAY_PROPERTIES"]["BRAND"]["VALUE"]) && $arResult["PROPERTIES"]["BRAND"]["LINK_IBLOCK_ID"])
{
	$arBrand = CCache::CIBLockElement_GetList(array('CACHE' => array("MULTI" =>"N", "TAG" => CCache::GetIBlockCacheTag($arResult["PROPERTIES"]["BRAND"]["LINK_IBLOCK_ID"]))), array("IBLOCK_ID" => $arResult["PROPERTIES"]["BRAND"]["LINK_IBLOCK_ID"], "ACTIVE"=>"Y", "ID" => $arResult["DISPLAY_PROPERTIES"]["BRAND"]["VALUE"]), false, false, array("ID", "NAME", "PREVIEW_TEXT", "PREVIEW_TEXT_TYPE", "DETAIL_TEXT", "DETAIL_TEXT_TYPE", "PREVIEW_PICTURE", "DETAIL_PICTURE", "DETAIL_PAGE_URL", "PROPERTY_SITE"));
	if($arBrand)
	{
		if($arBrand["PREVIEW_PICTURE"] || $arBrand["DETAIL_PICTURE"])
		{
			$picture = ($arBrand["PREVIEW_PICTURE"] ? $arBrand["PREVIEW_PICTURE"] : $arBrand["DETAIL_PICTURE"]);
			$arBrand["IMAGE"] = CFile::ResizeImageGet($picture, array("width" => 120, "height" => 40), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true);
			$arBrand["IMAGE"]["ALT"] = $arBrand["IMAGE"]["TITLE"] = $arBrand["NAME"];
			if($arBrand["DETAIL_PICTURE"])
			{
				$arBrand["IMAGE"]["INFO"] = CFile::GetFileArray($arBrand["DETAIL_PICTURE"]);

				$ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($arBrand["IBLOCK_ID"], $arBrand["ID"]);
				$arBrand["IMAGE"]["IPROPERTY_VALUES"] = $ipropValues->getValues();
				if($arBrand["IMAGE"]["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"])
					$arBrand["IMAGE"]["TITLE"] = $arBrand["IMAGE"]["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"];
				if($arBrand["IMAGE"]["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"])
					$arBrand["IMAGE"]["ALT"] = $arBrand["IMAGE"]["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"];

				if($arBrand["IMAGE"]["INFO"]["DESCRIPTION"])
					$arBrand["IMAGE"]["ALT"] = $arBrand["IMAGE"]["TITLE"] = $arBrand["IMAGE"]["INFO"]["DESCRIPTION"];
			}
		}
	}
}
$arResult["BRAND_ITEM"]=$arBrand;?>

<?$arResult['CONTENT_FROM_DYNAMIC'] = false;?>
<?$arResult['POPUP_VIDEO'] = false;?>

<?if(in_array('INCLUDE_TEXT', $arParams['PROPERTY_CODE'])):?>
	<?ob_start();?>
		<?$APPLICATION->IncludeComponent(
			"bitrix:main.include",
			"",
			Array(
				"AREA_FILE_SHOW" => "page",
				"AREA_FILE_SUFFIX" => "garanty",
				"EDIT_TEMPLATE" => ""
			)
		);?>
	<?$indexContent = ob_get_contents();
	ob_end_clean();?>
<?endif;?>

<?$arResult['ADDITIONAL_FROM_DYNAMIC'] = false;?>
<?ob_start();?>
	<?$APPLICATION->IncludeComponent(
		'bitrix:main.include',
		'',
		array(
			"AREA_FILE_SHOW" => "page",
			"AREA_FILE_SUFFIX" => "dops",
			"EDIT_TEMPLATE" => ""
		)
	);?>
<?$additional_text_tmp = ob_get_contents();		
ob_end_clean();
$bshowAdditionalTextFromFile = true;
if( strlen( trim($additional_text_tmp) ) < 1){
	$bshowAdditionalTextFromFile = false;
} else{
	$bIsBitrixDiv = ( strpos($additional_text_tmp, 'bx_incl_area') !== false );
	$textWithoutTags = strip_tags($additional_text_tmp);
	if( $bIsBitrixDiv && (strlen( trim($textWithoutTags) ) < 1) ){
		$bshowAdditionalTextFromFile = false;
	}
}

if( $bshowAdditionalTextFromFile ){
	//$additionalContent = $additional_text_tmp;
	$additionalContent = 'from_file';
}?>

<?
if($arResult['SECTION'])
{
	$arSectionIDs = $arSections = array();
	foreach($arResult['SECTION']['PATH'] as $arPath)
	{
		$arSectionIDs[$arPath['ID']] = $arPath['ID'];
	}
	if($arSectionIDs)
	{
		$arSections = CCache::CIBlockSection_GetList(array('CACHE' => array("MULTI" =>"N", "GROUP" => "ID", "TAG" => CCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array('GLOBAL_ACTIVE' => 'Y', "ID" => $arSectionIDs, "IBLOCK_ID" => $arParams["IBLOCK_ID"]), false, array("ID", "IBLOCK_ID", "NAME", "UF_INCLUDE_TEXT", "UF_POPUP_VIDEO", "UF_ADDITIONAL_TAB"));
		if($arSections)
		{
			foreach($arSections as $arSection)
			{
				if($arSection['UF_INCLUDE_TEXT'])
				{
					$indexContent = $arSection['UF_INCLUDE_TEXT'];
					$arResult['CONTENT_FROM_DYNAMIC'] = true;
				}
				if($arSection['UF_POPUP_VIDEO'])
					$arResult['POPUP_VIDEO'] = $arSection['UF_POPUP_VIDEO'];

				if($arSection['UF_ADDITIONAL_TAB'])
				{
					$additionalContent = $arSection['UF_ADDITIONAL_TAB'];
					$arResult['ADDITIONAL_FROM_DYNAMIC'] = true;
				}
			}
		}
	}
}
if(isset($arResult['PROPERTIES']['INCLUDE_TEXT']['~VALUE']['TEXT']) && $arResult['PROPERTIES']['INCLUDE_TEXT']['~VALUE']['TEXT'])
{
	$indexContent = $arResult['PROPERTIES']['INCLUDE_TEXT']['~VALUE']['TEXT'];
	$arResult['CONTENT_FROM_DYNAMIC'] = true;
}
if(isset($arResult['PROPERTIES']['ADDITIONAL_TAB_TEXT']['~VALUE']['TEXT']) && $arResult['PROPERTIES']['ADDITIONAL_TAB_TEXT']['~VALUE']['TEXT'])
{
	$additionalContent = $arResult['PROPERTIES']['ADDITIONAL_TAB_TEXT']['~VALUE']['TEXT'];
	$arResult['ADDITIONAL_FROM_DYNAMIC'] = true;
}
if($arResult['PROPERTIES']['POPUP_VIDEO']['VALUE'])
	$arResult['POPUP_VIDEO'] = $arResult['PROPERTIES']['POPUP_VIDEO']['VALUE'];

if(strlen(trim($indexContent)) > 1) {
	$textWithoutTags = strip_tags($indexContent); 
	if(strlen(trim($textWithoutTags)) > 1) {
		$arResult['INCLUDE_CONTENT'] = $indexContent;
	}	
}	

if( strlen($additionalContent) > 1 )
	$arResult['ADDITIONAL_CONTENT'] = $additionalContent;
?>