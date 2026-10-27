<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
$this->setFrameMode(true);
if($arParams["DISPLAY_PICTURE"] != "N"){
	$picture = ($arResult["FIELDS"]["DETAIL_PICTURE"] ? "DETAIL_PICTURE" : "PREVIEW_PICTURE");
	CAllcorp2::getFieldImageData($arResult, array($picture));
	$arPhoto = $arResult[$picture];
	if($arPhoto){
		$arImgs[] = array(
			'DETAIL' => $arPhoto,
			'PREVIEW' => CFile::ResizeImageGet($arPhoto["ID"], array('width' => 300, 'height' => 300), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true),
			'TITLE' => (strlen($arPhoto['DESCRIPTION']) ? $arPhoto['DESCRIPTION'] : (strlen($arPhoto['TITLE']) ? $arPhoto['TITLE'] : $arResult['NAME'])),
			'ALT' => (strlen($arPhoto['DESCRIPTION']) ? $arPhoto['DESCRIPTION'] : (strlen($arPhoto['ALT']) ? $arPhoto['ALT'] : $arResult['NAME'])),
		);
	}
}

/*set array props for component_epilog*/
$templateData = array(
	'DOCUMENTS' => (array)$arResult['DISPLAY_PROPERTIES']['DOCUMENTS']['VALUE'],
	'LINK_PROJECTS' => (array)$arResult['DISPLAY_PROPERTIES']['LINK_PROJECTS']['VALUE'],
	'LINK_SERVICES' => (array)$arResult['DISPLAY_PROPERTIES']['LINK_SERVICES']['VALUE'],
	'LINK_GOODS' => (array)$arResult['DISPLAY_PROPERTIES']['LINK_GOODS']['VALUE'],
	'LINK_REVIEWS' => (array)$arResult['DISPLAY_PROPERTIES']['LINK_REVIEWS']['VALUE'],
);
?>
<div class="detail <?=($templateName = $component->{"__parent"}->{"__template"}->{"__name"})?>">
	<article>
		<?// images?>
		<?if($arImgs):?>
			<div class="detailimage">
				<?if($arImgs):?>
					<img src="<?=$arImgs[0]["DETAIL"]["SRC"]?>" title="<?=$arImgs[0]["TITLE"]?>" alt="<?=$arImgs[0]["ALT"]?>" class="img-responsive" />
				<?endif;?>
			</div>
		<?endif;?>
		
		<?// date active from or dates period active?>
		<?if(strlen($arResult["DISPLAY_PROPERTIES"]["PERIOD"]["VALUE"]) || ($arResult["DISPLAY_ACTIVE_FROM"] && in_array("DATE_ACTIVE_FROM", $arParams["FIELD_CODE"]))):?>
			<div class="period">
				<?if(strlen($arResult["DISPLAY_PROPERTIES"]["PERIOD"]["VALUE"])):?>
					<span class="date"><?=$arResult["DISPLAY_PROPERTIES"]["PERIOD"]["VALUE"]?></span>
				<?else:?>
					<span class="date"><?=$arResult["DISPLAY_ACTIVE_FROM"]?></span>
				<?endif;?>
			</div>
		<?endif;?>
		
		<div class="post-content">
			<?if($arParams["DISPLAY_NAME"] != "N" && strlen($arResult["NAME"])):?>
				<h2><?=$arResult["NAME"]?></h2>
			<?endif;?>
			<div class="content">
				<?// display properties?>
				<?if($arResult["DISPLAY_PROPERTIES_FORMATED"]):?>
					<div class="properties">
						<?if($arResult["DISPLAY_PROPERTIES_FORMATED"]["POST"]):?>
							<div class="inner-wrapper post">
								<div class="property">
									<?=$arResult["DISPLAY_PROPERTIES_FORMATED"]["POST"]['NAME']?>:&nbsp;<span class="vals"><?=$arResult["DISPLAY_PROPERTIES_FORMATED"]["POST"]['DISPLAY_VALUE']?></span>
								</div>
							</div>
						<?endif;?>
						<?foreach($arResult["DISPLAY_PROPERTIES_FORMATED"] as $PCODE => $arProperty):
							if($PCODE == 'POST')
								continue;
							?>
							<?$bIconBlock = ($PCODE == 'EMAIL' || $PCODE == 'PHONE' || $PCODE == 'SITE');?>
							<div class="inner-wrapper <?=strtolower($PCODE);?>">
								<div class="property <?=($bIconBlock ? "icon-block" : "");?> <?=strtolower($PCODE);?>">
									<?if(!$bIconBlock):?>
										<?=$arProperty['NAME']?>:&nbsp;
									<?endif;?>
									
									<?if($PCODE == "SITE"):?>
										<!--noindex-->
										<a href="<?=(strpos($arProperty['VALUE'], 'http') === false ? 'http://' : '').$arProperty['VALUE'];?>" rel="nofollow" target="_blank">
											<?=$arProperty['VALUE'];?>
										</a>
										<!--/noindex-->
									<?elseif($PCODE == "EMAIL"):?>
										<?foreach ((array)$arProperty['DISPLAY_VALUE'] as $value):?>
											<div><a href="mailto:<?=$value?>"><?=$value?></a></div>
										<?endforeach;?>
									<?elseif($PCODE == "PHONE"):?>
										<?foreach ((array)$arProperty['DISPLAY_VALUE'] as $value):?>
											<div><a href="<?= preg_replace('/[^\d+]/', '', $value); ?>"><?= $value; ?></a></div>
										<?endforeach;?>
									<?else:?>
										<?=$val?>
									<?endif;?>
								</div>
							</div>
						<?endforeach;?>
						<?if((isset($arResult['SOCIAL_PROPS']) && $arResult['SOCIAL_PROPS'])):?>
							<div class="bottom-props">
								<!-- noindex -->
									<?foreach($arResult['SOCIAL_PROPS'] as $arProp):?>
										<a href="<?=$arProp['VALUE'];?>" target="_blank" rel="nofollow" class="value <?=strtolower($arProp['CODE']);?>"><?=$arProp['VALUE'];?></a>
									<?endforeach;?>
								<!-- /noindex -->
							</div>
						<?endif;?>
					</div>
					<hr/>
				<?endif;?>
				<?// text?>
				<?if(strlen($arResult["FIELDS"]["PREVIEW_TEXT"].$arResult["FIELDS"]["DETAIL_TEXT"])):?>
					<div class="text">
						<?if($arResult["PREVIEW_TEXT_TYPE"] == "text"):?>
							<p><?=$arResult["FIELDS"]["PREVIEW_TEXT"];?></p>
						<?else:?>
							<?=$arResult["FIELDS"]["PREVIEW_TEXT"];?>
						<?endif;?>
						<?if($arResult["DETAIL_TEXT_TYPE"] == "text"):?>
							<p><?=$arResult["FIELDS"]["DETAIL_TEXT"];?></p>
						<?else:?>
							<?=$arResult["FIELDS"]["DETAIL_TEXT"];?>
						<?endif;?>
					</div>
				<?endif;?>
				<?if($arResult['DISPLAY_PROPERTIES']['SEND_MESS']['VALUE'] == 'Y'):?>
					<div class="btn-block">
						<span><span class="btn btn-default animate-load" data-event="jqm" data-autoload-staff="<?=CAllcorp2::formatJsName($arResult['NAME'])?>" data-autoload-staff_email_hidden="<?=CAllcorp2::formatJsName($arResult['PROPERTIES']['EMAIL']['VALUE'])?>" data-param-id="<?=CAllcorp2::getFormID("aspro_allcorp2_callstaff");?>" data-name="staff"><span><?=GetMessage('SEND_STAFF_MESS');?></span></span></span>
					</div>
				<?endif;?>
			</div>
		</div>
	</article>
	<div class="clearfix"></div>
</div>