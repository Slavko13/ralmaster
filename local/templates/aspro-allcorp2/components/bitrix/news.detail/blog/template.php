<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?$this->setFrameMode(true);?>
<?use \Bitrix\Main\Localization\Loc;?>

<?// element name?>
<?if($arParams['DISPLAY_NAME'] != 'N' && strlen($arResult['NAME'])):?>
	<h2><?=$arResult['NAME']?></h2>
<?endif;?>

<?// date active from or dates period active?>
<?if(strlen($arResult['DISPLAY_PROPERTIES']['PERIOD']['VALUE']) || ($arResult['DISPLAY_ACTIVE_FROM'] && in_array('DATE_ACTIVE_FROM', $arParams['FIELD_CODE']))):?>
	<div class="period-wrapper">
		<div class="period">
			<?if(strlen($arResult['DISPLAY_PROPERTIES']['PERIOD']['VALUE'])):?>
				<span class="date"><?=$arResult['DISPLAY_PROPERTIES']['PERIOD']['VALUE']?></span>
			<?else:?>
				<span class="date"><?=$arResult['DISPLAY_ACTIVE_FROM']?></span>
			<?endif;?>
		</div>
		<?if($arResult['SECTIONS']):
			$arResult['SECTIONS']= current($arResult['SECTIONS']);?>
			<span class="section_name">
				//&nbsp;<?=$arResult['SECTIONS']['NAME']?>
			</span>
		<?endif;?>
	</div>
<?endif;?>

<?$bWideImg = false;
$bPreviewText = (strlen($arResult['FIELDS']['PREVIEW_TEXT']));?>
<?// single detail image?>
<?if($arResult['FIELDS']['DETAIL_PICTURE']):?>
	<?
	$atrTitle = (strlen($arResult['DETAIL_PICTURE']['DESCRIPTION']) ? $arResult['DETAIL_PICTURE']['DESCRIPTION'] : (strlen($arResult['DETAIL_PICTURE']['TITLE']) ? $arResult['DETAIL_PICTURE']['TITLE'] : $arResult['NAME']));
	$atrAlt = (strlen($arResult['DETAIL_PICTURE']['DESCRIPTION']) ? $arResult['DETAIL_PICTURE']['DESCRIPTION'] : (strlen($arResult['DETAIL_PICTURE']['ALT']) ? $arResult['DETAIL_PICTURE']['ALT'] : $arResult['NAME']));
	?>
	<?if($arResult['PROPERTIES']['PHOTOPOS']['VALUE_XML_ID'] == 'LEFT'):?>
		<div class="detailimage image-left"><a href="<?=$arResult['DETAIL_PICTURE']['SRC']?>" class="fancybox" title="<?=$atrTitle?>"><img src="<?=$arResult['DETAIL_PICTURE']['SRC']?>" class="img-responsive" title="<?=$atrTitle?>" alt="<?=$atrAlt?>" /></a></div>
	<?elseif($arResult['PROPERTIES']['PHOTOPOS']['VALUE_XML_ID'] == 'RIGHT'):?>
		<div class="detailimage image-right"><a href="<?=$arResult['DETAIL_PICTURE']['SRC']?>" class="fancybox" title="<?=$atrTitle?>"><img src="<?=$arResult['DETAIL_PICTURE']['SRC']?>" class="img-responsive" title="<?=$atrTitle?>" alt="<?=$atrAlt?>" /></a></div>
	<?elseif($arResult['PROPERTIES']['PHOTOPOS']['VALUE_XML_ID'] == 'TOP'):?>
		<?$this->SetViewTarget('top_section_filter_content');?>
		<div class="detailimage image-head"><img src="<?=$arResult['DETAIL_PICTURE']['SRC']?>" class="img-responsive" title="<?=$atrTitle?>" alt="<?=$atrAlt?>"/></div>
		<?$this->EndViewTarget();?>
	<?else:?>
		<div class="detailimage image-wide<?=($bPreviewText ? '' : ' np')?>"><a href="<?=$arResult['DETAIL_PICTURE']['SRC']?>" class="fancybox" title="<?=$atrTitle?>"><img src="<?=$arResult['DETAIL_PICTURE']['SRC']?>" class="img-responsive" title="<?=$atrTitle?>" alt="<?=$atrAlt?>" /></a></div>
		<?$bWideImg = true;?>
	<?endif;?>
