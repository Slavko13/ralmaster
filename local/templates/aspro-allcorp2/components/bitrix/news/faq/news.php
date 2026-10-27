<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?
$arItemFilter = CAllcorp2::GetIBlockAllElementsFilter($arParams);
$itemsCnt = CCache::CIblockElement_GetList(array("CACHE" => array("TAG" => CCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), $arItemFilter, array());
$arElement = CCache::CIblockElement_GetList(array("CACHE" => array("TAG" => CCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]), "MULTI" => "Y")), $arItemFilter, false, false, array('ID', 'NAME','PREVIEW_TEXT'));?>

<?$arSchema = array(
	"@context" => "https://schema.org",
	"@type" => "FAQPage",
	"mainEntity" => []
)?>
<?
foreach ($arElement as $element) {
	$arSchema['mainEntity'][] = array(
		"@type" => "Question",
		"name" => $element['NAME'],
		"acceptedAnswer" => array(
			"@type" => "Answer",
			"text" => $element['PREVIEW_TEXT']
		)
	);
}
?>

<script type="application/ld+json"><?=str_replace("'", "\"", CUtil::PhpToJSObject($arSchema, false, true));?></script>
<?
// rss
if ($arParams['USE_RSS'] !== 'N') {
	CAllcorp2::ShowRSSIcon($arResult['FOLDER'].$arResult['URL_TEMPLATES']['rss']);
}
?>
<?if(!$itemsCnt):?>
	<div class="alert alert-warning"><?=GetMessage("SECTION_EMPTY")?></div>
<?else:?>
	<?CAllcorp2::CheckComponentTemplatePageBlocksParams($arParams, __DIR__);?>
	<?// section elements?>
	<?@include_once('page_blocks/'.$arParams["SECTION_ELEMENTS_TYPE_VIEW"].'.php');?>
	<?if($arParams['SHOW_ASK_QUESTION_BLOCK'] !== 'N'):?>
		<table class="order-block">
			<tr>
				<td class="col-md-9 col-sm-8 col-xs-7 valign">
					<div class="text">
						<?=CAllcorp2::showIconSvg('order colored', SITE_TEMPLATE_PATH.'/images/svg/Order_service_lg.svg');?>
						<?$APPLICATION->IncludeComponent('bitrix:main.include', '', Array('AREA_FILE_SHOW' => 'file', 'PATH' => SITE_DIR.'include/ask_question_faq.php', 'EDIT_TEMPLATE' => ''));?>
					</div>
				</td>
				<td class="col-md-3 col-sm-4 col-xs-5 valign">
					<div class="btns">
						<span class="btn btn-default btn-lg" data-event="jqm" data-param-id="<?=CAllcorp2::getFormID("aspro_allcorp2_question");?>" data-name="question"><span><?=(strlen($arParams['S_ASK_QUESTION']) ? $arParams['S_ASK_QUESTION'] : GetMessage('S_ASK_QUESTION'))?></span></span>
					</div>
				</td>
			</tr>
		</table>
	<?endif;?>
<?endif;?>