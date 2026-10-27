<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>

<?$this->setFrameMode(true);?>
<?use \Bitrix\Main\Localization\Loc;?>

<?
/*set array props for component_epilog*/
$templateData = array(
	'LINK_SALE' => (array)$arResult['DISPLAY_PROPERTIES']['LINK_SALE']['VALUE'],
	'LINK_NEWS' => (array)$arResult['DISPLAY_PROPERTIES']['LINK_NEWS']['VALUE'],
	'LINK_TIZERS' => (array)$arResult['DISPLAY_PROPERTIES']['LINK_TIZERS']['VALUE'],
	'LINK_TARIF' => (array)$arResult['DISPLAY_PROPERTIES']['LINK_TARIF']['VALUE'],
	'DOCUMENTS' =>(array)$arResult['DISPLAY_PROPERTIES']['DOCUMENTS']['VALUE'],
	'LINK_FAQ' => (array)$arResult['DISPLAY_PROPERTIES']['LINK_FAQ']['VALUE'],
	'LINK_PROJECTS' => (array)$arResult['DISPLAY_PROPERTIES']['LINK_PROJECTS']['VALUE'],
	'LINK_SERVICES' => (array)$arResult['DISPLAY_PROPERTIES']['LINK_SERVICES']['VALUE'],
	'LINK_GOODS' => (array)$arResult['DISPLAY_PROPERTIES']['LINK_GOODS']['VALUE'],
	'LINK_STAFF' => (array)$arResult['DISPLAY_PROPERTIES']['LINK_STAFF']['VALUE'],
	'LINK_PARTNERS' => (array)$arResult['DISPLAY_PROPERTIES']['LINK_PARTNERS']['VALUE'],
	'LINK_REVIEWS' => (array)$arResult['DISPLAY_PROPERTIES']['LINK_REVIEWS']['VALUE'],
	'LINK_STUDY' => (array)$arResult['DISPLAY_PROPERTIES']['LINK_STUDY']['VALUE'],
	'LINK_ARTICLES' => (array)$arResult['DISPLAY_PROPERTIES']['LINK_ARTICLES']['VALUE'],
	'FORM_QUESTION' => ($arResult['DISPLAY_PROPERTIES']['FORM_QUESTION']['VALUE_XML_ID'] == 'YES'),
	'FORM_ORDER' => ($arResult['DISPLAY_PROPERTIES']['FORM_ORDER']['VALUE_XML_ID'] == 'YES'),
	'GALLERY_BIG' => (array)$arResult['GALLERY'],
	'CHARACTERISTICS' => (array)$arResult['CHARACTERISTICS'],
	'DETAIL_TEXT' => $arResult['FIELDS']['DETAIL_TEXT'],
	//'FORM_ORDER' => $arResult['DISPLAY_PROPERTIES']['FORM_ORDER']['VALUE_XML_ID'],
    'PRICE_DATA' => array_filter([
        'PRICE' => $arResult['DISPLAY_PROPERTIES']['PRICE'],
        'PRICEOLD' => $arResult['DISPLAY_PROPERTIES']['PRICEOLD'],
        'PRICE_CURRENCY' => $arResult['PROPERTIES']['PRICE_CURRENCY'],
        'FILTER_PRICE' => $arResult['PROPERTIES']['FILTER_PRICE']
    ]),
);

if(isset($arResult['PROPERTIES']['BNR_TOP']) && $arResult['PROPERTIES']['BNR_TOP']['VALUE_XML_ID'] == 'YES')
	$templateData['SECTION_BNR_CONTENT'] = true;
/**/
?>

<?if($arResult['CATEGORY_ITEM']):?>
	<meta itemprop="category" content="<?=$arResult['CATEGORY_ITEM'];?>" />
<?endif;?>
<?if($arResult['DETAIL_PICTURE']):?>
	<meta itemprop="image" content="<?=$arResult['DETAIL_PICTURE']['SRC'];?>" />
<?endif;?>
<meta itemprop="name" content="<?=$arResult['NAME'];?>" />
<link itemprop="url" href="<?=$arResult['DETAIL_PAGE_URL'];?>" />

<?$bShowForm = false;?>
<?// shot top banners start?>
<?$bShowTopBanner = (isset($templateData['SECTION_BNR_CONTENT'] ) && $templateData['SECTION_BNR_CONTENT'] == true);?>
<?if($bShowTopBanner):?>
	<?$this->SetViewTarget("section_bnr_content");?>
		<?CAllcorp2::ShowTopDetailBanner($arResult, $arParams);?>
	<?$this->EndViewTarget();?>
<?endif;?>
<?// shot top banners end?>

