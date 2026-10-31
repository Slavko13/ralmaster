<?php
$rmContentConfig = array(
	'kind' => 'sales',
	'title' => 'Акции',
	'kicker' => 'Специальные предложения',
	'intro' => 'Актуальные условия и предложения РАЛМАСТЕР. Подробности и сроки действия указаны в каждой публикации.',
	'empty' => 'Сейчас активных предложений нет.',
	'years' => true,
);
require $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/page_blocks/ralmaster_content_list.php';
