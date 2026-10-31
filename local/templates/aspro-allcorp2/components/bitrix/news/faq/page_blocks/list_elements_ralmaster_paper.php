<?php
$rmContentConfig = array(
	'kind' => 'faq',
	'title' => 'Вопросы и ответы',
	'kicker' => 'Полезная информация',
	'intro' => 'Собрали ответы на частые вопросы о заказе, производстве, материалах, сроках и ответственности.',
	'empty' => 'Ответы готовятся. Задайте вопрос нашей команде напрямую.',
	'questionForm' => true,
);
require $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/page_blocks/ralmaster_content_list.php';
