<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>

<?$this->setFrameMode(true);?>	
<?use \Bitrix\Main\Localization\Loc;?>

<?
/*set array props for component_epilog*/
$templateData = array(
	'LINK_SALE' => (array)$arResult['DISPLAY_PROPERTIES']['LINK_SALE']['VALUE'],
	'LINK_TIZERS' => (array)$arResult['DISPLAY_PROPERTIES']['LINK_TIZERS']['VALUE'],
	'LINK_NEWS' => (array)$arResult['DISPLAY_PROPERTIES']['LINK_NEWS']['VALUE'],
	'DOCUMENTS' => (array)$arResult['DISPLAY_PROPERTIES']['DOCUMENTS']['VALUE'],
	'LINK_FAQ' => (array)$arResult['DISPLAY_PROPERTIES']['LINK_FAQ']['VALUE'],
	'LINK_PROJECTS' => (array)$arResult['DISPLAY_PROPERTIES']['LINK_PROJECTS']['VALUE'],
	'LINK_SERVICES' => (array)$arResult['DISPLAY_PROPERTIES']['LINK_SERVICES']['VALUE'],
	'LINK_GOODS' => (array)$arResult['DISPLAY_PROPERTIES']['LINK_GOODS']['VALUE'],
	'LINK_STAFF' => (array)$arResult['DISPLAY_PROPERTIES']['LINK_STAFF']['VALUE'],
	'LINK_REVIEWS' => (array)$arResult['DISPLAY_PROPERTIES']['LINK_REVIEWS']['VALUE'],
	'LINK_STUDY' => (array)$arResult['DISPLAY_PROPERTIES']['LINK_STUDY']['VALUE'],
	'LINK_ARTICLES' => (array)$arResult['DISPLAY_PROPERTIES']['LINK_ARTICLES']['VALUE'],
	'FORM_QUESTION' => ($arResult['DISPLAY_PROPERTIES']['FORM_QUESTION']['VALUE_XML_ID'] == 'YES'),
	'GALLERY_BIG' => (array)$arResult['GALLERY_BIG'],
	'CHARACTERISTICS' => (array)$arResult['CHARACTERISTICS'],
	'DETAIL_TEXT' => $arResult['FIELDS']['DETAIL_TEXT'],
	'PREVIEW_TEXT' => $arResult['FIELDS']['PREVIEW_TEXT'],
	'COMPANY' => (array)$arResult['COMPANY'],
);

if(isset($arResult['PROPERTIES']['BNR_TOP']) && $arResult['PROPERTIES']['BNR_TOP']['VALUE_XML_ID'] == 'YES')
	$templateData['SECTION_BNR_CONTENT'] = true;
/**/
?>

<?// shot top banners start?>
<?$bShowTopBanner = (isset($templateData['SECTION_BNR_CONTENT'] ) && $templateData['SECTION_BNR_CONTENT'] == true);?>
<?if($bShowTopBanner):?>
	<?$this->SetViewTarget("section_bnr_content");?>
		<?CAllcorp2::ShowTopDetailBanner($arResult, $arParams);?>
	<?$this->EndViewTarget();?>
<?endif;?>
<?// shot top banners end?>

<?// element name?>
<?if($arParams['DISPLAY_NAME'] != 'N' && strlen($arResult['NAME'])):?>
	<h2><?=$arResult['NAME']?></h2>
<?endif;?>

<?$bWideImg = false;
$bImg = false;
$bPreviewText = (strlen($arResult['FIELDS']['PREVIEW_TEXT']));
?>

<?// single detail image?>
<?if($arResult['FIELDS']['DETAIL_PICTURE']):?>
	<?$bImg = true;?>
	<?
	$atrTitle = (strlen($arResult['DETAIL_PICTURE']['DESCRIPTION']) ? $arResult['DETAIL_PICTURE']['DESCRIPTION'] : (strlen($arResult['DETAIL_PICTURE']['TITLE']) ? $arResult['DETAIL_PICTURE']['TITLE'] : $arResult['NAME']));
	$atrAlt = (strlen($arResult['DETAIL_PICTURE']['DESCRIPTION']) ? $arResult['DETAIL_PICTURE']['DESCRIPTION'] : (strlen($arResult['DETAIL_PICTURE']['ALT']) ? $arResult['DETAIL_PICTURE']['ALT'] : $arResult['NAME']));
	?>
	<?if($arResult['PROPERTIES']['PHOTOPOS']['VALUE_XML_ID'] == 'LEFT'):?>
		<?if(!$bShowTopBanner):?>
			<div class="detailimage image-left"><a href="<?=$arResult['DETAIL_PICTURE']['SRC']?>" class="fancybox" title="<?=$atrTitle?>"><img src="<?=$arResult['DETAIL_PICTURE']['SRC']?>" class="img-responsive" title="<?=$atrTitle?>" alt="<?=$atrAlt?>" /></a></div>
		<?endif;?>
	<?elseif($arResult['PROPERTIES']['PHOTOPOS']['VALUE_XML_ID'] == 'RIGHT'):?>
		<?if(!$bShowTopBanner):?>
			<div class="detailimage image-right"><a href="<?=$arResult['DETAIL_PICTURE']['SRC']?>" class="fancybox" title="<?=$atrTitle?>"><img src="<?=$arResult['DETAIL_PICTURE']['SRC']?>" class="img-responsive" title="<?=$atrTitle?>" alt="<?=$atrAlt?>" /></a></div>
		<?endif;?>
	<?elseif($arResult['PROPERTIES']['PHOTOPOS']['VALUE_XML_ID'] == 'TOP'):?>
		<?if(!$bShowTopBanner):?>
			<?$this->SetViewTarget('top_section_filter_content');?>
			<div class="detailimage image-head"><img src="<?=$arResult['DETAIL_PICTURE']['SRC']?>" class="img-responsive" title="<?=$atrTitle?>" alt="<?=$atrAlt?>"/></div>
			<?$this->EndViewTarget();?>
		<?endif;?>
	<?else:?>
		<div class="detailimage image-wide<?=(($arResult['DISPLAY_PROPERTIES']['FORM_ORDER']['VALUE_XML_ID'] != 'YES') && (!$bPreviewText || ($bShowTopBanner && $bPreviewText)) ? ' np' : '');?>"><a href="<?=$arResult['DETAIL_PICTURE']['SRC']?>" class="fancybox" title="<?=$atrTitle?>"><img src="<?=$arResult['DETAIL_PICTURE']['SRC']?>" class="img-responsive" title="<?=$atrTitle?>" alt="<?=$atrAlt?>" /></a></div>
		<?$bWideImg = true;?>
	<?endif;?>
