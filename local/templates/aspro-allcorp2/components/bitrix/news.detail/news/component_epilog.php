<?
use \Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
if(isset($templateData['SECTION_BNR_CONTENT']) && $templateData['SECTION_BNR_CONTENT'] == true)
{
	global $SECTION_BNR_CONTENT;
	$SECTION_BNR_CONTENT = true;
}
?>
<?$arOrder = explode(",", $arParams["LIST_PRODUCT_BLOCKS_ORDER"]);?>


<?/*if($templateData['FORM_QUESTION']):?>
	<div class="row">
		<div class="col-md-9">
<?endif;*/?>
<?foreach($arOrder as $number => $value):?>
	<?//show sale block?>
	<?if($value == "sale"):?>
		<?if($templateData['LINK_SALE']):?>
			<div class="drag_block n_<?=$number;?> <?=$value;?>">
				<?$GLOBALS['arrSaleFilter'] = array('ID' => $templateData['LINK_SALE']); ?>
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
	<?endif;?>
	<?//show news block?>
	<?if($value == "news"):?>
		<?if ($templateData['LINK_NEWS']):
			$iItemsCount = CCache::CIBLockElement_GetList(array('CACHE' => array('MULTI' =>'Y', 'TAG' => CCache::GetIBlockCacheTag($arParams['NEWS_IBLOCK_ID']))), array('IBLOCK_ID' => $arParams['NEWS_IBLOCK_ID'], 'ACTIVE'=>'Y', 'ACTIVE_DATE' => 'Y', 'ID' => $templateData['LINK_NEWS']), array());
			if($iItemsCount):?>
				<div class="drag_block n_<?=$number;?> <?=$value;?>">
					<div class="wraps-block">
						<hr/>
						<?$GLOBALS['arrNewsFilter'] = array('ID' => $templateData['LINK_NEWS']);?>
						<h5><?=($arParams["T_NEWS"] ? $arParams["T_NEWS"] : Loc::getMessage("T_NEWS"));?></h5>
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
									3 => "DATE_ACTIVE_FROM",
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
								"T_PROJECTS" => ($arParams["T_NEWS"] ? $arParams["T_NEWS"] : Loc::getMessage("T_NEWS")),
								"AJAX_OPTION_ADDITIONAL" => ""
							),
							false, array("HIDE_ICONS" => "Y")
						);?>
					</div>
				</div>
			<?endif;?>
		<?endif;?>
	<?endif;?>
	<?//show dops block?>
	<?if($value == "dops"):?>
		<?if($arParams['SHOW_ADDITIONAL_TAB'] == 'Y'):?>
			<div class="drag_block n_<?=$number;?> <?=$value;?>">
				<hr/>
				<h5><?=($arParams["TAB_DOPS_NAME"] ? $arParams["TAB_DOPS_NAME"] : Loc::getMessage("TAB_DOPS_NAME"));?></h5>
				<div>
					<?$APPLICATION->IncludeComponent(
						'bitrix:main.include',
						'',
						array(
							"AREA_FILE_SHOW" => "page",
							"AREA_FILE_SUFFIX" => "dops",
							"EDIT_TEMPLATE" => ""
						)
					);?>
				</div>
			</div>
		<?endif;?>
	<?endif;?>
	<?//show study block?>
	<?if($value == "study"):?>
		<?if($templateData['LINK_STUDY']):
			$iItemsCount = CCache::CIBLockElement_GetList(array('CACHE' => array('MULTI' =>'Y', 'TAG' => CCache::GetIBlockCacheTag($arParams['STUDY_IBLOCK_ID']))), array('IBLOCK_ID' => $arParams['STUDY_IBLOCK_ID'], 'ACTIVE'=>'Y', 'ACTIVE_DATE' => 'Y', 'ID' => $templateData['LINK_STUDY']), array());
			if($iItemsCount):?>
				<div class="drag_block n_<?=$number;?> <?=$value;?>">
					<div class="wraps">
						<hr />
						<h5><?=($arParams["T_STUDY"] ? $arParams["T_STUDY"] : Loc::getMessage("T_STUDY"));?></h5>
						<div id="study" class=" wraps-block">
							<?$GLOBALS['arrStudyFilter'] = array('ID' => $templateData['LINK_STUDY']);?>
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
									"T_PROJECTS" => ($arParams["T_STUDY"] ? $arParams["T_STUDY"] : Loc::getMessage("T_STUDY")),
									"AJAX_OPTION_ADDITIONAL" => ""
								),
								false, array("HIDE_ICONS" => "Y")
							);?>
						</div>
					</div>
				</div>
			<?endif;?>
		<?endif;?>
	<?endif;?>
	<?//show articles block?>
	<?if($value == "articles"):?>
		<?if($templateData['LINK_ARTICLES']):
			$iItemsCount = CCache::CIBLockElement_GetList(array('CACHE' => array('MULTI' =>'Y', 'TAG' => CCache::GetIBlockCacheTag($arParams['ARTICLES_IBLOCK_ID']))), array('IBLOCK_ID' => $arParams['ARTICLES_IBLOCK_ID'], 'ACTIVE'=>'Y', 'ACTIVE_DATE' => 'Y', 'ID' => $templateData['LINK_ARTICLES']), array());
			if($iItemsCount):?>
				<div class="drag_block n_<?=$number;?> <?=$value;?>">
					<hr/>
					<h5><?=($arParams["T_ARTICLES"] ? $arParams["T_ARTICLES"] : Loc::getMessage("T_ARTICLES"));?></h5>
					<?$GLOBALS['arrArticlesFilter'] = array('ID' => $templateData['LINK_ARTICLES']); ?>
					<?$APPLICATION->IncludeComponent(
						"bitrix:news.list",
						"items-list5",
						array(
							"IBLOCK_TYPE" => "aspro_allcorp2_content",
							"IBLOCK_ID" => $arParams["ARTICLES_IBLOCK_ID"],
							"NEWS_COUNT" => "20",
							"SORT_BY1" => "SORT",
							"SORT_ORDER1" => "ASC",
							"SORT_BY2" => "ID",
							"SORT_ORDER2" => "DESC",
							"FILTER_NAME" => "arrArticlesFilter",
							"FIELD_CODE" => array(
								0 => "NAME",
								1 => "PREVIEW_TEXT",
								3 => "DATE_ACTIVE_FROM",
								4 => "PREVIEW_PICTURE",
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
							"VIEW_TYPE" => "accordion",
							"IMAGE_POSITION" => "left",
							"SHOW_SECTION_PREVIEW_DESCRIPTION" => "Y",
							"COUNT_IN_LINE" => "2",
							"SHOW_TITLE" => "Y",
							"T_TITLE" => ($arParams["T_ARTICLES"] ? $arParams["T_ARTICLES"] : Loc::getMessage("T_ARTICLES")),
						),
						false, array("HIDE_ICONS" => "Y")
					);?>
				</div>
			<?endif;?>
		<?endif;?>
	<?endif;?>
	<?//show projects block?>
	<?if($value == "projects"):?>
		<?if($templateData['LINK_PROJECTS']):
			$iItemsCount = CCache::CIBLockElement_GetList(array('CACHE' => array('MULTI' =>'Y', 'TAG' => CCache::GetIBlockCacheTag($arParams['PROJECTS_IBLOCK_ID']))), array('IBLOCK_ID' => $arParams['PROJECTS_IBLOCK_ID'], 'ACTIVE'=>'Y', 'ACTIVE_DATE' => 'Y', 'ID' => $templateData['LINK_PROJECTS']), array());
			if($iItemsCount):?>
				<div class="drag_block n_<?=$number;?> <?=$value;?>">
					<div class="wraps-block">
						<hr/>
						<?$GLOBALS['arrProjectFilter'] = array('ID' => $templateData['LINK_PROJECTS']);?>
						<h5><?=($arParams["T_PROJECTS"] ? $arParams["T_PROJECTS"] : Loc::getMessage("T_PROJECTS"));?></h5>
						<?$APPLICATION->IncludeComponent(
							"bitrix:news.list",
							// "items-row",
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
								"T_PROJECTS" => ($arParams["T_PROJECTS"] ? $arParams["T_PROJECTS"] : Loc::getMessage("T_PROJECTS")),
								"AJAX_OPTION_ADDITIONAL" => ""
							),
							false, array("HIDE_ICONS" => "Y")
						);?>
					</div>
				</div>
			<?endif;?>
		<?endif;?>
	<?endif;?>
	<?//show docs block?>
	<?if($value == "docs"):?>
		<?if($templateData['DOCUMENTS']):?>
			<div class="drag_block n_<?=$number;?> <?=$value;?>">
				<div class="docs-block">
					<hr/>
					<h5><?=($arParams["T_DOCS"] ? $arParams["T_DOCS"] : Loc::getMessage("T_DOCS"));?></h5>
					<div class="row flexbox">
						<?foreach($templateData['DOCUMENTS'] as $docID):?>
							<?$arItem = CAllcorp2::get_file_info($docID);?>
							<div class="col-md-4 col-xs-12">
								<?
								$fileName = substr($arItem['ORIGINAL_NAME'], 0, strrpos($arItem['ORIGINAL_NAME'], '.'));
								$fileTitle = (strlen($arItem['DESCRIPTION']) ? $arItem['DESCRIPTION'] : $fileName);

								?>
								<div class="blocks clearfix <?=$arItem["TYPE"];?>">
									<div class="inner-wrapper">
										<a href="<?=$arItem['SRC']?>" class="dark-color text" target="_blank"><?=$fileTitle?></a>
										<div class="filesize"><?=CAllcorp2::filesize_format($arItem['FILE_SIZE']);?></div>
									</div>
								</div>
							</div>
						<?endforeach;?>
					</div>
				</div>
			</div>
		<?endif;?>
	<?endif;?>
	<?//show faq block?>
	<?if($value == "faq"):?>
		<?if($templateData['LINK_FAQ']):
			$iItemsCount = CCache::CIBLockElement_GetList(array('CACHE' => array('MULTI' =>'Y', 'TAG' => CCache::GetIBlockCacheTag($arParams['FAQ_IBLOCK_ID']))), array('IBLOCK_ID' => $arParams['FAQ_IBLOCK_ID'], 'ACTIVE'=>'Y', 'ACTIVE_DATE' => 'Y', 'ID' => $templateData['LINK_FAQ']), array());
			if($iItemsCount):?>
				<div class="drag_block n_<?=$number;?> <?=$value;?>">
					<hr/>
					<h5><?=($arParams["T_FAQ"] ? $arParams["T_FAQ"] : Loc::getMessage("T_FAQ"));?></h5>
					<?$GLOBALS['arrFaqFilter'] = array('ID' => $templateData['LINK_FAQ']);?>
					<?$APPLICATION->IncludeComponent(
						"bitrix:news.list",
						"items-list",
						array(
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
								1 => "TITLE_BUTTON",
								2 => "LINK_BUTTON",
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
							"VIEW_TYPE" => "accordion",
							"IMAGE_POSITION" => "left",
							"SHOW_SECTION_PREVIEW_DESCRIPTION" => "Y",
							"COUNT_IN_LINE" => "3",
							"SHOW_TITLE" => "Y",
							"T_TITLE" => ($arParams["T_FAQ"] ? $arParams["T_FAQ"] : Loc::getMessage("T_FAQ")),
							"AJAX_OPTION_ADDITIONAL" => ""
						),
						false, array("HIDE_ICONS" => "Y")
					);?>
				</div>
			<?endif;?>
		<?endif;?>
	<?endif;?>
	<?//show gallery block?>
	<?if($value == "gallery"):?>
		<?if(count($templateData['GALLERY_BIG'])):?>
			<div class="drag_block n_<?=$number;?> <?=$value;?>">
				<div class="wraps gallerys">
					<hr/>
					<h5><?=($arParams["T_GALLERY"] ? $arParams["T_GALLERY"] : Loc::getMessage("T_GALLERY"));?></h5>
					<?if($arParams['GALLERY_TYPE'] == 'small'):?>
						<div class="small-gallery-block">
							<div class="flexslider unstyled row front bigs dark-nav2" data-plugin-options='{"animation": "slide", "directionNav": false, "controlNav" :true, "animationLoop": true, "slideshow": false, "counts": [4, 3, 2, 1]}'>
								<ul class="slides items">
									<?foreach($templateData['GALLERY_BIG'] as $i => $arPhoto):?>
										<li class="col-md-3 item">
											<div>
												<img src="<?=$arPhoto['PREVIEW']['src']?>" class="img-responsive inline" title="<?=$arPhoto['TITLE']?>" alt="<?=$arPhoto['ALT']?>" />
											</div>
											<a href="<?=$arPhoto['DETAIL']['SRC']?>" class="fancybox dark_block_animate" data-fancybox-group="gallery" target="_blank" title="<?=$arPhoto['TITLE']?>"></a>
										</li>
									<?endforeach;?>
								</ul>
							</div>
						</div>
					<?else:?>
						<div class="gallery-block">
							<div class="gallery-wrapper">
								<div class="inner">
									<?if(count($templateData["GALLERY_BIG"]) > 1):?>
										<div class="small-gallery-wrapper">
											<div class="thmb1 flexslider unstyled small-gallery rounded-nav" data-plugin-options='{"slideshow": "false", "animation": "slide", "animationLoop": true, "itemWidth": 60, "itemMargin": 20, "minItems": 1, "maxItems": 9, "slide_counts": 1, "asNavFor": ".gallery-wrapper .bigs"}' id="carousel1">
												<ul class="slides items">	
													<?foreach($templateData["GALLERY_BIG"] as $arPhoto):?>
														<li class="item">
															<img class="img-responsive inline" src="<?=$arPhoto["THUMB"]["src"]?>" title="<?=$arPhoto['TITLE']?>" alt="<?=$arPhoto['ALT']?>" />
														</li>
													<?endforeach;?>
												</ul>
											</div>
										</div>
									<?endif;?>
									<div class="thmb1 flexslider unstyled row bigs color-controls" id="slider" data-plugin-options='{"animation": "slide", "directionNav": true, "controlNav" :true, "animationLoop": true, "slideshow": false, "sync": ".gallery-wrapper .small-gallery", "counts": [1, 1, 1]}'>
										<ul class="slides items">
											<?foreach($templateData['GALLERY_BIG'] as $i => $arPhoto):?>
												<li class="col-md-12 item">
													<a href="<?=$arPhoto['DETAIL']['SRC']?>" class="fancybox" data-fancybox-group="gallery" target="_blank" title="<?=$arPhoto['TITLE']?>">
														<img src="<?=$arPhoto['PREVIEW']['src']?>" class="img-responsive inline" title="<?=$arPhoto['TITLE']?>" alt="<?=$arPhoto['ALT']?>" />
														<span class="zoom"></span>
													</a>
												</li>
											<?endforeach;?>
										</ul>
									</div>
								</div>
							</div>
						</div>
					<?endif;?>
				</div>
			</div>
		<?endif;?>
	<?endif;?>
	<?//show company block?>
	<?if($value == "company"):?>
		<?if($templateData['COMPANY']):?>
			<div class="drag_block n_<?=$number;?> <?=$value;?>">
				<div class="wraps barnd-block">
					<hr />
					<h5><?=(strlen($arParams['T_CLIENTS']) ? $arParams['T_CLIENTS'] : GetMessage('T_CLIENTS'))?></h5>
					<div class="item-views list list-type-block image_left">
						<div class="items row">
							<div class="col-md-12">
								<div class="item noborder clearfix">
									<?if($templateData['COMPANY']['IMAGE-BIG']):?>
										<div class="image">
											<a href="<?=$templateData['COMPANY']['DETAIL_PAGE_URL'];?>">
												<img src="<?=$templateData['COMPANY']['IMAGE-BIG']['src'];?>" alt="<?=$templateData['COMPANY']['NAME'];?>" title="<?=$templateData['COMPANY']['NAME'];?>" class="img-responsive">
											</a>
										</div>
									<?endif;?>
									<div class="body-info">
										<?if($templateData['COMPANY']['NAME']):?>
											<div class="title"><?=$templateData['COMPANY']['NAME'];?></div>
										<?endif;?>
										<?if($templateData['COMPANY']['DETAIL_TEXT']):?>
											<div class="previewtext">
												<?=$templateData['COMPANY']['DETAIL_TEXT'];?>
											</div>
										<?endif;?>
										<?if($templateData['COMPANY']['PROPERTY_SITE_VALUE']):?>
											<div class="properties">
												<div class="inner-wrapper">
													<!-- noindex -->
													<a class="property icon-block site" href="<?=$templateData['COMPANY']['PROPERTY_SITE_VALUE'];?>" target="_blank" rel="nofollow">
														<?=$templateData['COMPANY']['PROPERTY_SITE_VALUE'];?>
													</a>
													<!-- /noindex -->
												</div>
											</div>
										<?endif;?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?endif;?>
	<?endif;?>
	<?//show comments block?>
	<?if($value == "comments"):?>
		<?if($arParams["DETAIL_USE_COMMENTS"] == "Y" && $arParams["BLOG_USE"] == "Y"):?>
			<div class="drag_block n_<?=$number;?> <?=$value;?>">
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
						"ELEMENT_ID" => $arResult["ID"],
						"FB_USE" => $arParams["FB_USE"],
						"IBLOCK_ID" => $arParams["IBLOCK_ID"],
						"IBLOCK_TYPE" => "aspro_allcorp2_content",
						"SHOW_DEACTIVATED" => "N",
						"TEMPLATE_THEME" => "blue",
						"URL_TO_COMMENT" => "",
						"VK_USE" => $arParams["VK_USE"],
						"AJAX_POST" => "Y",
						"WIDTH" => "",
						"COMPONENT_TEMPLATE" => ".default",
						"BLOG_USE" => $arParams["BLOG_USE"],
						"BLOG_TITLE" => $arParams["BLOG_TITLE"],
						"BLOG_URL" => $arParams["BLOG_URL"],
						"PATH_TO_SMILE" => '',
						"EMAIL_NOTIFY" => $arParams["BLOG_EMAIL_NOTIFY"],
						"SHOW_SPAM" => "Y",
						"SHOW_RATING" => "Y",
						"RATING_TYPE" => "like_graphic",
						"FB_TITLE" => $arParams["FB_TITLE"],
						"FB_USER_ADMIN_ID" => "",
						"FB_APP_ID" => $arParams["FB_APP_ID"],
						"FB_COLORSCHEME" => "light",
						"FB_ORDER_BY" => "reverse_time",
						"VK_TITLE" => $arParams["VK_TITLE"],
						"VK_API_ID" => $arParams["VK_API_ID"]
					),
					false, array("HIDE_ICONS" => "Y")
				);?>
			</div>
		<?endif;?>
	<?endif;?>
	<?//show services block?>
	<?if($value == "services"):?>
		<?if($templateData['LINK_SERVICES']):
			$iItemsCount = CCache::CIBLockElement_GetList(array('CACHE' => array('MULTI' =>'Y', 'TAG' => CCache::GetIBlockCacheTag($arParams['SERVICES_IBLOCK_ID']))), array('IBLOCK_ID' => $arParams['SERVICES_IBLOCK_ID'], 'ACTIVE'=>'Y', 'ACTIVE_DATE' => 'Y', 'ID' => $templateData['LINK_SERVICES']), array());
			if($iItemsCount):?>
				<div class="drag_block n_<?=$number;?> <?=$value;?>">
					<div class="wraps">
						<hr />
						<h5><?=(strlen($arParams['T_SERVICES']) ? $arParams['T_SERVICES'] : Loc::getMessage('T_SERVICES'))?></h5>
						<?$GLOBALS['arrServicesFilter'] = array('ID' => $templateData['LINK_SERVICES']);?>
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
		<?endif;?>
	<?endif;?>
	<?//show reviews block?>
	<?if($value == "reviews"):?>
		<?if($templateData['LINK_REVIEWS']):
			$iItemsCount = CCache::CIBLockElement_GetList(array('CACHE' => array('MULTI' =>'Y', 'TAG' => CCache::GetIBlockCacheTag($arParams['REVIEWS_IBLOCK_ID']))), array('IBLOCK_ID' => $arParams['REVIEWS_IBLOCK_ID'], 'ACTIVE'=>'Y', 'ACTIVE_DATE' => 'Y', 'ID' => $templateData['LINK_REVIEWS']), array());
			if($iItemsCount):?>
				<div class="drag_block n_<?=$number;?> <?=$value;?>">
					<div class="wraps block">
						<hr />
						<h5><?=(strlen($arParams['T_REVIEWS']) ? $arParams['T_REVIEWS'] : Loc::getMessage('T_REVIEWS'))?></h5>
						<?$GLOBALS['arrReviewsFilter'] = array('ID' => $templateData['LINK_REVIEWS']);?>
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
		<?endif;?>
	<?endif;?>
	<?//show staff block?>
	<?if($value == "staff"):?>
		<?if($templateData['LINK_STAFF']):
			$iItemsCount = CCache::CIBLockElement_GetList(array('CACHE' => array('MULTI' =>'Y', 'TAG' => CCache::GetIBlockCacheTag($arParams['STAFF_IBLOCK_ID']))), array('IBLOCK_ID' => $arParams['STAFF_IBLOCK_ID'], 'ACTIVE'=>'Y', 'ACTIVE_DATE' => 'Y', 'ID' => $templateData['LINK_STAFF']), array());
			if($iItemsCount):?>
				<div class="drag_block n_<?=$number;?> <?=$value;?>">
					<hr/>
					<h5><?=(strlen($arParams['T_STAFF']) ? $arParams['T_STAFF'] : (count((array)$arElement['PROPERTY_LINK_STAFF_VALUE']) > 1 ? GetMessage('T_STAFF2') : GetMessage('T_STAFF1')))?></h5>
					<?$GLOBALS['arrStaffFilter'] = array('ID' => $templateData['LINK_STAFF']);?>
					<?$APPLICATION->IncludeComponent("bitrix:news.list", "staff-linked", array(
						"IBLOCK_TYPE" => "aspro_allcorp2_content",
						"IBLOCK_ID" => $arParams["STAFF_IBLOCK_ID"],
						"NEWS_COUNT" => "30",
						"SORT_BY1" => "SORT",
						"SORT_ORDER1" => "DESC",
						"SORT_BY2" => "",
						"SORT_ORDER2" => "ASC",
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
						"CACHE_TIME" => "360000",
						"CACHE_FILTER" => "Y",
						"CACHE_GROUPS" => "N",
						"PREVIEW_TRUNCATE_LEN" => "",
						"ACTIVE_DATE_FORMAT" => "d.m.Y",
						"SET_TITLE" => "N",
						"SET_STATUS_404" => "N",
						"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
						"ADD_SECTIONS_CHAIN" => "Y",
						"HIDE_LINK_WHEN_NO_DETAIL" => "N",
						"PARENT_SECTION" => "",
						"PARENT_SECTION_CODE" => "",
						"INCLUDE_SUBSECTIONS" => "Y",
						"PAGER_TEMPLATE" => "",
						"DISPLAY_TOP_PAGER" => "N",
						"DISPLAY_BOTTOM_PAGER" => "Y",
						"PAGER_TITLE" => "Новости",
						"PAGER_SHOW_ALWAYS" => "N",
						"PAGER_DESC_NUMBERING" => "N",
						"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
						"PAGER_SHOW_ALL" => "N",
						"IS_STAFF" => "Y",
						"SHOW_TABS" => "N",
						"SHOW_SECTION_PREVIEW_DESCRIPTION" => "N",
						"IMAGE_POSITION" => "left",
						"COUNT_IN_LINE" => "3",
						"AJAX_OPTION_ADDITIONAL" => ""
						),
						false, array("HIDE_ICONS" => "Y")
					);?>
				</div>
			<?endif;?>
		<?endif;?>
	<?endif;?>
	<?//show goods block?>
	<?if($value == "goods"):?>
		<?if($templateData['LINK_GOODS']):
			$iItemsCount = CCache::CIBLockElement_GetList(array('CACHE' => array('MULTI' =>'Y', 'TAG' => CCache::GetIBlockCacheTag($arParams['CATALOG_IBLOCK_ID']))), array('IBLOCK_ID' => $arParams['CATALOG_IBLOCK_ID'], 'ACTIVE'=>'Y', 'ACTIVE_DATE' => 'Y', 'ID' => $templateData['LINK_GOODS']), array());
			if($iItemsCount):?>
				<div class="drag_block n_<?=$number;?> <?=$value;?>">
					<?global $arTheme;
					$bOrderViewBasket = (trim($arTheme['ORDER_VIEW']['VALUE']) === 'Y');?>
					<div class="wraps goods-block">
						<hr />
						<?if($arParams["DETAIL_LINKED_TEMPLATE"] != 'table'):?>
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
						<?endif;?>
						<h5><?=(strlen($arParams['T_ITEMS']) ? $arParams['T_ITEMS'] : Loc::getMessage('T_GOODS'))?></h5>
						<?$GLOBALS['arrGoodsFilter'] = array('ID' => $templateData['LINK_GOODS']);?>
						<?global $arTheme;?>
						<?$propetryLink = $arTheme['CATALOG_LINKED_PROP']['VALUE'] ? explode(',', $arTheme['CATALOG_LINKED_PROP']['VALUE']) : '' ;?>

						<?$APPLICATION->IncludeComponent(
							"bitrix:news.list",
							str_replace('table', $arParams["DETAIL_LINKED_TEMPLATE"], $arParams["GOODS_TEMPLATE"]),
							Array(
								"S_ORDER_PRODUCT" => ($arParams["S_ORDER_PRODUCT"] ?: $arTheme['EXPRESSION_ORDER_BUTTON']['VALUE']),
								"S_MORE_PRODUCT" => $arParams["S_TO_ALL"] ?: $arTheme['EXPRESSION_READ_MORE_OFFERS_DEFAULT']['VALUE'], 
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
								"PROPERTY_CODE" => $propetryLink ? $propetryLink: $arParams['PROPERTY_CODE'],
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
		<?endif;?>
	<?endif;?>
<?endforeach;?>