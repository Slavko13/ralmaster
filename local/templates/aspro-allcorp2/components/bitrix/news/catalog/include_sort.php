<?if($itemsCnt):?>
	<!-- noindex -->
	<div class="row filters-wrap">
		<?
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
					if($prop == 'PRICE' && !$bFilterPrice)
						$prop = 'FILTER_PRICE';
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

		if(array_key_exists('sort', $_REQUEST) && !empty($_REQUEST['sort'])){
			setcookie('catalogSort', $_REQUEST['sort'], 0, SITE_DIR);
			$_COOKIE['catalogSort'] = $_REQUEST['sort'];
		}
		if(array_key_exists('order', $_REQUEST) && !empty($_REQUEST['order'])){
			setcookie('catalogOrder', $_REQUEST['order'], 0, SITE_DIR);
			$_COOKIE['catalogOrder'] = $_REQUEST['order'];
		}
		if(array_key_exists('show', $_REQUEST) && !empty($_REQUEST['show'])){
			setcookie('catalogPageElementCount', $_REQUEST['show'], 0, SITE_DIR);
			$_COOKIE['catalogPageElementCount'] = $_REQUEST['show'];
		}

		// Редизайн использует один предсказуемый плиточный вид на всех устройствах.
		$display = 'table';
		
		$show = !empty($_COOKIE['catalogPageElementCount']) ? $_COOKIE['catalogPageElementCount'] : $arParams['PAGE_ELEMENT_COUNT'];
		$sort = !empty($_COOKIE['catalogSort']) ? $_COOKIE['catalogSort'] : $sort_default;
		$order = !empty($_COOKIE['catalogOrder']) ? $_COOKIE['catalogOrder'] : $order_default;
		?>
		<div class="col-md-12 col-sm-12 ordering-wrap">
			<?if($GLOBALS['SHOW_SMART_FILTER']):?>
				<div class="filter-action"><span class=""><?=CAllcorp2::showIconSvg('filter', SITE_TEMPLATE_PATH.'/images/svg/Filter_black.svg');?></span></div>
			<?endif;?>
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
	</div>
	<!-- /noindex -->
<?endif;?>
