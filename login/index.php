<?
define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

if (is_string($_REQUEST["backurl"]) && mb_strpos($_REQUEST["backurl"], "/") === 0)
{
	LocalRedirect($_REQUEST["backurl"]);
}

$APPLICATION->SetTitle("Вход на сайт");
$rmSystemConfig = array(
	'kind' => 'login-success',
	'kicker' => 'Личный раздел',
	'title' => 'Вы вошли на сайт',
	'intro' => 'Авторизация выполнена успешно. Теперь доступны персональные возможности сайта.',
);
require($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/page_blocks/ralmaster_system_start.php');
?>
<div class="rm-system-note">
	<p>Вы зарегистрированы и успешно авторизовались.</p>
	<a class="rm-system-button" href="<?=SITE_DIR?>">Вернуться на главную</a>
</div>
<?php require($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/page_blocks/ralmaster_system_end.php'); ?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
