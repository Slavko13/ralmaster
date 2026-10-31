<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}
$this->setFrameMode(true);
$rmContentConfig = array('kind' => 'articles', 'title' => 'Статьи', 'kicker' => 'Экспертиза РАЛМАСТЕР');
require $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/page_blocks/ralmaster_content_detail.php';
