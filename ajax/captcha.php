<?define("STATISTIC_SKIP_ACTIVITY_CHECK", "true");?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
header('Cache-Control: no-store, private');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !check_bitrix_sessid()) {
	http_response_code(403);
	exit;
}

$window = 300;
$limit = 20;
$now = time();
$refreshes = $_SESSION['RALMASTER_CAPTCHA_REFRESH'] ?? [];
$refreshes = array_values(array_filter($refreshes, static fn($timestamp) => is_int($timestamp) && $timestamp > $now - $window));
if (count($refreshes) >= $limit) {
	http_response_code(429);
	header('Retry-After: '.max(1, $window - ($now - min($refreshes))));
	exit;
}
$refreshes[] = $now;
$_SESSION['RALMASTER_CAPTCHA_REFRESH'] = $refreshes;

include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php");
$cpt = new CCaptcha();
$captchaSid = (string) ($_POST['captcha_sid'] ?? '');
if (preg_match('/^[a-f0-9]{32}$/Di', $captchaSid) === 1) {
	$cpt->Delete($captchaSid);
}
echo htmlspecialchars($APPLICATION->CaptchaGetCode());?>
