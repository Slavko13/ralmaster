<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
global $arTheme, $arRegion;

if($arRegion)
	$bPhone = !empty($arRegion['PHONES']);
else
	$bPhone = (bool)(int)$arTheme['HEADER_PHONES'];
?>
<footer id="footer" class="rm-footer" data-rm-shell="footer">
	<div class="rm-footer__inner">
		<div class="rm-footer__main">
			<div class="rm-footer__brand">
				<a class="rm-footer__logo" href="<?=SITE_DIR?>" aria-label="РАЛМАСТЕР — на главную">
					<img src="<?=SITE_TEMPLATE_PATH?>/images/ralmaster/logo-light@2x.png" width="240" height="38" alt="РАЛМАСТЕР">
				</a>
				<p>Производство в Видном</p>
			</div>

			<nav class="rm-footer__navigation" aria-label="Навигация в подвале">
				<h2 class="rm-footer__heading">Навигация</h2>
				<ul class="rm-footer__nav-list">
					<li><a href="/product/">Продукция</a></li>
					<li><a href="/services/">Металлообработка</a></li>
					<li><a href="/painting/">Порошковая окраска</a></li>
					<li><a href="/company/">О компании</a></li>
					<li><a href="/projects/">Реализованные проекты</a></li>
					<li><a href="/contacts/">Контакты и схема проезда</a></li>
				</ul>
			</nav>

			<div class="rm-footer__addresses">
				<h2 class="rm-footer__heading">Адреса</h2>
				<div class="rm-footer__address">
					<strong>Производство</strong>
					<span>Московская область, г. Видное, Белокаменное шоссе, д. 20В, помещ. 2/3</span>
				</div>
				<div class="rm-footer__address">
					<strong>Офис продаж</strong>
					<span class="rm-footer__address-value">г. Подольск, Комсомольская ул., д. 59, офис 328</span>
				</div>
			</div>

			<div class="rm-footer__contacts">
				<h2 class="rm-footer__heading">Контактная информация</h2>
				<div class="rm-footer__contact-list">
					<?if($bPhone):?>
						<div class="rm-footer__phone"><?CAllcorp2::ShowHeaderPhones('', 'Phone_black.svg');?></div>
					<?endif;?>
					<?if(CAllcorp2::checkContentBlock(SITE_DIR.'include/footer/site-email.php', 'PROPERTY_EMAIL_VALUE')):?>
						<?CAllcorp2::showEmail('rm-footer__email');?>
					<?endif;?>
				</div>

				<div class="rm-footer__social">
					<p>Мы в социальных сетях</p>
					<div class="rm-footer__messengers" aria-label="Социальные сети">
						<span class="rm-footer__messenger" aria-label="Telegram: ссылка не настроена">
							<svg width="26" height="23" viewBox="170 285 575 480" aria-hidden="true"><path d="M226.328 494.722C372.089 431.217 469.285 389.35 517.917 369.122C656.773 311.367 685.625 301.335 704.431 301.004C708.568 300.931 717.816 301.956 723.806 306.817C728.865 310.921 730.257 316.466 730.923 320.357C731.589 324.249 732.418 333.114 731.759 340.041C724.234 419.102 691.675 610.965 675.111 699.515C668.102 736.984 654.301 749.548 640.941 750.777C611.905 753.449 589.856 731.588 561.733 713.153C517.727 684.306 492.866 666.349 450.15 638.2C400.784 605.669 432.786 587.789 460.919 558.569C468.282 550.921 596.215 434.556 598.691 424C599.001 422.68 599.288 417.759 596.365 415.16C593.441 412.562 589.126 413.45 586.012 414.157C581.599 415.159 511.298 461.625 375.11 553.556C355.155 567.259 337.081 573.935 320.887 573.585C303.034 573.199 268.693 563.491 243.164 555.192C211.851 545.014 186.964 539.633 189.132 522.346C190.26 513.343 202.659 504.135 226.328 494.722Z" fill="currentColor"/></svg>
							<span>Telegram</span>
							<svg class="rm-footer__messenger-arrow" width="16" height="16" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 19 19 5M5 5h14v14" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>
						</span>
						<span class="rm-footer__messenger" aria-label="MAX: ссылка не настроена">
							<svg width="23" height="23" viewBox="0 0 100 100" aria-hidden="true"><path d="M50.757.262c27.536 0 49.129 22.335 49.129 49.885 0 27.55-22.279 49.343-48.865 49.343-9.435 0-14.007-1.328-21.371-6.543a1.27 1.27 0 0 0-1.629.193c-5.664 6.043-20.171 10.286-20.835 2.036 0-14.386-7.186-23.729-7.186-45.3C0 21.555 23.221.262 50.757.262Zm.772 24.55C38.464 24.126 28.264 33.197 26.014 47.383c-1.864 11.75 1.436 26.072 4.265 26.793 1.2.307 4.078-1.9 6.178-3.879a1.244 1.244 0 0 1 1.45-.15c3.272 2 6.972 3.5 11.05 3.715 13.414.7 25.3-9.8 26.007-23.215.7-13.414-10.021-25.142-23.435-25.842v.007Z" fill="currentColor"/></svg>
							<span>MAX</span>
							<svg class="rm-footer__messenger-arrow" width="16" height="16" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 19 19 5M5 5h14v14" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>
						</span>
					</div>
				</div>
			</div>
		</div>

		<div class="rm-footer__legal">
			<div>© Ралмастер, <?=date('Y')?></div>
			<nav class="rm-footer__legal-links" aria-label="Юридическая информация">
				<a href="/contacts/#requisites">Реквизиты</a>
				<a href="/include/licenses_detail.php">Политика конфиденциальности</a>
				<a href="/include/licenses_detail.php">Согласие на обработку персональных данных</a>
			</nav>
			<div class="rm-footer__developer">Разработка сайта Dualweb</div>
		</div>
	</div>
</footer>
