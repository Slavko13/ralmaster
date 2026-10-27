<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Web\Json;

if (!Loader::includeModule('iblock'))
	return;

CBitrixComponent::includeComponentClass('bitrix:catalog.section');

/* show sort property */
$arPropertySort = $arPropertySortDefault = $arPropertyDefaultSort = array();
$arPropertySortDefault = array('name', 'sort');
$arPropertySort = array('name' => GetMessage('V_NAME'), 'sort' => GetMessage('V_SORT'));
$rsProp = CIBlockProperty::GetList(array('sort' => 'asc', 'name' => 'asc'), Array('ACTIVE' => 'Y', 'IBLOCK_ID' => (isset($arCurrentValues['IBLOCK_ID']) ? $arCurrentValues['IBLOCK_ID'] : $arCurrentValues['ID'])));
while($arr = $rsProp->Fetch()){
	$arPropertySort[$arr['CODE']] = $arr['NAME'];
	$strPropName = '['.$arr['ID'].']'.('' != $arr['CODE'] ? '['.$arr['CODE'].']' : '').' '.$arr['NAME'];
	if ('S' == $arr['PROPERTY_TYPE'] && 'directory' == $arr['USER_TYPE'] && CIBlockPriceTools::checkPropDirectory($arr))
		$arHighloadPropList[$arr['CODE']] = $strPropName;
}

if($arCurrentValues['SORT_PROP']){
	foreach($arCurrentValues['SORT_PROP'] as $code){
		$arPropertyDefaultSort[$code] = $arPropertySort[$code];
	}
}
else{
	foreach($arPropertySortDefault as $code){
		$arPropertyDefaultSort[$code] = $arPropertySort[$code];
	}
}

/* show sort direction */
$arSortDirection = array('asc' => GetMessage('SD_ASC'), 'desc' => GetMessage('SD_DESC'));
$arGalleryType = array('big' => GetMessage('GALLERY_BIG'), 'small' => GetMessage('GALLERY_SMALL'));

/* get component template pages & params array */
$arPageBlocksParams = array();
if(\Bitrix\Main\Loader::includeModule('aspro.allcorp2')){
	$arPageBlocks = CAllcorp2::GetComponentTemplatePageBlocks(__DIR__);
	$arPageBlocksParams = CAllcorp2::GetComponentTemplatePageBlocksParams($arPageBlocks);

	CAllcorp2::AddComponentTemplateModulePageBlocksParams(__DIR__, $arPageBlocksParams, array('SECTION' => 'CATALOG_PAGE', 'OPTION' => 'SECTIONS_TYPE_VIEW_CATALOG'), 'SECTIONS_TYPE_VIEW'); // add option value FROM_MODULE
	CAllcorp2::AddComponentTemplateModulePageBlocksParams(__DIR__, $arPageBlocksParams, array('SECTION' => 'CATALOG_PAGE', 'OPTION' => 'SECTION_TYPE_VIEW_CATALOG'), 'SECTION_TYPE_VIEW'); // add option value FROM_MODULE
	CAllcorp2::AddComponentTemplateModulePageBlocksParams(__DIR__, $arPageBlocksParams, array('SECTION' => 'CATALOG_PAGE', 'OPTION' => 'ELEMENTS_CATALOG')); // add option FROM_MODULE
	
	CAllcorp2::AddComponentTemplateModulePageBlocksParams(__DIR__, $arPageBlocksParams, array('SECTION' => 'CATALOG_PAGE', 'OPTION' => 'CATALOG')); // add option value FROM_MODULE

	CAllcorp2::AddComponentTemplateModulePageBlocksParams(__DIR__, $arPageBlocksParams, array('SECTION' => 'CATALOG_PAGE', 'OPTION' => 'ELEMENTS_TABLE_TYPE_VIEW')); // add option value FROM_MODULE to TABLE list
	CAllcorp2::AddComponentTemplateModulePageBlocksParams(__DIR__, $arPageBlocksParams, array('SECTION' => 'CATALOG_PAGE', 'OPTION' => 'ELEMENTS_LIST_TYPE_VIEW')); // add option value FROM_MODULE to LIST list
	CAllcorp2::AddComponentTemplateModulePageBlocksParams(__DIR__, $arPageBlocksParams, array('SECTION' => 'CATALOG_PAGE', 'OPTION' => 'ELEMENTS_PRICE_TYPE_VIEW')); // add option value FROM_MODULE to PRICE list
}