<?endif;?>

<?if(!$bShowTopBanner && strlen($arResult['FIELDS']['PREVIEW_TEXT'])):?>
	<?$bPreviewText = true?>
	<div class="introtext <?=($arResult['DISPLAY_PROPERTIES']['FORM_ORDER']['VALUE_XML_ID'] == 'YES' ? 'order' : 'norder');?><?=($bWideImg ? ' wides' : '');?>">
		<?if($arResult['PREVIEW_TEXT_TYPE'] == 'text'):?>
			<p><?=$arResult['FIELDS']['PREVIEW_TEXT'];?></p>
		<?else:?>
			<?=$arResult['FIELDS']['PREVIEW_TEXT'];?>
		<?endif;?>
	</div>
<?endif;?>

<?// order block?>
<?if($arResult['DISPLAY_PROPERTIES']['FORM_ORDER']['VALUE_XML_ID'] == 'YES'):?>
	<?if($bWideImg):?>
		<div class="wide-wrapper">
	<?endif;?>
	<table class="order-block<?=($bWideImg ? ' wides' : '');?><?=((!$bImg || $bShowTopBanner) && (!$bPreviewText || ($bShowTopBanner && $bPreviewText)) ? ' not' : '');?> news">
		<tr>
			<td class="col-md-9 col-sm-8 col-xs-7 valign">
				<div class="text">
					<?=CAllcorp2::showIconSvg('order colored', SITE_TEMPLATE_PATH.'/images/svg/Order_service_lg.svg');?>
					<?=\Aspro\Functions\CAsproAllcorp2::showPrice($arResult);?>
					<div class="inner-text">
						<?$APPLICATION->IncludeComponent(
							'bitrix:main.include',
							'',
							array(
								"AREA_FILE_SHOW" => "page",
								"AREA_FILE_SUFFIX" => "ask_services",
								"EDIT_TEMPLATE" => ""
							)
						);?>
					</div>
				</div>
			</td>
			<td class="col-md-3 col-sm-4 col-xs-5 valign">
				<div class="btns">
					<span class="btn btn-default btn-lg animate-load" data-event="jqm" data-param-id="<?=($arParams["FORM_ID_ORDER_SERVISE"] ? $arParams["FORM_ID_ORDER_SERVISE"] : CAllcorp2::getFormID("aspro_allcorp2_order_services"));?>" data-name="order_services" data-autoload-service="<?=CAllcorp2::formatJsName($arResult['NAME'])?>" data-autoload-study="<?=CAllcorp2::formatJsName($arResult['NAME'])?>" data-autoload-project="<?=CAllcorp2::formatJsName($arResult['NAME'])?>"><span><?=(strlen($arParams['S_ORDER_SERVISE']) ? $arParams['S_ORDER_SERVISE'] : Loc::getMessage('S_ORDER_SERVISE'))?></span></span>
				</div>
			</td>
		</tr>
	</table>
	<?if($bWideImg):?>
		</div>
	<?endif;?>
<?endif;?>

<?// date active from or dates period active?>
<?if(strlen($arResult['DISPLAY_PROPERTIES']['PERIOD']['VALUE']) || ($arResult['DISPLAY_ACTIVE_FROM'] && in_array('DATE_ACTIVE_FROM', $arParams['FIELD_CODE']))):?>
	<div class="period">
		<?if(strlen($arResult['DISPLAY_PROPERTIES']['PERIOD']['VALUE'])):?>
			<span class="date"><?=$arResult['DISPLAY_PROPERTIES']['PERIOD']['VALUE']?></span>
		<?else:?>
			<span class="date"><?=$arResult['DISPLAY_ACTIVE_FROM']?></span>
		<?endif;?>
	</div>
<?endif;?>

<?if(strlen($arResult['FIELDS']['DETAIL_TEXT'])):?>
	<div class="content">
		<?// element detail text?>
		<?if(strlen($arResult['FIELDS']['DETAIL_TEXT'])):?>
			<?if($arResult['DETAIL_TEXT_TYPE'] == 'text'):?>
				<p><?=$arResult['FIELDS']['DETAIL_TEXT'];?></p>
			<?else:?>
				<?=$arResult['FIELDS']['DETAIL_TEXT'];?>
			<?endif;?>
		<?endif;?>
	</div>
<?endif;?>