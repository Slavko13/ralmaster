<?$bAjaxMode = (isset($_POST["AJAX_POST"]) && $_POST["AJAX_POST"] == "Y");
use CAllcorp2 as Solution;
if($bAjaxMode)
{
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
	if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !check_bitrix_sessid()) {
		http_response_code(403);
		header('Cache-Control: no-store, private');
		exit;
	}

	$rawAjaxParams = $_POST['AJAX_PARAMS'] ?? '';
	$rawGlobalFilter = $_POST['GLOBAL_FILTER'] ?? '';
	if (!is_string($rawAjaxParams) || !is_string($rawGlobalFilter)
		|| strlen($rawAjaxParams) > 65535 || strlen($rawGlobalFilter) > 65535) {
		http_response_code(413);
		exit;
	}

	$now = time();
	$_SESSION['RALMASTER_CATALOG_AJAX'] = array_values(array_filter(
		(array) ($_SESSION['RALMASTER_CATALOG_AJAX'] ?? []),
		static fn ($timestamp) => is_int($timestamp) && $timestamp > $now - 60
	));
	if (count($_SESSION['RALMASTER_CATALOG_AJAX']) >= 30) {
		http_response_code(429);
		header('Retry-After: 60');
		exit;
	}
	$_SESSION['RALMASTER_CATALOG_AJAX'][] = $now;

	global $APPLICATION;
	\Bitrix\Main\Loader::includeModule("aspro.allcorp2");
}?>
<?if((isset($arParams["IBLOCK_ID"]) && $arParams["IBLOCK_ID"]) || $bAjaxMode):?>
	<?
	$arIncludeParams = ($bAjaxMode ? $rawAjaxParams : $arParamsTmp);
	$arGlobalFilter = ($bAjaxMode ? Solution::unserialize(urldecode($rawGlobalFilter)) : array());
	$arComponentParams = Solution::unserialize(urldecode($arIncludeParams));
	if (!is_array($arGlobalFilter) || !is_array($arComponentParams)) {
		http_response_code(400);
		exit;
	}

	$validatePayload = static function ($value, int $depth = 0, int &$items = 0) use (&$validatePayload): bool {
		if ($depth > 6 || ++$items > 500) {
			return false;
		}
		if (is_array($value)) {
			foreach ($value as $key => $item) {
				if ((!is_int($key) && (!is_string($key) || strlen($key) > 128))
					|| !$validatePayload($item, $depth + 1, $items)) {
					return false;
				}
			}
			return true;
		}
		return is_null($value) || is_bool($value) || is_int($value) || is_float($value)
			|| (is_string($value) && strlen($value) <= 4096);
	};
	$itemCount = 0;
	if (!$validatePayload($arGlobalFilter, 0, $itemCount)) {
		http_response_code(400);
		exit;
	}
	$itemCount = 0;
	if (!$validatePayload($arComponentParams, 0, $itemCount)) {
		http_response_code(400);
		exit;
	}

	$filterName = (string) ($arComponentParams['FILTER_NAME'] ?? 'arFrontItemsFilter');
	if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]{0,63}$/', $filterName)) {
		$filterName = 'arFrontItemsFilter';
	}
	$arComponentParams['FILTER_NAME'] = $filterName;
	?>
	
	<?
	if($bAjaxMode && (is_array($arGlobalFilter) && $arGlobalFilter))
		$GLOBALS[$arComponentParams["FILTER_NAME"]] = $arGlobalFilter;
	$GLOBALS[$arComponentParams["FILTER_NAME"]]["!PROPERTY_SHOW_ON_INDEX_PAGE"] = false;
	$template = (isset($arComponentParams["ELEMENTS_TABLE_TYPE_VIEW"]) ? ($arComponentParams["ELEMENTS_TABLE_TYPE_VIEW"] == "FROM_MODULE" ? Solution::GetFrontParametrValue("ELEMENTS_TABLE_TYPE_VIEW") : $arComponentParams["ELEMENTS_TABLE_TYPE_VIEW"]) : "front-catalog-slider");
	if(isset($arComponentParams["COMPONENT_TEMPLATE"]))
	{
		if(isset($arComponentParams["ELEMENTS_TABLE_TYPE_VIEW"]))
		{
			if($arComponentParams["COMPONENT_TEMPLATE"] == "slider")
			{
				$template = str_replace('catalog_table', 'front-catalog-slider', $template);
			}
		}
	}
	if (!is_string($template) || !preg_match('/^[A-Za-z0-9_-]{1,64}$/', $template)) {
		$template = 'front-catalog-slider';
	}
?>
	
	<?$APPLICATION->IncludeComponent(
		"bitrix:news.list", 
		$template,
		$arComponentParams,
		false, array('HIDE_ICONS' => 'Y')
	);?>
	
<?endif;?>
