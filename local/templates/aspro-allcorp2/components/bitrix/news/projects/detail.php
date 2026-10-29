<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<? $this->setFrameMode(true); ?>
<?

global $APPLICATION, $arTheme;
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/css/animation/animate.min.css');

// get element
$arItemFilter = CAllcorp2::GetCurrentElementFilter($arResult['VARIABLES'], $arParams);

if ($arParams['CACHE_GROUPS'] == 'Y') {
	$arItemFilter['CHECK_PERMISSIONS'] = 'Y';
	$arItemFilter['GROUPS'] = $GLOBALS["USER"]->GetGroups();
}

$arElement = CCache::CIblockElement_GetList(array('CACHE' => array('TAG' => CCache::GetIBlockCacheTag($arParams['IBLOCK_ID']), 'MULTI' => 'N')), $arItemFilter, false, false, array('ID', 'NAME', 'IBLOCK_ID', 'PREVIEW_TEXT', 'IBLOCK_SECTION_ID', 'PREVIEW_PICTURE', 'DETAIL_PICTURE', 'DETAIL_PAGE_URL', 'LIST_PAGE_URL', 'PROPERTY_LINK_PROJECTS', 'PROPERTY_LINK_GOODS', 'PROPERTY_LINK_REVIEWS', 'PROPERTY_LINK_STAFF', 'PROPERTY_LINK_SERVICES', 'PROPERTY_FORM_QUESTION', 'PROPERTY_FORM_ORDER'));

//bug fix bitrix for search element
if ($arElement) {
	$strict_check = $arParams["DETAIL_STRICT_SECTION_CHECK"] === "Y";
	if (!CIBlockFindTools::checkElement($arParams["IBLOCK_ID"], $arResult["VARIABLES"], $strict_check))
		$arElement = array();
}
?>
<? if (!$arElement && $arParams['SET_STATUS_404'] !== 'Y') : ?>
	<div class="alert alert-warning"><?= GetMessage("ELEMENT_NOTFOUND") ?></div>
<? elseif (!$arElement && $arParams['SET_STATUS_404'] === 'Y') : ?>
	<? CAllcorp2::goto404Page(); ?>