<?endif;?>

<?// ask question?>
<?if($arResult['DISPLAY_PROPERTIES']['FORM_QUESTION']['VALUE_XML_ID'] == 'YES'):?>
	<?$this->SetViewTarget('under_sidebar_content');?>
		
	<?$this->EndViewTarget();?>
<?endif;?>

<?if(strlen($arResult['FIELDS']['PREVIEW_TEXT'])):?>
	<div class="introtext<?=($bWideImg ? ' wides' : '');?> norder">
		<?if($arResult['PREVIEW_TEXT_TYPE'] == 'text'):?>
			<p><?=$arResult['FIELDS']['PREVIEW_TEXT'];?></p>
		<?else:?>
			<?=$arResult['FIELDS']['PREVIEW_TEXT'];?>
		<?endif;?>
	</div>
<?endif;?>


<?if(strlen($arResult['FIELDS']['DETAIL_TEXT'])):?>
	<div itemprop="articleBody" class="content">
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

<?if(count($arResult['GALLERY'])):?>
	<div class="drag_block">
		<div class="wraps gallerys">
			<hr/>
			<h5><?=($arParams["T_GALLERY"] ? $arParams["T_GALLERY"] : Loc::getMessage("T_GALLERY"));?></h5>
			<?if($arParams['GALLERY_TYPE'] == 'small'):?>
				<div class="small-gallery-block">
					<div class="flexslider unstyled row front bigs dark-nav2" data-plugin-options='{"animation": "slide", "directionNav": false, "controlNav" :true, "animationLoop": true, "slideshow": false, "counts": [4, 3, 2, 1]}'>
						<ul class="slides items">
							<?foreach($arResult['GALLERY'] as $i => $arPhoto):?>
								<li class="col-md-3 item">
									<div>
										<img src="<?=$arPhoto['PREVIEW']['src']?>" class="img-responsive inline" title="<?=$arPhoto['TITLE']?>" alt="<?=$arPhoto['ALT']?>" />
									</div>
									<a href="<?=$arPhoto['DETAIL']['SRC']?>" class="fancybox dark_block_animate" data-fancybox-group="gallery" target="_blank" title="<?=$arPhoto['TITLE']?>"></a>
								</li>
							<?endforeach;?>
						</ul>
					</div>
				</div>
			<?else:?>
				<div class="gallery-block">
					<div class="gallery-wrapper">
						<div class="inner">
							<?if(count($arResult["GALLERY"]) > 1):?>
								<div class="small-gallery-wrapper">
									<div class="thmb1 flexslider unstyled small-gallery rounded-nav" data-plugin-options='{"slideshow": "false", "animation": "slide", "animationLoop": true, "itemWidth": 60, "itemMargin": 20, "minItems": 1, "maxItems": 9, "slide_counts": 1, "asNavFor": ".gallery-wrapper .bigs"}' id="carousel1">
										<ul class="slides items">	
											<?foreach($arResult["GALLERY"] as $arPhoto):?>
												<li class="item">
													<img class="img-responsive inline" src="<?=$arPhoto["THUMB"]["src"]?>" title="<?=$arPhoto['TITLE']?>" alt="<?=$arPhoto['ALT']?>" />
												</li>
											<?endforeach;?>
										</ul>
									</div>
								</div>
							<?endif;?>
							<div class="thmb1 flexslider unstyled row bigs color-controls" id="slider" data-plugin-options='{"animation": "slide", "directionNav": true, "controlNav" :true, "animationLoop": true, "slideshow": false, "sync": ".gallery-wrapper .small-gallery", "counts": [1, 1, 1]}'>
								<ul class="slides items">
									<?foreach($arResult['GALLERY'] as $i => $arPhoto):?>
										<li class="col-md-12 item">
											<a href="<?=$arPhoto['DETAIL']['SRC']?>" class="fancybox" data-fancybox-group="gallery" target="_blank" title="<?=$arPhoto['TITLE']?>">
												<img src="<?=$arPhoto['PREVIEW']['src']?>" class="img-responsive inline" title="<?=$arPhoto['TITLE']?>" alt="<?=$arPhoto['ALT']?>" />
												<span class="zoom"></span>
											</a>
										</li>
									<?endforeach;?>
								</ul>
							</div>
						</div>
					</div>
				</div>
			<?endif;?>
		</div>
	</div>
