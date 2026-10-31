<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/urlrewrite.php');
CHTTP::SetStatus('404 Not Found');
@define('ERROR_404', 'Y');
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');

$APPLICATION->SetTitle('Страница не найдена');
$rmSystemConfig = array(
	'kind' => 'not-found',
	'kicker' => 'Ошибка 404',
	'title' => 'Страница не найдена',
	'intro' => 'Возможно, адрес изменился или страница была удалена. Перейдите на главную или воспользуйтесь поиском.',
);
require($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/page_blocks/ralmaster_system_start.php');
?>
<div class="rm-not-found" data-rm-not-found>
	<div class="rm-not-found__code" aria-hidden="true">404</div>
	<div>
		<h2>Здесь ничего нет</h2>
		<p>Проверьте адрес страницы или продолжите знакомство с продукцией и услугами РАЛМАСТЕР.</p>
		<div class="rm-not-found__actions">
			<a class="rm-system-button" href="<?=SITE_DIR?>">На главную</a>
			<a class="rm-system-link" href="/search/">Поиск по сайту</a>
		</div>
	</div>
</div>
<?php
require($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/page_blocks/ralmaster_system_end.php');
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
?>
