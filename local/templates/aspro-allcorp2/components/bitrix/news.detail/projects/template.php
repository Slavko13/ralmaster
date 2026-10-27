<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>

<?$this->setFrameMode(true);?>	
<?use \Bitrix\Main\Localization\Loc;?>

<?
/*set array props for component_epilog*/
$templateData = array(
	'LINK_SALE' => (array)$arResult['DISPLAY_PROPERTIES']['LINK_SALE']['VALUE'],
	'LINK_TIZERS' => (array)$arResult['DISPLAY_PROPERTIES']['LINK_TIZERS']['VALUE'],
	'LINK_NEWS' => (array)$arResult['DISPLAY_PROPERTIES']['LINK_NEWS']['VALUE'],
	'DOCUMENTS' => (array)$arResult['DISPLAY_PROPERTIES']['DOCUMENTS']['VALUE'],
	'LINK_FAQ' => (array)$arResult['DISPLAY_PROPERTIES']['LINK_FAQ']['VALUE'],
	'LINK_PROJECTS' => (array)$arResult['DISPLAY_PROPERTIES']['LINK_PROJECTS']['VALUE'],
	'LINK_SERVICES' => (array)$arResult['DISPLAY_PROPERTIES']['LINK_SERVICES']['VALUE'],
	'LINK_GOODS' => (array)$arResult['DISPLAY_PROPERTIES']['LINK_GOODS']['VALUE'],
	'LINK_STAFF' => (array)$arResult['DISPLAY_PROPERTIES']['LINK_STAFF']['VALUE'],
	'LINK_REVIEWS' => (array)$arResult['DISPLAY_PROPERTIES']['LINK_REVIEWS']['VALUE'],
	'LINK_STUDY' => (array)$arResult['DISPLAY_PROPERTIES']['LINK_STUDY']['VALUE'],
	'LINK_ARTICLES' => (array)$arResult['DISPLAY_PROPERTIES']['LINK_ARTICLES']['VALUE'],
	'LINK_MAP' => $arResult['PROPERTIES']['MAP']['VALUE'],
	'INFO' => (array)$arResult['PROPERTIES']['INFO'],
	'PREVIEW_PICTURE' => (array)$arResult['PREVIEW_PICTURE'],
	'FORM_QUESTION' => ($arResult['DISPLAY_PROPERTIES']['FORM_QUESTION']['VALUE_XML_ID'] == 'YES'),
	'GALLERY_BIG' => (array)$arResult['GALLERY_BIG'],
	'CHARACTERISTICS' => (array)$arResult['DISPLAY_PROPERTIES_FORMATTED'],
	'DETAIL_TEXT' => $arResult['FIELDS']['DETAIL_TEXT'],
	'PREVIEW_TEXT' => $arResult['FIELDS']['PREVIEW_TEXT'],
	'COMPANY' => (array)$arResult['COMPANY'],
);
if(isset($arResult['PROPERTIES']['BNR_TOP']) && $arResult['PROPERTIES']['BNR_TOP']['VALUE_XML_ID'] == 'YES')
	$templateData['SECTION_BNR_CONTENT'] = true;
/**/
?>

<?$bShowAskBlock = ($arResult['DISPLAY_PROPERTIES']['FORM_QUESTION']['VALUE_XML_ID'] == 'YES');?>
<?$bShowOrderBlock = ($arResult['DISPLAY_PROPERTIES']['FORM_ORDER']['VALUE_XML_ID'] == 'YES');?>
<?$bShowAllChar = (isset($arResult['DISPLAY_PROPERTIES_FORMATTED']) && count($arResult['DISPLAY_PROPERTIES_FORMATTED'])>3);?>
<?$bShowProps = ($arResult['DISPLAY_PROPERTIES_FORMATTED'] || $bShowAskBlock || $bShowOrderBlock || (isset($arResult['PROPERTIES']['TASK_PROJECT']) && isset($arResult['PROPERTIES']['TASK_PROJECT']['VALUE']['TEXT'])));?>
<?$bShowTopBanner = (isset($templateData['SECTION_BNR_CONTENT'] ) && $templateData['SECTION_BNR_CONTENT'] == true);?>
<?$bShowTopPicture = (isset($arResult['PROPERTIES']['TOP_PICTURE']) && $arResult['PROPERTIES']['TOP_PICTURE']['VALUE']);?>
<?
if($bShowProps && ($bShowTopBanner || $bShowTopPicture))
{
	$templateData['CUSTOM_IMG'] = true;
}
?>
<?// shot top banners start?>
<?if($bShowTopBanner):?>
	<?$this->SetViewTarget("section_bnr_content");?>
		<?CAllcorp2::ShowTopDetailBanner($arResult, $arParams);?>
	<?$this->EndViewTarget();?>
