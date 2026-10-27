<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?
// geting section items count and section [ID, NAME]
$arItemFilter = CAllcorp2::GetCurrentSectionElementFilter($arResult["VARIABLES"], $arParams);
$arSectionFilter = CAllcorp2::GetCurrentSectionFilter($arResult["VARIABLES"], $arParams);

if($arParams['CACHE_GROUPS'] == 'Y')
{
	$arSectionFilter['CHECK_PERMISSIONS'] = 'Y';
	$arSectionFilter['GROUPS'] = $GLOBALS["USER"]->GetGroups();
}

$arSection = CCache::CIblockSection_GetList(array("CACHE" => array("TAG" => CCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]), "MULTI" => "N")), $arSectionFilter, false, array('ID', 'NAME', 'DESCRIPTION', 'PICTURE', 'DETAIL_PICTURE', 'UF_TOP_SEO'), true);
CAllcorp2::AddMeta(
	array(
		'og:description' => $arSection['DESCRIPTION'],
		'og:image' => (($arSection['PICTURE'] || $arSection['DETAIL_PICTURE']) ? CFile::GetPath(($arSection['PICTURE'] ? $arSection['PICTURE'] : $arSection['DETAIL_PICTURE'])) : false),
	)
);

$bFoundSection = false;
$arYears = array();

