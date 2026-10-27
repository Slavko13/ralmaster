<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

if(!$arResult["NavShowAlways"]){
	if($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false))
		return;
}

if($arResult["NavQueryString"] != "")
{
	$arTmp = explode("&amp;", $arResult["NavQueryString"]);
	if($arTmp)
	{
		foreach($arTmp as $key => $value)
		{
			if(strpos($value, "AJAX_REQUEST") !== false)
			{
				unset($arTmp[$key]);
			}
		}
		if(!$arTmp)
			$arResult["NavQueryString"] = "";
		else
			$arResult["NavQueryString"]  = implode("&amp;", $arTmp);
	}
}

$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");

if($arResult["bShowAll"] && !$arResult["NavShowAll"]){
	echo '<link rel="canonical" href="'.$arResult["sUrlPath"].'?'.$strNavQueryString.'SHOWALL_'.$arResult["NavNum"].'=1">';
}

if($arResult["NavPageNomer"] == 1)
	$bPrevDisabled = true;
elseif($arResult["NavPageNomer"] < $arResult["NavPageCount"])
	$bPrevDisabled = false;
if($arResult["NavPageNomer"] == $arResult["NavPageCount"])
	$bNextDisabled = true;
else
	$bNextDisabled = false;
?>
<?if(!$bNextDisabled):?>
	<div class="ajax_load_btn">
		<span class="more_text_ajax">
			<?=GetMessage('PAGER_SHOW_MORE')?>
			<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15"><path class="cls-spin" d="M902,1459h-4a1,1,0,0,1,0-2h1.7a5.441,5.441,0,0,0-4.2-2,5.5,5.5,0,1,0,4.611,8.54l0.011,0.01A0.991,0.991,0,0,1,902,1464a1.023,1.023,0,0,1-.13.47h0c-0.038.06-.086,0.12-0.127,0.18-0.017.02-.026,0.04-0.044,0.06a7.522,7.522,0,1,1-.7-9.27V1454a1,1,0,0,1,2,0v4A1,1,0,0,1,902,1459Z" transform="translate(-888 -1453)"/></svg>
		</span>
	</div>
