<?if( !defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true ) die();?>
<?
$this->setFrameMode(true);
if($_arResult = CAllcorp2::CheckSmartFilterSEF($arParams, $component)){
	$arResult = $_arResult;
	include  __DIR__.'/section.php';
	return;
}

global $arTheme, $APPLICATION, $isCatalog;
if(!$isCatalog) {
	$APPLICATION->AddViewContent('right_block_class', 'catalog_page ');
}

$bOrderViewBasket = (trim($arTheme['ORDER_VIEW']['VALUE']) === 'Y');
?>
<div class="main-catalog-wrapper">
	<div class="section-content-wrapper">
		<?// intro text?>
		<div class="text_before_items">
			<?$APPLICATION->IncludeComponent(
				"bitrix:main.include",
				"",
				Array(
					"AREA_FILE_SHOW" => "page",
					"AREA_FILE_SUFFIX" => "inc",
					"EDIT_TEMPLATE" => ""
				)
			);?>
		</div>
		<?
		// get section items count and subsections
		$arItemFilter = CAllcorp2::GetCurrentSectionElementFilter($arResult["VARIABLES"], $arParams, false);
		$arSubSectionFilter = CAllcorp2::GetCurrentSectionSubSectionFilter($arResult["VARIABLES"], $arParams, false);
		$itemsCnt = CCache::CIBlockElement_GetList(array("CACHE" => array("TAG" => CCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), $arItemFilter, array());
		$arSubSections = CCache::CIBlockSection_GetList(array("CACHE" => array("TAG" => CCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]), "MULTI" => "Y")), $arSubSectionFilter, false, array("ID"));
		$isAjax = 'N';

		// rss
		if($arParams['USE_RSS'] !== 'N'){
			CAllcorp2::ShowRSSIcon($arResult['FOLDER'].$arResult['URL_TEMPLATES']['rss']);
		}
		?>
		<?if(!$itemsCnt && !$arSubSections):?>
			<div class="alert alert-warning"><?=GetMessage("SECTION_EMPTY")?></div>
		<?else:?>
			<?CAllcorp2::CheckComponentTemplatePageBlocksParams($arParams, __DIR__);?>
			<?// sections?>
			<?$sViewElementTemplate = ($arParams["SECTIONS_TYPE_VIEW"] == "FROM_MODULE" ? $arTheme["SECTIONS_TYPE_VIEW_CATALOG"]["VALUE"] : $arParams["SECTIONS_TYPE_VIEW"]);?>
			<?@include_once('page_blocks/'.$sViewElementTemplate.'.php');?>
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
