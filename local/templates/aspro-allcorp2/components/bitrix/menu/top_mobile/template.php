<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?$this->setFrameMode(true);?>
<?
global $arTheme;
$expandedLink = $arTheme['HEADER_MOBILE_MENU_EXPAND']['VALUE'];
?>
<?if($arResult):?>
	<div class="menu top">
		<ul class="top">
			<?foreach($arResult as $arItem):?>
				<?$bShowChilds = $arParams['MAX_LEVEL'] > 1;
				if($expandedLink == $arItem['LINK'] && $arParams['SHORT_MENU'] != 'Y')
					continue;
				?>
				<?$bParent = $arItem['CHILD'] && $bShowChilds;?>
				<li<?=($arItem['SELECTED'] ? ' class="selected"' : '')?>>
					<a class="dark-color<?=($bParent ? ' parent' : '')?>" href="<?=$arItem["LINK"]?>" title="<?=$arItem["TEXT"]?>">
						<span><?=$arItem['TEXT']?></span>
						<?if($bParent):?>
							<span class="arrow"><i class="svg svg_triangle_right"></i></span>
						<?endif;?>
					</a>
					<?if($bParent):?>
						<ul class="dropdown">
							<li class="menu_back"><a href="" class="dark-color" rel="nofollow"><?=CAllcorp2::showIconSvg("arrow-back", SITE_TEMPLATE_PATH."/images/svg/Arrow_right_white.svg");?><?=GetMessage('ALLCORP2_T_MENU_BACK')?></a></li>
							<li class="menu_title"><a href="<?=$arItem["LINK"]?>"><?=$arItem['TEXT']?></a></li>
							<?foreach($arItem['CHILD'] as $arSubItem):?>
								<?$bShowChilds = $arParams['MAX_LEVEL'] > 2;?>
								<?$bParent = $arSubItem['CHILD'] && $bShowChilds;?>
								<li<?=($arSubItem['SELECTED'] ? ' class="selected"' : '')?>>
									<a class="dark-color<?=($bParent ? ' parent' : '')?>" href="<?=$arSubItem["LINK"]?>" title="<?=$arSubItem["TEXT"]?>">
										<span><?=$arSubItem['TEXT']?></span>
										<?if($bParent):?>
											<span class="arrow"><i class="svg svg_triangle_right"></i></span>
										<?endif;?>
									</a>
									<?if($bParent):?>
										<ul class="dropdown">
											<li class="menu_back"><a href="" class="dark-color" rel="nofollow"><?=CAllcorp2::showIconSvg("arrow-back", SITE_TEMPLATE_PATH."/images/svg/Arrow_right.svg");?><?=GetMessage('ALLCORP2_T_MENU_BACK')?></a></li>
											<li class="menu_title"><a href="<?=$arSubItem["LINK"]?>"><?=$arSubItem['TEXT']?></a></li>
											<?foreach($arSubItem["CHILD"] as $arSubSubItem):?>
												<?$bShowChilds = $arParams['MAX_LEVEL'] > 3;?>
												<?$bParent = $arSubSubItem['CHILD'] && $bShowChilds;?>
												<li<?=($arSubSubItem['SELECTED'] ? ' class="selected"' : '')?>>
													<a class="dark-color<?=($bParent ? ' parent' : '')?>" href="<?=$arSubSubItem["LINK"]?>" title="<?=$arSubSubItem["TEXT"]?>">
														<span><?=$arSubSubItem['TEXT']?></span>
														<?if($bParent):?>
															<span class="arrow"><i class="svg svg_triangle_right"></i></span>
														<?endif;?>
													</a>
													<?if($bParent):?>
														<ul class="dropdown">
															<li class="menu_back"><a href="" class="dark-color" rel="nofollow"><?=CAllcorp2::showIconSvg("arrow-back", SITE_TEMPLATE_PATH."/images/svg/Arrow_right.svg");?><?=GetMessage('ALLCORP2_T_MENU_BACK')?></a></li>
															<li class="menu_title"><a href="<?=$arSubSubItem["LINK"]?>"><?=$arSubSubItem['TEXT']?></a></li>
															<?foreach($arSubSubItem["CHILD"] as $arSubSubSubItem):?>
																<li<?=($arSubSubSubItem['SELECTED'] ? ' class="selected"' : '')?>>
																	<a class="dark-color" href="<?=$arSubSubSubItem["LINK"]?>" title="<?=$arSubSubSubItem["TEXT"]?>">
																		<span><?=$arSubSubSubItem['TEXT']?></span>
																	</a>
																</li>
															<?endforeach;?>
														</ul>
													<?endif;?>
												</li>
											<?endforeach;?>
										</ul>
									<?endif;?>
								</li>
							<?endforeach;?>
						</ul>
					<?endif;?>
				</li>
			<?endforeach;?>
		</ul>
	</div>
<?endif;?>