$arTemplateParameters = array_merge($arPageBlocksParams, array(
	'SHOW_DETAIL_LINK' => array(
		'PARENT' => 'LIST_SETTINGS',
		'NAME' => GetMessage('SHOW_DETAIL_LINK'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	),
	'SORT_PROP' => array(
		'PARENT' => 'LIST_SETTINGS',
		'NAME' => GetMessage('T_SORT_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => $arPropertySort,
		'SIZE' => 3,
		'MULTIPLE' => 'Y',
		'REFRESH' => 'Y'
	),
	'IMAGE_CATALOG_POSITION' => array(
		'PARENT' => 'LIST_SETTINGS',
		'SORT' => 250,
		'NAME' => GetMessage('IMAGE_CATALOG_POSITION'),
		'TYPE' => 'LIST',
		'VALUES' => array(
			'left' => GetMessage('IMAGE_POSITION_LEFT'),
			'right' => GetMessage('IMAGE_POSITION_RIGHT'),
		),
		'DEFAULT' => 'left',
	),
	'VIEW_TYPE' => array(
		'PARENT' => 'LIST_SETTINGS',
		'SORT' => 250,
		'NAME' => GetMessage('VIEW_TYPE_TITLE'),
		'TYPE' => 'LIST',
		'VALUES' => array(
			'table' => GetMessage('VIEW_TYPE_TABLE'),
			'list' => GetMessage('VIEW_TYPE_LIST'),
			'price' => GetMessage('VIEW_TYPE_PRICE'),
		),
		'DEFAULT' => 'left',
	),
	'LINE_ELEMENT_COUNT' => array(
		'PARENT' => 'LIST_SETTINGS',
		'SORT' => 700,
		'NAME' => GetMessage('T_LINE_ELEMENT_COUNT'),
		'TYPE' => 'STRING',
		'DEFAULT' => 3,
	),
	/*'SHOW_BRAND_DETAIL' => array(
		'PARENT' => 'DETAIL_SETTINGS',
		'SORT' => 600,
		'NAME' => GetMessage('T_SHOW_BRAND_DETAIL'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	),*/
	'SORT_PROP_DEFAULT' => array(
		'PARENT' => 'LIST_SETTINGS',
		'NAME' => GetMessage('T_SORT_PROP_DEFAULT'),
		'TYPE' => 'LIST',
		'VALUES' => $arPropertyDefaultSort,
	),
	'SORT_DIRECTION' => array(
		'PARENT' => 'LIST_SETTINGS',
		'NAME' => GetMessage('T_SORT_DIRECTION'),
		'TYPE' => 'LIST',
		'VALUES' => $arSortDirection
	),
	'SHOW_TOP_DESCRIPTION' => array(
		'PARENT' => 'LIST_SETTINGS',
		'NAME' => GetMessage('T_SHOW_TOP_DESCRIPTION'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	),
	"INCLUDE_SUBSECTIONS" => array(
		"PARENT" => "LIST_SETTINGS",
		"NAME" => GetMessage("CP_BC_INCLUDE_SUBSECTIONS"),
		"TYPE" => "LIST",
		"VALUES" => array(
			"Y" => GetMessage('CP_BC_INCLUDE_SUBSECTIONS_ALL'),
			"A" => GetMessage('CP_BC_INCLUDE_SUBSECTIONS_ACTIVE'),
			"N" => GetMessage('CP_BC_INCLUDE_SUBSECTIONS_NO'),
		),
		"DEFAULT" => "Y",
	),
	'SHOW_SECTION_PREVIEW_DESCRIPTION' => array(
		'PARENT' => 'LIST_SETTINGS',
		'SORT' => 700,
		'NAME' => GetMessage('T_SHOW_SECTION_PREVIEW_DESCRIPTION'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	),
	'SHOW_CHILD_SECTIONS' => array(
		'PARENT' => 'LIST_SETTINGS',
		'SORT' => 700,
		'NAME' => GetMessage('SHOW_CHILD_SECTIONS'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	),
	'SHOW_CHILD_ELEMENTS' => array(
		'PARENT' => 'LIST_SETTINGS',
		'SORT' => 700,
		'NAME' => GetMessage('SHOW_CHILD_ELEMENTS'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
		'REFRESH' => 'Y',
	),
	'USE_SHARE' => array(
		'PARENT' => 'DETAIL_SETTINGS',
		'SORT' => 600,
		'NAME' => GetMessage('USE_SHARE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	),
	'SHOW_ADDITIONAL_TAB' => array(
		'PARENT' => 'DETAIL_SETTINGS',
		'SORT' => 600,
		'NAME' => GetMessage('SHOW_ADDITIONAL_TAB'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	),
	'GALLERY_TYPE' => array(
		'PARENT' => 'DETAIL_SETTINGS',
		'SORT' => 600,
		'NAME' => GetMessage('GALLERY_TYPE'),
		'TYPE' => 'LIST',
		'VALUES' => $arGalleryType
	),
	'DETAIL_LINKED_TEMPLATE' => array(
		'PARENT' => 'DETAIL_SETTINGS',
		'SORT' => 600,
		'NAME' => GetMessage('DETAIL_LINKED_TEMPLATE'),
		'TYPE' => 'LIST',
		'VALUES' => array(
			"linked" => GetMessage('DETAIL_LINKED'),
			"table" => GetMessage('DETAIL_TABLE')
		)
	),
	'S_ASK_QUESTION' => array(
		'SORT' => 700,
		'NAME' => GetMessage('S_ASK_QUESTION'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'S_ORDER_SERVISE' => array(
		'SORT' => 701,
		'NAME' => GetMessage('S_ORDER_SERVISE'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'S_TO_ALL' => array(
		'SORT' => 701,
		'NAME' => GetMessage('S_TO_ALL'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'FORM_ID_ORDER_SERVISE' => array(
		'SORT' => 701,
		'NAME' => GetMessage('T_FORM_ID_ORDER_SERVISE'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'T_GALLERY' => array(
		'SORT' => 702,
		'NAME' => GetMessage('T_GALLERY'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'T_DOCS' => array(
		'SORT' => 703,
		'NAME' => GetMessage('T_DOCS'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'T_PROJECTS' => array(
		'SORT' => 704,
		'NAME' => GetMessage('T_PROJECTS'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'T_CHARACTERISTICS' => array(
		'SORT' => 705,
		'NAME' => GetMessage('T_CHARACTERISTICS'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'T_FAQ' => array(
		'SORT' => 706,
		'NAME' => GetMessage('T_FAQ'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	/*'T_ASK' => array(
		'SORT' => 706,
		'NAME' => GetMessage('T_ASK'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),*/
	'T_TARIF' => array(
		'SORT' => 706,
		'NAME' => GetMessage('T_TARIF'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'T_DESC' => array(
		'SORT' => 706,
		'NAME' => GetMessage('T_DESC'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	/*'T_DEV' => array(
		'SORT' => 706,
		'NAME' => GetMessage('T_DEV'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),*/
	'T_SERVICES' => array(
		'SORT' => 706,
		'NAME' => GetMessage('T_SERVICES'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'T_ITEMS' => array(
		'SORT' => 706,
		'NAME' => GetMessage('T_ITEMS'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'T_REVIEWS' => array(
		'SORT' => 706,
		'NAME' => GetMessage('T_REVIEWS'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'T_VIDEO' => array(
		'SORT' => 706,
		'NAME' => GetMessage('T_VIDEO'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'T_NEWS' => array(
		'SORT' => 706,
		'NAME' => GetMessage('T_NEWS'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'T_STAFF' => array(
		'SORT' => 706,
		'NAME' => GetMessage('T_STAFF'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'T_ARTICLES' => array(
		'SORT' => 706,
		'NAME' => GetMessage('T_ARTICLES'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'T_STUDY' => array(
		'SORT' => 706,
		'NAME' => GetMessage('T_STUDY'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'TAB_DOPS_NAME' => array(
		'SORT' => 706,
		'NAME' => GetMessage('TAB_DOPS_NAME'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'REVIEWS_IBLOCK_ID' => array(
		'SORT' => 704,
		'NAME' => GetMessage('REVIEWS_IBLOCK_ID'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'PROJECTS_IBLOCK_ID' => array(
		'SORT' => 704,
		'NAME' => GetMessage('PROJECTS_IBLOCK_ID'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'SERVICES_IBLOCK_ID' => array(
		'SORT' => 704,
		'NAME' => GetMessage('SERVICES_IBLOCK_ID'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'STAFF_IBLOCK_ID' => array(
		'SORT' => 704,
		'NAME' => GetMessage('STAFF_IBLOCK_ID'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'PARTNERS_IBLOCK_ID' => array(
		'SORT' => 704,
		'NAME' => GetMessage('PARTNERS_IBLOCK_ID'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'SALE_IBLOCK_ID' => array(
		'SORT' => 704,
		'NAME' => GetMessage('SALE_IBLOCK_ID'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'NEWS_IBLOCK_ID' => array(
		'SORT' => 704,
		'NAME' => GetMessage('NEWS_IBLOCK_ID'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'FAQ_IBLOCK_ID' => array(
		'SORT' => 704,
		'NAME' => GetMessage('FAQ_IBLOCK_ID'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'TARIF_IBLOCK_ID' => array(
		'SORT' => 704,
		'NAME' => GetMessage('TARIF_IBLOCK_ID'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'TIZERS_IBLOCK_ID' => array(
		'SORT' => 704,
		'NAME' => GetMessage('TIZERS_IBLOCK_ID'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'ARTICLES_IBLOCK_ID' => array(
		'SORT' => 704,
		'NAME' => GetMessage('ARTICLES_IBLOCK_ID'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'STUDY_IBLOCK_ID' => array(
		'SORT' => 704,
		'NAME' => GetMessage('STUDY_IBLOCK_ID'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
));

	$arTemplateParameters['SHOW_LANDING'] = array(
		'PARENT' => 'ADDITIONAL',
		'SORT' => 1,
		'NAME' => GetMessage('SHOW_LANDING'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
		'REFRESH' => 'Y',
	);
if ('Y' != $arCurrentValues['SHOW_LANDING'])
{
	$arTemplateParameters['LANDING_IBLOCK_ID'] = array(
		'SORT' => 1,
		'NAME' => GetMessage('LANDING_IBLOCK_ID_TITLE'),
		'TYPE' => 'TEXT',
		'PARENT' => 'ADDITIONAL',
		'DEFAULT' => '',
		'REFRESH' => 'N',
	);
	$arTemplateParameters['LANDING_TIZER_IBLOCK_ID'] = array(
		'SORT' => 1,
		'NAME' => GetMessage('LANDING_TIZER_IBLOCK_ID_TITLE'),
		'TYPE' => 'TEXT',
		'PARENT' => 'ADDITIONAL',
		'DEFAULT' => '',
		'REFRESH' => 'N',
	);
	$arTemplateParameters['LANDING'] = array(
		'SORT' => 1,
		'NAME' => GetMessage('LANDING_TITLE'),
		'TYPE' => 'TEXT',
		'PARENT' => 'ADDITIONAL',
		'DEFAULT' => GetMessage('LANDING_DEFAULT_TITLE'),
		'REFRESH' => 'N',
	);
	$arTemplateParameters['LANDING_SECTION_COUNT'] = array(
		'SORT' => 1,
		'NAME' => GetMessage('LANDING_SECTION_COUNT_TITLE'),
		'TYPE' => 'TEXT',
		'PARENT' => 'ADDITIONAL',
		'DEFAULT' => '20',
		'REFRESH' => 'N',
	);
	$arTemplateParameters['LANDING_SECTION_COUNT_VISIBLE'] = array(
		'SORT' => 1,
		'NAME' => GetMessage('LANDING_SECTION_COUNT_VISIBLE_TITLE'),
		'TYPE' => 'TEXT',
		'PARENT' => 'ADDITIONAL',
		'DEFAULT' => '20',
		'REFRESH' => 'N',
	);
}

if($arCurrentValues['SEF_MODE'] == 'Y'){
	$arTemplateParameters['FILTER_URL_TEMPLATE'] = array(
		'PARENT' => 'SEF_MODE',
		'SORT' => 500,
		'NAME' => GetMessage('FILTER_URL_TEMPLATE'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '#SECTION_CODE_PATH#/filter/#SMART_FILTER_PATH#/apply/',
	);
}
if($arCurrentValues['SHOW_CHILD_ELEMENTS'] == 'Y'){
	$arTemplateParameters['SHOW_ELEMENTS_IN_LAST_SECTION'] = array(
		'PARENT' => 'LIST_SETTINGS',
		'SORT' => 700,
		'NAME' => GetMessage('SHOW_ELEMENTS_IN_LAST_SECTION'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	);
}

if(\Bitrix\Main\ModuleManager::isModuleInstalled("highloadblock"))
{
	$arTemplateParameters['DETAIL_BRAND_USE'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('CP_BC_TPL_DETAIL_BRAND_USE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
		'REFRESH' => 'Y'
	);

	if (isset($arCurrentValues['DETAIL_BRAND_USE']) && 'Y' == $arCurrentValues['DETAIL_BRAND_USE'])
	{
		$arTemplateParameters['DETAIL_BRAND_PROP_CODE'] = array(
			'PARENT' => 'VISUAL',
			"NAME" => GetMessage("CP_BC_TPL_DETAIL_PROP_CODE"),
			"TYPE" => "LIST",
			"VALUES" => $arHighloadPropList,
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "Y"
		);
	}
}

$arTemplateParameters['DETAIL_USE_COMMENTS'] = array(
	'PARENT' => 'DETAIL_SETTINGS',
	'NAME' => GetMessage('CP_BC_TPL_DETAIL_USE_COMMENTS'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'N',
	'REFRESH' => 'Y'
);

if ('Y' == $arCurrentValues['DETAIL_USE_COMMENTS'])
{
	if (\Bitrix\Main\ModuleManager::isModuleInstalled("blog"))
	{
		$arTemplateParameters['DETAIL_BLOG_USE'] = array(
			'PARENT' => 'DETAIL_SETTINGS',
			'NAME' => GetMessage('CP_BC_TPL_DETAIL_BLOG_USE'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
			'REFRESH' => 'Y'
		);
		if (isset($arCurrentValues['DETAIL_BLOG_USE']) && $arCurrentValues['DETAIL_BLOG_USE'] == 'Y')
		{
			$arTemplateParameters['DETAIL_BLOG_URL'] = array(
				'PARENT' => 'DETAIL_SETTINGS',
				'NAME' => GetMessage('CP_BC_DETAIL_TPL_BLOG_URL'),
				'TYPE' => 'STRING',
				'DEFAULT' => 'catalog_comments'
			);
			$arTemplateParameters['COMMENTS_COUNT'] = array(
				'PARENT' => 'DETAIL_SETTINGS',
				'NAME' => GetMessage('T_COMMENTS_COUNT'),
				'TYPE' => 'STRING',
				'DEFAULT' => '5'
			);
			$arTemplateParameters['BLOG_TITLE'] = array(
				'PARENT' => 'DETAIL_SETTINGS',
				'NAME' => GetMessage('BLOCK_TITLE_TAB'),
				'TYPE' => 'STRING',
				'DEFAULT' => GetMessage('S_COMMENTS_VALUE')
			);
			$arTemplateParameters['DETAIL_BLOG_EMAIL_NOTIFY'] = array(
				'PARENT' => 'DETAIL_SETTINGS',
				'NAME' => GetMessage('CP_BC_TPL_DETAIL_BLOG_EMAIL_NOTIFY'),
				'TYPE' => 'CHECKBOX',
				'DEFAULT' => 'N'
			);
		}
	}

	$boolRus = false;
	$langBy = "id";
	$langOrder = "asc";
	$rsLangs = CLanguage::GetList($langBy, $langOrder, array('ID' => 'ru',"ACTIVE" => "Y"));
	if ($arLang = $rsLangs->Fetch())
	{
		$boolRus = true;
	}

	if ($boolRus)
	{
		$arTemplateParameters['DETAIL_VK_USE'] = array(
			'PARENT' => 'DETAIL_SETTINGS',
			'NAME' => GetMessage('CP_BC_TPL_DETAIL_VK_USE'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
			'REFRESH' => 'Y'
		);

		if (isset($arCurrentValues['DETAIL_VK_USE']) && 'Y' == $arCurrentValues['DETAIL_VK_USE'])
		{
			$arTemplateParameters['VK_TITLE'] = array(
				'PARENT' => 'DETAIL_SETTINGS',
				'NAME' => GetMessage('BLOCK_TITLE_TAB'),
				'TYPE' => 'STRING',
				'DEFAULT' => GetMessage('S_VK_VALUE')
			);
			$arTemplateParameters['DETAIL_VK_API_ID'] = array(
				'PARENT' => 'DETAIL_SETTINGS',
				'NAME' => GetMessage('CP_BC_TPL_DETAIL_VK_API_ID'),
				'TYPE' => 'STRING',
				'DEFAULT' => 'API_ID'
			);
		}
	}

	$arTemplateParameters['DETAIL_FB_USE'] = array(
		'PARENT' => 'DETAIL_SETTINGS',
		'NAME' => GetMessage('CP_BC_TPL_DETAIL_FB_USE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
		'REFRESH' => 'Y'
	);

	if (isset($arCurrentValues['DETAIL_FB_USE']) && 'Y' == $arCurrentValues['DETAIL_FB_USE'])
	{
		$arTemplateParameters['FB_TITLE'] = array(
			'PARENT' => 'DETAIL_SETTINGS',
			'NAME' => GetMessage('BLOCK_TITLE_TAB'),
			'TYPE' => 'STRING',
			'DEFAULT' => GetMessage('S_FB_VALUE')
		);
		$arTemplateParameters['DETAIL_FB_APP_ID'] = array(
			'PARENT' => 'DETAIL_SETTINGS',
			'NAME' => GetMessage('CP_BC_TPL_DETAIL_FB_APP_ID'),
			'TYPE' => 'STRING',
			'DEFAULT' => ''
		);
	}
}
$arTemplateParameters['LIST_PRODUCT_BLOCKS_ORDER'] = array(
	'PARENT' => 'DETAIL_SETTINGS',
	'NAME' => GetMessage('CP_BC_TPL_PRODUCT_BLOCKS_ORDER'),
	'TYPE' => 'CUSTOM',
	'JS_FILE' => CatalogSectionComponent::getSettingsScript('/bitrix/components/bitrix/catalog.section', 'dragdrop_order'),
	'JS_EVENT' => 'initDraggableOrderControl',
	'JS_DATA' => Json::encode(array(
		'tizers' => GetMessage('CP_BC_TPL_PRODUCT_BLOCK_TIZERS'),
		'sale' => GetMessage('CP_BC_TPL_PRODUCT_BLOCK_SALE'),
		'tab' => GetMessage('CP_BC_TPL_PRODUCT_BLOCK_TAB'),
		'gallery' => GetMessage('CP_BC_TPL_PRODUCT_BLOCK_GALLERY'),
		'reviews' => GetMessage('CP_BC_TPL_PRODUCT_BLOCK_REVIEWS'),
		'news' => GetMessage('CP_BC_TPL_PRODUCT_BLOCK_NEWS'),
		'services' => GetMessage('CP_BC_TPL_PRODUCT_BLOCK_SERVICES'),
		'study' => GetMessage('CP_BC_TPL_PRODUCT_BLOCK_STUDY'),
		'staff' => GetMessage('CP_BC_TPL_PRODUCT_BLOCK_STAFF'),
		'goods' => GetMessage('CP_BC_TPL_PRODUCT_BLOCK_GOODS'),
		'comments' => GetMessage('CP_BC_TPL_PRODUCT_BLOCK_COMMENTS'),
	)),
	'DEFAULT' => 'tizers,sale,tab,gallery,services,news,reviews,study,staff,goods,comments'
);
$arTemplateParameters['LIST_PRODUCT_BLOCKS_TAB_ORDER'] = array(
	'PARENT' => 'DETAIL_SETTINGS',
	'NAME' => GetMessage('CP_BC_TPL_PRODUCT_BLOCKS_TAB_ORDER'),
	'TYPE' => 'CUSTOM',
	'JS_FILE' => CatalogSectionComponent::getSettingsScript('/bitrix/components/bitrix/catalog.section', 'dragdrop_order'),
	'JS_EVENT' => 'initDraggableOrderControl',
	'JS_DATA' => Json::encode(array(
		'tarif' => GetMessage('CP_BC_TPL_PRODUCT_BLOCK_TARIF'),
		'desc' => GetMessage('CP_BC_TPL_PRODUCT_BLOCK_DESC'),
		'char' => GetMessage('CP_BC_TPL_PRODUCT_BLOCK_CHAR'),
		'projects' => GetMessage('CP_BC_TPL_PRODUCT_BLOCK_PROJECTS'),
		'faq' => GetMessage('CP_BC_TPL_PRODUCT_BLOCK_FAQ'),
		'articles' => GetMessage('CP_BC_TPL_PRODUCT_BLOCK_ARTICLES'),
		'docs' => GetMessage('CP_BC_TPL_PRODUCT_BLOCK_DOCS'),
		'video' => GetMessage('CP_BC_TPL_PRODUCT_BLOCK_VIDEO'),
		'dops' => GetMessage('CP_BC_TPL_PRODUCT_BLOCK_DOPS'),
	)),
	'DEFAULT' => 'tarif,desc,char,projects,articles,faq,docs,video,dops'
);
$arTemplateParameters['LIST_PRODUCT_BLOCKS_ALL_ORDER'] = array(
	'PARENT' => 'DETAIL_SETTINGS',
	'NAME' => GetMessage('CP_BC_TPL_PRODUCT_BLOCKS_ALL_ORDER'),
	'TYPE' => 'CUSTOM',
	'JS_FILE' => CatalogSectionComponent::getSettingsScript('/bitrix/components/bitrix/catalog.section', 'dragdrop_order'),
	'JS_EVENT' => 'initDraggableOrderControl',
	'JS_DATA' => Json::encode(array(
		'tizers' => GetMessage('CP_BC_TPL_PRODUCT_BLOCK_TIZERS'),
		'sale' => GetMessage('CP_BC_TPL_PRODUCT_BLOCK_SALE'),
		'gallery' => GetMessage('CP_BC_TPL_PRODUCT_BLOCK_GALLERY'),
		'services' => GetMessage('CP_BC_TPL_PRODUCT_BLOCK_SERVICES'),
		'tarif' => GetMessage('CP_BC_TPL_PRODUCT_BLOCK_TARIF'),
		'desc' => GetMessage('CP_BC_TPL_PRODUCT_BLOCK_DESC'),
		'char' => GetMessage('CP_BC_TPL_PRODUCT_BLOCK_CHAR'),
		'projects' => GetMessage('CP_BC_TPL_PRODUCT_BLOCK_PROJECTS'),
		'reviews' => GetMessage('CP_BC_TPL_PRODUCT_BLOCK_REVIEWS'),
		'articles' => GetMessage('CP_BC_TPL_PRODUCT_BLOCK_ARTICLES'),
		'study' => GetMessage('CP_BC_TPL_PRODUCT_BLOCK_STUDY'),
		'news' => GetMessage('CP_BC_TPL_PRODUCT_BLOCK_NEWS'),
		'faq' => GetMessage('CP_BC_TPL_PRODUCT_BLOCK_FAQ'),
		'docs' => GetMessage('CP_BC_TPL_PRODUCT_BLOCK_DOCS'),
		'video' => GetMessage('CP_BC_TPL_PRODUCT_BLOCK_VIDEO'),
		'staff' => GetMessage('CP_BC_TPL_PRODUCT_BLOCK_STAFF'),
		'goods' => GetMessage('CP_BC_TPL_PRODUCT_BLOCK_GOODS'),
		'dops' => GetMessage('CP_BC_TPL_PRODUCT_BLOCK_DOPS'),
		'comments' => GetMessage('CP_BC_TPL_PRODUCT_BLOCK_COMMENTS'),
	)),
	'DEFAULT' => 'tizers,sale,tarif,desc,char,projects,faq,docs,video,gallery,articles,study,news,dops,services,reviews,staff,goods,comments'
);
?>