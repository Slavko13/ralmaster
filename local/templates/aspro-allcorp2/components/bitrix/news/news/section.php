<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?
$arSectionFilter = CAllcorp2::GetCurrentSectionFilter($arResult["VARIABLES"], $arParams);

if($arParams['CACHE_GROUPS'] == 'Y')
{
	$arSectionFilter['CHECK_PERMISSIONS'] = 'Y';
	$arSectionFilter['GROUPS'] = $GLOBALS["USER"]->GetGroups();
}

$title_news = GetMessage('CURRENT_NEWS', array('#YEAR#' => $arResult['VARIABLES']['YEAR']));
?>

<?$arItems = CCache::CIBLockElement_GetList(array('SORT' => 'ASC', 'NAME' => 'ASC', 'CACHE' => array('TAG' => CCache::GetIBlockCacheTag($arParams['IBLOCK_ID']))), array('IBLOCK_ID' => $arParams['IBLOCK_ID'], 'ACTIVE' => 'Y'), false, false, array('ID', 'NAME', 'ACTIVE_FROM'));
$arYears = array();
if($arItems)
{
	$bFoundSection = false;
	foreach($arItems as $arItem)
	{
		if($arItem['ACTIVE_FROM'])
		{
			if($arDateTime = ParseDateTime($arItem['ACTIVE_FROM'], FORMAT_DATETIME))
				$arYears[$arDateTime['YYYY']] = $arDateTime['YYYY'];
		}
	}
	if($arYears)
	{
		if(((isset($arResult['VARIABLES']['YEAR'])) && $arResult['VARIABLES']['YEAR']) && $arYears[$arResult['VARIABLES']['YEAR']])
			$bFoundSection = true;

		if($arParams['USE_FILTER'] != 'N')
		{
			rsort($arYears);?>
			<div class="head-block top">
				<div class="bottom_border"></div>
				<div class="item-link">
					<div class="title">
						<a class="btn-inline black" href="<?=$arResult['FOLDER'];?>"><?=GetMessage('ALL_TIME');?></a>
					</div>
				</div>
				<?foreach($arYears as $year):?>
					<?$bSelected = ($year == $arResult['VARIABLES']['YEAR']);?>
					<div class="item-link <?=($bSelected ? 'active' : '');?>">
						<?if($bSelected):?>
							<div class="title btn-inline black">
						<?else:?>
							<a class="btn-inline black" href="<?=$arResult['FOLDER'].str_replace('#YEAR#', $year, $arResult['URL_TEMPLATES']['section']);?>">
						<?endif;?>
							<span class="btn-inline black"><?=$year;?></span>
						<?if($bSelected):?>
							</div>
						<?else:?>
							</a>
						<?endif;?>
					</div>
				<?endforeach;?>
			</div>
		<?}
	}
}?>

<?if(!$bFoundSection && $arParams['SET_STATUS_404'] !== 'Y'):?>
	<div class="alert alert-warning"><?=GetMessage("SECTION_NOTFOUND")?></div>
<?elseif(!$bFoundSection && $arParams['SET_STATUS_404'] === 'Y'):?>
	<?CAllcorp2::goto404Page();?>
<?else:?>
	<?CAllcorp2::CheckComponentTemplatePageBlocksParams($arParams, __DIR__);?>
	<?// rss
	if($arParams['USE_RSS'] !== 'N')
		CAllcorp2::ShowRSSIcon(CComponentEngine::makePathFromTemplate($arResult['FOLDER'].$arResult['URL_TEMPLATES']['rss_section'], array_map('urlencode', $arResult['VARIABLES'])));
	?>

	<?global $arTheme, $isMenu;?>

	<?if(!$isMenu):?>
		<div class="sub_container fixed_wrapper">
		<div class="row">
			<div class="col-md-9">
	<?endif;?>

	<?// section elements?>
	<?$GLOBALS[$arParams["FILTER_NAME"]] = array(
		">DATE_ACTIVE_FROM" => ConvertDateTime("31.12.".($year-1), FORMAT_DATETIME),
		"<=DATE_ACTIVE_FROM" => ConvertDateTime("31.12.".$year, FORMAT_DATETIME),
	);?>
	
	<?$sViewElementsTemplate = ($arParams["SECTION_ELEMENTS_TYPE_VIEW"] == "FROM_MODULE" ? $arTheme["NEWS_PAGE"]["VALUE"] : $arParams["SECTION_ELEMENTS_TYPE_VIEW"]);?>
	<?@include_once('page_blocks/'.$sViewElementsTemplate.'.php');?>

	<?// ask block?>
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
				<span><span class="btn btn-default btn-lg btn-transparent-bg animate-load" data-event="jqm" data-param-id="<?=CAllcorp2::getFormID("aspro_allcorp2_question");?>" data-name="question"><span><?=(strlen($arParams['S_ASK_QUESTION']) ? $arParams['S_ASK_QUESTION'] : GetMessage('S_ASK_QUESTION'))?></span></span></span>
			</div>
		</div>
	<?$html = ob_get_contents();?>
	<?ob_end_clean();?>

	<?if(!$isMenu):?>
			</div>
			<div class="col-md-3 with-padding-left hidden-xs hidden-sm">
				<div class="fixed_block_fix"></div>
				<div class="ask_a_question_wrapper">
					<?=$html;?>
				</div>
			</div>
		</div>
		</div>
	<?else:?>
		<?$this->SetViewTarget('under_sidebar_content');?>
			<?=$html;?>
		<?$this->EndViewTarget();?>
	<?endif;?>

	<?$APPLICATION->SetTitle($title_news);?>
	<?$APPLICATION->AddChainItem($title_news);?>
<?endif;?>