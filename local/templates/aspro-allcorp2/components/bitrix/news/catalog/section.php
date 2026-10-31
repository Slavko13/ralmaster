<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$this->setFrameMode(true);
global $APPLICATION, $arTheme, $arRegion, $isCatalog;
if(!$isCatalog) {
	$APPLICATION->AddViewContent('right_block_class', 'catalog_page ');
}

$bOrderViewBasket = (trim($arTheme['ORDER_VIEW']['VALUE']) === 'Y');

// get section items count and subsections
$arItemFilter = CAllcorp2::GetCurrentSectionElementFilter($arResult["VARIABLES"], $arParams);
$arSectionFilter = CAllcorp2::GetCurrentSectionFilter($arResult["VARIABLES"], $arParams);

$dbProperty = CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "CODE" => "LINK_REGION"));
if(!$dbProperty->SelectedRowsCount() && $arItemFilter['PROPERTY_LINK_REGION'])
	unset($arItemFilter['PROPERTY_LINK_REGION']);

$itemsCnt = CCache::CIblockElement_GetList(array("CACHE" => array("TAG" => CCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), $arItemFilter, array());

$arSection = CCache::CIblockSection_GetList(array("CACHE" => array("TAG" => CCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]), "MULTI" => "N")), $arSectionFilter, false, array('ID', 'IBLOCK_ID', 'NAME', 'CODE', 'SECTION_PAGE_URL', 'DESCRIPTION', 'PICTURE', 'DETAIL_PICTURE',  'UF_VIEWTYPE', 'UF_TOP_SEO'), true);
CAllcorp2::AddMeta(
	array(
		'og:description' => $arSection['DESCRIPTION'],
		'og:image' => (($arSection['PICTURE'] || $arSection['DETAIL_PICTURE']) ? CFile::GetPath(($arSection['PICTURE'] ? $arSection['PICTURE'] : $arSection['DETAIL_PICTURE'])) : false),
	)
);
$arSubSectionFilter = CAllcorp2::GetCurrentSectionSubSectionFilter($arResult["VARIABLES"], $arParams, $arSection['ID']);
$arSubSections = CCache::CIblockSection_GetList(array("CACHE" => array("TAG" => CCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]), "MULTI" => "Y")), $arSubSectionFilter, false, array("ID", "DEPTH_LEVEL"));

if ($arSection && $arParams['SECTION_TYPE_VIEW'] === 'section_product_paper') {
	include __DIR__.'/page_blocks/section_product_paper.php';
	CAllcorp2::checkBreadcrumbsChain($arParams, $arSection);
	return;
}
?>

<?if(!$arSection && $arParams['SET_STATUS_404'] !== 'Y'):?>
	<div class="alert alert-warning"><?=GetMessage("SECTION_NOTFOUND")?></div>
<?elseif(!$arSection && $arParams['SET_STATUS_404'] === 'Y'):?>
	<?CAllcorp2::goto404Page();?>
<?else:?>
	<?CAllcorp2::CheckComponentTemplatePageBlocksParams($arParams, __DIR__);?>
	<?// rss
	if($arParams['USE_RSS'] !== 'N'){
		CAllcorp2::ShowRSSIcon(CComponentEngine::makePathFromTemplate($arResult['FOLDER'].$arResult['URL_TEMPLATES']['rss_section'], array_map('urlencode', $arResult['VARIABLES'])));
	}?>
	<?
	//seo
	$arSeoItems = CCache::CIBLockElement_GetList(array('CACHE' => array("MULTI" =>"Y", "TAG" => CCache::GetIBlockCacheTag($arParams["LANDING_IBLOCK_ID"]))), array("IBLOCK_ID" => $arParams["LANDING_IBLOCK_ID"], "ACTIVE"=>"Y"), false, false, array("ID", "IBLOCK_ID", "NAME", "PREVIEW_TEXT", "DETAIL_PICTURE", "PROPERTY_FILTER_URL", "PROPERTY_LINK_REGION", "PROPERTY_FORM_QUESTION", "PROPERTY_TIZERS", "PROPERTY_SECTION", "DETAIL_TEXT", "ElementValues"));
	$arSeoItem = array();
	if($arSeoItems)
	{
		$current_url =  $APPLICATION->GetCurDir();
		$url = urldecode(str_replace(' ', '+', $current_url));
		$iLandingItemID = 0;
		foreach($arSeoItems as $arItem)
		{
			if(urldecode($arItem["PROPERTY_FILTER_URL_VALUE"]) == $url)
			{
				$arSeoItem = $arItem;
				$iLandingItemID = $arSeoItem['ID'];
				break;
			}
		}
		if($arRegion)
		{
			if($arSeoItem)
			{
				if($arSeoItem['PROPERTY_LINK_REGION_VALUE'])
				{
					if(!is_array($arSeoItem['PROPERTY_LINK_REGION_VALUE']))
						$arSeoItem['PROPERTY_LINK_REGION_VALUE'] = (array)$arSeoItem['PROPERTY_LINK_REGION_VALUE'];
					if(!in_array($arRegion['ID'], $arSeoItem['PROPERTY_LINK_REGION_VALUE']))
						$arSeoItem = array();
				}
			}
			else
			{
				foreach($arSeoItems as $arItem)
				{
					if($arItem['PROPERTY_LINK_REGION_VALUE'])
					{
						if(!is_array($arItem['PROPERTY_LINK_REGION_VALUE']))
							$arItem['PROPERTY_LINK_REGION_VALUE'] = (array)$arItem['PROPERTY_LINK_REGION_VALUE'];
						if(!in_array($arRegion['ID'], $arItem['PROPERTY_LINK_REGION_VALUE']))
							$arTmpRegionsLanding[] = $arItem['ID'];
					}
				}
			}
		}
	}
	?>
	<?$isAjax="N";?>
