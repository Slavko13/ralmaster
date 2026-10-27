<?if( !defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true ) die();?>
<?$this->setFrameMode(true);?>
<?// intro text?>
<div class="block_before_items">
	<div class="text_before_items">
		<?$APPLICATION->IncludeComponent(
			"bitrix:main.include",
			"",
			Array(
				"AREA_FILE_SHOW" => "file",
				"PATH" => SITE_DIR."include/reviews.php",
				"EDIT_TEMPLATE" => ""
			)
		);?>
	</div>
	<?if($arParams['SHOW_FORM'] == 'Y'):?>
	<?
		$additionalParams = array("iblock_type" => $arParams["IBLOCK_TYPE"], "set_name" => "Y", "deactivate" => $arParams["FORM_CREATE_DEACTIVATED"] ?? "");
		$additionalFormParams = base64_encode (serialize($additionalParams));
	?>
	<div class="add_review">
		<div class="btns">
			<?
			$id = CAllcorp2::getFormID("aspro_allcorp2_feedback");
			$id = $id ? $id : $arParams['IBLOCK_ID'];
			?>
		
			<span class="btn btn-default btn-lg animate-load" data-event="jqm" data-param-additional="<?=$additionalFormParams?>" data-param-id="<?=$id?>" data-name="feedback"><span><?=(isset($arParams['S_FEEDBACK']) && strlen($arParams['S_FEEDBACK']) ? $arParams['S_FEEDBACK'] : GetMessage('S_FEEDBACK'))?></span></span>
		</div>
	</div>
<?endif;?>
</div>
<?
$arItemFilter = CAllcorp2::GetIBlockAllElementsFilter($arParams);
$itemsCnt = CCache::CIblockElement_GetList(array("CACHE" => array("TAG" => CCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), $arItemFilter, array());
?>
<?if(!$itemsCnt):?>
	<div class="alert alert-warning"><?=GetMessage("SECTION_EMPTY")?></div>
<?else:?>
	<?CAllcorp2::CheckComponentTemplatePageBlocksParams($arParams, __DIR__);?>
	<?// section elements?>
	<?@include_once('page_blocks/'.$arParams["SECTION_ELEMENTS_TYPE_VIEW"].'.php');?>
<?endif;?>