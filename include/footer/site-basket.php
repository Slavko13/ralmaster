<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$isMutation = isset($_REQUEST['itemData']) || isset($_REQUEST['remove']) || isset($_REQUEST['removeAll']);
if ($isMutation && ($_SERVER['REQUEST_METHOD'] !== 'POST' || !check_bitrix_sessid())) {
    http_response_code(403);
    header('Cache-Control: no-store, private');
    exit;
}

$isAjax = $_SERVER['REQUEST_METHOD'] === 'POST'
    && (isset($_POST['itemData']) || (isset($_POST['ajaxPost']) && $_POST['ajaxPost'] === 'Y')); ?>
<? if ($isAjax): ?>
    <? \Bitrix\Main\Loader::includeModule('aspro.allcorp2');

    $arTheme = CAllcorp2::GetFrontParametrsValues(SITE_ID);
    $arBasketItems = CAllcorp2::processBasket();

    $template = strtolower($arTheme["ORDER_BASKET_VIEW"]);
    $bUseBasket = ($arTheme["ORDER_VIEW"] == "Y");
    $bHideBasketPage = (CAllcorp2::IsBasketPage($arTheme["URL_BASKET_SECTION"]) || CAllcorp2::IsOrderPage($arTheme["URL_ORDER_SECTION"]));
    $catalog_path = $arTheme["URL_CATALOG_SECTION"]; ?>
<? else: ?>
    <? $template = strtolower($arTheme["ORDER_VIEW"]["DEPENDENT_PARAMS"]["ORDER_BASKET_VIEW"]["VALUE"]);
    $catalog_path = $arTheme["URL_CATALOG_SECTION"]["VALUE"];
    $bHideBasketPage = (CAllcorp2::IsBasketPage($arTheme["ORDER_VIEW"]["DEPENDENT_PARAMS"]["URL_BASKET_SECTION"]["VALUE"]) || CAllcorp2::IsOrderPage($arTheme["ORDER_VIEW"]["DEPENDENT_PARAMS"]["URL_ORDER_SECTION"]["VALUE"]));
    $bUseBasket = ($arTheme["ORDER_VIEW"]["VALUE"] == "Y"); ?>
    <!-- noindex -->
    <div class="ajax_basket">
<? endif; ?>
<? $checkBasketUrl = "Y"; ?>
<? $APPLICATION->IncludeComponent(
        "aspro:basket.allcorp2",
        $template,
        array(
                "COMPONENT_TEMPLATE" => $template,
                "NO_REDIRECT" => "Y",
                "CHECK_BASKET_URL" => $checkBasketUrl,
                "PATH_TO_CATALOG" => $catalog_path
        ),
        false, array("HIDE_ICONS" => "Y")
); ?>
<? if ($template == "header" || !$bUseBasket || ($bHideBasketPage && $checkBasketUrl == "Y")): ?>
    <div class="fixed_wrapper">
        <? CAllcorp2::showRightDok(); ?>
    </div>
<? endif; ?>
<? if (!$isAjax): ?>
    </div>
    <!-- /noindex -->
<? endif; ?>
