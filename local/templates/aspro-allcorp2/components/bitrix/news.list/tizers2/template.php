<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? $this->setFrameMode( true ); ?>
<?if($arResult["ITEMS"]):?>
	<?
	$qntyItems = count($arResult['ITEMS']);
	$colmd = ($qntyItems > 3 ? 3 : ($qntyItems > 2 ? 4 : 6));
	$colsm = ($qntyItems > 1 ? 6 : 12);
	if(isset($arParams['ONE_ROW'] ) && $arParams['ONE_ROW'] == 'Y')
	{
		$colmd = $colsm = 12;
	}
	?>
	<div class="maxwidth-theme">
		<div class="col-md-12">
			<div class="tizers_block2 <?=$qntyItems > 2 && !isset($arParams['PAGE']) ? 'vertical' : ''?>">
				<div class="row flexbox">
					<?foreach($arResult["ITEMS"] as $arItem){
						$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
						$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
						$name = $arItem['NAME'];
						?>
						<div class="col-md-<?=$colmd;?> col-sm-<?=$colsm;?> col-xs-6">
							<div id="<?=$this->GetEditAreaId($arItem['ID']);?>" class="item">
								<?if($arItem["PREVIEW_PICTURE"]["SRC"]){?>
									<div class="img">
										<?if($arItem["PROPERTIES"]["LINK"]["VALUE"]):?>
											<a class="name" href="<?=$arItem["PROPERTIES"]["LINK"]["VALUE"]?>" <?= isset($arItem["PROPERTIES"]["NEW_TAB"]['VALUE_XML_ID']) && strlen($arItem["PROPERTIES"]["NEW_TAB"]['VALUE_XML_ID']) ? 'target="' . $arItem["PROPERTIES"]["NEW_TAB"]['VALUE_XML_ID'] . '"' : ''; ?>>
										<?endif;?>
										<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$name;?>" title="<?=$name;?>"/>
										<?if($arItem["PROPERTIES"]["LINK"]["VALUE"]):?>
											</a>
										<?endif;?>
									</div>
								<?}?>
								<div class="title">
									<?if($arItem["PROPERTIES"]["LINK"]["VALUE"]):?>
										<a class="name dark-color" href="<?=$arItem["PROPERTIES"]["LINK"]["VALUE"]?>" <?= isset($arItem["PROPERTIES"]["NEW_TAB"]['VALUE_XML_ID']) && strlen($arItem["PROPERTIES"]["NEW_TAB"]['VALUE_XML_ID']) ? 'target="' . $arItem["PROPERTIES"]["NEW_TAB"]['VALUE_XML_ID'] . '"' : ''; ?>>
									<?endif;?>
										<span class="top-text"><?=$name;?></span>
										<?if($arItem["PREVIEW_TEXT"]):?>
											<span class="desc-text"><?=$arItem["PREVIEW_TEXT"];?></span>
										<?endif;?>
									<?if($arItem["PROPERTIES"]["LINK"]["VALUE"]):?>
										</a>
									<?endif;?>
								</div>
							</div>
						</div>
					<?}?>
				</div>
			</div>
		</div>
	</div>
<?endif;?>