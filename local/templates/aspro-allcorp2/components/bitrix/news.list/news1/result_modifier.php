<?if(!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();?>
<?
if($arResult['ITEMS'])
{
	$arGoodsSections = $arGoodsSectionsIDs = array();
	foreach($arResult["ITEMS"] as $key => $arItem)
	{
		if($arItem['IBLOCK_SECTION_ID'] && $arParams['SHOW_SECTION'] == 'Y')
		{
			$resGroups = CIBlockElement::GetElementGroups($arItem['ID'], true, array('ID'));
			while($arGroup = $resGroups->Fetch())
			{
				$arResult['ITEMS'][$key]['SECTIONS'][$arGroup['ID']] = $arGroup['ID'];
				$arGoodsSectionsIDs[$arGroup['ID']] = $arGroup['ID'];
			}
		}
		$arResult["ITEMS"][$key]["DETAIL_PAGE_URL"] = CAllcorp2::FormatNewsUrl($arItem);
		$arResult["ITEMS"][$key]["PROPS_FORMAT"] = CAllcorp2::PrepareItemProps($arItem['DISPLAY_PROPERTIES']);
		CAllcorp2::getFieldImageData($arResult['ITEMS'][$key], array('PREVIEW_PICTURE'));
	}
	if($arGoodsSectionsIDs)
	{
		$arGoodsSections = CCache::CIBLockSection_GetList(array('CACHE' => array('TAG' => CCache::GetIBlockCacheTag($arParams['IBLOCK_ID']), 'GROUP' => array('ID'), 'MULTI' => 'N', 'RESULT' => array('NAME'))), array('ID' => $arGoodsSectionsIDs), false, array('ID', 'NAME'));
		foreach($arResult['ITEMS'] as $key => $arItem)
		{
			if($arItem['IBLOCK_SECTION_ID'])
			{
				foreach($arItem['SECTIONS'] as $id => $name)
					$arResult['ITEMS'][$key]['SECTIONS'][$id] = $arGoodsSections[$id];
			}
		}
	}
}
?>