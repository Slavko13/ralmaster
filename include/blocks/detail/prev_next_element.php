<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>

<? //options from \Aspro\Functions\CAsproAllcorp2::showBlockHtml?>
<?
$arOptions = $arConfig['PARAMS'];
$arParams = $arOptions['PARAMS'];
$arNextElement = $arOptions['NEXT_ELEMENT'];
$sectionId = $arNextElement['SECTION_ID'];

$bUseShare = isset($arParams["USE_SHARE"]) && $arParams['USE_SHARE'] === "Y" && $arOptions['ELEMENT'];
?>


<div class="row links-block">
    <div class="row margin0">
        <div class="col-md-6 col-sm-6 share">
            <? if ($bUseShare) : ?>
                <? $APPLICATION->IncludeFile(SITE_DIR . "include/share_buttons.php", array(), array("MODE" => "html", "NAME" => GetMessage('CT_BCE_CATALOG_SOC_BUTTON'))); ?>
            <? endif; ?>
        </div>
        <div class="col-md-6 col-sm-6">
        </div>
    </div>


    <div class="col-md-12 links">
        <a class="back-url url-block" href="<?= $arOptions['LIST_URL']; ?>"><i class="fa fa-angle-left"></i><span><?= ($arParams["T_PREV_LINK"] ? $arParams["T_PREV_LINK"] : GetMessage('BACK_LINK')); ?></span></a>
        <? if ($arNextElement) : ?>
            <a class="next-url url-block" href="<?= $arNextElement['NEXT_ELEMENT']['DETAIL_PAGE_URL'][$sectionId] ?>"><i class="fa fa-angle-right"></i><span><?= ($arParams["T_NEXT_LINK"] ? $arParams["T_NEXT_LINK"] : GetMessage('NEXT_LINK')); ?></span></a>
        <? endif; ?>
    </div>
</div>