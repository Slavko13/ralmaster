<?if( !defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true ) die();?>

<?if(isset($arSeoItem) && $arSeoItem):?>
	<div class="seo_block">
		<?if($arSeoItem["DETAIL_PICTURE"]):?>
			<img src="<?=CFile::GetPath($arSeoItem["DETAIL_PICTURE"]);?>" alt="" title="" class="img-responsive"/>
		<?endif;?>

		<div><?$APPLICATION->ShowViewContent('sotbit_seometa_top_desc');?></div>

		<?if($arSeoItem["PREVIEW_TEXT"]):?>
			<div class="top_text">
				<?=$arSeoItem["PREVIEW_TEXT"]?>
			</div>
		<?endif;?>
		<?if($arSeoItem["PROPERTY_FORM_QUESTION_VALUE"]):?>
			<table class="order-block noicons">
				<tbody>
					<tr>
						<td class="col-md-9 col-sm-8 col-xs-7 valign">
							<div class="text">
								<?=CAllcorp2::showIconSvg('order colored', SITE_TEMPLATE_PATH.'/images/svg/Order_service_lg.svg');?>
								<?$APPLICATION->IncludeComponent(
									'bitrix:main.include',
									'',
									array(
										'AREA_FILE_SHOW' => 'page',
										'AREA_FILE_SUFFIX' => 'landing',
										'EDIT_TEMPLATE' => ''
									)
								);?>
							</div>
						</td>
						<td class="col-md-3 col-sm-4 col-xs-5 valign">
							<div class="btns">
								<span><span class="btn btn-default btn-lg btn-transparent-bg transparent animate-load" data-event="jqm" data-param-id="<?=CAllcorp2::getFormID('aspro_allcorp2_question');?>" data-name="question"><span><?=(strlen($arParams['S_ASK_QUESTION']) ? $arParams['S_ASK_QUESTION'] : GetMessage('S_ASK_QUESTION'))?></span></span></span>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		<?endif;?>
		<?if($arSeoItem["PROPERTY_TIZERS_VALUE"]):?>
			<?$GLOBALS["arLandingTizers"] = array("ID" => $arSeoItem["PROPERTY_TIZERS_VALUE"]);?>
			<?$APPLICATION->IncludeComponent(
				"bitrix:news.list", 
				"tizers", 
				array(
					"IBLOCK_TYPE" => "aspro_allcorp2_content",
					"IBLOCK_ID" => $arParams["LANDING_TIZER_IBLOCK_ID"],
					"NEWS_COUNT" => "4",
					"SORT_BY1" => "SORT",
					"SORT_ORDER1" => "ASC",
					"SORT_BY2" => "ID",
					"SORT_ORDER2" => "DESC",
					"FILTER_NAME" => "arLandingTizers",
					"FIELD_CODE" => array(
						0 => "",
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
					"CACHE_TYPE" =>$arParams["CACHE_TYPE"],
					"CACHE_TIME" => $arParams["CACHE_TIME"],
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
					"PAGER_TEMPLATE" => "",
					"DISPLAY_TOP_PAGER" => "N",
					"DISPLAY_BOTTOM_PAGER" => "N",
					"PAGER_TITLE" => "",
					"PAGER_SHOW_ALWAYS" => "N",
					"PAGER_DESC_NUMBERING" => "N",
					"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
					"PAGER_SHOW_ALL" => "N",
					"AJAX_OPTION_ADDITIONAL" => "",
					"COMPONENT_TEMPLATE" => "tizers",
					"SET_BROWSER_TITLE" => "N",
					"SET_META_KEYWORDS" => "N",
					"SET_META_DESCRIPTION" => "N",
					"SET_LAST_MODIFIED" => "N",
					"PAGER_BASE_LINK_ENABLE" => "N",
					"SHOW_404" => "N",
					"MESSAGE_404" => ""
				),
				false, array("HIDE_ICONS" => "Y")
			);?>
		<?endif;?>
		<div><?$APPLICATION->ShowViewContent('sotbit_seometa_add_desc');?></div>
	</div>	
<?endif;?>

<?include_once(__DIR__."/../include_filter.php");?>
<?if($isAjax == "N"):?>
	<?
	$frame = new \Bitrix\Main\Page\FrameHelper('catalog-elements-block');
	$frame->begin();
	$frame->setAnimation(true);
	?>
<?endif;?>
<?$itemsCnt = CCache::CIblockElement_GetList(array("CACHE" => array("TAG" => CCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), ($GLOBALS[$arParams['FILTER_NAME']] ? $GLOBALS[$arParams['FILTER_NAME']] : $arItemFilter), array());
?>

	<?include_once(__DIR__."/../include_sort.php");?>
<?if($isAjax == "Y"):?>
	<?$APPLICATION->RestartBuffer();?>
<?endif;?>

<?$upperDisplay = $display ? strtoupper($display): 'TABLE';?>
<?if($isAjax == "N"):?>
	<div class="ajax_load <?=$display;?>">
<?endif;?>

<?// section elements?>
<?$sViewElementsTemplate = ($arParams["ELEMENTS_".$upperDisplay."_TYPE_VIEW"] == "FROM_MODULE" ? $arTheme["ELEMENTS_".$upperDisplay."_TYPE_VIEW"]["VALUE"] : $arParams["ELEMENTS_".$upperDisplay."_TYPE_VIEW"]);?>
<?@include_once($sViewElementsTemplate.'.php');?>
<?if($isAjax == "N"):?>
	</div>
	<?$frame->end();?>

	<?if($arSeoItem):?>
		<?if($arSeoItem["DETAIL_TEXT"]):?>
			<?=$arSeoItem["DETAIL_TEXT"];?>
		<?endif;?>
		
		<div><?$APPLICATION->ShowViewContent('sotbit_seometa_bottom_desc');?></div>
	<?endif;?>

	<?if($arSeoItems):?>
		<?$arLandingFilter = array();
		if($arSeoItem)
			$arLandingFilter = array("PROPERTY_SECTION" => $arSeoItem["PROPERTY_SECTION_VALUE"], "!ID" => $arSeoItem["ID"]);
		else
		{
			$arLandingFilter = array("PROPERTY_SECTION" => $arSection["ID"]);
			if($iLandingItemID)
				$arLandingFilter["!ID"] = $iLandingItemID;
			elseif($arTmpRegionsLanding)
				$arLandingFilter["!ID"] = $arTmpRegionsLanding;
		}
		?>
		<?$GLOBALS["arLandingSections"] = $arLandingFilter;?>
		<?//$GLOBALS["arLandingSections"] = array("PROPERTY_SECTION" => $arSeoItem["PROPERTY_SECTION_VALUE"], "!ID" => $arSeoItem["ID"]);?>
		<?if ($arParams['SHOW_LANDING'] !== "Y"):?>
			<?$APPLICATION->IncludeComponent(
				"bitrix:news.list", 
				"landings_list", 
				array(
					"IBLOCK_TYPE" => "aspro_allcorp2_catalog",
					"IBLOCK_ID" => $arParams["LANDING_IBLOCK_ID"],
					"NEWS_COUNT" => ($arParams["LANDING_SECTION_COUNT"] < 1 ? 1 : $arParams["LANDING_SECTION_COUNT"]),
					"SHOW_COUNT" => ($arParams["LANDING_SECTION_COUNT_VISIBLE"] < 1 ? 1 : $arParams["LANDING_SECTION_COUNT_VISIBLE"]),
					"COMPARE_FIELD" => "FILTER_URL",
					"COMPARE_PROP" => "Y",
					"SORT_BY1" => "SORT",
					"SORT_ORDER1" => "ASC",
					"SORT_BY2" => "ID",
					"SORT_ORDER2" => "DESC",
					"FILTER_NAME" => "arLandingSections",
					"FIELD_CODE" => array(
						0 => "",
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
					"CACHE_TYPE" =>$arParams["CACHE_TYPE"],
					"CACHE_TIME" => $arParams["CACHE_TIME"],
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
					"PAGER_TEMPLATE" => "",
					"DISPLAY_TOP_PAGER" => "N",
					"DISPLAY_BOTTOM_PAGER" => "N",
					"PAGER_TITLE" => "",
					"PAGER_SHOW_ALWAYS" => "N",
					"PAGER_DESC_NUMBERING" => "N",
					"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
					"PAGER_SHOW_ALL" => "N",
					"AJAX_OPTION_ADDITIONAL" => "",
					"COMPONENT_TEMPLATE" => "next",
					"SET_BROWSER_TITLE" => "N",
					"SET_META_KEYWORDS" => "N",
					"SET_META_DESCRIPTION" => "N",
					"SET_LAST_MODIFIED" => "N",
					"PAGER_BASE_LINK_ENABLE" => "N",
					"TITLE_BLOCK" => $arParams["LANDING"],
					"SHOW_404" => "N",
					"MESSAGE_404" => ""
				),
				false, array("HIDE_ICONS" => "Y")
			);?>
		<?endif;?>
	<?endif;?>
	<?if($arSeoItem):?>
		<?if(!isset($arSeoItem["IPROPERTY_VALUES"]))
		{
			$ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($arSeoItem["IBLOCK_ID"], $arSeoItem["ID"]);
			$arSeoItem["IPROPERTY_VALUES"] = $ipropValues->getValues();
		}
		$langing_seo_h1 = ($arSeoItem["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"] != "" ? $arSeoItem["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"] : $arSeoItem["NAME"]);
		$langing_seo_title = ($arSeoItem["IPROPERTY_VALUES"]["ELEMENT_META_TITLE"] != "" ? $arSeoItem["IPROPERTY_VALUES"]["ELEMENT_META_TITLE"] : $langing_seo_h1);
		$APPLICATION->SetTitle($langing_seo_h1);
		$APPLICATION->AddChainItem($langing_seo_h1);

		if($langing_seo_title)
			$APPLICATION->SetPageProperty("title", $langing_seo_title);
		
		if($arSeoItem["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"])
			$APPLICATION->SetPageProperty("description", $arSeoItem["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"]);
		
		if($arSeoItem["IPROPERTY_VALUES"]['ELEMENT_META_KEYWORDS'])
			$APPLICATION->SetPageProperty("keywords", $arSeoItem["IPROPERTY_VALUES"]['ELEMENT_META_KEYWORDS']);
		?>
	<?endif;?>
<?else:?>
	<?die();?>
<?endif;?>