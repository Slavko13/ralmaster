<?define("STATISTIC_SKIP_ACTIVITY_CHECK", "true");?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?if(isset($_GET['term']) && $_GET['term'])
{
	if(\Bitrix\Main\Loader::includeModule('aspro.allcorp2'))
	{
		$arRegions = CAllcorp2Regionality::getRegions();
		if($arRegions)
		{
			$city = iconv('UTF-8', LANG_CHARSET, $_GET['term']);
			$arRegionsJS = array();
			$bFuncExists = (function_exists('mb_strtolower'));

			$arSortFields=array("NAME" => SORT_ASC);
			\Bitrix\Main\Type\Collection::sortByColumn($arRegions, $arSortFields);

			$type_regions = \Bitrix\Main\Config\Option::get('aspro.allcorp2', 'REGIONALITY_TYPE', 'ONE_DOMAIN');
			$host = (CMain::IsHTTPS() ? 'https://' : 'http://');
			$uri = ralmaster_safe_local_path($_GET['url'] ?? '', SITE_DIR);

			foreach($arRegions as $arTmpRegion)
			{
				if($bFuncExists)
				{
					$cityNameTmp = mb_strtolower($arTmpRegion['NAME']);
					$city = mb_strtolower($city);
				}
				else
				{
					$cityNameTmp = strtolower($arTmpRegion['NAME']);
					$city = strtolower($city);
				}
				if(strpos($cityNameTmp, $city) !== false)
				{
					$cityName = iconv(LANG_CHARSET, 'UTF-8', $arTmpRegion['NAME']);
					$href = $uri;
					$regionHost = ralmaster_safe_configured_host($arTmpRegion['PROPERTY_MAIN_DOMAIN_VALUE'] ?? '');
					if($regionHost !== '' && $type_regions == 'SUBDOMAIN')
						$href = $host.$regionHost.$uri;

					$arRegionsJS[] = array(
						'label' => $cityName,
						'HREF' => $href,
						'ID' => $arTmpRegion['ID'],
					);
				}
			}
			header('Content-Type: application/json; charset=UTF-8');
			header('Cache-Control: no-store');
			if($arRegionsJS)
				echo json_encode($arRegionsJS, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
			else
				echo json_encode(array());
		}
		else
			echo json_encode(array());
	}
	else
		echo json_encode(array());
}
?>
