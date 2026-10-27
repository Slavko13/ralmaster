<?if( !defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true ) die();?>
<?$this->setFrameMode(true);?>
<?
if(!function_exists("ShowSubItems")){
	function ShowSubItems($arItem){
		?>
		<?if(isset($arItem["SELECTED"]) && $arItem["SELECTED"]  && isset($arItem["CHILD"]) && $arItem["CHILD"] ):?>
			<?$noMoreSubMenuOnThisDepth = false;?>
			<div class="submenu-wrapper">
				<ul class="submenu">
					<?foreach($arItem["CHILD"] as $arSubItem):?>
						<li class="<?=(isset($arSubItem["SELECTED"]) && $arSubItem["SELECTED"]  ? "active" : "")?><?=(isset($arSubItem["CHILD"]) && $arSubItem["CHILD"]  ? " child" : "")?>">
							<a href="<?=$arSubItem["LINK"]?>"><?=$arSubItem["TEXT"]?></a>
							<?if(!$noMoreSubMenuOnThisDepth):?>
								<?ShowSubItems($arSubItem);?>
							<?endif;?>
						</li>
						<?if(isset($arSubItem["CHILD"]) && $arSubItem["CHILD"]) {
							$noMoreSubMenuOnThisDepth |= CAllcorp2::isChildsSelected($arSubItem["CHILD"]);
						}?>
					<?endforeach;?>
				</ul>
			</div>
		<?endif;?>
		<?
	}
}
?>
<?if($arResult):?>
	<aside class="sidebar dropdown-menu">
		<ul class="nav nav-list side-menu">
			<?foreach($arResult as $arItem):?>
				<li class="<?=($arItem["SELECTED"] ? "active" : "")?> <?=($arItem["CHILD"] ? "child" : "")?>">
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