<div class="maxwidth-theme">
	<div class="logo-row v1 row margin0">
		<div class="pull-left">
			<div class="inner-table-block sep-left nopadding logo-block">
				<div class="logo<?=($arTheme["COLORED_LOGO"]["VALUE"] !== "Y" ? '' : ' colored')?>">
					<?=CAllcorp2::ShowLogo();?>
				</div>
			</div>
		</div>
		<div class="pull-left">
			<div class="inner-table-block menu-block rows sep-left">
				<div class="title"><?=CAllcorp2::showIconSvg("burger", SITE_TEMPLATE_PATH."/images/svg/burger_black.svg");?><?=GetMessage("S_MOBILE_MENU")?>&nbsp;&nbsp;<i class="fa fa-angle-down"></i></div>
				<div class="navs table-menu js-nav">
					<?if(CAllcorp2::nlo('menu-fixed')):?>
					<!-- noindex -->
						<?$APPLICATION->IncludeComponent(
							"bitrix:menu",
							"top_fixed_field",
							Array(
								"COMPONENT_TEMPLATE" => "top_fixed_field",
								"MENU_CACHE_TIME" => "3600000",
								"MENU_CACHE_TYPE" => "A",
								"MENU_CACHE_USE_GROUPS" => "N",
								"MENU_CACHE_GET_VARS" => array(
								),
								"DELAY" => "N",
								"MAX_LEVEL" => "4",
								"ALLOW_MULTI_SELECT" => "Y",
								"ROOT_MENU_TYPE" => "top",
								"CHILD_MENU_TYPE" => "left",
								"USE_EXT" => "Y"
							)
						);?>
					<!-- /noindex -->
					<?endif;?>
					<?CAllcorp2::nlo('menu-fixed');?>
				</div>
			</div>
		</div>
		<div class="pull-left col-md-4 nopadding hidden-sm hidden-xs search animation-width">
			<div class="inner-table-block">
			<?global $isFixedTopSearch;
				$isFixedTopSearch = true;?>
				<?$APPLICATION->IncludeComponent(
					"bitrix:main.include",
					"",
					Array(
						"AREA_FILE_SHOW" => "file",
						"PATH" => SITE_DIR."include/header/search.title.php",
						"EDIT_TEMPLATE" => "include_area.php",
						"IDFORMSEARCH" => 1,
					)
				);?>
			</div>
		</div>
		<div class="pull-right">
			<?=CAllcorp2::ShowBasketLink('wrap_icon inner-table-block', 'big', '');?>
		</div>
		<?if($arTheme["CABINET"]["VALUE"]=='Y'):?>
			<div class="pull-right">
				<div class="inner-table-block small-block cabinet">
					<div class="wrap_icon wrap_cabinet">
						<?=CAllcorp2::showCabinetLink(true, false, 'big');?>
					</div>
				</div>
			</div>
		<?endif;?>
		<?if($arTheme["CALLBACK_BUTTON"]["VALUE"] == "Y"):?>
			<div class="pull-right">
				<div class="inner-table-block">
					<div class="animate-load btn btn-default btn-transparent-bg btn-sm" data-event="jqm" data-param-id="<?=CAllcorp2::getFormID("aspro_allcorp2_callback");?>" data-name="callback">
						<span><?=GetMessage("S_CALLBACK")?></span>
					</div>
				</div>
			</div>
		<?endif;?>
		<div class="pull-right logo_and_menu-row">
			<div class="inner-table-block phones">
				<?CAllcorp2::ShowHeaderPhones('big', 'Phone_black.svg');?>
			</div>
		</div>
	</div>
</div>