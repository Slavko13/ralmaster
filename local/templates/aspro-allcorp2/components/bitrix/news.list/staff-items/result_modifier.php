<?
foreach($arResult['ITEMS'] as $arItem){
	$itemsIds[] = $arItem['ID'];
}

$resGroups = CIBlockElement::GetElementGroups($itemsIds, true, array('ID','IBLOCK_ELEMENT_ID'));
while($group = $resGroups->Fetch()){
	$itemsSections[$group['IBLOCK_ELEMENT_ID']][] = $group['ID'];
	$arSectionsIDs[$group['ID']] = $group['ID'];
}

if($arSectionsIDs){
	$arResult['SECTIONS'] = CCache::CIBLockSection_GetList(array('SORT' => 'ASC', 'NAME' => 'ASC', 'CACHE' => array('TAG' => CCache::GetIBlockCacheTag($arParams['IBLOCK_ID']), 'GROUP' => array('ID'), 'MULTI' => 'N')), array('ID' => $arSectionsIDs));
}

// group elements by sections
foreach($arResult['ITEMS'] as $arItem){
	$SID = ($itemsSections[$arItem['ID']] ? $itemsSections[$arItem['ID']] : 0);

	if($arItem['PROPERTIES'])
	{
		foreach($arItem['PROPERTIES'] as $key2 => $arProp)
		{
			if(($key2 == 'EMAIL' || $key2 == 'PHONE') && $arProp['VALUE'])
				$arItem['MIDDLE_PROPS'][] = $arProp;
			if(strpos($key2, 'SOCIAL') !== false && $arProp['VALUE'])
				$arItem['SOCIAL_PROPS'][] = $arProp;
		}
	}
	
	foreach ($SID as $itemSection) {
		$arResult['SECTIONS'][$itemSection]['ITEMS'][$arItem['ID']] = $arItem;
	}
}

// unset empty sections
if(is_array($arResult['SECTIONS'])){
	foreach($arResult['SECTIONS'] as $i => $arSection){
		if(!$arSection['ITEMS']){
			unset($arResult['SECTIONS'][$i]);
		}
	}
}
?>