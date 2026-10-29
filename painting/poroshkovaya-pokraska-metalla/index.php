<?php
require $_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php';

$APPLICATION->SetTitle('Порошковая окраска');
$APPLICATION->SetPageProperty('title', 'Порошковая окраска металлических изделий');
$APPLICATION->SetPageProperty('description', 'Порошковая окраска металлических изделий и производственных партий в Видном.');
$APPLICATION->SetPageProperty('HIDETITLE', 'Y');
$APPLICATION->SetPageProperty('FULLWIDTH', 'Y');
$APPLICATION->SetPageProperty('MENU', 'N');

require $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/page_blocks/ralmaster_powder.php';

require $_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php';
