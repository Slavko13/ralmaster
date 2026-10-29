<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
global $arTheme, $arRegion;
$GLOBALS['RM_DESIGN_HEADER_ACTIVE'] = true;

$bOrder = (
	$arTheme['ORDER_VIEW']['VALUE'] === 'Y' &&
	$arTheme['ORDER_VIEW']['DEPENDENT_PARAMS']['ORDER_BASKET_VIEW']['VALUE'] === 'HEADER'
);
$bCabinet = ($arTheme['CABINET']['VALUE'] === 'Y');

if($arRegion)
	$bPhone = !empty($arRegion['PHONES']);
else
	$bPhone = (bool)(int)$arTheme['HEADER_PHONES'];

$logoPath = SITE_TEMPLATE_PATH.'/images/ralmaster/';
$bRalHome = CSite::InDir(SITE_DIR.'index.php');
?>
<header class="<?=basename(__FILE__, '.php')?> long rm-header<?=$bRalHome ? ' rm-header--overlay' : ''?>" data-rm-shell="header">
	<div class="rm-header__inner">
		<a class="rm-header__logo" href="<?=SITE_DIR?>" aria-label="РАЛМАСТЕР — на главную">
			<img class="rm-header__logo-image rm-header__logo-image--dark" src="<?=$logoPath?>logo-dark@2x.png" width="152" height="24" alt="РАЛМАСТЕР">
			<img class="rm-header__logo-image rm-header__logo-image--light" src="<?=$logoPath?>logo-light@2x.png" width="152" height="24" alt="" aria-hidden="true">
		</a>

		<div class="rm-header__middle menu-row">
			<nav class="mega-menu sliced rm-header__nav" aria-label="Основная навигация">
				<?$APPLICATION->IncludeComponent(
					'bitrix:main.include',
					'.default',
					array(
						'COMPONENT_TEMPLATE' => '.default',
						'PATH' => SITE_DIR.'include/header/menu.php',
						'AREA_FILE_SHOW' => 'file',
						'AREA_FILE_SUFFIX' => '',
						'AREA_FILE_RECURSIVE' => 'Y',
						'EDIT_TEMPLATE' => 'include_area.php',
					),
					false,
					array('HIDE_ICONS' => 'Y')
				);?>
			</nav>

			<div class="rm-header__tools" aria-label="Сервисы сайта">
				<button class="inline-search-show twosmallfont rm-header__tool" type="button" title="<?=GetMessage('SEARCH_TITLE')?>">
					<?=CAllcorp2::showIconSvg('search', SITE_TEMPLATE_PATH.'/images/svg/Search_black.svg');?>
				</button>
				<?if($bOrder):?>
					<div class="rm-header__tool rm-header__basket">
						<?=CAllcorp2::showBasketLink('', '', '');?>
					</div>
				<?endif;?>
				<?if($bCabinet):?>
					<div class="rm-header__tool rm-header__cabinet">
						<?=CAllcorp2::showCabinetLink(true, false);?>
					</div>
				<?endif;?>
			</div>
		</div>

		<div class="rm-header__contact">
			<?if($bPhone):?>
				<div class="rm-header__phone">
					<?CAllcorp2::ShowHeaderPhones('big', 'Phone_black.svg');?>
				</div>
			<?endif;?>
			<div class="rm-visually-hidden">
				<?CAllcorp2::showHeaderSchedule();?>
			</div>
		</div>
	</div>
</header>
