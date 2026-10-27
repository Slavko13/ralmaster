<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
	<?$APPLICATION->IncludeComponent(
		"bitrix:map.google.view",
		"map",
		array(
			"INIT_MAP_TYPE" => "ROADMAP",
			"MAP_DATA" => "a:4:{s:10:\"google_lat\";d:55.754619520794115;s:10:\"google_lon\";d:37.62022412333155;s:12:\"google_scale\";i:15;s:10:\"PLACEMARKS\";a:1:{i:0;a:3:{s:3:\"LON\";d:37.620438700053;s:3:\"LAT\";d:55.753445723095;s:4:\"TEXT\";s:10:\"Наша фирма\";}}}",
			"MAP_WIDTH" => "100%",
			"MAP_HEIGHT" => "500",
			"KEY" => "AKk9BlwBAAAAcf5CSgMAHyfAq9knnHW9nsNrwnKOBpJ8-FUAAAAAAAAAAABE8lP1ifeROCbNOEGuF0oRi1P0xQ==",
			"CONTROLS" => array(
				0 => "SMALL_ZOOM_CONTROL",
				1 => "TYPECONTROL",
				2 => "SCALELINE",
			),
			"OPTIONS" => array(
				0 => "ENABLE_DBLCLICK_SMALL_ZOOM_CONTROL",
				1 => "ENABLE_DRAGGING",
			),
			"MAP_ID" => "",
			"API_KEY" => \Bitrix\Main\Config\Option::get("fileman","google_map_api_key",""),
			"COMPONENT_TEMPLATE" => ".default"
		),
		false
	); ?>