<?endif;?>
<?// shot top banners end?>

<?// shot top props start?>
<?if($bShowTopBanner || $bShowTopPicture):?>
	<?$this->SetViewTarget("top_section_filter_content");?>
		<?if($bShowTopPicture):?>
			<?$arFile = CFile::GetFileArray($arResult['PROPERTIES']['TOP_PICTURE']['VALUE']);
			$title = ($arFile['DESCRIPTION'] ? $arFile['DESCRIPTION'] : $arResult['NAME']);?>
			<div class="detailimage image-head">
				<img src="<?=$arFile['SRC'];?>" class="img-responsive" title="<?=$title;?>" alt="<?=$title;?>">
			</div>
		<?endif;?>
		<?if($bShowProps):?>
			<div class="bg_block props gray">
				<div class="row">
					<div class="maxwidth-theme">
						<div class="col-md-12">
							<div class="row">
								<div class="col-md-9 col-sm-7">
									<div class="projects-blocks">
										<div class="head-block">
											<div class="info">
												<?if(isset($arResult['PROPERTIES']['TASK_PROJECT']) && isset($arResult['PROPERTIES']['TASK_PROJECT']['~VALUE']['TEXT'])):?>
													<div class="hh">
														<div class="title_grey_small"><?=$arResult['PROPERTIES']['TASK_PROJECT']['NAME'];?></div>
														<div class="text"><?=$arResult['PROPERTIES']['TASK_PROJECT']['~VALUE']['TEXT'];?></div>
													</div>
												<?endif;?>
												<?if($arResult['DISPLAY_PROPERTIES_FORMATTED']):?>
													<div class="row">
														<?$i = 0;?>
														<?foreach($arResult['DISPLAY_PROPERTIES_FORMATTED'] as $code => $arProp):
															if($i++ > 2)
																continue;?>
															<div class="col-md-4">
																<div class="prop-block">
																	<div class="title title_grey_small">
																		<?if($arProp['HINT']):?>
																			<div class="hint">
																				<span class="icons" data-toggle="tooltip" data-placement="top" title="<?=$arProp['HINT']?>"></span>
																			</div>
																		<?endif;?>
																		<span><?=$arProp['NAME']?></span>
																	</div>
																	<div class="value">
																		<?if(is_array($arProp['DISPLAY_VALUE'])):?>
																			<?foreach($arProp['DISPLAY_VALUE'] as $key => $value):?>
																				<?if($arProp['DISPLAY_VALUE'][$key + 1]):?>
																					<?=$value.'&nbsp;/ '?>
																				<?else:?>
																					<?=$value?>
																				<?endif;?>
																			<?endforeach;?>
																		<?else:?>
																			<?if($code == 'SITE')
																			{
																				$arProp['DISPLAY_VALUE'] = str_replace('<a', '<a target="_blank"', $arProp['DISPLAY_VALUE']);
																			}?>
																			<?=$arProp['DISPLAY_VALUE']?>
																		<?endif;?>
																	</div>
																</div>
															</div>
														<?endforeach;?>
													</div>
													<?if($bShowAllChar):?>
														<div class="all_char colored"><span class="choise" data-block=".chars-block"><?=Loc::getMessage('ALL_CHAR');?></span></div>
													<?endif;?>
												<?endif;?>
											</div>
										</div>
									</div>
								</div>
								<div class="col-md-3 col-sm-5">
									<div class="block-wrapper">
										<div class="buttons-block">
											<?if($bShowOrderBlock):?>
												<div class="block">
													<span class="btn btn-default btn-lg animate-load" data-event="jqm" data-param-id="<?=($arParams["FORM_ID_ORDER_SERVISE"] ? $arParams["FORM_ID_ORDER_SERVISE"] : CAllcorp2::getFormID("aspro_allcorp2_order_project"));?>" data-name="order_services" data-autoload-service="<?=CAllcorp2::formatJsName($arResult['NAME'])?>" data-autoload-study="<?=CAllcorp2::formatJsName($arResult['NAME'])?>" data-autoload-project="<?=CAllcorp2::formatJsName($arResult['NAME'])?>"><span><?=(strlen($arParams['S_ORDER_SERVISE']) ? $arParams['S_ORDER_SERVISE'] : Loc::getMessage('S_ORDER_SERVISE'))?></span></span>
												</div>
											<?endif;?>
											<?if($bShowAskBlock):?>
												<div class="block">
													<span class="btn btn-default btn-lg btn-transparent-bg animate-load" data-event="jqm" data-param-id="<?=CAllcorp2::getFormID("aspro_allcorp2_question");?>" data-autoload-need_product="<?=CAllcorp2::formatJsName($arResult['NAME'])?>" data-name="question"><span><?=(strlen($arParams['S_ASK_QUESTION']) ? $arParams['S_ASK_QUESTION'] : Loc::getMessage('S_ASK_QUESTION'))?></span></span>
												</div>
											<?endif;?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?endif;?>
	<?$this->EndViewTarget();?>
