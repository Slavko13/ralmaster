<?$APPLICATION->IncludeComponent(
	"bitrix:map.yandex.view", 
	"front_map", 
	array(
		"INIT_MAP_TYPE" => "MAP",
		"MAP_DATA" => "a:4:{s:10:\"yandex_lat\";d:55.54735299304102;s:10:\"yandex_lon\";d:37.77275753632931;s:12:\"yandex_scale\";i:18;s:10:\"PLACEMARKS\";a:2:{i:0;a:3:{s:3:\"LON\";d:37.54250395520052;s:3:\"LAT\";d:55.43550555732687;s:4:\"TEXT\";s:39:\"Центральный офис###RN###\";}i:1;a:3:{s:3:\"LON\";d:37.77276494554863;s:3:\"LAT\";d:55.54752317588656;s:4:\"TEXT\";s:24:\"Производство\";}}}",
		"MAP_HEIGHT" => "500",
		"MAP_ID" => "",
		"MAP_WIDTH" => "100%",
		"CONTROLS" => array(
			0 => "ZOOM",
			1 => "SMALLZOOM",
			2 => "MINIMAP",
			3 => "TYPECONTROL",
			4 => "SCALELINE",
		),
		"OPTIONS" => array(
			0 => "ENABLE_DBLCLICK_ZOOM",
			1 => "ENABLE_DRAGGING",
		),
		"API_KEY" => \Bitrix\Main\Config\Option::get("fileman","yandex_map_api_key",""),
		"COMPONENT_TEMPLATE" => "front_map"
	),
	false
);?>