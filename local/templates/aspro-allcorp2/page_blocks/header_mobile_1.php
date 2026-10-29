<div class="mobileheader-v1 rm-mobile-header">
	<div class="burger pull-left">
		<?=CAllcorp2::showIconSvg("burger dark", SITE_TEMPLATE_PATH."/images/svg/Burger_big_white.svg");?>
		<?=CAllcorp2::showIconSvg("close dark", SITE_TEMPLATE_PATH."/images/svg/Close.svg");?>
	</div>
	<div class="logo-block pull-left rm-mobile-header__logo">
		<a href="<?=SITE_DIR?>" aria-label="РАЛМАСТЕР — на главную">
			<img src="<?=SITE_TEMPLATE_PATH?>/images/ralmaster/logo-dark@2x.png" width="152" height="24" alt="РАЛМАСТЕР">
		</a>
	</div>
	<div class="right-icons pull-right">
		<div class="pull-right">
			<div class="wrap_icon wrap_basket">
				<?=CAllcorp2::showBasketLink('', 'big', '', '', true);?>
			</div>
		</div>
		<div class="pull-right">
			<div class="wrap_icon">
				<button class="inline-search-show twosmallfont" title="<?=GetMessage("SEARCH_TITLE")?>">
					<?=CAllcorp2::showIconSvg("search", SITE_TEMPLATE_PATH."/images/svg/Search_big_black.svg");?>
				</button>
			</div>
		</div>
		<div class="pull-right">
			<div class="wrap_icon wrap_phones">
				<?CAllcorp2::ShowHeaderMobilePhones("big");?>
			</div>
		</div>	
	</div>
</div>
