<div class="mobileheader-v1 rm-mobile-header">
	<button class="burger pull-left" type="button" aria-label="Открыть меню" aria-controls="mobilemenu" aria-expanded="false">
		<?=CAllcorp2::showIconSvg("burger dark", SITE_TEMPLATE_PATH."/images/svg/Burger_big_white.svg");?>
		<?=CAllcorp2::showIconSvg("close dark", SITE_TEMPLATE_PATH."/images/svg/Close.svg");?>
	</button>
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

<script>
(function () {
	function initRalmasterMobileMenuA11y() {
		var menu = document.getElementById('mobilemenu');
		var trigger = document.querySelector('#mobileheader .burger');
		if (!menu || !trigger || trigger.getAttribute('data-rm-a11y-ready') === 'true') return;

		function syncState() {
			var isOpen = menu.classList.contains('show');
			trigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
			trigger.setAttribute('aria-label', isOpen ? 'Закрыть меню' : 'Открыть меню');
		}

		trigger.setAttribute('data-rm-a11y-ready', 'true');
		new MutationObserver(syncState).observe(menu, {attributes: true, attributeFilter: ['class']});
		syncState();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initRalmasterMobileMenuA11y, {once: true});
	} else {
		initRalmasterMobileMenuA11y();
	}
})();
</script>