<?if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest"  && isset($_GET["ajax_get"]) && $_GET["ajax_get"] == "Y" || (isset($_GET["AJAX_REQUEST"]) && $_GET["AJAX_REQUEST"]=="Y")){
	$isAjax="Y";
}?>
	
	<div class="main-catalog-wrapper">
		<div class="section-content-wrapper">
			<?if(strpos($_SERVER['REQUEST_URI'], 'PAGEN') === false && !$arSeoItem):?>
				<div><?$APPLICATION->ShowViewContent('sotbit_seometa_top_desc');?></div>
				<?if($arParams['SHOW_TOP_DESCRIPTION'] != 'N' && $arSection['UF_TOP_SEO']):?>
					<div class="introtext">
						<p><?=$arSection['UF_TOP_SEO'];?></p>
					</div>
				<?endif;?>
			<?endif;?>

			<?if($arSubSections):?>
				<?// sections list?>
				<?$sViewElementTemplate = ($arParams["SECTION_TYPE_VIEW"] == "FROM_MODULE" ? $arTheme["SECTION_TYPE_VIEW_CATALOG"]["VALUE"] : $arParams["SECTION_TYPE_VIEW"]);?>
				<?@include_once('page_blocks/'.$sViewElementTemplate.'.php');?>
			<?endif;?>
			<?// section elements?>
			<?if($itemsCnt ):?>
				<?$sViewElementTemplate = ($arParams["SECTION_ELEMENTS_TYPE_VIEW"] == "FROM_MODULE" ? $arTheme["ELEMENTS_CATALOG_PAGE"]["VALUE"] : $arParams["SECTION_ELEMENTS_TYPE_VIEW"]);?>
				<?@include_once('page_blocks/'.$sViewElementTemplate.'.php');?>
			<?endif;?>
			<?if(!$arSubSections && !$itemsCnt && !$arLandingFilter ):?>
				<div class="alert alert-warning"><?=GetMessage("SECTION_EMPTY")?></div>
			<?endif?>
			<?if(!$itemsCnt && $arSection):?>
				<?
				$ipropValues = new \Bitrix\Iblock\InheritedProperty\SectionValues($arParams["IBLOCK_ID"], IntVal($arSection["ID"]));
				$arValues = $ipropValues->getValues();
				if($arParams["SET_TITLE"] !== 'N'){
					$page_h1 = $arValues['SECTION_PAGE_TITLE'] ? $arValues['SECTION_PAGE_TITLE'] : $arSection["NAME"];
					if($page_h1){
						$APPLICATION->SetTitle($page_h1);
					}
					else{
						$APPLICATION->SetTitle($arSection["NAME"]);
					}
				}
				$page_title = $arValues['SECTION_META_TITLE'] ? $arValues['SECTION_META_TITLE'] : $arSection["NAME"];
				if($page_title){
					$APPLICATION->SetPageProperty("title", $page_title);
				}
				if(isset($arValues['SECTION_META_DESCRIPTION']) && $arValues['SECTION_META_DESCRIPTION'] ){
					$APPLICATION->SetPageProperty("description", $arValues['SECTION_META_DESCRIPTION']);
				}
				if(isset($arValues['SECTION_META_KEYWORDS']) && $arValues['SECTION_META_KEYWORDS']){
					$APPLICATION->SetPageProperty("keywords", $arValues['SECTION_META_KEYWORDS']);
				}
				?>
			<?endif?>
		
			<?if(strpos($_SERVER['REQUEST_URI'], 'PAGEN') === false && !$arSeoItem):?>
				<div class="text_after_items">
					
					<?if($arSection['DESCRIPTION']):?>
						<?=$arSection['DESCRIPTION'];?>
					<?endif;?>
					<div><?$APPLICATION->ShowViewContent('sotbit_seometa_bottom_desc');?></div>
					<div><?$APPLICATION->ShowViewContent('sotbit_seometa_add_desc');?></div>
				</div>
			<?endif;?>
			
		</div>
		<?if(isset($_REQUEST['bxajaxid']) && $_REQUEST['bxajaxid']):?>
			<? 
			$add = 'narrow_'.$APPLICATION->GetProperty("MENU");
			$remove = 'narrow_'.($APPLICATION->GetProperty("MENU") == 'Y' ? 'N' : 'Y');
			?>
			<script>
				if($('.right_block').length){
					$('.right_block').addClass('<?=$add?>');
					$('.right_block').removeClass('<?=$remove?>');
				}
				
				$(window).resize();
			</script>
		<?endif;?>
		<?if($APPLICATION->GetProperty("MENU") != "N" && !defined("ERROR_404")):?>
			<?CAllcorp2::ShowPageType('left_block');?>
		<?endif;?>
	</div>
<?endif;?>
<?if(\Bitrix\Main\Loader::includeModule("sotbit.seometa") && $itemsCnt):?>
	<?$APPLICATION->IncludeComponent(
		"sotbit:seo.meta",
		".default",
		array(
			"FILTER_NAME" => $arParams["FILTER_NAME"],
			"SECTION_ID" => $arSection['ID'],
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
		)
	);?>
<?endif;?>

<?CAllcorp2::checkBreadcrumbsChain($arParams, $arSection);?>
