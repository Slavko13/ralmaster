<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?
function custom_mb_in_array(array $_hayStack,$_needle){
	$_needle = (is_array($_needle) && isset($_needle['TEXT'])) ? $_needle['TEXT'] : $_needle;
	foreach ($_hayStack as $value)
	{
		if((mb_strtolower($value)) == (mb_strtolower($_needle)))
			return true;
	}
	return false;
}

if($arResult['ITEMS'])
{
	$arResult['PROPS'] = array();
	$arHideProps = array('PRICE', 'FORM_ORDER', 'FILTER_PRICE');
	$arPlusValue = array('+', 'true', 'y', GetMessage('YES'), GetMessage('TRUE'));
	$arMinusValue = array('-', 'false', 'n', GetMessage('NO'), GetMessage('FALSE'));
	
	foreach($arResult['ITEMS'] as $key_main => $arItem)
	{
		CAllcorp2::getFieldImageData($arResult['ITEMS'][$key_main], array('PREVIEW_PICTURE'));
		if (!empty($arItem['PROPERTIES'])) {
			foreach ($arItem['PROPERTIES'] as $key => $arProp) {
				if (!empty($arProp['VALUE']) && !in_array($arProp['CODE'], $arHideProps)) {
					$arResult['PROPS'][$key]['NAME'] = $arProp['NAME'];

					if (custom_mb_in_array($arPlusValue, $arProp['VALUE'])) {
						$arResult['ITEMS'][$key_main]['PROPERTIES'][$key]['TYPE'] = 'Y';
					} elseif (custom_mb_in_array($arMinusValue, $arProp['VALUE'])) {
						$arResult['ITEMS'][$key_main]['PROPERTIES'][$key]['TYPE'] = 'N';
					}
		
					if (is_array($arProp['VALUE']) && !empty($arProp['VALUE']['TYPE']) && !empty($arProp['VALUE']['TEXT'])) {
						$arResult['ITEMS'][$key_main]['PROPERTIES'][$key]['VALUE'] = $arProp['VALUE']['TYPE'] === 'HTML'
							? $arProp['~VALUE']['TEXT']
							: $arProp['VALUE']['TEXT'];
					}
				}
			}
		}
	}
}
?>


