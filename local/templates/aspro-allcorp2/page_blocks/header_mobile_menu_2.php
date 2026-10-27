<?
global $arTheme;
$compactMenu = ($arTheme['COMPACT_MOBILE_MENU']['VALUE'] == "Y" ? "compact_menu": '' );
?>
<div class="mobilemenu-v2 downdrop scroller <?=$compactMenu?>">
	<div class="wrap">
		<?
		// show regions
		CAllcorp2::ShowMobileRegions();
		?>
		<?if(CAllcorp2::nlo('menu-mobile', 'class="loader_circle" style="height:70px;"')):?>
		<!-- noindex -->
			<?$APPLICATION->IncludeComponent(
				"bitrix:menu",
				"top_mobile",
				Array(
					"COMPONENT_TEMPLATE" => "top_mobile",
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
					"USE_EXT" => "Y",
					'SHORT_MENU' => 'Y'
				)
			);?>
		<!-- /noindex -->
		<?endif;?>
		<?CAllcorp2::nlo('menu-mobile');?>

	</div>
</div>