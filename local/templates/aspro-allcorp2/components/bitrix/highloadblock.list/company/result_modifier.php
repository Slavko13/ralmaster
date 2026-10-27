<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true ) die();?>
<?
if($arResult['rows'])
{
	\Bitrix\Main\Type\Collection::sortByColumn($arResult['rows'], array('SORT' => array(SORT_NUMERIC, SORT_ASC)));
	foreach($arResult['rows'] as $key => $arItem){		
		$needHLValues[$arItem['ID']] = $key;
	}	
}
?>
<?
$hlBlockID = intval($arParams["BLOCK_ID"]);
if( $hlBlockID > 0 && is_array($needHLValues) && count($needHLValues)>0 ){
	$needHLValuesIDs = array_keys($needHLValues);
	\Bitrix\Main\Loader::IncludeModule("highloadblock");
	$hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getById($hlBlockID)->fetch();
	$entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
	$entityDataClass = $entity->getDataClass();

	$result = $entityDataClass::getList(array(
		"select" => array( 'ID', 'UF_CLASS', 'UF_DESCRIPTION', 'UF_LINK'),
		"order" => array("ID"=>"DESC"),
		"filter" => array('ID' => $needHLValuesIDs),
	));

	while ($arRow = $result->Fetch())
	{
		$arResult['rows'][$needHLValues[$arRow['ID']]]['UF_CLASS_FORMAT'] = $arRow['UF_CLASS'];
		$arResult['rows'][$needHLValues[$arRow['ID']]]['UF_DESCRIPTION_FORMAT'] = $arRow['UF_DESCRIPTION'];
		$arResult['rows'][$needHLValues[$arRow['ID']]]['UF_LINK'] = $arRow['UF_LINK'];
	}	
}

?>