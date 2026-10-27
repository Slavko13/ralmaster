<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true ) die();?>
<?$this->setFrameMode(true);?>
<?use \Bitrix\Main\Localization\Loc;?>
<?$bImage = strlen($arResult['FIELDS']['PREVIEW_PICTURE']['SRC']);
$arImage = ($bImage ? CFile::ResizeImageGet($arResult['FIELDS']['PREVIEW_PICTURE']['ID'], array('width' => 50, 'height' => 50), BX_RESIZE_IMAGE_EXACT, true) : array());
$imageSrc = ($bImage ? $arImage['src'] : SITE_TEMPLATE_PATH.'/images/svg/Staff_noimage2.svg');?>
<div class="popup review-detail">
	<div class="item-views reviews slider front">
		<div class="item">
			<div class="header-block">
				<?if($imageSrc):?>
					<div class="image <?=($bImage ? '' : 'wpi')?>">
						<div class="image-wrapper">
							<div class="image-inner">
								<img class="img-responsive" src="<?=$imageSrc?>" alt="<?=($bImage ? $arResult['PREVIEW_PICTURE']['ALT'] : $arResult['NAME'])?>" title="<?=($bImage ? $arResult['PREVIEW_PICTURE']['TITLE'] : $arResult['NAME'])?>" />
							</div>
						</div>
					</div>
				<?endif;?>
				<div class="text">
					<?$bHasSocProps = (isset($arResult['SOCIAL_PROPS']) && $arResult['SOCIAL_PROPS']);?>
					<div class="title-wrapper <?=($bHasSocProps ? 'bottom-props' : '');?>">
						<div class="title"><?=$arResult['NAME']?></div>
						<?if($bHasSocProps):?>
							<!-- noindex -->
								<?foreach($arResult['SOCIAL_PROPS'] as $arProp):?>
									<a href="<?=$arProp['VALUE'];?>" target="_blank" rel="nofollow" class="value <?=strtolower($arProp['CODE']);?>"><?=$arProp['VALUE'];?></a>
								<?endforeach;?>
							<!-- /noindex -->
						<?endif;?>
					</div>
					<?if($arResult['PROPERTIES']['POST']['VALUE']):?>
						<div class="company"><?=$arResult['PROPERTIES']['POST']['VALUE'];?></div>
					<?endif;?>
				</div>
			</div>
			<div class="bottom-block">
				<?if($arResult["PREVIEW_TEXT"] && (isset($arResult['FIELDS']['PREVIEW_TEXT']) && $arResult['FIELDS']['PREVIEW_TEXT'])):?>
					<div class="preview-text-wrapper">
						<div class="quote"><?=CAllcorp2::showIconSvg('', SITE_TEMPLATE_PATH.'/images/svg/Quote.svg', '', false);?></div>
						<?=$arResult['FIELDS']['PREVIEW_TEXT'];?>
					</div>
				<?endif;?>

				<?if($arResult['PROPERTIES']['DOCUMENTS']['VALUE']):?>
					<div class="row docs-block flexbox">
						<?foreach((array)$arResult['PROPERTIES']['DOCUMENTS']['VALUE'] as $docID):?>
							<?$arFile = CAllcorp2::get_file_info($docID);?>
							<div class="col-md-6 col-xs-12">
								<?
								$fileName = substr($arFile['ORIGINAL_NAME'], 0, strrpos($arFile['ORIGINAL_NAME'], '.'));
								$fileTitle = (strlen($arFile['DESCRIPTION']) ? $arFile['DESCRIPTION'] : $fileName);

								?>
								<div class="blocks clearfix <?=$arFile['TYPE']?>">
									<div class="inner-wrapper">
										<a href="<?=$arFile['SRC']?>" class="dark-color text" target="_blank"><?=$fileTitle?></a>
										<div class="filesize"><?=CAllcorp2::filesize_format($arFile['FILE_SIZE']);?></div>
									</div>
								</div>
							</div>
						<?endforeach;?>
					</div>
				<?endif;?>
				<??>
				<?if($arResult['PROPERTIES']['VIDEO']['VALUE']):?>
					<div class="video">
						<?foreach($arResult['PROPERTIES']['VIDEO']['~VALUE'] as $value):?>
							<div class="video-inner"><?=$value;?></div>
						<?endforeach;?>
					</div>
				<?endif;?>

				<div class="close-block">
					<span class="btn btn-default btn-lg jqmClose"><?=Loc::getMessage('CLOSE');?></span>
				</div>
			</div>
		</div>
	</div>
</div>