<?
define("NO_KEEP_STATISTIC", true);
define('NO_AGENT_CHECK', true);
define('STATISTIC_SKIP_ACTIVITY_CHECK', true);
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?
header('Cache-Control: no-store, private');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !check_bitrix_sessid()) {
	http_response_code(403);
	exit;
}

if((isset($_POST['NAME']) && $_POST['NAME']) && (isset($_POST['VALUE']) && $_POST['VALUE']))
{
	$name = (string) $_POST['NAME'];
	$value = (string) $_POST['VALUE'];
	if (strlen($name) > 100 || strlen($value) > 1000 || preg_match('/^[A-Z0-9_]+$/D', $name) !== 1) {
		http_response_code(400);
		exit;
	}
	if(\Bitrix\Main\Loader::includeModule('aspro.allcorp2'))
	{
		$_SESSION['THEME'][SITE_ID][$name] = $value;
	}
}
?>
