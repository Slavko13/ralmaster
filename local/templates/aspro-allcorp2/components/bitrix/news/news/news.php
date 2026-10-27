<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?// intro text?>
<?ob_start();?>
<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "page",
		"AREA_FILE_SUFFIX" => "inc",
		"EDIT_TEMPLATE" => ""
	)
);?>
<?$html = ob_get_contents();
ob_end_clean();?>
<?if($html):?>
	<div class="text_before_items">
		<?$APPLICATION->IncludeComponent(
			"bitrix:main.include",
			"",
			Array(
				"AREA_FILE_SHOW" => "page",
				"AREA_FILE_SUFFIX" => "inc",
				"EDIT_TEMPLATE" => ""
			)
		);?>
	</div>
<?endif;?>
<?
$arItemFilter = CAllcorp2::GetIBlockAllElementsFilter($arParams);

if($arParams['CACHE_GROUPS'] == 'Y')
{
	$arItemFilter['CHECK_PERMISSIONS'] = 'Y';
	$arItemFilter['GROUPS'] = $GLOBALS["USER"]->GetGroups();
}

$itemsCnt = CCache::CIblockElement_GetList(array("CACHE" => array("TAG" => CCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), $arItemFilter, array());?>

<?if(!$itemsCnt):?>
	<div class="alert alert-warning"><?=GetMessage("SECTION_EMPTY")?></div>
<?else:?>
	<?CAllcorp2::CheckComponentTemplatePageBlocksParams($arParams, __DIR__);?>
	<?// rss
	if($arParams['USE_RSS'] !== 'N')
		CAllcorp2::ShowRSSIcon($arResult['FOLDER'].$arResult['URL_TEMPLATES']['rss']);
	?>

	<?$arItems = CCache::CIBLockElement_GetList(array('SORT' => 'ASC', 'NAME' => 'ASC', 'CACHE' => array('TAG' => CCache::GetIBlockCacheTag($arParams['IBLOCK_ID']))), array('IBLOCK_ID' => $arParams['IBLOCK_ID'], 'ACTIVE' => 'Y'), false, false, array('ID', 'NAME', 'ACTIVE_FROM'));
	$arYears = array();
	if($arItems)
	{
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
			if($arParams['USE_FILTER'] != 'N')
			{
				rsort($arYears);
				$bHasYear = (isset($_GET['year']) && (int)$_GET['year']);
				$year = ($bHasYear ? (int)$_GET['year'] : 0);?>
				<div class="head-block top">
					<div class="bottom_border"></div>
					<div class="item-link <?=($bHasYear ? '' : 'active');?>">
						<?if($bHasYear):?>
							<a class="title btn-inline black" href="<?=$arResult['FOLDER'];?>">
						<?else:?>
							<div class="title">
						<?endif;?>
								<span class="btn-inline black"><?=GetMessage('ALL_TIME');?></span>
						<?if($bHasYear):?>
							</a>
						<?else:?>
							</div>
						<?endif;?>
					</div>
					<?foreach($arYears as $value):
						$bSelected = ($bHasYear && $value == $year);?>
						<div class="item-link <?=($bSelected ? 'active' : '');?>">
							<?if($bSelected):?>
								<div class="title btn-inline black">
							<?else:?>
								<a class="title btn-inline black" href="<?=$APPLICATION->GetCurPageParam('year='.$value, array('year'));?>">
							<?endif;?>
									<span class="btn-inline black"><?=$value;?></span>
							<?if($bSelected):?>
								</div>
							<?else:?>
								</a>
							<?endif;?>
						</div>
					<?endforeach;?>
				</div>
				<?
				if($bHasYear) 
				{
					$GLOBALS[$arParams["FILTER_NAME"]] = array(
						">=DATE_ACTIVE_FROM" => ConvertDateTime("01.01.".$year, FORMAT_DATE,''),
						"<DATE_ACTIVE_FROM" => ConvertDateTime("01.01.".($year+1), FORMAT_DATE,''),
					);
				}?>
			<?}
		}
	}?>

	<?global $arTheme, $isMenu;?>
	<?if(!$isMenu &&  (!isset($arParams["SHOW_FORM_QUESTION_LIST"]) || $arParams["SHOW_FORM_QUESTION_LIST"]!="N")):?>
		<div class="sub_container fixed_wrapper">
		<div class="row">
			<div class="col-md-9">
	<?endif;?>

	<?// section elements?>
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

	<?if(!$isMenu &&  (!isset($arParams["SHOW_FORM_QUESTION_LIST"]) || $arParams["SHOW_FORM_QUESTION_LIST"]!="N")):?>
			</div>
			<div class="col-md-3  with-padding-left hidden-xs hidden-sm">
				<div class="fixed_block_fix"></div>
				<div class="ask_a_question_wrapper">
					<?=$html;?>
				</div>
			</div>
		</div>
		</div>
	<?else:?>
		<?$this->SetViewTarget('under_sidebar_content');?>
        <? if(!isset($arParams["SHOW_FORM_QUESTION_LIST"]) || $arParams["SHOW_FORM_QUESTION_LIST"]!="N") : ?>
			<?=$html;?>
        <? endif ?>
		<?$this->EndViewTarget();?>
	<?endif;?>
<?endif;?>