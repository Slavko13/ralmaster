<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
global $arTheme, $arRegion;
$bOrder = ($arTheme['ORDER_VIEW']['VALUE'] == 'Y' && $arTheme['ORDER_VIEW']['DEPENDENT_PARAMS']['ORDER_BASKET_VIEW']['VALUE']=='HEADER' ? true : false);
$bCabinet = ($arTheme["CABINET"]["VALUE"]=='Y' ? true : false);
$logoClass = ($arTheme['COLORED_LOGO']['VALUE'] !== 'Y' ? '' : ' colored');

if($arRegion)
	$bPhone = ($arRegion['PHONES'] ? true : false);
else
	$bPhone = ((int)$arTheme['HEADER_PHONES'] ? true : false);
?>
<header class="<?=basename(__FILE__, ".php")?> long <?=($arRegion ? 'with_regions' : '')?>">
	<div class="logo_and_menu-row">
		<div class="logo-row top-fill">
			<div class="maxwidth-theme">
				<?//show logo?>
				<div class="logo-block paddings pull-left">
					<div class="logo<?=$logoClass?>">
						<?=CAllcorp2::ShowLogo();?>
					</div>
				</div>
				<?//check slogan text?>
				<?if(CAllcorp2::checkContentFile(SITE_DIR.'include/header/header-text.php')):?>
					<div class="col-md-2 visible-lg nopadding-right slogan">
						<div class="top-description">
							<div>
								<?$APPLICATION->IncludeFile(SITE_DIR."include/header/header-text.php", array(), array(
										"MODE" => "html",
										"NAME" => "Text in title",
										"TEMPLATE" => "include_area.php",
									)
								);?>
							</div>
						</div>
					</div>
				<?endif;?>
				<?//show phone and callback?>
				<div class="right-icons pull-right">
					<div class="phone-block with_btn">
						<div class="region-block inner-table-block">
							<div class="inner-table-block p-block">
								<?CAllcorp2::ShowListRegions();?>
							</div>
							<?//check phone text?>
							<?if($bPhone || ($arRegion ? $arRegion['PROPERTY_SHCEDULE_VALUE']['TEXT'] : CAllcorp2::checkContentFile(SITE_DIR.'include/header-schedule.php'))):?>
								<div class="inner-table-block p-block">
									<?CAllcorp2::ShowHeaderPhones('big', 'Phone_black.svg');?>
									<?CAllcorp2::showHeaderSchedule();?>
								</div>
							<?endif?>
						</div>
						<?if($arTheme["CALLBACK_BUTTON"]["VALUE"] == "Y"):?>
							<div class="inner-table-block">
								<span class="callback-block animate-load colored  btn-transparent-bg btn-default btn" data-event="jqm" data-param-id="<?=CAllcorp2::getFormID("aspro_allcorp2_callback");?>" data-name="callback"><?=GetMessage("S_CALLBACK")?></span>
							</div>
						<?endif;?>
					</div>
				</div>
				<?//check address text?>
				<div class="pull-right">
					<div class="inner-table-block address">
						<?CAllcorp2::showAddress('address-block', 'address colored');?>
					</div>
				</div>
			</div>
		</div><?// class=logo-row?>
	</div>
	<?//show menu?>
	<div class="menu-row with-color bg<?=strtolower($arTheme['MENU_COLOR']['VALUE'])?> <?=(in_array($arTheme['MENU_COLOR']['VALUE'], array("COLORED", "DARK")) ? "colored_all" : "colored_dark");?> sliced">
		<div class="maxwidth-theme">
			<div class="col-md-12">
				<div class="right-icons pull-right">
					<?if($bOrder):?>
						<div class="pull-right">
							<div class="wrap_icon inner-table-block">
								<?=CAllcorp2::showBasketLink('', '','');?>
							</div>
						</div>
					<?endif;?>
					<div class="pull-right">
						<div class="wrap_icon inner-table-block">
							<button class="inline-search-show twosmallfont" title="<?=GetMessage("SEARCH_TITLE")?>">
								<?=CAllcorp2::showIconSvg("search", SITE_TEMPLATE_PATH."/images/svg/Search_black.svg");?>
							</button>
						</div>
					</div>
					<?if($bCabinet):?>
						<div class="pull-right">
							<div class="wrap_icon inner-table-block">
								<?=CAllcorp2::showCabinetLink(true, false);?>
							</div>
						</div>
					<?endif;?>
				</div>
				<div class="menu-only">
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
				</div>
			</div>
			<div class="lines"></div>
		</div>
	</div>
	<div class="line-row"></div>
</header>