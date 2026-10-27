<div class="mobileheader-v2">
	<div class="burger pull-left">
		<?=CAllcorp2::showIconSvg("burger white", SITE_TEMPLATE_PATH."/images/svg/Burger_big_white.svg");?>
		<?=CAllcorp2::showIconSvg("close white", SITE_TEMPLATE_PATH."/images/svg/Close.svg");?>
	</div>
	<div class="title-block col-sm-8 col-xs-7 pull-left"><?$APPLICATION->ShowTitle(false)?></div>
	<div class="right-icons pull-right">
		<div class="pull-right">
			<div class="wrap_icon wrap_basket">
				<?=CAllcorp2::showBasketLink('', 'big', '', '', true);?>
			</div>
		</div>
		<div class="pull-right">
			<div class="wrap_icon">
				<button class="inline-search-show twosmallfont" title="<?=GetMessage("SEARCH_TITLE")?>">
					<?=CAllcorp2::showIconSvg("search white", SITE_TEMPLATE_PATH."/images/svg/Search_big_black.svg");?>
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