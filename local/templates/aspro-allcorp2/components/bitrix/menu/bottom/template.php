<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?
$this->setFrameMode(true);
$colmd = 12;
$colsm = 12;
?>
<?if($arResult):?>
	<?
	global $arTheme;
	$compactFooterMobile = $arTheme['COMPACT_FOOTER_MOBILE']['VALUE'];
	if(!function_exists("ShowSubItems2")){
		function ShowSubItems2($arItem, $indexSection){
			?>
			<?if($arItem["CHILD"]):?>
				<?$noMoreSubMenuOnThisDepth = false;
				$count = count($arItem["CHILD"]);?>
				<?$lastIndex = count($arItem["CHILD"]) - 1;?>
				<?foreach($arItem["CHILD"] as $i => $arSubItem):?>
					<?if(!$i):?>
						<div id="<?=$indexSection?>" class="wrap panel-collapse wrap_compact_mobile">
					<?endif;?>
						<?$bLink = strlen($arSubItem['LINK']);?>
						<div class="item-link item-link">
							<div class="item<?=($arSubItem["SELECTED"] ? " active" : "")?>">
								<div class="title">
									<?if($bLink):?>
										<a href="<?=$arSubItem['LINK']?>"><?=$arSubItem['TEXT']?></a>
									<?else:?>
										<span><?=$arSubItem['TEXT']?></span>
									<?endif;?>
								</div>
							</div>
						</div>

						<?$noMoreSubMenuOnThisDepth |= boolval(CAllcorp2::isChildsSelected($arSubItem["CHILD"]));?>
					<?if($i && $i === $lastIndex || $count == 1):?>
						</div>
					<?endif;?>
				<?endforeach;?>

			<?endif;?>
			<?
		}
	}
	?>
	<?$indexSection = $arParams['ROOT_MENU_TYPE'];?>
	<div class="bottom-menu">
		<div class="items">

			<?$lastIndex = count($arResult) - 1;?>
			<?foreach($arResult as $i => $arItem):?>

				<?if($i === 1):?>
					<div id="<?=$indexSection?>" class="wrap panel-collapse wrap_compact_mobile">
				<?endif;?>
					<?$bLink = strlen($arItem['LINK']);?>
					<div class="item-link accordion-close item-link" data-parent="#<?=$indexSection?>" data-target="#<?=$indexSection?>">
						<div class="item<?=($arItem["SELECTED"] ? " active" : "")?> ">
							<div class="title">
								<?if($bLink):?>
									<a href="<?=$arItem['LINK']?>"><?=$arItem['TEXT']?></a>
								<?else:?>
									<span><?=$arItem['TEXT']?></span>
								<?endif;?>
							</div>
						</div>

						<?if( $compactFooterMobile == "Y" && ($arItem["CHILD"] || $i < 1) ):?>
							<span><i class="fa fa-angle-down"></i></span>
						<?endif;?>
					</div>
				<?if($i && $i === $lastIndex):?>
					</div>
				<?endif;?>
				<?ShowSubItems2($arItem, $indexSection);?>
			<?endforeach;?>
		</div>
	</div>
<?endif;?>