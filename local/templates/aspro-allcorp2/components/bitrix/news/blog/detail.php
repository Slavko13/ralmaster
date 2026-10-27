<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?$this->setFrameMode(true);?>
<?global $arTheme;
$catalog_template = ($arParams["ELEMENTS_TABLE_TYPE_VIEW"] ? ($arParams["ELEMENTS_TABLE_TYPE_VIEW"] == "FROM_MODULE" ? $arTheme["ELEMENTS_TABLE_TYPE_VIEW"]["VALUE"] : $arParams["ELEMENTS_TABLE_TYPE_VIEW"]) : "catalog_linked");
$detail_linked_template = (isset($arParams['DETAIL_LINKED_TEMPLATE']) ? $arParams['DETAIL_LINKED_TEMPLATE'] : 'linked');
$propetryLink = $arTheme['CATALOG_LINKED_PROP']['VALUE'] ? explode(',', $arTheme['CATALOG_LINKED_PROP']['VALUE']) : '' ;
?>
<?
// get element
$arItemFilter = CAllcorp2::GetCurrentElementFilter($arResult['VARIABLES'], $arParams);

if($arParams['CACHE_GROUPS'] == 'Y')
{
	$arItemFilter['CHECK_PERMISSIONS'] = 'Y';
	$arItemFilter['GROUPS'] = $GLOBALS["USER"]->GetGroups();
}

$arElement = CCache::CIblockElement_GetList(array('CACHE' => array('TAG' => CCache::GetIBlockCacheTag($arParams['IBLOCK_ID']), 'MULTI' => 'N')), $arItemFilter, false, false, array('ID', 'PREVIEW_TEXT', 'IBLOCK_SECTION_ID', 'IBLOCK_ID', 'PREVIEW_PICTURE', 'DETAIL_PICTURE', 'PROPERTY_LINK_GOODS', 'PROPERTY_LINK_NEWS', 'PROPERTY_LINK_SALE', 'PROPERTY_LINK_PROJECTS', 'PROPERTY_LINK_REVIEWS', 'PROPERTY_LINK_SERVICES', 'PROPERTY_LINK_STAFF', 'PROPERTY_LINK_FAQ', 'PROPERTY_LINK_STUDY'));

//bug fix bitrix for search element
if($arElement)
{
	$strict_check = $arParams["DETAIL_STRICT_SECTION_CHECK"] === "Y";
	if(!CIBlockFindTools::checkElement($arParams["IBLOCK_ID"], $arResult["VARIABLES"], $strict_check))
		$arElement = array();
}
?>
<?if(!$arElement && $arParams['SET_STATUS_404'] !== 'Y'):?>
	<div class="alert alert-warning"><?=GetMessage("ELEMENT_NOTFOUND")?></div>
<?elseif(!$arElement && $arParams['SET_STATUS_404'] === 'Y'):?>
	<?CAllcorp2::goto404Page();?>
