<?
$arGoodsSectionsIDs  = array();
foreach($arResult['ITEMS'] as $key => $arItem)
{
	CAllcorp2::getFieldImageData($arResult['ITEMS'][$key], array('PREVIEW_PICTURE'));
	if($arItem['IBLOCK_SECTION_ID'])
	{
		$resGroups = CIBlockElement::GetElementGroups($arItem['ID'], true, array('ID'));
		while($arGroup = $resGroups->Fetch())
		{
			$arResult['ITEMS'][$key]['SECTIONS'][$arGroup['ID']] = $arGroup['ID'];
			$arGoodsSectionsIDs[$arGroup['ID']] = $arGroup['ID'];
		}
	}
}

if($arGoodsSectionsIDs){
	$arResult['SECTIONS'] = CCache::CIBLockSection_GetList(array('SORT' => 'ASC', 'NAME' => 'ASC', 'CACHE' => array('TAG' => CCache::GetIBlockCacheTag($arParams['IBLOCK_ID']), 'GROUP' => array('ID'), 'MULTI' => 'N')), array('ID' => $arGoodsSectionsIDs, 'IBLOCK_ID' => $arParams['IBLOCK_ID']), false, array('ID', 'IBLOCK_ID','DESCRIPTION', 'PICTURE', 'DETAIL_PICTURE', 'UF_TOP_SEO', 'SECTION_PAGE_URL'));
}

// group elements by sections
foreach($arResult['ITEMS'] as $key => $arItem){
	$SID = ($arItem['IBLOCK_SECTION_ID'] ? $arItem['IBLOCK_SECTION_ID'] : 0);
	if(strlen($arItem['DISPLAY_PROPERTIES']['REDIRECT']['VALUE']))
	{
		$arItem['DETAIL_PAGE_URL'] = $arItem['DISPLAY_PROPERTIES']['REDIRECT']['VALUE'];
		$arResult['ITEMS'][$key]['DETAIL_PAGE_URL'] = $arItem['DISPLAY_PROPERTIES']['REDIRECT']['VALUE'];
	}
	if($arItem['DISPLAY_PROPERTIES'])
		$arItem['SHOW_PROPS'] = CAllcorp2::PrepareItemProps($arItem['DISPLAY_PROPERTIES']);
	
	if($arItem['IBLOCK_SECTION_ID'])
	{
		foreach($arItem['SECTIONS'] as $id => $name)
			$arResult['SECTIONS'][$id]['ITEMS'][$arItem['ID']] = $arItem;
	}
	// $arResult['SECTIONS'][$SID]['ITEMS'][$arItem['ID']] = $arItem;
}

// unset empty sections
if(is_array($arResult['SECTIONS'])){
	foreach($arResult['SECTIONS'] as $i => $arSection){
		if(!$arSection['ITEMS']){
			unset($arResult['SECTIONS'][$i]);
		}
	}
}
?>