<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?
use \Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$arItemFilter = CAllcorp2::GetIBlockAllElementsFilter($arParams);
$arItemSelect = array('ID', 'NAME', 'IBLOCK_ID', 'IBLOCK_SECTION_ID', 'PROPERTY_MAP', 'PROPERTY_SCHEDULE', 'PROPERTY_EMAIL', 'PROPERTY_METRO', 'PROPERTY_PHONE');
$arItems = CCache::CIblockElement_GetList(array("CACHE" => array("TAG" => CCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), $arItemFilter, false, false, $arItemSelect);

$arAllSections = array();
if($arItems)
	$arAllSections = CAllcorp2::GetSections($arItems, $arParams);
?>
<?if($arParams['SHOW_TOP_MAP'] != 'Y'):?>
	<?CAllcorp2::ShowPageType('page_title');?>
	<div class="contacts-page-top">
		<div class="contacts row maxwidth-theme">
			<?$bHasSections = (isset($arAllSections['ALL_SECTIONS']) && $arAllSections['ALL_SECTIONS']);?>
			<?$bHasChildSections = (isset($arAllSections['CHILD_SECTIONS']) && $arAllSections['CHILD_SECTIONS']);?>
			<?if($bHasSections):?>
				<?$selectRegionText = ($arParams['SHOOSE_REGION_TEXT'] ? $arParams['SHOOSE_REGION_TEXT'] : Loc::getMessage( 'CHOISE_ITEM', array( '#ITEM#' => ( $bHasChildSections ? Loc::getMessage('REGION') : Loc::getMessage('CITY') ) ) ) )?>
				<div class="col-md-7">
					<div class="row">
						<div class="col-md-6 col-sm-4">
							<div class="select-outer black">
								<select class="<?=($bHasChildSections ? 'region' : 'city');?>">
									<option value="0" selected><?=$selectRegionText?></option>
									<?foreach($arAllSections['ALL_SECTIONS'] as $arSection):?>
										<option value="<?=$arSection['SECTION']['ID'];?>"><?=$arSection['SECTION']['NAME'];?></option>
									<?endforeach;?>
								</select>
								<i class="fa fa-angle-down"></i>
							</div>
						</div>
						<?if($bHasChildSections):?>
							<div class="col-md-6 col-sm-4">
								<div class="select-outer black">
									<select class="city">
										<option value="0" selected><?=Loc::getMessage('CHOISE_ITEM', array('#ITEM#' => Loc::getMessage('CITY')))?></option>
										<?foreach($arAllSections['CHILD_SECTIONS'] as $arSection):?>
											<option style="display:none;" value="<?=$arSection['ID'];?>" data-parent_section="<?=$arSection['IBLOCK_SECTION_ID'];?>"><?=$arSection['NAME'];?></option>
										<?endforeach;?>
									</select>
									<i class="fa fa-angle-down"></i>
								</div>
							</div>
						<?endif;?>
					</div>
				</div>
			<?endif;?>
			<div class="col-md-<?=($bHasSections ? 5 : 12);?>">
				<div class="row">
					<div class="col-md-6">
						<?CAllcorp2::showContactPhones();?>
					</div>
					<div class="col-md-6 b-block">
						<button data-event="jqm" data-param-id="<?=CAllcorp2::getFormID("aspro_allcorp2_question");?>" data-name="order_services" class="btn btn-default btn-transparent-bg btn-block"><span><?=Loc::getMessage('S_ASK_QUESTION');?></span></button>
					</div>
				</div>
			</div>
		</div>
	</div>
<?endif;?>
<div class="ajax_items">
	<?if((isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest") || (strtolower($_REQUEST['ajax']) == 'y')){
		$APPLICATION->RestartBuffer();?>
	<?}?>
	<?if($arItems):?>
		<?CAllcorp2::CheckComponentTemplatePageBlocksParams($arParams, __DIR__);?>
		<?@include_once('page_blocks/'.$arParams["SECTIONS_TYPE_VIEW"].'.php');?>
		<?CAllcorp2::checkRestartBuffer();?>
	<?endif;?>
</div>