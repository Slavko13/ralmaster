<?
$arGoodsSectionsIDs = array();
foreach($arResult['ITEMS'] as $key => $arItem)
{
	if($arItem['IBLOCK_SECTION_ID']){
		$resGroups = CIBlockElement::GetElementGroups($arItem['ID'], true, array('ID'));
		while($arGroup = $resGroups->Fetch())
		{
			$arResult['ITEMS'][$key]['SECTIONS'][$arGroup['ID']] = $arGroup['ID'];
			$arGoodsSectionsIDs[$arGroup['ID']] = $arGroup['ID'];
		}
	}
	CAllcorp2::getFieldImageData($arResult['ITEMS'][$key], array('PREVIEW_PICTURE'));
}
if($arGoodsSectionsIDs){
	$arResult['SECTIONS'] = CCache::CIBLockSection_GetList(array('SORT' => 'ASC', 'NAME' => 'ASC', 'CACHE' => array('TAG' => CCache::GetIBlockCacheTag($arParams['IBLOCK_ID']), 'GROUP' => array('ID'), 'MULTI' => 'N')), array('ID' => $arGoodsSectionsIDs));
	foreach($arResult['ITEMS'] as $key => $arItem)
	{
		if($arItem['IBLOCK_SECTION_ID'])
		{
			foreach($arItem['SECTIONS'] as $id => $name)
				$arResult['ITEMS'][$key]['SECTIONS'][$id] = $arGoodsSections[$id];
		}
	}
}
?>