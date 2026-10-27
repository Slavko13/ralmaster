<?if( !defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true ) die();?>
<?$this->setFrameMode(true);?>
<?
if(!function_exists("ShowSubItems")){
	function ShowSubItems($arItem){
		?>
		<?if($arItem["SELECTED"] && $arItem["CHILD"]):?>
			<?$noMoreSubMenuOnThisDepth = false;?>
			<div class="submenu-wrapper">
				<ul class="submenu">
					<?foreach($arItem["CHILD"] as $arSubItem):?>
						<li class="<?=($arSubItem["SELECTED"] ? "active" : "")?><?=($arSubItem["CHILD"] ? " child" : "")?>">
							<a href="<?=$arSubItem["LINK"]?>"><?=$arSubItem["TEXT"]?></a>
							<?if(!$noMoreSubMenuOnThisDepth):?>
								<?ShowSubItems($arSubItem);?>
							<?endif;?>
						</li>
						<?$noMoreSubMenuOnThisDepth |= CAllcorp2::isChildsSelected($arSubItem["CHILD"]);?>
					<?endforeach;?>
				</ul>
			</div>
		<?endif;?>
		<?
	}
}
?>
<?if($arResult):?>
	<aside class="sidebar">
		<ul class="nav nav-list side-menu">
			<?foreach($arResult as $arItem):?>
				<li class="<?=($arItem["SELECTED"] ? "active" : "")?> <?=(isset($arItem["CHILD"]) && $arItem["CHILD"] && !isset($arItem["NO_PARENT"]) ? "child" : "")?>">
					<?
					if( strpos($arItem["LINK"] ,'?logout=yes') !== false ){
						$arItem["LINK"].= '&'.bitrix_sessid_get();
					}
					?>
					<a href="<?=$arItem["LINK"]?>"><?=(isset($arItem["PARAMS"]["BLOCK"]) && $arItem["PARAMS"]["BLOCK"] ? $arItem["PARAMS"]["BLOCK"] : "");?><?=$arItem["TEXT"]?></a>
					<?ShowSubItems($arItem);?>
				</li>
			<?endforeach;?>
		</ul>
	</aside>
<?endif;?>