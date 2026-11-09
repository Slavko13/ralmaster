<?define("STATISTIC_SKIP_ACTIVITY_CHECK", "true");?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
header('Content-Type: text/plain; charset=UTF-8');
header('Cache-Control: no-store, private');
http_response_code(410);
echo 'false';?>
