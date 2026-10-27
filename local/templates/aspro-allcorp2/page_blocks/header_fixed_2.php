<div class="maxwidth-theme">
	<div class="logo-row v2 row margin0">
		<div class="inner-table-block nopadding logo-block">
			<div class="logo<?=($arTheme["COLORED_LOGO"]["VALUE"] !== "Y" ? '' : ' colored')?>">
				<?=CAllcorp2::ShowLogo();?>
			</div>
		</div>
		<div class="inner-table-block menu-block menu-row">
			<div class="navs table-menu js-nav">
				<?if(CAllcorp2::nlo('menu-fixed')):?>
				<!-- noindex -->
					<nav class="mega-menu sliced">
						<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
							array(
								"COMPONENT_TEMPLATE" => ".default",
								"PATH" => SITE_DIR."include/header/menu.php",
								"AREA_FILE_SHOW" => "file",
								"AREA_FILE_SUFFIX" => "",
								"AREA_FILE_RECURSIVE" => "Y",
								"EDIT_TEMPLATE" => "include_area.php"
							),
							false, array("HIDE_ICONS" => "Y")
						);?>
					</nav>
				<!-- /noindex -->
				<?endif;?>
				<?CAllcorp2::nlo('menu-fixed');?>
			</div>
		</div>
		<?if($arTheme["CABINET"]["VALUE"]=='Y'):?>
			<div class="inner-table-block nopadding small-block cabinet">
				<div class="wrap_icon wrap_cabinet">
					<?=CAllcorp2::showCabinetLink(true, false, 'big');?>
				</div>
			</div>
		<?endif;?>
		<div class="inner-table-block small-block nopadding inline-search-show" data-type_search="fixed">
			<div class="search-block wrap_icon" title="<?=GetMessage("SEARCH_TITLE")?>">
				<?=CAllcorp2::showIconSvg("search big", SITE_TEMPLATE_PATH."/images/svg/Search_big_black.svg");?>
			</div>
		</div>
		<?=CAllcorp2::showBasketLink('wrap_icon inner-table-block nopadding', 'big','');?>
	</div>
</div>