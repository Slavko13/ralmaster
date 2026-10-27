<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */

$this->setFrameMode(true);?>
<?
global $USER;

if(!is_object($USER)){
    $USER = new CUser();
}
?>
<?if(isset($_POST["AJAX_REQUEST_INSTAGRAM"]) && $_POST["AJAX_REQUEST_INSTAGRAM"] == "Y"):
	$inst=new CInstargramAllcorp2($arParams["TOKEN"], \Bitrix\Main\Config\Option::get("aspro.allcorp2", "INSTAGRAMM_ITEMS_COUNT", 8));
	
	$arInstagramPosts=$inst->getInstagramPosts();
	$arInstagramUser= isset($arInstagramPosts['data'][0]['username']) ? $arInstagramPosts['data'][0]['username'] : '' ;?>
	<?$iCountSlide1 = \Bitrix\Main\Config\Option::get("aspro.allcorp2", "INSTAGRAMM_ITEMS_COUNT", 8);?>

	<?if($arInstagramPosts["error"]["message"] && $USER->IsAdmin()):?>
		<br>
		<div class="alert alert-danger">
			<strong>Error: </strong><?=$arInstagramPosts["error"]["message"]?>
		</div>
	<?else:?>
		<?if($arInstagramPosts && !$arInstagramPosts["error"]["message"]):?>
			<?if($arInstagramPosts['data']):?>
				<div class="item-views front blocks">
					<h3 class="title_block"><?=($arParams["TITLE"] ? $arParams["TITLE"] : GetMessage("TITLE"));?></h3>
					<a href="https://www.instagram.com/<?=$arInstagramUser?>/" target="_blank" class="right_link_block"><?=GetMessage('INSTAGRAM_ALL_ITEMS');?></a>
					<div class="instagram clearfix">
						<?$iCountSlide = \Bitrix\Main\Config\Option::get("aspro.allcorp2", "INSTAGRAMM_ITEMS_VISIBLE", 4);?>
						<div class="container">
							<?//$index = 0;?>
							<div class="items row1 flexbox1 flexslider dark-nav2" data-plugin-options='{"animation": "slide", "move": 0, "directionNav": true, "itemMargin":0, "controlNav" :true, "animationLoop": true, "slideshow": false, "slideshowSpeed": 5000, "animationSpeed": 900, "counts": [<?=$iCountSlide;?>,4,3,2,1]}'>
								<ul class="slides row flexbox">
									<?foreach ($arInstagramPosts['data'] as $arItem):?>
										<?$arItem['LINK'] = $arItem['thumbnail_url'] ? $arItem['thumbnail_url'] : $arItem['media_url'];?>
										<li class="item">
											<div class="image" style="background:url(<?=$arItem['LINK'];?>) center center/cover no-repeat;">
											<div class="desc">
												<div class="wrap">
													<div class="date font_upper"><?=CAllcorp2::showIconSvg("date_insta", SITE_TEMPLATE_PATH.'/images/svg/instagram_mainpage.svg');?><span><?=FormatDate('d F', strtotime($arItem['timestamp']), 'SHORT');?></span></div>
													<?if($arItem['caption']):?>
														<div class="text font_xs"><?=$arItem['caption'];?></div>
													<?endif;?>
													<a href="<?=$arItem['permalink']?>" target="_blank" rel="nofollow"></a>
												</div>
											</div>
											</div>
										</li>
										<?//if ($index == 3) break;?>
										<?//++$index;?>
									<?endforeach;?>
								</ul>
							</div>
						</div>
					</div>
				</div>
			<?endif;?>
		<?endif;?>
	<?endif;?>
<?else:?>
	<div class="row margin0">
		<div class="maxwidth-theme">
			<div class="col-md-12">
				<div class="instagram_ajax loader_circle"></div>
			</div>
		</div>
	</div>
<?endif;?>