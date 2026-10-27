<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */

$this->setFrameMode(true);
if($arResult["TABS"]):?>
	<div class="maxwidth-theme">
		<div class="col-md-12">
			<div class="row margin0 block-with-bg">
				<div class="item-views catalog blocks">
					<div class="title_block row">
						<div class="col-md-3">
							<?if($arParams["TITLE"]):?>
								<h3><?=$arParams["TITLE"];?></h3>
							<?endif;?>
						</div>
						<?$bItemBlock = (isset($arParams['ELEMENTS_TABLE_TYPE_VIEW']) && $arParams['ELEMENTS_TABLE_TYPE_VIEW'])?>
						<div class="col-md-6 text-center">
							<div class="items head-block" <?=(count($arResult["TABS"]) == 1 ? "style='display:none;'" : "");?>>
								<div class="row margin0">
									<div class="maxwidth-theme">
										<div class="col-md-12">
											<?$i = 0;?>
											<?foreach($arResult["TABS"] as $key => $arItem):?>
												<div class="item-link <?=(!$i ? 'active clicked' : '');?>">
													<div class="title">
														<span><?=$arItem['TITLE']?></span>
													</div>
												</div>
												<?++$i;?>
											<?endforeach;?>
										</div>
									</div>
								</div>
							</div>
						</div>
						<?if(!$bItemBlock):?>
							<div class="col-md-3">
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
					</div>
					<?$arParams['SET_TITLE'] = 'N';$arParamsTmp = urlencode(serialize($arParams));?>
					<span class='request-data' data-value='<?=$arParamsTmp?>'></span>
					<div class="tabs_ajax">
						<div class="body-block">
							<div class="row margin0">
								<?$i = 0;?>
								<?foreach($arResult["TABS"] as $key => $arItem):?>
									<div class="item-block <?=(!$i ? 'active opacity1' : '');?>" data-filter="<?=($arItem["FILTER"] ? urlencode(serialize($arItem["FILTER"])) : '');?>">
										<?if(!$i)
										{
											if($arItem["FILTER"])
												$GLOBALS[$arParams["FILTER_NAME"]] = $arItem["FILTER"];

											include(str_replace("//", "/", $_SERVER["DOCUMENT_ROOT"].SITE_DIR."include/mainpage/comp_catalog_ajax.php"));
										}?>
									</div>
									<?++$i;?>
								<?endforeach;?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?endif;?>