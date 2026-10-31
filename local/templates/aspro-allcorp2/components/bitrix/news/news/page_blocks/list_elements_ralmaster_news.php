<?php
$rmContentConfig = array(
	'kind' => 'news',
	'title' => 'Новости',
	'kicker' => 'РАЛМАСТЕР сегодня',
	'intro' => 'Новости производства, новые возможности, завершённые проекты и важные события компании.',
	'empty' => 'Новые публикации готовятся.',
	'years' => true,
);
require $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/page_blocks/ralmaster_content_list.php';
