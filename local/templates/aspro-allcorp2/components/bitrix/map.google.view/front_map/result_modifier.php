<?
global $arRegion, $arTheme;


if($arTheme['USE_REGIONALITY']['DEPENDENT_PARAMS']['SHOW_REGION_CONTACT']['VALUE'] == 'Y' && $arRegion["PROPERTY_REGION_TAG_YANDEX_MAP_VALUE"])
{
	$arCoord = explode(",", $arRegion["PROPERTY_REGION_TAG_YANDEX_MAP_VALUE"]);
	// $arBaloonPhones = $arRegion['PHONES'];
	// $baloonShedule = $arRegion['PROPERTY_SHCEDULE_VALUE']['TEXT'];
	// $baloonAddress = $arRegion['PROPERTY_ADDRESS_VALUE']['TEXT'];
	// $baloonEmail = $arRegion['PROPERTY_EMAIL_VALUE'];


	// $html = '<div class="pane_info_wrapper"><div class="pane_info">';
	// $html .= '<div class="title">'.'<div class="name">'.$baloonAddress.'</div></div>';
	// if($baloonShedule)
	// {
	// 	$html .= '<div class="value-block schedule"><div>'.CAllcorp2::showIconSvg("clock colored", SITE_TEMPLATE_PATH."/images/svg/WorkingHours.svg").'<div>'.$baloonShedule.'</div></div></div>';
	// }
	// if($arBaloonPhones)
	// {
	// 	if(!is_array($arBaloonPhones))
	// 		$arBaloonPhones = (array)$arBaloonPhones;
	// 	$html .= '<div class="value-block phones"><div>'.CAllcorp2::showIconSvg("phone colored", SITE_TEMPLATE_PATH."/images/svg/Phone_black.svg");
	// 	foreach($arBaloonPhones as $phone)
	// 	{
	// 		$html .= '<div class="phone"><a rel="nofollow" href="tel:'.str_replace(array(' ', ',', '-', '(', ')'), '', $phone).'">'.$phone.'</a></div>';
	// 	}
	// 	$html .= '</div></div>';
	// }
	// if($baloonEmail)
	// {
	// 	if(!is_array($baloonEmail))
	// 		$baloonEmail = (array)$baloonEmail;
	// 	$html .= '<div class="value-block email"><div>'.CAllcorp2::showIconSvg("email colored", SITE_TEMPLATE_PATH."/images/svg/Email.svg");
	// 	foreach($baloonEmail as $email)
	// 	{
	// 		$html .= '<div><a rel="nofollow" href="mailto:'.$email.'">'.$email.'</a></div>';
	// 	}
	// 	$html .= '</div></div>';
	// }

	// $html .= '</div></div>';

// $baloonText = $html ? $html : $arResult['POSITION']['PLACEMARKS'][0]['TEXT'];


	$arResult['POSITION']['google_lat'] = $arCoord[0];
	$arResult['POSITION']['google_lon'] = $arCoord[1];
	$arTmpMark = array(
		"LON" => $arResult['POSITION']['google_lon'],
		"LAT" => $arResult['POSITION']['google_lat'],
		// "HTML" =>  $baloonText,

	);
	$arResult['POSITION']['PLACEMARKS'] = array();
	$arResult['POSITION']['PLACEMARKS'][] = $arTmpMark;
}
?>