<?
foreach($arResult['ITEMS'] as $key => $arItem){
	CAllcorp2::getFieldImageData($arResult['ITEMS'][$key], array('PREVIEW_PICTURE'));
}
?>