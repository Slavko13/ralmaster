<?
$arRootItems = $arChildItems = array();
foreach($arResult['SECTIONS'] as $key => $arSection)
{
	if($arSection['DEPTH_LEVEL'] == 1)
		$arRootItems[$arSection['ID']] = $arSection;
	else
		$arChildItems[$arSection['ID']] = $arSection;
	unset($arResult['SECTIONS'][$key]);
}
if($arChildItems)
{
	foreach($arChildItems as $key => $arSection)
	{
		$arRootSection = CCache::CIBlockSection_GetList(array('CACHE' => array('MULTI' =>'N', 'TAG' => CCache::GetIBlockCacheTag($arParams['IBLOCK_ID']))), array('GLOBAL_ACTIVE' => 'Y', '<=LEFT_BORDER' => $arSection['LEFT_MARGIN'], '>=RIGHT_BORDER' => $arSection['RIGHT_MARGIN'], 'DEPTH_LEVEL' => 1, 'IBLOCK_ID' => $arParams['IBLOCK_ID']), false, array('ID', 'NAME', 'IBLOCK_ID', 'SORT', 'SECTION_PAGE_URL', 'PICTURE', 'UF_TOP_SEO'));
		if (0 < $arRootSection["PICTURE"]) {
			$mxPicture = CFile::GetFileArray($arRootSection["PICTURE"]);
		
			$arRootSection["PICTURE"] = $mxPicture;
			if ($arRootSection["PICTURE"]) {
				$arRootSection["PICTURE"]["ALT"] = $arRootSection["IPROPERTY_VALUES"]["SECTION_PICTURE_FILE_ALT"];
				if ($arRootSection["PICTURE"]["ALT"] == "") {
					$arRootSection["PICTURE"]["ALT"] = $arRootSection["NAME"];
				}
		
				$arRootSection["PICTURE"]["TITLE"] = $arRootSection["IPROPERTY_VALUES"]["SECTION_PICTURE_FILE_TITLE"];
				if ($arRootSection["PICTURE"]["TITLE"] == "") {
					$arRootSection["PICTURE"]["TITLE"] = $arRootSection["NAME"];
				}
			}
		}
		if(!isset($arRootItems[$arRootSection['ID']])){
			$arRootItems[$arRootSection['ID']] = $arRootSection;
		}
		$arRootItems[$arRootSection['ID']]['ITEMS'][] = $arSection;
	}
}

\Bitrix\Main\Type\Collection::sortByColumn($arRootItems, array('SORT' => array(SORT_NUMERIC, SORT_ASC), 'NAME' => SORT_ASC));

$arResult['SECTIONS'] = $arRootItems;
?>