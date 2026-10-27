<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
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

use \Bitrix\Main\Localization\Loc;
$bOrderViewBasket = (trim($arTheme['ORDER_VIEW']['VALUE']) === 'Y');

// get element
$arItemFilter = CAllcorp2::GetCurrentElementFilter($arResult['VARIABLES'], $arParams);

global $APPLICATION;
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/animation/animate.min.css');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/sly.js');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/vendor/jquery.bxslider.js');

$arElement = CCache::CIblockElement_GetList(array('CACHE' => array('TAG' => CCache::GetIBlockCacheTag($arParams['IBLOCK_ID']), 'MULTI' => 'N')), $arItemFilter, false, false, array('ID', 'PREVIEW_TEXT', 'IBLOCK_SECTION_ID', 'IBLOCK_ID', 'PREVIEW_PICTURE', 'DETAIL_PICTURE'));

$sViewElementTemplate = ($arParams["ELEMENT_TYPE_VIEW"] == "FROM_MODULE" ? $arTheme["CATALOG_PAGE_DETAIL"]["VALUE"] : $arParams["ELEMENT_TYPE_VIEW"]);

// get current section & element
if(isset($arResult["VARIABLES"]["SECTION_ID"]) && $arResult["VARIABLES"]["SECTION_ID"] > 0)
{
	$arSection = CCache::CIBlockSection_GetList(array('CACHE' => array("MULTI" =>"N", "TAG" => CCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array('GLOBAL_ACTIVE' => 'Y', "ID" => $arResult["VARIABLES"]["SECTION_ID"], "IBLOCK_ID" => $arParams["IBLOCK_ID"]), false, array("ID", "IBLOCK_ID", "UF_TIZERS", "NAME", "IBLOCK_SECTION_ID", "DEPTH_LEVEL", "LEFT_MARGIN", "RIGHT_MARGIN", "UF_ELEMENT_DETAIL"));
}
elseif(strlen(trim($arResult["VARIABLES"]["SECTION_CODE"])) > 0)
{
	$arSection = CCache::CIBlockSection_GetList(array('CACHE' => array("MULTI" =>"N", "TAG" => CCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array('GLOBAL_ACTIVE' => 'Y', "=CODE" => $arResult["VARIABLES"]["SECTION_CODE"], "IBLOCK_ID" => $arParams["IBLOCK_ID"]), false, array("ID", "IBLOCK_ID", "UF_TIZERS", "NAME", "IBLOCK_SECTION_ID", "DEPTH_LEVEL", "LEFT_MARGIN", "RIGHT_MARGIN", "UF_ELEMENT_DETAIL"));
}
//set offer view type
$typeTmpDetail = 0;
if ($arSection) {
	if($arSection['UF_ELEMENT_DETAIL'])
		$typeTmpDetail = $arSection['UF_ELEMENT_DETAIL'];
	else
	{
		if($arSection["DEPTH_LEVEL"] > 2)
		{
			$sectionParent = CCache::CIBlockSection_GetList(array('CACHE' => array("MULTI" =>"N", "TAG" => CCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array('GLOBAL_ACTIVE' => 'Y', "ID" => $arSection["IBLOCK_SECTION_ID"], "IBLOCK_ID" => $arParams["IBLOCK_ID"]), false, array("ID", "IBLOCK_ID", "NAME", "UF_ELEMENT_DETAIL"));
			if($sectionParent['UF_ELEMENT_DETAIL'] && !$typeTmpDetail)
				$typeTmpDetail = $sectionParent['UF_ELEMENT_DETAIL'];

			if(!$typeTmpDetail)
			{
				$sectionRoot = CCache::CIBlockSection_GetList(array('CACHE' => array("MULTI" =>"N", "TAG" => CCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array('GLOBAL_ACTIVE' => 'Y', "<=LEFT_BORDER" => $arSection["LEFT_MARGIN"], ">=RIGHT_BORDER" => $arSection["RIGHT_MARGIN"], "DEPTH_LEVEL" => 1, "IBLOCK_ID" => $arParams["IBLOCK_ID"]), false, array("ID", "IBLOCK_ID", "NAME", "UF_ELEMENT_DETAIL"));
				if($sectionRoot['UF_ELEMENT_DETAIL'] && !$typeTmpDetail)
					$typeTmpDetail = $sectionRoot['UF_ELEMENT_DETAIL'];
			}
		}
		else
		{
			$sectionRoot = CCache::CIBlockSection_GetList(array('CACHE' => array("MULTI" =>"N", "TAG" => CCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array('GLOBAL_ACTIVE' => 'Y', "<=LEFT_BORDER" => $arSection["LEFT_MARGIN"], ">=RIGHT_BORDER" => $arSection["RIGHT_MARGIN"], "DEPTH_LEVEL" => 1, "IBLOCK_ID" => $arParams["IBLOCK_ID"]), false, array("ID", "IBLOCK_ID", "NAME", "UF_ELEMENT_DETAIL"));
			if($sectionRoot['UF_ELEMENT_DETAIL'] && !$typeTmpDetail)
				$typeTmpDetail = $sectionRoot['UF_ELEMENT_DETAIL'];
		}
	}
}
if($typeTmpDetail)
{
	$rsTypes = CUserFieldEnum::GetList(array(), array("ID" => $typeTmpDetail));
	if($arType = $rsTypes->GetNext())
		$typeDetail = $arType['XML_ID'];
	if($typeDetail)
		$sViewElementTemplate = $typeDetail;
}

/* set force CATALOG_PAGE_DETAIL from theme */
if (
	class_exists('\Aspro\Functions\CAsproAllcorp2Custom') &&
	method_exists('\Aspro\Functions\CAsproAllcorp2Custom', 'setPageDetail')
) {
	$sViewElementTemplate = \Aspro\Functions\CAsproAllcorp2Custom::setPageDetail($sViewElementTemplate);
}
/* */

//bug fix bitrix for search element
if($arElement)
{
	$strict_check = $arParams["DETAIL_STRICT_SECTION_CHECK"] === "Y";
	if(!CIBlockFindTools::checkElement($arParams["IBLOCK_ID"], $arResult["VARIABLES"], $strict_check))
		$arElement = array();
}
?>

<?$APPLICATION->SetPageProperty("MENU", $arTheme['SHOW_LEFT_BLOCK']['VALUE'])?>

<?if(!$arElement && $arParams['SET_STATUS_404'] !== 'Y'):?>
	<div class="alert alert-warning"><?=GetMessage("ELEMENT_NOTFOUND")?></div>
<?elseif(!$arElement && $arParams['SET_STATUS_404'] === 'Y'):?>
	<?CAllcorp2::goto404Page();?>
<?else:?>
	<?CAllcorp2::CheckComponentTemplatePageBlocksParams($arParams, __DIR__);?>
	<?// rss
	if($arParams['USE_RSS'] !== 'N'){
		CAllcorp2::ShowRSSIcon($arResult['FOLDER'].$arResult['URL_TEMPLATES']['rss']);
	}
	?>
	<?CAllcorp2::AddMeta(
		array(
			'og:description' => $arElement['PREVIEW_TEXT'],
			'og:image' => (($arElement['PREVIEW_PICTURE'] || $arElement['DETAIL_PICTURE']) ? CFile::GetPath(($arElement['PREVIEW_PICTURE'] ? $arElement['PREVIEW_PICTURE'] : $arElement['DETAIL_PICTURE'])) : false),
		)
	);?>
	<div class="main-catalog-wrapper">
		<div class="section-content-wrapper">
			<div class="catalog detail detail_narrow_<?=$arTheme['SHOW_LEFT_BLOCK']['VALUE'];?> fixed_wrapper" itemscope itemtype="http://schema.org/Product">
				<?if($arParams["USE_SHARE"] == "Y" && $arElement):?>
					<div class="share top <?=($arParams['USE_RSS'] !== 'N' ? 'rss-block' : '');?>">
						<?$APPLICATION->IncludeFile(SITE_DIR."include/share_buttons.php", Array(), Array("MODE" => "html", "NAME" => GetMessage('CT_BCE_CATALOG_SOC_BUTTON')));?>
					</div>
					<script type="text/javascript">
						showTopIcons();
					</script>
				<?endif;?>

				<?$arParams["GRUPPER_PROPS"] = $arTheme["GRUPPER_PROPS"]["VALUE"];
				if($arTheme["GRUPPER_PROPS"]["VALUE"] != "NOT")
				{
					$arParams["PROPERTIES_DISPLAY_TYPE"] = "TABLE";

					if($arParams["GRUPPER_PROPS"] == "GRUPPER" && !\Bitrix\Main\Loader::includeModule("redsign.grupper"))
						$arParams["GRUPPER_PROPS"] = "NOT";
					if($arParams["GRUPPER_PROPS"] == "WEBDEBUG" && !\Bitrix\Main\Loader::includeModule("webdebug.utilities"))
						$arParams["GRUPPER_PROPS"] = "NOT";
					if($arParams["GRUPPER_PROPS"] == "YENISITE_GRUPPER" && !\Bitrix\Main\Loader::includeModule("yenisite.infoblockpropsplus"))
						$arParams["GRUPPER_PROPS"] = "NOT";
				}?>


				<?//element?>
				<?@include_once('page_blocks/'.$sViewElementTemplate.'.php');?>

				<?if($arTheme['SHOW_LEFT_BLOCK']['VALUE'] == 'N'):?>
					<div class="row">
						<div class="col-md-9">
				<?endif;?>
				<hr class="bottoms" />
				<div class="row">
					<div class="col-md-6 col-sm-6 share">
						<?if($arParams["USE_SHARE"] == "Y" && $arElement):?>
							<?$APPLICATION->IncludeFile(SITE_DIR."include/share_buttons.php", Array(), Array("MODE" => "html", "NAME" => GetMessage('CT_BCE_CATALOG_SOC_BUTTON')));?>
						<?endif;?>
					</div>
					<div class="col-md-6 col-sm-6">
						<?
						$list_url = $arResult['FOLDER'].$arResult['URL_TEMPLATES']['news'];
						if($arElement['IBLOCK_SECTION_ID'])
						{
							$arSection = CCache::CIBlockSection_GetList(array('CACHE' => array('TAG' => CCache::GetIBlockCacheTag($arElement['IBLOCK_ID']), 'MULTI' => 'N')), array('ID' => $arElement['IBLOCK_SECTION_ID'], 'ACTIVE' => 'Y'), false, array('ID', 'NAME', 'SECTION_PAGE_URL'));
							if($arSection['SECTION_PAGE_URL'])
								$list_url = $arSection['SECTION_PAGE_URL'];
						}
						?>
						<a class="back-url url-block" href="<?=$list_url;?>"><i class="fa fa-angle-left"></i><span><?=(isset($arParams["T_PREV_LINK"]) && $arParams["T_PREV_LINK"]  ? $arParams["T_PREV_LINK"] : GetMessage('BACK_LINK'));?></span></a>
					</div>
				</div>
				<?if($arTheme['SHOW_LEFT_BLOCK']['VALUE'] == 'N'):?>
						</div>
					</div>
				<?endif;?>

			</div>
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
	<?
	if(is_array($arElement['IBLOCK_SECTION_ID']) && count($arElement['IBLOCK_SECTION_ID']) > 1){
		CAllcorp2::CheckAdditionalChainInMultiLevel($arResult, $arParams, $arElement);
	}
	?>
<?endif;?>
<?CAllcorp2::checkBreadcrumbsChain($arParams, $arSection);?>