<?endif;?>

<?
$frame = $this->createFrame('video')->begin('');
$frame->setAnimation(true);
?>
<?// video?>
<?if($arResult['VIDEO']):?>
	<div class="wraps">
		<hr />
		<h5><?=(strlen($arParams['T_VIDEO']) ? $arParams['T_VIDEO'] : Loc::getMessage('T_VIDEO'))?></h5>
		<div class="row video">
			<?foreach($arResult['VIDEO'] as $i => $arVideo):?>
				<div class="col-md-6 item">
					<div class="video_body">
						<video id="js-video_<?=$i?>" width="350" height="217"  class="video-js" controls="controls" preload="metadata" data-setup="{}">
							<source src="<?=$arVideo["path"]?>" type='video/mp4' />
							<p class="vjs-no-js">
								To view this video please enable JavaScript, and consider upgrading to a web browser that supports HTML5 video
							</p>
						</video>
					</div>
					<div class="title"><?=(strlen($arVideo["title"]) ? $arVideo["title"] : $i)?></div>
				</div>
			<?endforeach;?>
		</div>
	</div>
<?endif;?>

<?if(!empty($arResult['DISPLAY_PROPERTIES']['DOCUMENTS']['VALUE'])) :?>
	<div class="docs-block">
		<h4 class="title-block-middle"><?=$arParams["T_DOCS"] ?: Loc::getMessage("T_DOCS");?></h4>
		<div class="row flexbox">
			<?foreach($arResult['DISPLAY_PROPERTIES']['DOCUMENTS']['VALUE'] as $docID):?>
				<?$arItem = CAllcorp2::get_file_info($docID);?>
				<div class="col-md-4 col-xs-12">
					<?$fileName = substr($arItem['ORIGINAL_NAME'], 0, strrpos($arItem['ORIGINAL_NAME'], '.'));
					$fileTitle = (strlen($arItem['DESCRIPTION']) ? $arItem['DESCRIPTION'] : $fileName);?>
					<div class="blocks clearfix <?=$arItem["TYPE"];?>">
						<div class="inner-wrapper">
							<a href="<?=$arItem['SRC']?>" class="dark-color text" target="_blank"><?=$fileTitle?></a>
							<div class="filesize"><?=CAllcorp2::filesize_format($arItem['FILE_SIZE']);?></div>
						</div>
					</div>
				</div>
			<?endforeach;?>
		</div>
	</div>
<?endif;?>

<?$frame->end();?>
<?if($arResult['TAGS']):?>
	<?$this->SetViewTarget('tags_content');?>
		<hr class="bottoms" />
		<div class="search-tags-cloud">
			<div class="title-block-middle"><?=Loc::getMessage('TAGS');?></div>
			<div class="tags">
				<?$arTags = explode(",", $arResult['TAGS']);?>
				<?foreach($arTags as $text):?>
					<a href="<?=SITE_DIR;?>search/index.php?tags=<?=htmlspecialcharsex($text);?>" rel="nofollow"><?=$text;?></a>
				<?endforeach;?>
			</div>
		</div>
	<?$this->EndViewTarget();?>
<?endif;?>