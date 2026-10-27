					<?if(!$isIndex):?>
						<?CAllcorp2::checkRestartBuffer();?>
					<?endif;?>
					<?IncludeTemplateLangFile(__FILE__);?>
					<?global $arTheme, $isIndex, $is404;?>
					<?if(!$isIndex):?>
							<?if($is404):?>
								</div>
							<?else:?>
									<?CAllcorp2::get_banners_position('CONTENT_BOTTOM');?>
									</div> <?// class=right_block?>
									<?if($APPLICATION->GetProperty("MENU") != "N" && !defined("ERROR_404")):?>
										<?CAllcorp2::ShowPageType('left_block');?>
									<?endif;?>
								</div><?// class=col-md-12 col-sm-12 col-xs-12 content-md?>
							<?endif;?>
							<?if($APPLICATION->GetProperty("FULLWIDTH")!=='Y'):?>
								</div><?// class="maxwidth-theme?>
							<?endif;?>
						</div><?// class=row?>
					<?else:?>
						<?CAllcorp2::ShowPageType('indexblocks');?>
					<?endif;?>
				</div><?// class=container?>
				<?CAllcorp2::get_banners_position('FOOTER');?>
			</div><?// class=main?>
		</div><?// class=body?>
		<?CAllcorp2::ShowPageType('footer');?>
		<div class="bx_areas">
			<?CAllcorp2::ShowPageType('bottom_counter');?>
		</div>
		<?CAllcorp2::SetMeta();?>
		<?CAllcorp2::ShowPageType('search_title_component');?>
		<?CAllcorp2::ShowPageType('basket_component');?>
		<?CAllcorp2::AjaxAuth();?>
		<?CAllcorp2::bottomActions();?>
	</body>
</html>