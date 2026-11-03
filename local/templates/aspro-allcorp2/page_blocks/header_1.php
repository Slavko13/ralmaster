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
$bRalCart = CSite::InDir(SITE_DIR.'cart/');
$rmBasketCount = 0;
if ($bRalCart && class_exists('CAllcorp2')) {
	foreach ((array)CAllcorp2::getBasketItems() as $rmBasketItem) {
		$rmBasketCount += max(1, (int)($rmBasketItem['QUANTITY'] ?? 1));
	}
}
?>
<header class="<?=basename(__FILE__, '.php')?> long rm-header<?=$bRalHome ? ' rm-header--overlay' : ''?>" data-rm-shell="header">
	<div class="rm-header__inner">
		<a class="rm-header__logo" href="<?=SITE_DIR?>" aria-label="РАЛМАСТЕР — на главную">
			<img class="rm-header__logo-image rm-header__logo-image--dark" src="<?=$logoPath?>ralmaster-logo-dark.svg" width="152" height="24" alt="РАЛМАСТЕР">
			<img class="rm-header__logo-image rm-header__logo-image--light" src="<?=$logoPath?>ralmaster-logo-light.svg" width="152" height="24" alt="" aria-hidden="true">
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

		</div>

		<div class="rm-header__email">
			<?CAllcorp2::showEmail('rm-header__email-link');?>
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

		<?if($bOrder):?>
			<div class="rm-header__tool rm-header__basket<?=$bRalCart ? ' is-active' : ''?>" role="group" aria-label="Корзина">
				<?if($bRalCart):?>
					<a rel="nofollow" href="<?=SITE_DIR?>cart/" class="basket-link" aria-label="Корзина" aria-current="page">
						<svg class="rm-header__basket-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 4h2l2.1 9.8a2 2 0 0 0 2 1.6h7.9a2 2 0 0 0 2-1.6L21 7H6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><circle cx="10" cy="20" r="1.2" fill="currentColor"/><circle cx="18" cy="20" r="1.2" fill="currentColor"/></svg>
						<span class="count"<?=$rmBasketCount > 0 ? '' : ' hidden'?>><?=$rmBasketCount?></span>
					</a>
				<?else:?>
					<?=CAllcorp2::showBasketLink('', '', '');?>
				<?endif;?>
			</div>
		<?endif;?>
	</div>
</header>
