<?
global $arTheme;
$compactMenu = ($arTheme['COMPACT_MOBILE_MENU']['VALUE'] == "Y" ? "compact_menu": '' );
?>
<div class="mobilemenu-v1 scroller <?=$compactMenu?>">
	<div class="wrap">
		<?
		// show regions
		CAllcorp2::ShowMobileRegions();
		?>
		<?		// show cabinet item
		CAllcorp2::ShowMobileMenuCabinet();
		?>
		<?if(CAllcorp2::nlo('menu-mobile', 'class="loader_circle" style="height:70px;"')):?>
		<!-- noindex -->
			<?$APPLICATION->IncludeComponent(
				"bitrix:menu",
				"ralmaster_flat",
				Array(
					"COMPONENT_TEMPLATE" => "ralmaster_flat",
					"MENU_CACHE_TIME" => "3600000",
					"MENU_CACHE_TYPE" => "A",
					"MENU_CACHE_USE_GROUPS" => "N",
					"MENU_CACHE_GET_VARS" => array(
					),
					"DELAY" => "N",
					"MAX_LEVEL" => "1",
					"ALLOW_MULTI_SELECT" => "N",
					"ROOT_MENU_TYPE" => "top",
					"CHILD_MENU_TYPE" => "left",
					"USE_EXT" => "N"
				)
			);?>
		<!-- /noindex -->
		<?endif;?>
		<?CAllcorp2::nlo('menu-mobile');?>
		<?
		// show basket item
		CAllcorp2::ShowMobileMenuBasket();

		// use module options for change contacts
		CAllcorp2::ShowMobileMenuContacts();
		?>
		<?$APPLICATION->IncludeComponent(
			"aspro:social.info.allcorp2",
			".default",
			array(
				"CACHE_TYPE" => "A",
				"CACHE_TIME" => "3600000",
				"CACHE_GROUPS" => "N",
				"COMPONENT_TEMPLATE" => ".default"
			),
			false
		);?>
	</div>
</div>
