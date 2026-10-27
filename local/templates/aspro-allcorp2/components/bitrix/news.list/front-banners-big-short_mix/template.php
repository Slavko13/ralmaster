<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?$this->setFrameMode(true);?>
<div class="maxwidth-theme">
		<div class="mixed_banners padding-banner-block">
			<?if($arResult['SECTIONS']['BIG'] && $arResult['SECTIONS']['SMALL']):?>
				<div class="row">
			<?endif;?>
			<?if($arResult['SECTIONS']['BIG']):?>
				<div class="big_banners_block">
					<?include('slider.php');?>
				</div>
			<?endif;?>
			<?if($arResult['SECTIONS']['BIG'] && $arResult['SECTIONS']['SMALL']):?>
					<div class="small_banners_block">
						<?include('normal.php');?>
					</div>
				</div>
			<?endif;?>
		</div>
</div>