<? else : ?>
	<? CAllcorp2::CheckComponentTemplatePageBlocksParams($arParams, __DIR__); ?>
	<? // rss
	if ($arParams['USE_RSS'] !== 'N') {
		CAllcorp2::ShowRSSIcon($arResult['FOLDER'] . $arResult['URL_TEMPLATES']['rss']);
	} ?>
	<? CAllcorp2::AddMeta(
		array(
			'og:description' => $arElement['PREVIEW_TEXT'],
			'og:image' => (($arElement['PREVIEW_PICTURE'] || $arElement['DETAIL_PICTURE']) ? CFile::GetPath(($arElement['PREVIEW_PICTURE'] ? $arElement['PREVIEW_PICTURE'] : $arElement['DETAIL_PICTURE'])) : false),
		)
	); ?>
	<?$rmProjectPaperView = $arParams['ELEMENT_TYPE_VIEW'] === 'element_projects_paper';?>
	<div class="detail <?= ($templateName = $component->{'__template'}->{'__name'}) ?> fixed_wrapper<?=$rmProjectPaperView ? ' rm-project-paper-wrapper' : ''?>">
		<? if (!$rmProjectPaperView && $arParams["USE_SHARE"] == "Y" && $arElement) : ?>
			<div class="share top <?= ($arParams['USE_RSS'] !== 'N' ? 'rss-block' : ''); ?>">
				<? $APPLICATION->IncludeFile(SITE_DIR . "include/share_buttons.php", array(), array("MODE" => "html", "NAME" => GetMessage('CT_BCE_CATALOG_SOC_BUTTON'))); ?>
			</div>
			<script type="text/javascript">
				showTopIcons();
			</script>
		<? endif; ?>

		<? $arParams["GRUPPER_PROPS"] = $arTheme["GRUPPER_PROPS"]["VALUE"];
		if ($arTheme["GRUPPER_PROPS"]["VALUE"] != "NOT") {
			$arParams["PROPERTIES_DISPLAY_TYPE"] = "TABLE";

			if ($arParams["GRUPPER_PROPS"] == "GRUPPER" && !\Bitrix\Main\Loader::includeModule("redsign.grupper"))
				$arParams["GRUPPER_PROPS"] = "NOT";
			if ($arParams["GRUPPER_PROPS"] == "WEBDEBUG" && !\Bitrix\Main\Loader::includeModule("webdebug.utilities"))
				$arParams["GRUPPER_PROPS"] = "NOT";
			if ($arParams["GRUPPER_PROPS"] == "YENISITE_GRUPPER" && !\Bitrix\Main\Loader::includeModule("yenisite.infoblockpropsplus"))
				$arParams["GRUPPER_PROPS"] = "NOT";
		} ?>

		<? $arSections = CCache::CIBLockSection_GetList(array('SORT' => 'ASC', 'NAME' => 'ASC', 'CACHE' => array('TAG' => CCache::GetIBlockCacheTag($arParams['IBLOCK_ID']), 'GROUP' => array('ID'), 'MULTI' => 'N', 'URL_TEMPLATE' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['section'])), array('IBLOCK_ID' => $arParams['IBLOCK_ID'], 'DEPTH_LEVEL' => 1, 'ACTIVE' => 'Y', 'CNT_ACTIVE' => "Y"), true);
		?>

		<? //element
		?>
		<? $sViewElementTemplate = ($arParams["ELEMENT_TYPE_VIEW"] == "FROM_MODULE" ? $arTheme["ELEMENT_PROJECT_PAGE_DETAIL"]["VALUE"] : $arParams["ELEMENT_TYPE_VIEW"]); ?>
		<? @include_once('page_blocks/' . $sViewElementTemplate . '.php'); ?>

	</div>
	<?
	if (is_array($arElement['IBLOCK_SECTION_ID']) && count($arElement['IBLOCK_SECTION_ID']) > 1) {
		CAllcorp2::CheckAdditionalChainInMultiLevel($arResult, $arParams, $arElement);
	}
	?>
	<?if (!$rmProjectPaperView):?>
	<? if (in_array('FORM_QUESTION', $arParams['DETAIL_PROPERTY_CODE']) && $arElement['PROPERTY_FORM_QUESTION_VALUE']) : ?>
		<div class="row">
			<div class="col-md-9">
			<? endif; ?>
			<div style="clear:both"></div>
			<hr class="bottoms" />
			<?
			$list_url = $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['news'];
			if ($arElement['IBLOCK_SECTION_ID']) {
				$arSection = CCache::CIBlockSection_GetList(array('CACHE' => array('TAG' => CCache::GetIBlockCacheTag($arElement['IBLOCK_ID']), 'MULTI' => 'N')), array('ID' => $arElement['IBLOCK_SECTION_ID'], 'ACTIVE' => 'Y'), false, array('ID', 'NAME', 'SECTION_PAGE_URL'));
				if ($arSection['SECTION_PAGE_URL'])
					$list_url = $arSection['SECTION_PAGE_URL'];
			}
			?>
			<? if ($arParams["SHOW_NEXT_ELEMENT"] == "Y") : ?>
				<?
				$arOptions = [
					'ELEMENT' => $arElement,
					'RESULT' => $arResult,
					'SECTIONS' => $arSections,
					'PARAMS' => $arParams,
					'LIST_URL' => $list_url,
					'FILTER' => $arItemFilter,
				];

				$arNextElement = \Aspro\Functions\CAsproAllcorp2::getNextElementInfo($arOptions);
				?>

				<? \Aspro\Functions\CAsproAllcorp2::showBlockHtml([
					'FILE' => 'detail/prev_next_element.php',
					'PARAMS' => $arOptions + [
						'NEXT_ELEMENT' => $arNextElement
					]
				]); ?>
			<? else : ?>
				<div class="row">
					<div class="col-md-6 col-sm-6 share">
						<? if ($arParams["USE_SHARE"] == "Y" && $arElement) : ?>
							<? $APPLICATION->IncludeFile(SITE_DIR . "include/share_buttons.php", array(), array("MODE" => "html", "NAME" => GetMessage('CT_BCE_CATALOG_SOC_BUTTON'))); ?>
						<? endif; ?>
					</div>
					<div class="col-md-6 col-sm-6">
						<a class="back-url url-block" href="<?= $list_url; ?>"><i class="fa fa-angle-left"></i><span><?= ($arParams["T_PREV_LINK"] ? $arParams["T_PREV_LINK"] : GetMessage('BACK_LINK')); ?></span></a>
					</div>
				</div>
			<? endif; ?>
			<? if (in_array('FORM_QUESTION', $arParams['DETAIL_PROPERTY_CODE']) && $arElement['PROPERTY_FORM_QUESTION_VALUE']) : ?>
			</div>
		</div>
	<? endif; ?>
	<?endif;?>
<? endif; ?>