<?// form question?>
<?global $isMenu;?>
<?$bShowFormQuestion = ($arResult['DISPLAY_PROPERTIES']['FORM_QUESTION']['VALUE_XML_ID'] == 'YES');?>
<?if($bShowFormQuestion):?>
	<?ob_start();?>
		<div class="ask_a_question">
			<div class="inner">
				<div class="text-block">
					<?=CAllcorp2::showIconSvg('ask colored', SITE_TEMPLATE_PATH.'/images/svg/Question_lg.svg');?>
					<?$APPLICATION->IncludeComponent(
						'bitrix:main.include',
						'',
						array(
							"AREA_FILE_SHOW" => "page",
							"AREA_FILE_SUFFIX" => "ask",
							"EDIT_TEMPLATE" => ""
						)
					);?>
				</div>
			</div>
			<div class="outer">
				<span><span class="btn btn-default btn-lg btn-transparent-bg animate-load" data-event="jqm" data-param-id="<?=CAllcorp2::getFormID("aspro_allcorp2_question");?>" data-autoload-need_product="<?=CAllcorp2::formatJsName($arResult['NAME'])?>" data-name="question"><span><?=(strlen($arParams['S_ASK_QUESTION']) ? $arParams['S_ASK_QUESTION'] : Loc::getMessage('S_ASK_QUESTION'))?></span></span></span>
			</div>
		</div>
	<?$sFormQuestion = ob_get_contents();
	ob_end_clean();?>
	<?if($isMenu):?>
		<?$this->SetViewTarget('under_sidebar_content');?>
			<?=$sFormQuestion;?>
		<?$this->EndViewTarget();?>
	<?else:?>
		<div class="row">
			<div class="col-md-9">
	<?endif;?>
<?endif;?>

<?// element name?>
<?if($arParams['DISPLAY_NAME'] != 'N' && strlen($arResult['NAME'])):?>
	<h2><?=$arResult['NAME']?></h2>
	<?$bShowForm = true;?>
<?endif;?>

<?$bWideImg = false;
$bImg = false;
$bPreviewText = (strlen($arResult['FIELDS']['PREVIEW_TEXT']));
?>
<?// single detail image?>
<?if($arResult['FIELDS']['DETAIL_PICTURE']):?>
	<?$bShowForm = true;?>
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
	<?$bShowForm = true;?>
	<div class="introtext <?=($arResult['DISPLAY_PROPERTIES']['FORM_ORDER']['VALUE_XML_ID'] == 'YES' ? 'order' : 'norder');?><?=($bWideImg ? ' wides' : '');?>">
		<?if($arResult['PREVIEW_TEXT_TYPE'] == 'text'):?>
			<p><?=$arResult['FIELDS']['PREVIEW_TEXT'];?></p>
		<?else:?>
			<?=$arResult['FIELDS']['PREVIEW_TEXT'];?>
		<?endif;?>
	</div>
<?endif;?>




<?if($arResult['COMPANY']):?>
	<?$bShowForm = true;?>
	<div class="wraps barnd-block">
		<div class="item-views list list-type-block image_left">
			<?if($arResult['COMPANY']['PROPERTY_SLOGAN_VALUE']):?>
				<div class="slogan"><?=$arResult['COMPANY']['PROPERTY_SLOGAN_VALUE'];?></div>
			<?endif;?>
			<div class="items row">
				<div class="col-md-12">
					<div class="item noborder clearfix">
						<?if($arResult['COMPANY']['IMAGE-BIG']):?>
							<div class="image">
								<a href="<?=$arResult['COMPANY']['DETAIL_PAGE_URL'];?>">
									<img src="<?=$arResult['COMPANY']['IMAGE-BIG']['src'];?>" alt="<?=$arResult['COMPANY']['NAME'];?>" title="<?=$arResult['COMPANY']['NAME'];?>" class="img-responsive">
								</a>
							</div>
						<?endif;?>
						<div class="body-info">
							<?if($arResult['COMPANY']['DETAIL_TEXT']):?>
								<div class="previewtext">
									<?=$arResult['COMPANY']['DETAIL_TEXT'];?>
								</div>
							<?endif;?>
							<?if($arResult['COMPANY']['PROPERTY_SITE_VALUE']):?>
								<div class="properties">
									<div class="inner-wrapper">
										<!-- noindex -->
										<a class="property icon-block site" href="<?=$arResult['COMPANY']['PROPERTY_SITE_VALUE'];?>" target="_blank" rel="nofollow">
											<?=$arResult['COMPANY']['PROPERTY_SITE_VALUE'];?>
										</a>
										<!-- /noindex -->
									</div>
								</div>
							<?endif;?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<hr>
	</div>
<?endif;?>

<?// date active from or dates period active?>
<?if(strlen($arResult['DISPLAY_PROPERTIES']['PERIOD']['VALUE']) || ($arResult['DISPLAY_ACTIVE_FROM'] && in_array('DATE_ACTIVE_FROM', $arParams['FIELD_CODE']))):?>
	<?$bShowForm = true;?>
	<div class="period">
		<?if(strlen($arResult['DISPLAY_PROPERTIES']['PERIOD']['VALUE'])):?>
			<span class="date"><?=$arResult['DISPLAY_PROPERTIES']['PERIOD']['VALUE']?></span>
		<?else:?>
			<span class="date"><?=$arResult['DISPLAY_ACTIVE_FROM']?></span>
		<?endif;?>
	</div>
<?endif;?>

<?if($bShowTopBanner):?>
	<?$bShowForm = false;?>
<?endif;?>

<?// form question?>
<?if($bShowFormQuestion && !$isMenu):?>
		</div>
		<div class="col-md-3 hidden-xs hidden-sm <?=($bShowForm ? 'nform' : '');?>">
			<?if($bShowForm):?>
				<div class="fixed_block_fix"></div>
				<div class="ask_a_question_wrapper">
					<?=$sFormQuestion;?>
				</div>
			<?endif;?>
		</div>
	</div>
<?endif;?>
<?$templateData['ASK_SHOW_FORM'] = $bShowForm;?>
<?$templateData['QUESTION_SHOW_FORM'] = $bShowFormQuestion;?>