<?else:?>
	<?CAllcorp2::CheckComponentTemplatePageBlocksParams($arParams, __DIR__);?>
	<?// rss
	if($arParams['USE_RSS'] !== 'N'){
		CAllcorp2::ShowRSSIcon($arResult['FOLDER'].$arResult['URL_TEMPLATES']['rss']);
	}?>
	<?CAllcorp2::AddMeta(
		array(
			'og:description' => $arElement['PREVIEW_TEXT'],
			'og:image' => (($arElement['PREVIEW_PICTURE'] || $arElement['DETAIL_PICTURE']) ? CFile::GetPath(($arElement['PREVIEW_PICTURE'] ? $arElement['PREVIEW_PICTURE'] : $arElement['DETAIL_PICTURE'])) : false),
		)
	);?>

	<?// filter for linked items?>
	<?$GLOBALS['arAlsoFilter'] = array('!ID' => $arElement['ID'], 'INCLUDE_SUBSECTIONS' => 'Y');
	if($arElement['IBLOCK_SECTION_ID'])
		$GLOBALS['arAlsoFilter']['SECTION_ID'] = $arElement['IBLOCK_SECTION_ID'];
	?>

	<div itemscope itemtype="http://schema.org/Article" class="detail <?=($templateName = $component->{'__template'}->{'__name'})?>">
		<meta itemprop="identifier" content="<?=$arElement['ID']?>">
		<meta itemprop="headline"  content="<?$APPLICATION->ShowTitle(false)?>">
		<?if($arParams["USE_SHARE"] == "Y" && $arElement):?>
			<div class="share top <?=($arParams['USE_RSS'] !== 'N' ? 'rss-block' : '');?>">
				<?$APPLICATION->IncludeFile(SITE_DIR."include/share_buttons.php", Array(), Array("MODE" => "html", "NAME" => GetMessage('CT_BCE_CATALOG_SOC_BUTTON')));?>
			</div>
			<script type="text/javascript">
				showTopIcons();
			</script>
		<?endif;?>
		<?$arSections = CCache::CIBLockSection_GetList(array('SORT' => 'ASC', 'NAME' => 'ASC', 'CACHE' => array('TAG' => CCache::GetIBlockCacheTag($arParams['IBLOCK_ID']), 'GROUP' => array('ID'), 'MULTI' => 'N', 'URL_TEMPLATE' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['section'])), array('IBLOCK_ID' => $arParams['IBLOCK_ID'], 'DEPTH_LEVEL' => 1, 'ACTIVE' => 'Y', 'CNT_ACTIVE' => "Y"), true);
		?>

		<?$this->__component->__template->SetViewTarget('under_sidebar_content');?>
			<?if($arSections):?>
				<div class="fill-block container-block">
					<div class="title-block-middle"><?=GetMessage('CATEGORY');?></div>
					<?
					$cur_page = $GLOBALS['APPLICATION']->GetCurPage(true);
					$cur_page_no_index = $GLOBALS['APPLICATION']->GetCurPage(false);
					?>
					<ul class="categorys">
						<?foreach($arSections as $arRootSection):
							if(isset($arRootSection['NAME']) && $arRootSection['NAME']):?>
								<li><a href="<?=$arRootSection['SECTION_PAGE_URL'];?>" class="dark-color <?=(CMenu::IsItemSelected($arRootSection['SECTION_PAGE_URL'], $cur_page, $cur_page_no_index) ? 'active' : '');?>"><span class="text"><?=$arRootSection['NAME'];?></span><span class="count"><?=$arRootSection['ELEMENT_CNT'];?></span></a></li>
							<?endif;?>
						<?endforeach;?>
					</ul>
				</div>
			<?endif;?>
			<?if($arParams['ALSO_ITEMS_POSITION'] == 'side'):?>
				<?$APPLICATION->IncludeComponent("bitrix:news.list", "items-blog-list", array(
					"IBLOCK_TYPE" => "aspro_allcorp2_content",
					"IBLOCK_ID" => $arParams["IBLOCK_ID"],
					"NEWS_COUNT" => (isset($arParams["COUNT_ELEMENTS_INTERESTING"]) && $arParams["COUNT_ELEMENTS_INTERESTING"] ? $arParams["COUNT_ELEMENTS_INTERESTING"] : 10),
					"TITLE_BLOCK" => ($arParams["T_ALSO_ITEMS"] ? $arParams["T_ALSO_ITEMS"] : GetMessage('T_ALSO_ITEMS')),
					"SORT_BY1" => "ACTIVE_FROM",
					"SORT_ORDER1" => "DESC",
					"SORT_BY2" => "SORT",
					"SORT_ORDER2" => "ASC",
					"FILTER_NAME" => "arAlsoFilter",
					"FIELD_CODE" => array(
						0 => "NAME",
						1 => "PREVIEW_TEXT",
						2 => "PREVIEW_PICTURE",
						3 => "DATE_ACTIVE_FROM",
					),
					"PROPERTY_CODE" => array(
						0 => "DOCUMENTS",
						1 => "POST",
					),
					"CHECK_DATES" => "Y",
					"DETAIL_URL" => "",
					"AJAX_MODE" => "N",
					"AJAX_OPTION_JUMP" => "N",
					"AJAX_OPTION_STYLE" => "Y",
					"AJAX_OPTION_HISTORY" => "N",
					"CACHE_TYPE" => "A",
					"CACHE_TIME" => "36000000",
					"CACHE_FILTER" => "Y",
					"CACHE_GROUPS" => "N",
					"PREVIEW_TRUNCATE_LEN" => "",
					"ACTIVE_DATE_FORMAT" => "j F Y",
					"SET_TITLE" => "N",
					"SET_STATUS_404" => "N",
					"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
					"ADD_SECTIONS_CHAIN" => "N",
					"HIDE_LINK_WHEN_NO_DETAIL" => "N",
					"PARENT_SECTION" => "",
					"PARENT_SECTION_CODE" => "",
					"INCLUDE_SUBSECTIONS" => "Y",
					"PAGER_TEMPLATE" => ".default",
					"DISPLAY_TOP_PAGER" => "N",
					"DISPLAY_BOTTOM_PAGER" => "Y",
					"PAGER_TITLE" => "Новости",
					"PAGER_SHOW_ALWAYS" => "N",
					"PAGER_DESC_NUMBERING" => "N",
					"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
					"PAGER_SHOW_ALL" => "N",
					"VIEW_TYPE" => "list",
					"SHOW_TABS" => "N",
					"SHOW_IMAGE" => "Y",
					"SHOW_NAME" => "Y",
					"SHOW_DETAIL" => "Y",
					"IMAGE_POSITION" => "left",
					"COUNT_IN_LINE" => "3",
					"AJAX_OPTION_ADDITIONAL" => ""
					),
				false, array("HIDE_ICONS" => "Y")
				);?>
				<?$APPLICATION->IncludeComponent(
					"bitrix:search.tags.cloud",
					"main",
					Array(
						"CACHE_TIME" => "86400",
						"CACHE_TYPE" => "A",
						"CHECK_DATES" => "Y",
						"COLOR_NEW" => "3E74E6",
						"COLOR_OLD" => "C0C0C0",
						"COLOR_TYPE" => "N",
						"FILTER_NAME" => "",
						"FONT_MAX" => "50",
						"FONT_MIN" => "10",
						"PAGE_ELEMENTS" => "150",
						"PERIOD" => "",
						"PERIOD_NEW_TAGS" => "",
						"SHOW_CHAIN" => "N",
						"SORT" => "NAME",
						"TAGS_INHERIT" => "Y",
						"URL_SEARCH" => SITE_DIR."search/index.php",
						"WIDTH" => "100%",
						"arrFILTER" => array("iblock_aspro_allcorp2_content"),
						"arrFILTER_iblock_aspro_allcorp2_content" => array($arParams["IBLOCK_ID"])
					), $component
				);?>
			<?endif;?>
		<?$this->__component->__template->EndViewTarget();?>

		<?//element?>
		<?$sViewElementTemplate = ($arParams["ELEMENT_TYPE_VIEW"] == "FROM_MODULE" ? $arTheme["BLOG_PAGE_DETAIL"]["VALUE"] : $arParams["ELEMENT_TYPE_VIEW"]);?>
		<?@include_once('page_blocks/'.$sViewElementTemplate.'.php');?>

	</div>
	<?
	if(is_array($arElement['IBLOCK_SECTION_ID']) && count($arElement['IBLOCK_SECTION_ID']) > 1){
		CAllcorp2::CheckAdditionalChainInMultiLevel($arResult, $arParams, $arElement);
	}
	?>

	<div style="clear:both"></div>

	<?$APPLICATION->ShowViewContent('tags_content');?>

	<?if($arParams['ALSO_ITEMS_POSITION'] != 'side'):?>
		<?$APPLICATION->IncludeComponent("bitrix:news.list", "items-blog-slider", array(
			"IBLOCK_TYPE" => "aspro_allcorp2_content",
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"NEWS_COUNT" => ($arParams["COUNT_ELEMENTS_INTERESTING"] ? $arParams["COUNT_ELEMENTS_INTERESTING"] : 10),
			"TITLE_BLOCK" => ($arParams["T_ALSO_ITEMS"] ? $arParams["T_ALSO_ITEMS"] : GetMessage('T_ALSO_ITEMS')),
			"SORT_BY1" => "ACTIVE_FROM",
			"SORT_ORDER1" => "DESC",
			"SORT_BY2" => "SORT",
			"SORT_ORDER2" => "ASC",
			"FILTER_NAME" => "arAlsoFilter",
			"FIELD_CODE" => array(
				0 => "NAME",
				1 => "PREVIEW_TEXT",
				2 => "PREVIEW_PICTURE",
				3 => "DATE_ACTIVE_FROM",
			),
			"PROPERTY_CODE" => array(
				0 => "DOCUMENTS",
				1 => "POST",
			),
			"CHECK_DATES" => "Y",
			"DETAIL_URL" => "",
			"AJAX_MODE" => "N",
			"AJAX_OPTION_JUMP" => "N",
			"AJAX_OPTION_STYLE" => "Y",
			"AJAX_OPTION_HISTORY" => "N",
			"CACHE_TYPE" => "A",
			"CACHE_TIME" => "36000000",
			"CACHE_FILTER" => "Y",
			"CACHE_GROUPS" => "N",
			"PREVIEW_TRUNCATE_LEN" => "",
			"ACTIVE_DATE_FORMAT" => "j F Y",
			"SET_TITLE" => "N",
			"SET_STATUS_404" => "N",
			"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
			"ADD_SECTIONS_CHAIN" => "N",
			"HIDE_LINK_WHEN_NO_DETAIL" => "N",
			"PARENT_SECTION" => "",
			"PARENT_SECTION_CODE" => "",
			"INCLUDE_SUBSECTIONS" => "Y",
			"PAGER_TEMPLATE" => ".default",
			"DISPLAY_TOP_PAGER" => "N",
			"DISPLAY_BOTTOM_PAGER" => "Y",
			"PAGER_TITLE" => "Новости",
			"PAGER_SHOW_ALWAYS" => "N",
			"PAGER_DESC_NUMBERING" => "N",
			"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
			"PAGER_SHOW_ALL" => "N",
			"VIEW_TYPE" => "list",
			"SHOW_TABS" => "N",
			"SHOW_IMAGE" => "Y",
			"SHOW_NAME" => "Y",
			"SHOW_DETAIL" => "Y",
			"IMAGE_POSITION" => "left",
			"COUNT_IN_LINE" => "3",
			"AJAX_OPTION_ADDITIONAL" => ""
			),
		false, array("HIDE_ICONS" => "Y")
		);?>
	<?endif;?>
	<div class="detail">
	<?//show sale block?>
	<?if($arElement['PROPERTY_LINK_SALE_VALUE']):?>
		<div class="drag_block">
		<?$GLOBALS['arrSaleFilter'] = array('ID' => $arElement['PROPERTY_LINK_SALE_VALUE']); ?>
		<div class="stockblock">
		<?$APPLICATION->IncludeComponent(
			"bitrix:news.list",
			"news1",
			array(
				"IBLOCK_TYPE" => "aspro_allcorp2_content",
				"IBLOCK_ID" => $arParams["SALES_IBLOCK_ID"],
				"NEWS_COUNT" => "20",
				"SORT_BY1" => "SORT",
				"SORT_ORDER1" => "ASC",
				"SORT_BY2" => "ID",
				"SORT_ORDER2" => "DESC",
				"FILTER_NAME" => "arrSaleFilter",
				"FIELD_CODE" => array(
					0 => "NAME",
					1 => "PREVIEW_TEXT",
					3 => "DATE_ACTIVE_FROM",
					4 => "",
				),
				"PROPERTY_CODE" => array(
					0 => "PERIOD",
					1 => "REDIRECT",
					2 => "",
				),
				"CHECK_DATES" => "Y",
				"DETAIL_URL" => "",
				"AJAX_MODE" => "N",
				"AJAX_OPTION_JUMP" => "N",
				"AJAX_OPTION_STYLE" => "Y",
				"AJAX_OPTION_HISTORY" => "N",
				"CACHE_TYPE" => "A",
				"CACHE_TIME" => "36000000",
				"CACHE_FILTER" => "Y",
				"CACHE_GROUPS" => "N",
				"PREVIEW_TRUNCATE_LEN" => "",
				"ACTIVE_DATE_FORMAT" => "d.m.Y",
				"SET_TITLE" => "N",
				"SET_STATUS_404" => "N",
				"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
				"ADD_SECTIONS_CHAIN" => "N",
				"HIDE_LINK_WHEN_NO_DETAIL" => "N",
				"PARENT_SECTION" => "",
				"PARENT_SECTION_CODE" => "",
				"INCLUDE_SUBSECTIONS" => "Y",
				"PAGER_TEMPLATE" => ".default",
				"DISPLAY_TOP_PAGER" => "N",
				"DISPLAY_BOTTOM_PAGER" => "Y",
				"PAGER_TITLE" => "Новости",
				"PAGER_SHOW_ALWAYS" => "N",
				"PAGER_DESC_NUMBERING" => "N",
				"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
				"PAGER_SHOW_ALL" => "N",
				"VIEW_TYPE" => "table",
				"BIG_BLOCK" => "Y",
				"IMAGE_POSITION" => "left",
				"COUNT_IN_LINE" => "2",
			),
			false, array("HIDE_ICONS" => "Y")
		);?>
		</div>
		</div>
	<?endif;?>
	<?//show study block?>
	<?if($arElement['PROPERTY_LINK_STUDY_VALUE']):?>
		<div class="drag_block">
			<div class="wraps">
				<hr />
				<h5><?=($arParams["T_STUDY"] ? $arParams["T_STUDY"] : GetMessage("T_STUDY"));?></h5>
				<div id="study" class=" wraps-block">
					<?$GLOBALS['arrStudyFilter'] = array('ID' => $arElement['PROPERTY_LINK_STUDY_VALUE']);?>
					<?$APPLICATION->IncludeComponent(
						"bitrix:news.list",
						"front-news",
						array(
							"IBLOCK_TYPE" => "aspro_allcorp2_content",
							"IBLOCK_ID" => $arParams["STUDY_IBLOCK_ID"],
							"NEWS_COUNT" => "20",
							"SORT_BY1" => "SORT",
							"SORT_ORDER1" => "ASC",
							"SORT_BY2" => "ID",
							"SORT_ORDER2" => "DESC",
							"FILTER_NAME" => "arrStudyFilter",
							"FIELD_CODE" => array(
								0 => "NAME",
								1 => "PREVIEW_TEXT",
								2 => "PREVIEW_PICTURE",
								3 => "",
							),
							"PROPERTY_CODE" => array(
								0 => "LINK",
								1 => "",
							),
							"CHECK_DATES" => "Y",
							"DETAIL_URL" => "",
							"AJAX_MODE" => "N",
							"AJAX_OPTION_JUMP" => "N",
							"AJAX_OPTION_STYLE" => "Y",
							"AJAX_OPTION_HISTORY" => "N",
							"CACHE_TYPE" => "A",
							"CACHE_TIME" => "36000000",
							"CACHE_FILTER" => "Y",
							"CACHE_GROUPS" => "N",
							"PREVIEW_TRUNCATE_LEN" => "",
							"ACTIVE_DATE_FORMAT" => "j F Y",
							"SET_TITLE" => "N",
							"SET_STATUS_404" => "N",
							"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
							"ADD_SECTIONS_CHAIN" => "N",
							"HIDE_LINK_WHEN_NO_DETAIL" => "N",
							"PARENT_SECTION" => "",
							"PARENT_SECTION_CODE" => "",
							"INCLUDE_SUBSECTIONS" => "Y",
							"PAGER_TEMPLATE" => ".default",
							"DISPLAY_TOP_PAGER" => "N",
							"DISPLAY_BOTTOM_PAGER" => "Y",
							"PAGER_TITLE" => "Новости",
							"PAGER_SHOW_ALWAYS" => "N",
							"PAGER_DESC_NUMBERING" => "N",
							"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
							"PAGER_SHOW_ALL" => "N",
							"VIEW_TYPE" => "list",
							"IMAGE_POSITION" => "left",
							"COUNT_IN_LINE" => "3",
							"SHOW_TITLE" => "N",
							"SHOW_DATE" => "N",
							"T_PROJECTS" => ($arParams["T_STUDY"] ? $arParams["T_STUDY"] : GetMessage("T_STUDY")),
							"AJAX_OPTION_ADDITIONAL" => ""
						),
						false, array("HIDE_ICONS" => "Y")
					);?>
				</div>
			</div>
		</div>
	<?endif;?>
	<?//show news block?>
	<?if($arElement['PROPERTY_LINK_NEWS_VALUE']):?>
		<div class="drag_block">
			<div class="wraps-block">
				<hr/>
				<?$GLOBALS['arrNewsFilter'] = array('ID' => $arElement['PROPERTY_LINK_NEWS_VALUE']);?>
				<h5><?=($arParams["T_NEWS"] ? $arParams["T_NEWS"] : GetMessage("T_NEWS"));?></h5>
				<?$APPLICATION->IncludeComponent(
					"bitrix:news.list",
					"front-news",
					array(
						"IBLOCK_TYPE" => "aspro_allcorp2_content",
						"IBLOCK_ID" => $arParams["NEWS_IBLOCK_ID"],
						"NEWS_COUNT" => "20",
						"SORT_BY1" => "ACTIVE_FROM",
						"SORT_ORDER1" => "DESC",
						"SORT_BY2" => "SORT",
						"SORT_ORDER2" => "ASC",
						"FILTER_NAME" => "arrNewsFilter",
						"FIELD_CODE" => array(
							0 => "NAME",
							1 => "PREVIEW_TEXT",
							2 => "PREVIEW_PICTURE",
							3 => "",
						),
						"PROPERTY_CODE" => array(
							0 => "LINK",
							1 => "PERIOD",
						),
						"CHECK_DATES" => "Y",
						"DETAIL_URL" => "",
						"AJAX_MODE" => "N",
						"AJAX_OPTION_JUMP" => "N",
						"AJAX_OPTION_STYLE" => "Y",
						"AJAX_OPTION_HISTORY" => "N",
						"CACHE_TYPE" => "A",
						"CACHE_TIME" => "36000000",
						"CACHE_FILTER" => "Y",
						"CACHE_GROUPS" => "N",
						"PREVIEW_TRUNCATE_LEN" => "",
						"ACTIVE_DATE_FORMAT" => "j F Y",
						"SET_TITLE" => "N",
						"SET_STATUS_404" => "N",
						"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
						"ADD_SECTIONS_CHAIN" => "N",
						"HIDE_LINK_WHEN_NO_DETAIL" => "N",
						"PARENT_SECTION" => "",
						"PARENT_SECTION_CODE" => "",
						"INCLUDE_SUBSECTIONS" => "Y",
						"PAGER_TEMPLATE" => ".default",
						"DISPLAY_TOP_PAGER" => "N",
						"DISPLAY_BOTTOM_PAGER" => "Y",
						"PAGER_TITLE" => "Новости",
						"PAGER_SHOW_ALWAYS" => "N",
						"PAGER_DESC_NUMBERING" => "N",
						"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
						"PAGER_SHOW_ALL" => "N",
						"VIEW_TYPE" => "list",
						"IMAGE_POSITION" => "left",
						"COUNT_IN_LINE" => "3",
						"SHOW_TITLE" => "N",
						"SHOW_DATE" => "Y",
						"T_PROJECTS" => ($arParams["T_NEWS"] ? $arParams["T_NEWS"] : GetMessage("T_NEWS")),
						"AJAX_OPTION_ADDITIONAL" => ""
					),
					false, array("HIDE_ICONS" => "Y")
				);?>
			</div>
		</div>
	<?endif;?>
	<?//show projects block?>
	<?if($arElement['PROPERTY_LINK_PROJECTS_VALUE']):?>
		<div class="drag_block">
			<div class="wraps-block">
				<hr/>
				<?$GLOBALS['arrProjectFilter'] = array('ID' => $arElement['PROPERTY_LINK_PROJECTS_VALUE']);?>
				<h5><?=($arParams["T_PROJECTS"] ? $arParams["T_PROJECTS"] : GetMessage("T_PROJECTS"));?></h5>
				<?$APPLICATION->IncludeComponent(
					"bitrix:news.list",
					"front-news",
					array(
						"IBLOCK_TYPE" => "aspro_allcorp2_content",
						"IBLOCK_ID" => $arParams["PROJECTS_IBLOCK_ID"],
						"NEWS_COUNT" => "20",
						"SORT_BY1" => "SORT",
						"SORT_ORDER1" => "ASC",
						"SORT_BY2" => "ID",
						"SORT_ORDER2" => "DESC",
						"FILTER_NAME" => "arrProjectFilter",
						"FIELD_CODE" => array(
							0 => "NAME",
							1 => "PREVIEW_TEXT",
							2 => "PREVIEW_PICTURE",
							3 => "",
						),
						"PROPERTY_CODE" => array(
							0 => "LINK",
							1 => "",
						),
						"CHECK_DATES" => "Y",
						"DETAIL_URL" => "",
						"AJAX_MODE" => "N",
						"AJAX_OPTION_JUMP" => "N",
						"AJAX_OPTION_STYLE" => "Y",
						"AJAX_OPTION_HISTORY" => "N",
						"CACHE_TYPE" => "A",
						"CACHE_TIME" => "36000000",
						"CACHE_FILTER" => "Y",
						"CACHE_GROUPS" => "N",
						"PREVIEW_TRUNCATE_LEN" => "",
						"ACTIVE_DATE_FORMAT" => "j F Y",
						"SET_TITLE" => "N",
						"SET_STATUS_404" => "N",
						"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
						"ADD_SECTIONS_CHAIN" => "N",
						"HIDE_LINK_WHEN_NO_DETAIL" => "N",
						"PARENT_SECTION" => "",
						"PARENT_SECTION_CODE" => "",
						"INCLUDE_SUBSECTIONS" => "Y",
						"PAGER_TEMPLATE" => ".default",
						"DISPLAY_TOP_PAGER" => "N",
						"DISPLAY_BOTTOM_PAGER" => "Y",
						"PAGER_TITLE" => "Новости",
						"PAGER_SHOW_ALWAYS" => "N",
						"PAGER_DESC_NUMBERING" => "N",
						"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
						"PAGER_SHOW_ALL" => "N",
						"VIEW_TYPE" => "list",
						"IMAGE_POSITION" => "left",
						"COUNT_IN_LINE" => "3",
						"SHOW_TITLE" => "N",
						"SHOW_DATE" => "N",
						"T_PROJECTS" => ($arParams["T_PROJECTS"] ? $arParams["T_PROJECTS"] : GetMessage("T_PROJECTS")),
						"AJAX_OPTION_ADDITIONAL" => ""
					),
					false, array("HIDE_ICONS" => "Y")
				);?>
			</div>
		</div>
	<?endif;?>
	<?if($arElement['PROPERTY_LINK_SERVICES_VALUE']):?>
		<div class="drag_block">
		<div class="wraps">
			<hr />
			<h5><?=(strlen($arParams['T_SERVICES']) ? $arParams['T_SERVICES'] : GetMessage('T_SERVICES'))?></h5>
			<?$GLOBALS['arrServicesFilter'] = array('ID' => $arElement['PROPERTY_LINK_SERVICES_VALUE']);?>
			<?$APPLICATION->IncludeComponent(
				"bitrix:news.list",
				"items-list5",
				array(
					"IBLOCK_TYPE" => "aspro_allcorp2_content",
					"IBLOCK_ID" => $arParams["SERVICES_IBLOCK_ID"],
					"NEWS_COUNT" => "20",
					"SORT_BY1" => "SORT",
					"SORT_ORDER1" => "ASC",
					"SORT_BY2" => "ID",
					"SORT_ORDER2" => "DESC",
					"FILTER_NAME" => "arrServicesFilter",
					"FIELD_CODE" => array(
						0 => "PREVIEW_PICTURE",
						1 => "NAME",
						2 => "PREVIEW_TEXT",
					),
					"PROPERTY_CODE" => array(
						0 => "",
						1 => "",
					),
					"CHECK_DATES" => "Y",
					"DETAIL_URL" => "",
					"AJAX_MODE" => "N",
					"AJAX_OPTION_JUMP" => "N",
					"AJAX_OPTION_STYLE" => "Y",
					"AJAX_OPTION_HISTORY" => "N",
					"CACHE_TYPE" => "A",
					"CACHE_TIME" => "36000000",
					"CACHE_FILTER" => "Y",
					"CACHE_GROUPS" => "N",
					"PREVIEW_TRUNCATE_LEN" => "",
					"ACTIVE_DATE_FORMAT" => "d.m.Y",
					"SET_TITLE" => "N",
					"SET_STATUS_404" => "N",
					"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
					"ADD_SECTIONS_CHAIN" => "N",
					"HIDE_LINK_WHEN_NO_DETAIL" => "N",
					"PARENT_SECTION" => "",
					"PARENT_SECTION_CODE" => "",
					"INCLUDE_SUBSECTIONS" => "Y",
					"PAGER_TEMPLATE" => ".default",
					"DISPLAY_TOP_PAGER" => "N",
					"DISPLAY_BOTTOM_PAGER" => "Y",
					"PAGER_TITLE" => "Новости",
					"PAGER_SHOW_ALWAYS" => "N",
					"PAGER_DESC_NUMBERING" => "N",
					"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
					"PAGER_SHOW_ALL" => "N",
					"VIEW_TYPE" => "table",
					"BIG_BLOCK" => "Y",
					"IMAGE_POSITION" => "left",
					"COUNT_IN_LINE" => "2",
				),
				false, array("HIDE_ICONS" => "Y")
			);?>
		</div>
		</div>
	<?endif;?>
	<?//show reviews block?>
	<?if($arElement['PROPERTY_LINK_REVIEWS_VALUE']):?>
		<div class="drag_block">
		<div class="wraps goods-block">
			<hr />
			<h5><?=(strlen($arParams['T_REVIEWS']) ? $arParams['T_REVIEWS'] : GetMessage('T_REVIEWS'))?></h5>
			<?$GLOBALS['arrReviewsFilter'] = array('ID' => $arElement['PROPERTY_LINK_REVIEWS_VALUE']);?>
			<?$APPLICATION->IncludeComponent("bitrix:news.list", "reviews", array(
				"IBLOCK_TYPE" => "aspro_allcorp2_content",
				"IBLOCK_ID" => $arParams["REVIEWS_IBLOCK_ID"],
				"NEWS_COUNT" => "20",
				"SORT_BY1" => "ACTIVE_FROM",
				"SORT_ORDER1" => "DESC",
				"SORT_BY2" => "SORT",
				"SORT_ORDER2" => "ASC",
				"FILTER_NAME" => "arrReviewsFilter",
				"FIELD_CODE" => array(
					0 => "NAME",
					1 => "PREVIEW_TEXT",
					2 => "PREVIEW_PICTURE",
					3 => "",
				),
				"PROPERTY_CODE" => array(
					0 => "DOCUMENTS",
					1 => "POST",
				),
				"CHECK_DATES" => "Y",
				"DETAIL_URL" => "",
				"AJAX_MODE" => "N",
				"AJAX_OPTION_JUMP" => "N",
				"AJAX_OPTION_STYLE" => "Y",
				"AJAX_OPTION_HISTORY" => "N",
				"CACHE_TYPE" => "A",
				"CACHE_TIME" => "36000000",
				"CACHE_FILTER" => "Y",
				"CACHE_GROUPS" => "N",
				"PREVIEW_TRUNCATE_LEN" => "",
				"ACTIVE_DATE_FORMAT" => "d.m.Y",
				"SET_TITLE" => "N",
				"SET_STATUS_404" => "N",
				"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
				"ADD_SECTIONS_CHAIN" => "N",
				"HIDE_LINK_WHEN_NO_DETAIL" => "N",
				"PARENT_SECTION" => "",
				"PARENT_SECTION_CODE" => "",
				"INCLUDE_SUBSECTIONS" => "Y",
				"PAGER_TEMPLATE" => ".default",
				"DISPLAY_TOP_PAGER" => "N",
				"DISPLAY_BOTTOM_PAGER" => "Y",
				"PAGER_TITLE" => "Новости",
				"PAGER_SHOW_ALWAYS" => "N",
				"PAGER_DESC_NUMBERING" => "N",
				"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
				"PAGER_SHOW_ALL" => "N",
				"VIEW_TYPE" => "list",
				"SHOW_TABS" => "N",
				"SHOW_IMAGE" => "Y",
				"SHOW_NAME" => "Y",
				"SHOW_DETAIL" => "Y",
				"IMAGE_POSITION" => "left",
				"COUNT_IN_LINE" => "3",
				"AJAX_OPTION_ADDITIONAL" => ""
				),
			false, array("HIDE_ICONS" => "Y")
			);?>
		</div>
		</div>
	<?endif;?>
	<?//show staff block?>
	<?if($arElement['PROPERTY_LINK_STAFF_VALUE']):?>
		<div class="drag_block">
			<div class="wraps goods-block">
				<hr />
				<h5><?=(strlen($arParams['T_STAFF']) ? $arParams['T_STAFF'] : GetMessage('T_STAFF'))?></h5>
				<?$GLOBALS['arrStaffFilter'] = array('ID' => $arElement['PROPERTY_LINK_STAFF_VALUE']);?>
				<?$APPLICATION->IncludeComponent("bitrix:news.list", "staff-linked", array(
					"IBLOCK_TYPE" => "aspro_allcorp2_content",
					"IBLOCK_ID" => $arParams["STAFF_IBLOCK_ID"],
					"NEWS_COUNT" => "20",
					"SORT_BY1" => "SORT",
					"SORT_ORDER1" => "ASC",
					"SORT_BY2" => "ID",
					"SORT_ORDER2" => "DESC",
					"FILTER_NAME" => "arrStaffFilter",
					"FIELD_CODE" => array(
						0 => "NAME",
						1 => "PREVIEW_TEXT",
						2 => "PREVIEW_PICTURE",
						3 => "",
					),
					"PROPERTY_CODE" => array(
						0 => "EMAIL",
						1 => "POST",
						2 => "PHONE",
						3 => "",
					),
					"CHECK_DATES" => "Y",
					"DETAIL_URL" => "",
					"AJAX_MODE" => "N",
					"AJAX_OPTION_JUMP" => "N",
					"AJAX_OPTION_STYLE" => "Y",
					"AJAX_OPTION_HISTORY" => "N",
					"CACHE_TYPE" => "A",
					"CACHE_TIME" => "36000000",
					"CACHE_FILTER" => "Y",
					"CACHE_GROUPS" => "N",
					"PREVIEW_TRUNCATE_LEN" => "",
					"ACTIVE_DATE_FORMAT" => "d.m.Y",
					"SET_TITLE" => "N",
					"SET_STATUS_404" => "N",
					"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
					"ADD_SECTIONS_CHAIN" => "N",
					"HIDE_LINK_WHEN_NO_DETAIL" => "N",
					"PARENT_SECTION" => "",
					"PARENT_SECTION_CODE" => "",
					"INCLUDE_SUBSECTIONS" => "Y",
					"PAGER_TEMPLATE" => ".default",
					"DISPLAY_TOP_PAGER" => "N",
					"DISPLAY_BOTTOM_PAGER" => "Y",
					"PAGER_TITLE" => "Новости",
					"PAGER_SHOW_ALWAYS" => "N",
					"PAGER_DESC_NUMBERING" => "N",
					"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
					"PAGER_SHOW_ALL" => "N",
					"VIEW_TYPE" => "list",
					"IS_STAFF" => "Y",
					"SHOW_TABS" => "N",
					"SHOW_IMAGE" => "Y",
					"SHOW_NAME" => "Y",
					"SHOW_DETAIL" => "Y",
					"IMAGE_POSITION" => "left",
					"COUNT_IN_LINE" => "3",
					"AJAX_OPTION_ADDITIONAL" => ""
					),
				false, array("HIDE_ICONS" => "Y")
				);?>
			</div>
		</div>
	<?endif;?>
	<?//show staff block?>
	<?if($arElement['PROPERTY_LINK_FAQ_VALUE']):?>
		<div class="drag_block">
			<div class="wraps goods-block">
				<hr />
				<h5><?=(strlen($arParams['T_FAQ']) ? $arParams['T_FAQ'] : GetMessage('T_FAQ'))?></h5>
				<?$GLOBALS['arrFaqFilter'] = array('ID' => $arElement['PROPERTY_LINK_FAQ_VALUE']);?>
				<?$APPLICATION->IncludeComponent("bitrix:news.list", "items-list", array(
					"IBLOCK_TYPE" => "aspro_allcorp2_content",
					"IBLOCK_ID" => $arParams["FAQ_IBLOCK_ID"],
					"NEWS_COUNT" => "20",
					"SORT_BY1" => "SORT",
					"SORT_ORDER1" => "ASC",
					"SORT_BY2" => "ID",
					"SORT_ORDER2" => "DESC",
					"FILTER_NAME" => "arrFaqFilter",
					"FIELD_CODE" => array(
						0 => "PREVIEW_TEXT",
						1 => "",
					),
					"PROPERTY_CODE" => array(
						0 => "LINK",
						1 => "",
					),
					"CHECK_DATES" => "Y",
					"DETAIL_URL" => "",
					"AJAX_MODE" => "N",
					"AJAX_OPTION_JUMP" => "N",
					"AJAX_OPTION_STYLE" => "Y",
					"AJAX_OPTION_HISTORY" => "N",
					"CACHE_TYPE" => "A",
					"CACHE_TIME" => "36000000",
					"CACHE_FILTER" => "Y",
					"CACHE_GROUPS" => "N",
					"PREVIEW_TRUNCATE_LEN" => "",
					"ACTIVE_DATE_FORMAT" => "d.m.Y",
					"SET_TITLE" => "N",
					"SET_STATUS_404" => "N",
					"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
					"ADD_SECTIONS_CHAIN" => "N",
					"HIDE_LINK_WHEN_NO_DETAIL" => "N",
					"PARENT_SECTION" => "",
					"PARENT_SECTION_CODE" => "",
					"INCLUDE_SUBSECTIONS" => "Y",
					"PAGER_TEMPLATE" => ".default",
					"DISPLAY_TOP_PAGER" => "N",
					"DISPLAY_BOTTOM_PAGER" => "Y",
					"PAGER_TITLE" => "Новости",
					"PAGER_SHOW_ALWAYS" => "N",
					"PAGER_DESC_NUMBERING" => "N",
					"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
					"PAGER_SHOW_ALL" => "N",
					"VIEW_TYPE" => "accordion",
					"IMAGE_POSITION" => "left",
					"SHOW_SECTION_PREVIEW_DESCRIPTION" => "Y",
					"COUNT_IN_LINE" => "3",
					"SHOW_TITLE" => "Y",
					"T_TITLE" => ($arParams["T_FAQ"] ? $arParams["T_FAQ"] : GetMessage("T_FAQ")),
					"AJAX_OPTION_ADDITIONAL" => ""
					),
				false, array("HIDE_ICONS" => "Y")
				);?>
			</div>
		</div>
	<?endif;?>
	<?//show dops block?>
	<?if($arParams['SHOW_ADDITIONAL_TAB'] == 'Y'):?>
		<div class="drag_block">
			<hr/>
			<h5><?=($arParams["TAB_DOPS_NAME"] ? $arParams["TAB_DOPS_NAME"] : GetMessage("TAB_DOPS_NAME"));?></h5>
			<div>
				<?$APPLICATION->IncludeComponent(
					'bitrix:main.include',
					'',
					array(
						"AREA_FILE_SHOW" => "page",
						"AREA_FILE_SUFFIX" => "dops",
						"AREA_FILE_RECURSIVE" => "Y",
						"EDIT_TEMPLATE" => ""
					)
				);?>
			</div>
		</div>
	<?endif;?>
	<?if($arElement['PROPERTY_LINK_GOODS_VALUE']):?>
		<div class="drag_block">
		<?global $arTheme;
		$bOrderViewBasket = (trim($arTheme['ORDER_VIEW']['VALUE']) === 'Y');?>
		<div class="wraps goods-block">
			<hr />
			<div class="pull-right">
				<div class="nav-direction">
					<ul class="flex-direction-nav">
						<li class="flex-nav-prev">
							<a href="javascript:void(0)" class="flex-prev"><span>Prev</span></a>
						</li>
						<li class="flex-nav-next">
							<a href="javascript:void(0)" class="flex-next"><span>Next</span></a>
						</li>
					</ul>
				</div>
			</div>
			<h5><?=(strlen($arParams['T_GOODS']) ? $arParams['T_GOODS'] : GetMessage('T_GOODS'))?></h5>
			<?$GLOBALS['arrGoodsFilter'] = array('ID' => $arElement['PROPERTY_LINK_GOODS_VALUE']);?>
			<?global $arTheme;?>
			<?$APPLICATION->IncludeComponent(
				"bitrix:news.list",
				str_replace('table', $detail_linked_template, $catalog_template),
				Array(
					"S_ORDER_PRODUCT" => (isset($arParams["S_ORDER_PRODUCT"]) && $arParams["S_ORDER_PRODUCT"] ?: $arTheme['EXPRESSION_ORDER_BUTTON']['VALUE']),
					"S_MORE_PRODUCT" => (isset($arParams["S_TO_ALL"]) && $arParams["S_TO_ALL"] ?: $arTheme['EXPRESSION_READ_MORE_OFFERS_DEFAULT']['VALUE']), 
					"IBLOCK_TYPE" => "aspro_allcorp2_catalog",
					"IBLOCK_ID" => $arParams["CATALOG_IBLOCK_ID"],
					"NEWS_COUNT" => "20",
					"SORT_BY1" => "SORT",
					"SORT_ORDER1" => "ASC",
					"SORT_BY2" => "ID",
					"SORT_ORDER2" => "DESC",
					"FILTER_NAME" => "arrGoodsFilter",
					"FIELD_CODE" => array(
						0 => "NAME",
						1 => "PREVIEW_TEXT",
						2 => "PREVIEW_PICTURE",
						3 => "DETAIL_PICTURE",
						4 => "",
					),
					"PROPERTY_CODE" => array_merge($arParams['DETAIL_PROPERTY_CODE'],explode(',', $arTheme['CATALOG_LINKED_PROP']['VALUE'])),
					"PROPERTY_CODE" => ($propetryLink ? $propetryLink: $arParams['DETAIL_PROPERTY_CODE']),
					"CHECK_DATES" => "Y",
					"DETAIL_URL" => "",
					"PREVIEW_TRUNCATE_LEN" => "",
					"ACTIVE_DATE_FORMAT" => "d.m.Y",
					"SET_TITLE" => "N",
					"SET_STATUS_404" => "N",
					"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
					"ADD_SECTIONS_CHAIN" => "N",
					"HIDE_LINK_WHEN_NO_DETAIL" => "N",
					"PARENT_SECTION" => "",
					"PARENT_SECTION_CODE" => "",
					"INCLUDE_SUBSECTIONS" => "Y",
					"CACHE_TYPE" => "A",
					"CACHE_TIME" => "36000000",
					"CACHE_FILTER" => "Y",
					"CACHE_GROUPS" => "N",
					"PAGER_TEMPLATE" => ".default",
					"DISPLAY_TOP_PAGER" => "N",
					"DISPLAY_BOTTOM_PAGER" => "Y",
					"PAGER_TITLE" => "Новости",
					"PAGER_SHOW_ALWAYS" => "N",
					"PAGER_DESC_NUMBERING" => "N",
					"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
					"PAGER_SHOW_ALL" => "N",
					"AJAX_MODE" => "N",
					"AJAX_OPTION_JUMP" => "N",
					"AJAX_OPTION_STYLE" => "Y",
					"AJAX_OPTION_HISTORY" => "N",
					"SHOW_DETAIL_LINK" => "Y",
					"IMAGE_POSITION" => "left",
					"ORDER_VIEW" => $bOrderViewBasket,
				),
			false, array("HIDE_ICONS" => "Y")
			);?>
		</div>
		</div>
	<?endif;?>
