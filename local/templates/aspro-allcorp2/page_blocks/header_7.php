<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
global $arTheme, $arRegion;
$bOrder = ($arTheme['ORDER_VIEW']['VALUE'] == 'Y' && $arTheme['ORDER_VIEW']['DEPENDENT_PARAMS']['ORDER_BASKET_VIEW']['VALUE']=='HEADER' ? true : false);
$bCabinet = ($arTheme["CABINET"]["VALUE"]=='Y' ? true : false);
if($arRegion)
	$bPhone = ($arRegion['PHONES'] ? true : false);
else
	$bPhone = ((int)$arTheme['HEADER_PHONES'] ? true : false);
$logoClass = ($arTheme['COLORED_LOGO']['VALUE'] !== 'Y' ? '' : ' colored');
?>
<header class="<?=basename(__FILE__, ".php")?> <?=($arRegion ? 'with_regions' : '')?>">
	<div class="logo_and_menu-row full-fill">
		<div class="logo-row">
			<div class="maxwidth-theme">
				<?//show logo?>
				<div class="logo-block col-md-2 col-sm-3">
					<div class="logo<?=$logoClass?>">
						<?=CAllcorp2::ShowLogo();?>
					</div>
				</div>
				<?//show menu?>
				<div class="col-md-10 menu-row">
					<div class="right-icons wide sm pull-right">
						<?if($bOrder):?>
							<div class="pull-right">
								<div class="wrap_icon wrap_basket">
									<?=CAllcorp2::showBasketLink('', 'big', '', '');?>
								</div>
							</div>
						<?endif;?>
						<div class="pull-right">
							<div class="wrap_icon inner-table-block">
								<button class="inline-search-show twosmallfont" title="<?=GetMessage("SEARCH_TITLE")?>">
									<?=CAllcorp2::showIconSvg("search", SITE_TEMPLATE_PATH."/images/svg/Search_big_black.svg");?>
								</button>
							</div>
						</div>
						<?if($bCabinet):?>
							<div class="pull-right">
								<div class="wrap_icon inner-table-block">
									<?=CAllcorp2::showCabinetLink(true, false, 'big');?>
								</div>
							</div>
						<?endif;?>
						<div class="pull-right">
							<div class="inner-table-block">
								<?CAllcorp2::ShowListRegions();?>
							</div>
						</div>

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
		</div><?// class=logo-row?>
	</div>
	<div class="line-row"></div>
</header>