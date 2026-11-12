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
if (class_exists('CAllcorp2')) {
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
				<a rel="nofollow" href="<?=SITE_DIR?>cart/" class="basket-link" aria-label="Корзина<?=$rmBasketCount > 0 ? ': '.$rmBasketCount : ''?>"<?=$bRalCart ? ' aria-current="page"' : ''?>>
					<svg class="rm-header__basket-icon" viewBox="0 0 17 16" aria-hidden="true"><path d="M1500,66.344l-0.63.656h-10.62l-0.75-.781L1486.85,59H1485a1,1,0,1,1,0-2h2c0.26,0,.52.006,0.52.006a1.048,1.048,0,0,1,1.15.763L1489.16,60h11.34l0.5,0.5V62ZM1489.37,62l0.41,3h8.5l0.72-3h-9.63Zm1.13,6a2.5,2.5,0,1,1-2.5,2.5A2.5,2.5,0,0,1,1490.5,68Zm-0.5,3h1V70h-1v1Zm7.5-3a2.5,2.5,0,1,1-2.5,2.5A2.5,2.5,0,0,1,1497.5,68Zm-0.5,3h1V70h-1v1Z" transform="translate(-1484 -57)"/></svg>
					<span class="count"<?=$rmBasketCount > 0 ? '' : ' hidden'?>><?=$rmBasketCount?></span>
				</a>
			</div>
		<?endif;?>
	</div>
</header>