<?endif;?>
<?// shot top props end?>

<?// element name?>
<?if($arParams['DISPLAY_NAME'] != 'N' && strlen($arResult['NAME'])):?>
	<h2 itemprop="name"><?=$arResult['NAME']?></h2>
<?endif;?>
<?if(!$bShowTopBanner && !$bShowTopPicture):?>
	<div class="item projects-blocks">
		<div class="head-block<?=($arResult['GALLERY'] ? '' : ' wti')?>">
			<div class="row">
				<?if($arResult['GALLERY']):?>
					<div class="col-md-7 col-sm-7">
						<div class="inner">
							<div class="flexslider color-controls dark-nav show-nav-controls" data-slice="Y" data-plugin-options='{"animation": "slide", "directionNav": true, "controlNav" :true, "animationLoop": true, "slideshow": false, "counts": [1, 1, 1]}'>
								<ul class="slides items">
									<?$countAll = count($arResult['GALLERY']);?>
									<?foreach($arResult['GALLERY'] as $i => $arPhoto):?>
										<li class="item" data-slice-block="Y" data-slice-params='{"lineheight": -3}'>
											<a href="<?=$arPhoto['DETAIL']['SRC']?>" target="_blank" title="<?=$arPhoto['TITLE']?>" class="fancybox" data-fancybox-group="gallery">
												<img src="<?=$arPhoto['PREVIEW']['src']?>" class="img-responsive inline" title="<?=$arPhoto['TITLE']?>" alt="<?=$arPhoto['ALT']?>" itemprop="image" />
												<span class="zoom"></span>
											</a>
										</li>
									<?endforeach;?>
								</ul>
							</div>
						</div>
					</div>
				<?endif;?>
				<div class="<?=($arResult['GALLERY'] ? 'col-md-5 col-sm-5' : 'col-md-12 col-sm-12');?>">
					<div class="info" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
						<?if(isset($arResult['PROPERTIES']['TASK_PROJECT']) && isset($arResult['PROPERTIES']['TASK_PROJECT']['~VALUE']['TEXT'])):?>
							<div class="hh">
								<div class="title_grey_small"><?=$arResult['PROPERTIES']['TASK_PROJECT']['NAME'];?></div>
								<div class="text"><?=$arResult['PROPERTIES']['TASK_PROJECT']['~VALUE']['TEXT'];?></div>
							</div>
						<?endif;?>
						<?if($arResult['DISPLAY_PROPERTIES_FORMATTED'] || ($bShowAskBlock || $bShowOrderBlock)):?>
							<div class="row">
								<?if($arResult['DISPLAY_PROPERTIES_FORMATTED']):?>
									<div class="col-md-<?=(($bShowAskBlock || $bShowOrderBlock) ? 6 : 12)?>">
										<?$i = 0;?>
										<?foreach($arResult['DISPLAY_PROPERTIES_FORMATTED'] as $code => $arProp):
											if($i++ > 2)
												continue;?>
											<div class="prop-block">
												<div class="title title_grey_small">
													<?if($arProp['HINT']):?>
														<div class="hint">
															<span class="icons" data-toggle="tooltip" data-placement="top" title="<?=$arProp['HINT']?>"></span>
														</div>
													<?endif;?>
													<span><?=$arProp['NAME']?></span>
												</div>
												<div class="value">
													<?if(is_array($arProp['DISPLAY_VALUE'])):?>
														<?foreach($arProp['DISPLAY_VALUE'] as $key => $value):?>
															<?if($arProp['DISPLAY_VALUE'][$key + 1]):?>
																<?=$value.'&nbsp;/ '?>
															<?else:?>
																<?=$value?>
															<?endif;?>
														<?endforeach;?>
													<?else:?>
														<?if($code == 'SITE')
														{
															$arProp['DISPLAY_VALUE'] = str_replace('<a', '<a target="_blank"', $arProp['DISPLAY_VALUE']);
														}?>
														<?=$arProp['DISPLAY_VALUE']?>
													<?endif;?>
												</div>
											</div>
										<?endforeach;?>
										<?if($bShowAllChar):?>
											<div class="all_char colored"><span class="choise" data-block=".chars-block"><?=Loc::getMessage('ALL_CHAR');?></span></div>
										<?endif;?>
									</div>
								<?endif;?>
								<?if($bShowAskBlock || $bShowOrderBlock):?>
									<div class="col-md-<?=($arResult['DISPLAY_PROPERTIES_FORMATTED'] ? 6 : 12)?>">
										<div class="buttons-block">
											<?if($bShowOrderBlock):?>
												<div class="block">
													<span class="btn btn-default btn-lg animate-load" data-event="jqm" data-param-id="<?=($arParams["FORM_ID_ORDER_SERVISE"] ? $arParams["FORM_ID_ORDER_SERVISE"] : CAllcorp2::getFormID("aspro_allcorp2_order_project"));?>" data-name="order_services" data-autoload-service="<?=CAllcorp2::formatJsName($arResult['NAME'])?>" data-autoload-study="<?=CAllcorp2::formatJsName($arResult['NAME'])?>" data-autoload-project="<?=CAllcorp2::formatJsName($arResult['NAME'])?>"><span><?=(strlen($arParams['S_ORDER_SERVISE']) ? $arParams['S_ORDER_SERVISE'] : Loc::getMessage('S_ORDER_SERVISE'))?></span></span>
												</div>
											<?endif;?>
											<?if($bShowAskBlock):?>
												<div class="block">
													<span class="btn btn-default btn-lg btn-transparent-bg animate-load" data-event="jqm" data-param-id="<?=CAllcorp2::getFormID("aspro_allcorp2_question");?>" data-autoload-need_product="<?=CAllcorp2::formatJsName($arResult['NAME'])?>" data-name="question"><span><?=(strlen($arParams['S_ASK_QUESTION']) ? $arParams['S_ASK_QUESTION'] : Loc::getMessage('S_ASK_QUESTION'))?></span></span>
												</div>
												<div class="text">
													<?$APPLICATION->IncludeComponent(
														'bitrix:main.include',
														'',
														array(
															'AREA_FILE_SHOW' => 'page',
															'AREA_FILE_SUFFIX' => 'detail',
															'EDIT_TEMPLATE' => ''
														)
													);?>
												</div>
											<?endif;?>
										</div>
									</div>
								<?endif;?>
							</div>
						<?endif;?>
					</div>
				</div>
			</div>
		</div>
	</div>
<?endif;?>