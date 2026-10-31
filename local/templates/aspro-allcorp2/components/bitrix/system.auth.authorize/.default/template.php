<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

global $APPLICATION;
$APPLICATION->SetPageProperty('HIDETITLE', 'Y');
$APPLICATION->SetPageProperty('FULLWIDTH', 'Y');
$APPLICATION->SetPageProperty('MENU', 'N');
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/page_blocks/ralmaster_system.css');

$rmAuthUrl = htmlspecialcharsbx($arResult['AUTH_URL']);
$rmLastLogin = htmlspecialcharsbx($arResult['LAST_LOGIN']);
?>
<div class="rm-auth-shell" data-rm-auth-page>
	<section class="rm-auth-shell__intro" aria-labelledby="rm-auth-title">
		<p class="rm-system-kicker">Личный раздел</p>
		<h1 id="rm-auth-title">Вход на сайт</h1>
		<p>Авторизуйтесь, чтобы продолжить работу с персональными возможностями сайта.</p>
	</section>
	<section class="rm-auth-panel" aria-label="Форма входа">
		<div class="rm-auth-card">
			<h2>С возвращением</h2>
			<p class="rm-auth-card__note">Введите логин и пароль вашей учётной записи.</p>
			<?php if (!empty($arParams['~AUTH_RESULT'])) { ?><div class="rm-auth-message" role="alert"><?php ShowMessage($arParams['~AUTH_RESULT']); ?></div><?php } ?>
			<?php if (!empty($arResult['ERROR_MESSAGE'])) { ?><div class="rm-auth-message" role="alert"><?php ShowMessage($arResult['ERROR_MESSAGE']); ?></div><?php } ?>

			<form class="rm-auth-form" name="form_auth" method="post" target="_top" action="<?=$rmAuthUrl?>">
				<input type="hidden" name="AUTH_FORM" value="Y">
				<input type="hidden" name="TYPE" value="AUTH">
				<?php if ($arResult['BACKURL'] !== '') { ?><input type="hidden" name="backurl" value="<?=htmlspecialcharsbx($arResult['BACKURL'])?>"><?php } ?>
				<?php foreach ($arResult['POST'] as $key => $value) { ?><input type="hidden" name="<?=htmlspecialcharsbx($key)?>" value="<?=htmlspecialcharsbx($value)?>"><?php } ?>

				<div class="rm-auth-fields">
					<div class="rm-auth-field">
						<label for="rm-auth-login"><?=GetMessage('AUTH_LOGIN')?></label>
						<input id="rm-auth-login" type="text" name="USER_LOGIN" maxlength="255" value="<?=$rmLastLogin?>" autocomplete="username" required>
					</div>
					<div class="rm-auth-field">
						<label for="rm-auth-password"><?=GetMessage('AUTH_PASSWORD')?></label>
						<input id="rm-auth-password" type="password" name="USER_PASSWORD" maxlength="255" autocomplete="current-password" required>
					</div>
					<?php if ($arResult['SECURE_AUTH']) { ?>
						<p class="rm-auth-secure-note"><?=GetMessage('AUTH_SECURE_NOTE')?></p>
					<?php } ?>
					<?php if ($arResult['CAPTCHA_CODE']) { ?>
						<div class="rm-auth-field">
							<input type="hidden" name="captcha_sid" value="<?=htmlspecialcharsbx($arResult['CAPTCHA_CODE'])?>">
							<img src="/bitrix/tools/captcha.php?captcha_sid=<?=urlencode($arResult['CAPTCHA_CODE'])?>" width="180" height="40" alt="Защитный код">
							<label for="rm-auth-captcha"><?=GetMessage('AUTH_CAPTCHA_PROMT')?></label>
							<input id="rm-auth-captcha" type="text" name="captcha_word" maxlength="50" autocomplete="off" required>
						</div>
					<?php } ?>
				</div>

				<?php if ($arResult['STORE_PASSWORD'] === 'Y') { ?>
					<label class="rm-auth-remember" for="rm-auth-remember"><input id="rm-auth-remember" type="checkbox" name="USER_REMEMBER" value="Y"> <span><?=GetMessage('AUTH_REMEMBER_ME')?></span></label>
				<?php } ?>
				<button class="rm-system-button" type="submit" name="Login" value="Y"><?=GetMessage('AUTH_AUTHORIZE')?></button>
			</form>

			<?php if ($arParams['NOT_SHOW_LINKS'] !== 'Y') { ?>
				<nav class="rm-auth-links" aria-label="Помощь со входом">
					<a href="<?=htmlspecialcharsbx($arResult['AUTH_FORGOT_PASSWORD_URL'])?>" rel="nofollow"><?=GetMessage('AUTH_FORGOT_PASSWORD_2')?></a>
					<?php if ($arResult['NEW_USER_REGISTRATION'] === 'Y') { ?><a href="<?=htmlspecialcharsbx($arResult['AUTH_REGISTER_URL'])?>" rel="nofollow"><?=GetMessage('AUTH_REGISTER')?></a><?php } ?>
				</nav>
			<?php } ?>

			<?php if ($arResult['AUTH_SERVICES']) { ?>
				<div class="rm-auth-social">
					<p>Другой способ входа</p>
					<?php
				$APPLICATION->IncludeComponent(
					'bitrix:socserv.auth.form',
					'',
					array(
						'AUTH_SERVICES' => $arResult['AUTH_SERVICES'],
						'CURRENT_SERVICE' => $arResult['CURRENT_SERVICE'],
						'AUTH_URL' => $arResult['AUTH_URL'],
						'POST' => $arResult['POST'],
						'SHOW_TITLES' => $arResult['FOR_INTRANET'] ? 'N' : 'Y',
						'FOR_SPLIT' => $arResult['FOR_INTRANET'] ? 'Y' : 'N',
						'AUTH_LINE' => $arResult['FOR_INTRANET'] ? 'N' : 'Y',
					),
					$component,
					array('HIDE_ICONS' => 'Y')
				);
					?>
				</div>
			<?php } ?>
		</div>
	</section>
</div>
<script>
(function () {
	var field = document.getElementById(<?=$arResult['LAST_LOGIN'] !== '' ? "'rm-auth-password'" : "'rm-auth-login'"?>);
	if (field) { field.focus(); }
})();
</script>
