<?php
require $_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php';

$APPLICATION->SetTitle('О производстве');
$APPLICATION->SetPageProperty('title', 'Производство РАЛМАСТЕР в Видном');
$APPLICATION->SetPageProperty('description', 'Собственное производство РАЛМАСТЕР: лазерная резка, гибка, сварка, порошковая окраска и сборка металлических изделий.');
$APPLICATION->SetPageProperty('HIDETITLE', 'Y');
$APPLICATION->SetPageProperty('FULLWIDTH', 'Y');
$APPLICATION->SetPageProperty('MENU', 'N');

require $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/page_blocks/ralmaster_production.php';

require $_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php';