if($arSection)
{
	$bFoundSection = true;
	$itemsCnt = CCache::CIblockElement_GetList(array("CACHE" => array("TAG" => CCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), $arItemFilter, array());
}

global $arTheme;?>

<?if(!$bFoundSection && $arParams['SET_STATUS_404'] !== 'Y'):?>
	<div class="alert alert-warning"><?=GetMessage("SECTION_NOTFOUND")?></div>
<?elseif(!$bFoundSection && $arParams['SET_STATUS_404'] === 'Y'):?>
	<?CAllcorp2::goto404Page();?>
<?else:?>
	<?if($arTheme['SHOW_PROJECTS_MAP']['VALUE'] == 'Y'):?>
		<?
		$mapLAT = $mapLON = $iCountShops =0;
		$arItems = $arSections = $arPlacemarks = array();

		$arItems = CCache::CIblockElement_GetList(array("CACHE" => array("TAG" => CCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array('IBLOCK_ID' => $arParams['IBLOCK_ID'], 'INCLUDE_SUBSECTIONS' => 'Y', 'ACTIVE' => 'Y', 'GLOBAL_ACTIVE' => 'Y', 'ACTIVE_DATE' => 'Y', 'SECTION_ID' => $arSection['ID']), false, false, array('ID', 'NAME', 'PREVIEW_PICTURE', 'PROPERTY_MAP', 'DETAIL_PAGE_URL', 'IBLOCK_SECTION_ID', 'PROPERTY_INFO'));

		foreach($arItems as $arItem){
			if(!in_array($arItem['IBLOCK_SECTION_ID'] ,$arSections)){
				if(is_array($arItem['IBLOCK_SECTION_ID']) ){
					foreach ($arItem['IBLOCK_SECTION_ID'] as $tmpSection) {
						if(!in_array($tmpSection,$arSections)) {
							$arSections[] = $tmpSection;
						}
					}
				} else {
					$arSections[] = $arItem['IBLOCK_SECTION_ID'];
				}
			}
		}

		if($arSections){
			$arSections = CCache::CIBlockSection_GetList(array("CACHE" => array("TAG" => CCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]), "MULTI" => "N", 'GROUP' => 'ID')), array('IBLOCK_ID' => $arParams['IBLOCK_ID'], 'ID' => $arSections), false, array("ID", 'NAME'));
		}
		if($arItems){
			foreach($arItems as $arItem)
			{
				if($arItem['PROPERTY_MAP_VALUE']){
					$arCoords = explode(',', $arItem['PROPERTY_MAP_VALUE']);
					$mapLAT += $arCoords[0];
					$mapLON += $arCoords[1];
					$html = '<div class="pane_info_wrapper"><div class="pane_info clearfix">';
					if($arItem['PREVIEW_PICTURE']){
						$html .= '<div class="image"><a href="'.$arItem['DETAIL_PAGE_URL'].'"><img src="'.CFile::GetPath($arItem['PREVIEW_PICTURE']).'" alt="'.$arItem['NAME'].'" title="'.$arItem['NAME'].'"></a></div>';
					}
					$html .= '<div class="body-info'.(!$arItem['PREVIEW_PICTURE'] ? ' wti' : '').'">';
					
					if(is_array($arItem['IBLOCK_SECTION_ID']) || is_array($arItem['DETAIL_PAGE_URL']) ){
						if(is_array($arItem['IBLOCK_SECTION_ID'])){
							if(isset($arSection['ID']) && $arSection['NAME']){
								$html .= '<div class="section_name">'.$arSection['NAME'].'</div>';
							}
						}
						if(is_array($arItem['DETAIL_PAGE_URL']) ){
							$html .= '<div class="title">'.'<div class="name"><a class="dark-color" href="'.$arItem["DETAIL_PAGE_URL"][$arSection["ID"]].'">'.$arItem['NAME'].'</a></div></div>';
							if(isset($arItem['PROPERTY_INFO_VALUE']) && $arItem['PROPERTY_INFO_VALUE']['TEXT']){
								$html .= '<div class="info">'.$arItem['~PROPERTY_INFO_VALUE']['TEXT'].'</div>';
							}
						}
					}else{
						if(isset($arSections[$arItem['IBLOCK_SECTION_ID']]) && $arSections[$arItem['IBLOCK_SECTION_ID']]['NAME']){
							$html .= '<div class="section_name">'.$arSections[$arItem['IBLOCK_SECTION_ID']]['NAME'].'</div>';
						}
						$html .= '<div class="title">'.'<div class="name"><a class="dark-color" href="'.$arItem['DETAIL_PAGE_URL'].'">'.$arItem['NAME'].'</a></div></div>';
						if(isset($arItem['PROPERTY_INFO_VALUE']) && $arItem['PROPERTY_INFO_VALUE']['TEXT']){
							$html .= '<div class="info">'.$arItem['~PROPERTY_INFO_VALUE']['TEXT'].'</div>';
						}
					}
					
					
					$html .= '</div></div></div>';
					$arPlacemarks[] = array(
						"ID" => $arItem["ID"],
						"LAT" => $arCoords[0],
						"LON" => $arCoords[1],
						"TEXT" => $arItem['NAME'],
						"HTML" => $html
					);
					++$iCountShops;
				}
			}
			if($iCountShops)
			{
				$mapLAT = floatval($mapLAT / $iCountShops);
				$mapLON = floatval($mapLON / $iCountShops);?>
				<?ob_start()?>
					<div class="contacts-page-map-top projects">
							<?if (CAllcorp2::GetFrontParametrValue('CONTACTS_TYPE_MAP') == 'YANDEX') {?>
							<?$APPLICATION->IncludeComponent(
								"bitrix:map.yandex.view",
								"map",
								array(
									"INIT_MAP_TYPE" => "MAP",
									"MAP_DATA" => serialize(array("yandex_lat" => $mapLAT, "yandex_lon" => $mapLON, "yandex_scale" => 19, "PLACEMARKS" => $arPlacemarks)),
									"MAP_WIDTH" => "100%",
									"MAP_HEIGHT" => "500",
									"CONTROLS" => array(
										0 => "ZOOM",
										1 => "TYPECONTROL",
										2 => "SCALELINE",
									),
									"OPTIONS" => array(
										0 => "ENABLE_DBLCLICK_ZOOM",
										1 => "ENABLE_DRAGGING",
									),
									"MAP_ID" => "MAP_v33",
									"API_KEY" => \Bitrix\Main\Config\Option::get("fileman","yandex_map_api_key",""),
									"COMPONENT_TEMPLATE" => "map"
								),
								false
							);
						}?>
						<?if (CAllcorp2::GetFrontParametrValue('CONTACTS_TYPE_MAP') == 'GOOGLE') {?>
							<?$APPLICATION->IncludeComponent(
								"bitrix:map.google.view",
								"map",
								array(
									"INIT_MAP_TYPE" => "ROADMAP",
									"MAP_DATA" => serialize(array("google_lat" => $mapLAT, "google_lon" => $mapLON, "google_scale" => 19, "PLACEMARKS" => $arPlacemarks)),
									"MAP_WIDTH" => "100%",
									"MAP_HEIGHT" => "500",
									"CONTROLS" => array(
										0 => "SMALL_ZOOM_CONTROL",
										1 => "TYPECONTROL",
										2 => "SCALELINE",
									),
									"OPTIONS" => array(
										0 => "ENABLE_DBLCLICK_ZOOM",
										1 => "ENABLE_DRAGGING",
									),
									"MAP_ID" => "MAP_v33",
									"API_KEY" => \Bitrix\Main\Config\Option::get("fileman","google_map_api_key",""),
									"COMPONENT_TEMPLATE" => "map"
								),
								false
							);
						}?>
					</div>
				<?$html=ob_get_clean();?>
				<?$APPLICATION->AddViewContent('top_section_filter_content', $html);?>
			<?}?>
		<?}?>
	<?endif;?>
	<?CAllcorp2::CheckComponentTemplatePageBlocksParams($arParams, __DIR__);?>
	<?// rss
	if($arParams['USE_RSS'] !== 'N'){
		CAllcorp2::ShowRSSIcon(CComponentEngine::makePathFromTemplate($arResult['FOLDER'].$arResult['URL_TEMPLATES']['rss_section'], array_map('urlencode', $arResult['VARIABLES'])));
	}?>
	<?if(!$itemsCnt):?>
		<div class="alert alert-warning"><?=GetMessage("SECTION_EMPTY")?></div>
	<?endif;?>

	<?$sViewElementsTemplate = ($arParams["SECTION_ELEMENTS_TYPE_VIEW"] == "FROM_MODULE" ? $arTheme["ELEMENTS_PROJECT_PAGE"]["VALUE"] : $arParams["SECTION_ELEMENTS_TYPE_VIEW"]);?>
	<?@include_once('page_blocks/'.$sViewElementsTemplate.'.php');?>
<?endif;?>
<?if($arYears && $bFoundSection)
{
	$APPLICATION->SetTitle($title_news);
	$APPLICATION->AddChainItem($title_news);
}?>