<?if($iCountElement):?>
	<!-- noindex -->
	<div class="row filters-wrap">
		<?
		if($_SESSION['UF_VIEWTYPE_LANDING_'.$arParams['IBLOCK_ID']] === NULL){
			$arUserFieldViewType = CUserTypeEntity::GetList(array(), array('ENTITY_ID' => 'IBLOCK_'.$arParams['IBLOCK_ID'].'_SECTION', 'FIELD_NAME' => 'UF_VIEWTYPE'))->Fetch();
			$resUserFieldViewTypeEnum = CUserFieldEnum::GetList(array(), array('USER_FIELD_ID' => $arUserFieldViewType['ID']));
			while($arUserFieldViewTypeEnum = $resUserFieldViewTypeEnum->GetNext()){
				$_SESSION['UF_VIEWTYPE_LANDING_'.$arParams['IBLOCK_ID']][$arUserFieldViewTypeEnum['ID']] = $arUserFieldViewTypeEnum['XML_ID'];
			}
		}
		
		$sort_default = $arParams['SORT_PROP_DEFAULT'] ? $arParams['SORT_PROP_DEFAULT'] : 'name';
		$order_default = $arParams['SORT_DIRECTION'] ? $arParams['SORT_DIRECTION'] : 'asc';
		$arPropertySortDefault = array('name', 'sort');
		
		$arAvailableSort = array(
			'name' => array(
				'SORT' => 'NAME',
				'ORDER_VALUES' => array(
					'asc' => GetMessage('sort_title').GetMessage('sort_name_asc'),
					'desc' => GetMessage('sort_title').GetMessage('sort_name_desc'),
				),
			),
			'sort' => array(
				'SORT' => 'SORT',
				'ORDER_VALUES' => array(
					'asc' => GetMessage('sort_title').GetMessage('sort_sort', array('#ORDER#' => GetMessage('sort_prop_asc'))),
					'desc' => GetMessage('sort_title').GetMessage('sort_sort', array('#ORDER#' => GetMessage('sort_prop_desc'))),
				)
			),
		);
		
		
		foreach($arAvailableSort as $prop => $arProp){
			if(!in_array($prop, $arParams['SORT_PROP']) && $sort_default !== $prop){
				unset($arAvailableSort[$prop]);
			}
		}

		if($arParams['SORT_PROP']){
			if(!isset($_SESSION[$arParams['IBLOCK_ID'].md5(serialize((array)$arParams['SORT_PROP']))])){
				foreach($arParams['SORT_PROP'] as $prop){
					if(!isset($arAvailableSort[$prop])){
						$dbRes = CIBlockProperty::GetList(array(), array('ACTIVE' => 'Y', 'IBLOCK_ID' => $arParams['IBLOCK_ID'], 'CODE' => $prop));
						while($arPropperty = $dbRes->Fetch()){
							$arAvailableSort[$prop] = array(
								'SORT' => 'PROPERTY_'.$prop,
								'ORDER_VALUES' => array(),
							);

							if($prop == 'PRICE' || $prop == 'FILTER_PRICE'){
								$arAvailableSort[$prop]['ORDER_VALUES']['asc'] = GetMessage('sort_title').GetMessage('sort_PRICE_asc');
								$arAvailableSort[$prop]['ORDER_VALUES']['desc'] = GetMessage('sort_title').GetMessage('sort_PRICE_desc');
							}
							else{
								$arAvailableSort[$prop]['ORDER_VALUES']['asc'] = GetMessage('sort_title_property', array('#CODE#' => $arPropperty['NAME'], '#ORDER#' => GetMessage('sort_prop_asc')));
								$arAvailableSort[$prop]['ORDER_VALUES']['desc'] = GetMessage('sort_title_property', array('#CODE#' => $arPropperty['NAME'], '#ORDER#' => GetMessage('sort_prop_desc')));
							}
						}
					}
				}
				$_SESSION[$arParams['IBLOCK_ID'].md5(serialize((array)$arParams['SORT_PROP']))] = $arAvailableSort;
			}
			else{
				$arAvailableSort = $_SESSION[$arParams['IBLOCK_ID'].md5(serialize((array)$arParams['SORT_PROP']))];
			}
		}

		if(array_key_exists('display', $_REQUEST) && !empty($_REQUEST['display'])){
			setcookie('catalogViewModeLanding', $_REQUEST['display'], 0, SITE_DIR);
			$_COOKIE['catalogViewModeLanding'] = $_REQUEST['display'];
		}
		if(array_key_exists('sort', $_REQUEST) && !empty($_REQUEST['sort'])){
			setcookie('catalogSortLanding', $_REQUEST['sort'], 0, SITE_DIR);
			$_COOKIE['catalogSortLanding'] = $_REQUEST['sort'];
		}
		if(array_key_exists('order', $_REQUEST) && !empty($_REQUEST['order'])){
			setcookie('catalogOrderLanding', $_REQUEST['order'], 0, SITE_DIR);
			$_COOKIE['catalogOrderLanding'] = $_REQUEST['order'];
		}
		if(array_key_exists('show', $_REQUEST) && !empty($_REQUEST['show'])){
			setcookie('catalogPageElementCount', $_REQUEST['show'], 0, SITE_DIR);
			$_COOKIE['catalogPageElementCount'] = $_REQUEST['show'];
		}

		if(isset($_COOKIE['catalogViewModeLanding']) && $_COOKIE['catalogViewModeLanding'])
		{
			$display = $_COOKIE['catalogViewModeLanding'];
		}
		else
		{
			if($arSection['UF_VIEWTYPE'] && isset($_SESSION['UF_VIEWTYPE_LANDING_'.$arParams['IBLOCK_ID']][$arSection['UF_VIEWTYPE']]))
				$display = $_SESSION['UF_VIEWTYPE_LANDING_'.$arParams['IBLOCK_ID']][$arSection['UF_VIEWTYPE']];
			else
				$display = $arParams['VIEW_TYPE'];
		}
		
		$show = !empty($_COOKIE['catalogPageElementCount']) ? $_COOKIE['catalogPageElementCount'] : $arParams['PAGE_ELEMENT_COUNT'];
		$sort = !empty($_COOKIE['catalogSortLanding']) ? $_COOKIE['catalogSortLanding'] : $sort_default;
		$order = !empty($_COOKIE['catalogOrderLanding']) ? $_COOKIE['catalogOrderLanding'] : $order_default;
		?>
		<div class="col-md-7 col-sm-5 ordering-wrap">
			<div class="filter-action"><span class=""><?=CAllcorp2::showIconSvg('filter', SITE_TEMPLATE_PATH.'/images/svg/Filter_black.svg');?></span></div>
			<div class="select-outer black">
				<select class="sort">
					<?foreach($arAvailableSort as $newSort => $arSort):?>
						<?if(is_array($arSort['ORDER_VALUES'])):?>
							<?foreach($arSort['ORDER_VALUES'] as $newOrder => $sortTitle):?>
								<?$selected = ($sort == $newSort && $order == $newOrder);?>
								<option <?=($selected ? "selected='selected'" : "")?>  value="<?=$APPLICATION->GetCurPageParam('sort='.$newSort.'&order='.$newOrder, array('sort', 'order'))?>" class="ordering"><?=$sortTitle?></option>
							<?endforeach;?>
						<?endif;?>
					<?endforeach;?>
				</select>
				<i class="fa fa-angle-down"></i>
			</div>
		</div>
		<div class="col-md-5 col-sm-7 display-type pull-right text-right">
			<a rel="nofollow" href="<?=$APPLICATION->GetCurPageParam('display=table', array('display'))?>" class="view-button view-tiles <?=$display == 'table' ? 'cur' : '';?>" title="<?=GetMessage('T_LIST_VIEW');?>">
				<?=CAllcorp2::showIconSvg('tiles', SITE_TEMPLATE_PATH.'/images/svg/View_tiles.svg');?>
			</a>
			<a rel="nofollow" href="<?=$APPLICATION->GetCurPageParam('display=list', array('display'))?>" class="view-button view-list <?=$display == 'list' ? 'cur' : '';?>"  title="<?=GetMessage('T_TABLE_VIEW');?>">
				<?=CAllcorp2::showIconSvg('list', SITE_TEMPLATE_PATH.'/images/svg/View_list.svg');?>
			</a>
			<a rel="nofollow" href="<?=$APPLICATION->GetCurPageParam('display=price', array('display'))?>" class="view-button view-price <?=$display == 'price' ? 'cur' : '';?>" title="<?=GetMessage('T_PRICE_VIEW');?>">
				<?=CAllcorp2::showIconSvg('price', SITE_TEMPLATE_PATH.'/images/svg/View_price.svg');?>
			</a>
		</div>
	</div>
	<!-- /noindex -->
<?endif;?>