<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?
// get section items count and subsections
$arItemFilter = CAllcorp2::GetCurrentSectionElementFilter($arResult["VARIABLES"], $arParams);
$arSectionFilter = CAllcorp2::GetCurrentSectionFilter($arResult["VARIABLES"], $arParams);
$itemsCnt = CCache::CIblockElement_GetList(array("CACHE" => array("TAG" => CCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), $arItemFilter, array());
$arSection = CCache::CIblockSection_GetList(array("CACHE" => array("TAG" => CCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]), "MULTI" => "N")), $arSectionFilter, false, array('ID', 'NAME', 'CODE', 'SECTION_PAGE_URL', 'DESCRIPTION', 'PICTURE', 'DETAIL_PICTURE', 'IBLOCK_ID', 'UF_TOP_SEO'));
CAllcorp2::AddMeta(
	array(
		'og:description' => $arSection['DESCRIPTION'],
		'og:image' => (($arSection['PICTURE'] || $arSection['DETAIL_PICTURE']) ? CFile::GetPath(($arSection['PICTURE'] ? $arSection['PICTURE'] : $arSection['DETAIL_PICTURE'])) : false),
	)
);
$arSubSectionFilter = CAllcorp2::GetCurrentSectionSubSectionFilter($arResult["VARIABLES"], $arParams, $arSection['ID']);
$arSubSections = CCache::CIblockSection_GetList(array("CACHE" => array("TAG" => CCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]), "MULTI" => "Y")), $arSubSectionFilter, false, array("ID", "DEPTH_LEVEL"));

if ($arSection && $arParams['SECTION_TYPE_VIEW'] === 'section_service_paper') {
	include __DIR__.'/page_blocks/section_service_paper.php';
	return;
}
?>
<?if(!$arSection && $arParams['SET_STATUS_404'] !== 'Y'):?>
<!--<div class="alert alert-warning"><?//=GetMessage("SECTION_NOTFOUND")?></div>-->
<?elseif(!$arSection && $arParams['SET_STATUS_404'] === 'Y'):?>
	<?CAllcorp2::goto404Page();?>
<?else:?>
	<?CAllcorp2::CheckComponentTemplatePageBlocksParams($arParams, __DIR__);?>
	<?// rss
	if($arParams['USE_RSS'] !== 'N'){
		CAllcorp2::ShowRSSIcon(CComponentEngine::makePathFromTemplate($arResult['FOLDER'].$arResult['URL_TEMPLATES']['rss_section'], array_map('urlencode', $arResult['VARIABLES'])));
	}?>
	<?if(!$arSubSections && !$itemsCnt):?>
<!--<div class="alert alert-warning"><?//=GetMessage("SECTION_EMPTY")?></div>-->
	<?endif;?>

	<div class="main-section-wrapper">
		<?if($arSection['UF_TOP_SEO'] && strpos($_SERVER['REQUEST_URI'], 'PAGEN') === false):?>
			<div class="text_before_items">
				<p class="introtext"><?=$arSection['UF_TOP_SEO'];?></p>
			</div>
		<?endif;?>
		<?global $arTheme;?>
		<?if($arSubSections):?>
			<?// sections list?>
			<?$sViewElementTemplate = ($arParams["SECTION_TYPE_VIEW"] == "FROM_MODULE" ? $arTheme["SECTION_TYPE_VIEW"]["VALUE"] : $arParams["SECTION_TYPE_VIEW"]);?>
			<?@include_once('page_blocks/'.$sViewElementTemplate.'.php');?>
		<?endif;?>
	    
		<?if(strlen($arParams["FILTER_NAME"])):?>
			<?$GLOBALS[$arParams["FILTER_NAME"]] = array_merge((array)$GLOBALS[$arParams["FILTER_NAME"]], $arItemFilter);?>
		<?else:?>
			<?$arParams["FILTER_NAME"] = "arrFilter";?>
			<?$GLOBALS[$arParams["FILTER_NAME"]] = $arItemFilter;?>
		<?endif;?>

		<?// section elements?>
		<?$sViewElementTemplate = ($arParams["SECTION_ELEMENTS_TYPE_VIEW"] == "FROM_MODULE" ? $arTheme["ELEMENTS_PAGE"]["VALUE"] : $arParams["SECTION_ELEMENTS_TYPE_VIEW"]);?>
		<?@include_once('page_blocks/'.$sViewElementTemplate.'.php');?>

		<?if($arSection['DESCRIPTION'] && strpos($_SERVER['REQUEST_URI'], 'PAGEN') === false):?>
			<div class="text_after_items">
				<?=$arSection['DESCRIPTION'];?>
			</div>
		<?endif;?>
	</div>
<?endif;?>
