<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die;?>
<?if($arResult['DISPLAY_PROPERTIES'])
{
	$arResult['DISPLAY_PROPERTIES_FORMATED'] = CAllcorp2::PrepareItemProps($arResult['DISPLAY_PROPERTIES']);
}?>
<?
if($arResult['PROPERTIES'])
{
	foreach($arResult['PROPERTIES'] as $key2 => $arProp)
	{
		if(strpos($key2, 'SOCIAL') !== false && $arProp['VALUE'])
			$arResult['SOCIAL_PROPS'][] = $arProp;
	}
}
?>