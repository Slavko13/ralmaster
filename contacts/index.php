<?php
require $_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php';

$APPLICATION->SetTitle('Контакты');
$APPLICATION->SetPageProperty('title', 'Контакты РАЛМАСТЕР');
$APPLICATION->SetPageProperty('description', 'Контакты производства РАЛМАСТЕР в Видном и офиса продаж в Подольске, схема проезда и реквизиты компании.');
$APPLICATION->SetPageProperty('HIDETITLE', 'Y');
$APPLICATION->SetPageProperty('FULLWIDTH', 'Y');
$APPLICATION->SetPageProperty('MENU', 'N');

require $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/page_blocks/ralmaster_contacts.php';

require $_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php';
