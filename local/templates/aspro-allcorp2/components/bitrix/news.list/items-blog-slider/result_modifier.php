<?
if($arResult['ITEMS'])
{
	foreach($arResult['ITEMS'] as $i => $arItem)
	{
		CAllcorp2::getFieldImageData($arResult['ITEMS'][$i], array('PREVIEW_PICTURE'));
	}
	
}
?>