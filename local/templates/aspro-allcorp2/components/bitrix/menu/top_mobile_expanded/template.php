<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?$this->setFrameMode(true);?>
<?global $arTheme;?>
<?if($arResult):?>
	<div class="menu top expanded-menu">
		<ul class="top">
			<?foreach($arResult as $arItem):?>
				<li class="menu_title">
					<a class="dark-color expanded-title" href="<?=$arItem['LINK']?>">
						<?=$arItem['TEXT']?>
					</a>
				</li>
				<?if($arItem['CHILD']):?>
					<?foreach($arItem['CHILD'] as $arItem2):?>
						<?$bShowChilds = $arParams['MAX_LEVEL'] > 1;?>
						<?$bParent = $arItem2['CHILD'] && $bShowChilds;?>
						<li<?=($arItem2['SELECTED'] ? ' class="selected"' : '')?>>
							<a class="dark-color<?=($bParent ? ' parent' : '')?>" href="<?=$arItem2["LINK"]?>" title="<?=$arItem2["TEXT"]?>">
								<span><?=$arItem2['TEXT']?></span>
								<?if($bParent):?>
									<span class="arrow"><i class="svg svg_triangle_right"></i></span>
								<?endif;?>
							</a>
							<?if($bParent):?>
								<ul class="dropdown<?=(!$arItem2['CHILD'] ? ' wtc' : '')?>">
									<?if($arTheme['HEADER_MOBILE_MENU_OPEN']['VALUE'] == 1):?>
										<li>
											<?=CAllcorp2::showIconSvg('close', SITE_TEMPLATE_PATH.'/images/include_svg/close.svg');?>
										</li>
									<?endif;?>
								
									<li class="menu_back">
										<a href="" class="dark-color" rel="nofollow">
											<?=CAllcorp2::showIconSvg("arrow-back", SITE_TEMPLATE_PATH."/images/svg/Arrow_right.svg");?><?=GetMessage('ALLCORP2_T_MENU_BACK')?>	
										</a>
									</li>
									<li class="menu_title"><a class="dark-color" href="<?=$arItem2['LINK'];?>"><?=$arItem2['TEXT']?></a></li>
									<?foreach($arItem2['CHILD'] as $arSubItem):?>
										<?
										if($arSubItem['PARAMS']['TYPE'] == 'PRODUCT')
											continue;
										?>
									
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
												<ul class="dropdown<?=(!$arSubItem['CHILD'] ? ' wtc' : '')?>">
													<?if($arTheme['HEADER_MOBILE_MENU_OPEN']['VALUE'] == 1):?>
														<li>
															<?=CAllcorp2::showIconSvg('close', SITE_TEMPLATE_PATH.'/images/include_svg/close.svg');?>
														</li>
													<?endif;?>
												
													<li class="menu_back">
														<a href="" class="dark-color" rel="nofollow">
															<?=CAllcorp2::showIconSvg("arrow-back", SITE_TEMPLATE_PATH."/images/svg/Arrow_right.svg");?><?=GetMessage('ALLCORP2_T_MENU_BACK')?>	
														</a>
													</li>
													<li class="menu_title"><a class="dark-color" href="<?=$arSubItem['LINK'];?>"><?=$arSubItem['TEXT']?></a></li>
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
																<ul class="dropdown<?=(!$arSubSubItem['CHILD'] ? ' wtc' : '')?>">
																	<?if($arTheme['HEADER_MOBILE_MENU_OPEN']['VALUE'] == 1):?>
																		<li>
																			<?=CAllcorp2::showIconSvg('close', SITE_TEMPLATE_PATH.'/images/include_svg/close.svg');?>
																		</li>
																	<?endif;?>
																
																	<li class="menu_back">
																		<a href="" class="dark-color" rel="nofollow">
																			<?=CAllcorp2::showIconSvg("arrow-back", SITE_TEMPLATE_PATH."/images/svg/Arrow_right.svg");?><?=GetMessage('ALLCORP2_T_MENU_BACK')?>	
																		</a>
																	</li>
																	<li class="menu_title"><a class="dark-color" href="<?=$arSubSubItem['LINK'];?>"><?=$arSubSubItem['TEXT']?></a></li>
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
				<?endif;?>
			<?endforeach;?>
		</ul>
	</div>
<?endif;?>