<?endif;?>
<div class="wrap_pagination module-pagination">
	<ul class="pagination">
		<?if($arResult["bDescPageNumbering"] === true):?>
			<?if($arResult["NavPageCount"] > $arResult["NavPageNomer"]):?>
				<?// prev?>
				<?if($arResult["NavPageCount"] - $arResult["NavPageNomer"] > 1):?>
					<?$href = $arResult["sUrlPath"].'?'.$strNavQueryString.'PAGEN_'.$arResult["NavNum"].'='.($arResult["NavPageNomer"] + 1);?>
				<?else:?>
					<?$href = $arResult["sUrlPath"].$strNavQueryStringFull;?>
				<?endif;?>
				<li class="prev"><a href="<?=$href?>"><?=CAllcorp2::showIconSvg('cabinet', SITE_TEMPLATE_PATH.'/images/svg/Arrow_left_black_sm.svg');?></a></li>
				<link rel="prev" href="<?=$href;?>">
				<link rel="canonical" href="<?=$arResult["sUrlPath"]?>" />
			<?endif;?>
			<?if($arResult["NavPageCount"] > $arResult["nStartPage"]):?>
				<?// first?>
				<li><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>">1</a></li>
			<?endif;?>
			<?if($arResult["NavPageCount"] - ($arResult["nStartPage"]) > 2):?>
				<?// ...?>
				<li class="before"><span>...</span></li>
			<?elseif($arResult["NavPageCount"] - ($arResult["nStartPage"]) == 2):?>
				<li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["NavPageCount"] - 1?>">2</a></li>
			<?endif;?>
			<?for($i = $arResult["nStartPage"]; $i >= $arResult["nEndPage"]; --$i):?>
				<?// 2 items before current?>
				<?// current?>
				<?// 2 items after current?>
				<?if($i == $arResult["NavPageNomer"]):?>
					<li class="active"><span><?=($arResult["NavPageCount"] - $i + 1)?></span></li>
				<?else:?>
					<?if($i == $arResult["NavPageCount"]):?>
						<li><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>">1</a></li>
					<?else:?>
						<li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$i?>"><?=($arResult["NavPageCount"] - $i + 1)?></a></li>
					<?endif;?>
				<?endif;?>
			<?endfor;?>
			<?if($arResult["nEndPage"] > 3):?>
				<?// ...?>
				<li class="before"><span>...</span></li>
			<?elseif($arResult["nEndPage"] == 3):?>
				<li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=2"><?=$arResult["NavPageCount"] - 1?></a></li>
			<?endif;?>
			<?if($arResult["nEndPage"] > 1):?>
				<?// last?>
				<li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=1"><?=$arResult["NavPageCount"]?></a></li>
			<?endif;?>
			<?if($arResult["NavPageNomer"] > 1):?>
				<?// next?>
				<?$href = $arResult["sUrlPath"].'?'.$strNavQueryString.'PAGEN_'.$arResult["NavNum"].'='.($arResult["NavPageNomer"] - 1);?>
				<li class="next"><a href="<?=$href?>"><?=CAllcorp2::showIconSvg('cabinet', SITE_TEMPLATE_PATH.'/images/svg/Arrow_right_black_sm.svg');?></a></li>
				<link rel="next" href="<?=$href;?>">
			<?endif;?>
		<?else:?>
			<?if($arResult["NavPageNomer"] > 1):?>
				<?// prev?>
				<?if($arResult["NavPageNomer"] > 2):?>
					<?$href = $arResult["sUrlPath"].'?'.$strNavQueryString.'PAGEN_'.$arResult["NavNum"].'='.($arResult["NavPageNomer"] - 1);?>
				<?else:?>
					<?$href = $arResult["sUrlPath"].$strNavQueryStringFull;?>
				<?endif;?>
				<li class="prev"><a href="<?=$href?>"><?=CAllcorp2::showIconSvg('cabinet', SITE_TEMPLATE_PATH.'/images/svg/Arrow_left_black_sm.svg');?></a></li>
				<link rel="prev" href="<?=$href;?>">
				<link rel="canonical" href="<?=$arResult["sUrlPath"]?>" />
			<?endif;?>
			<?if($arResult["nStartPage"] > 1):?>
				<?// first?>
				<li><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>">1</a></li>
			<?endif;?>
			<?if(($arResult["nStartPage"] - 1) > 2):?>
				<?// ...?>
				<li class="before"><span>...</span></li>
			<?elseif(($arResult["nStartPage"] - 1) == 2):?>
				<li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=2">2</a></li>
			<?endif;?>
			<?for($i = $arResult["nStartPage"]; $i <= $arResult["nEndPage"]; ++$i):?>
				<?// 2 items before current?>
				<?// current?>
				<?// 2 items after current?>
				<?if($i == $arResult["NavPageNomer"]):?>
					<li class="active"><span><?=$i?></span></li>
				<?else:?>
					<?if($i == 1):?>
						<li><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>">1</a></li>
					<?else:?>
						<li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$i?>"><?=$i?></a></li>
					<?endif;?>
				<?endif;?>
			<?endfor;?>
			<?if($arResult["NavPageCount"] - $arResult["nEndPage"] > 2):?>
				<?// ...?>
				<li class="before"><span>...</span></li>
			<?elseif(($arResult["NavPageCount"] - $arResult["nEndPage"]) == 2):?>
				<li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["NavPageCount"] - 1?>"><?=$arResult["NavPageCount"] -1?></a></li>
			<?endif;?>
			<?if($arResult["nEndPage"] < $arResult["NavPageCount"]):?>
				<?// last?>
				<li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["NavPageCount"]?>"><?=$arResult["NavPageCount"]?></a></li>
			<?endif;?>
			<?if($arResult["NavPageNomer"] < $arResult["NavPageCount"]):?>
				<?// next?>
				<?$href = $arResult["sUrlPath"].'?'.$strNavQueryString.'PAGEN_'.$arResult["NavNum"].'='.($arResult["NavPageNomer"] + 1);?>
				<li class="next"><a href="<?=$href?>"><?=CAllcorp2::showIconSvg('cabinet', SITE_TEMPLATE_PATH.'/images/svg/Arrow_right_black_sm.svg');?></a></li>
				<link rel="next" href="<?=$href;?>">
			<?endif;?>
		<?endif;?>
		<?if($arResult["bShowAll"]):?>
			<!-- noindex -->
				<?if($arResult["NavShowAll"]):?>
					<li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>SHOWALL_<?=$arResult["NavNum"]?>=0" rel="nofollow"><?=GetMessage("nav_paged")?></a></li>
				<?else:?>
					<li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>SHOWALL_<?=$arResult["NavNum"]?>=1" rel="nofollow"><?=GetMessage("nav_all")?></a></li>
				<?endif?>
			<!-- /noindex -->
		<?endif?>
	</ul>
</div>