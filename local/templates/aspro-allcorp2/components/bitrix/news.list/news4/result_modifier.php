<?
$arGoodsSectionsIDs  = array();
foreach($arResult['ITEMS'] as $key => $arItem)
{
	CAllcorp2::getFieldImageData($arResult['ITEMS'][$key], array('PREVIEW_PICTURE'));
	if(strlen($arItem['DISPLAY_PROPERTIES']['REDIRECT']['VALUE']))
	{
		$arItem['DETAIL_PAGE_URL'] = $arItem['DISPLAY_PROPERTIES']['REDIRECT']['VALUE'];
		$arResult['ITEMS'][$key]['DETAIL_PAGE_URL'] = $arItem['DISPLAY_PROPERTIES']['REDIRECT']['VALUE'];
	}
	if($arItem['DISPLAY_PROPERTIES'])
		$arItem['SHOW_PROPS'] = CAllcorp2::PrepareItemProps($arItem['DISPLAY_PROPERTIES']);
}
?>