</div>
	<?if($arParams["DETAIL_USE_COMMENTS"] == "Y"):?>
		<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/rating_likes.js");?>
		<?$APPLICATION->IncludeComponent(
			"bitrix:catalog.comments",
			"main",
			array(
				'CACHE_TYPE' => $arParams['CACHE_TYPE'],
				'CACHE_TIME' => $arParams['CACHE_TIME'],
				'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
				"COMMENTS_COUNT" => $arParams['COMMENTS_COUNT'],
				"ELEMENT_CODE" => "",
				"ELEMENT_ID" => $arElement["ID"],
				"FB_USE" => $arParams["DETAIL_FB_USE"],
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],
				"IBLOCK_TYPE" => "aspro_allcorp2_content",
				"SHOW_DEACTIVATED" => "N",
				"TEMPLATE_THEME" => "blue",
				"URL_TO_COMMENT" => "",
				"VK_USE" => $arParams["DETAIL_VK_USE"],
				"AJAX_POST" => "Y",
				"WIDTH" => "",
				"COMPONENT_TEMPLATE" => ".default",
				"BLOG_USE" => $arParams["DETAIL_BLOG_USE"],
				"BLOG_TITLE" => $arParams["BLOG_TITLE"],
				"BLOG_URL" => $arParams["DETAIL_BLOG_URL"],
				"PATH_TO_SMILE" => '',
				"EMAIL_NOTIFY" => $arParams["DETAIL_BLOG_EMAIL_NOTIFY"],
				"SHOW_SPAM" => "Y",
				"SHOW_RATING" => "Y",
				"RATING_TYPE" => "like_graphic",
				"FB_TITLE" => $arParams["FB_TITLE"],
				"FB_USER_ADMIN_ID" => "",
				"FB_APP_ID" => $arParams["DETAIL_FB_APP_ID"],
				"FB_COLORSCHEME" => "light",
				"FB_ORDER_BY" => "reverse_time",
				"VK_TITLE" => $arParams["VK_TITLE"],
				"VK_API_ID" => $arParams["DETAIL_VK_API_ID"]
			),
			false, array("HIDE_ICONS" => "Y")
		);?>
		<hr class="bottoms" />
	<?endif;?>
	<?
	$list_url = $arResult['FOLDER'].$arResult['URL_TEMPLATES']['news'];
	if($arElement['IBLOCK_SECTION_ID'])
	{
		$arSection = CCache::CIBlockSection_GetList(array('CACHE' => array('TAG' => CCache::GetIBlockCacheTag($arElement['IBLOCK_ID']), 'MULTI' => 'N')), array('ID' => $arElement['IBLOCK_SECTION_ID'], 'ACTIVE' => 'Y'), false, array('ID', 'NAME', 'SECTION_PAGE_URL'));
		if($arSection['SECTION_PAGE_URL'])
			$list_url = $arSection['SECTION_PAGE_URL'];
	}
	?>
	<?if($arParams["SHOW_NEXT_ELEMENT"] == "Y"): ?>
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

	<?\Aspro\Functions\CAsproAllcorp2::showBlockHtml([
		'FILE' => 'detail/prev_next_element.php',
		'PARAMS' => $arOptions + [
			'NEXT_ELEMENT' => $arNextElement
		]
	]);?>
	
	<?else:?>
		<div class="row">
			<div class="col-md-6 col-sm-6 share">
				<?if($arParams["USE_SHARE"] == "Y" && $arElement):?>
					<?$APPLICATION->IncludeFile(SITE_DIR."include/share_buttons.php", Array(), Array("MODE" => "html", "NAME" => GetMessage('CT_BCE_CATALOG_SOC_BUTTON')));?>
				<?endif;?>
			</div>
			<div class="col-md-6  col-sm-6">
				<a class="back-url url-block" href="<?=$list_url;?>"><i class="fa fa-angle-left"></i><span><?=($arParams["T_PREV_LINK"] ? $arParams["T_PREV_LINK"] : GetMessage('BACK_LINK'));?></span></a>
			</div>
		</div>
	<?endif;?>
<?endif;?>