<?$APPLICATION->IncludeComponent(
"bitrix:map.google.view", 
"front_map", 
array(
    "INIT_MAP_TYPE" => "ROADMAP",
    "MAP_DATA" => "a:4:{s:10:\"google_lat\";d:55.7289429754008;s:10:\"google_lon\";d:37.61089089256047;s:12:\"google_scale\";i:14;s:10:\"PLACEMARKS\";a:1:{i:0;a:3:{s:3:\"LON\";d:37.612693337019;s:3:\"LAT\";d:55.727562544985;s:4:\"TEXT\";s:7:\"Company\";}}}",
    "MAP_HEIGHT" => "500",
    "MAP_ID" => "",
    "MAP_WIDTH" => "100%",
    "CONTROLS" => array(
    ),
    "OPTIONS" => array(
        0 => "ENABLE_DBLCLICK_ZOOM",
        1 => "ENABLE_DRAGGING",
    ),
    "API_KEY" => \Bitrix\Main\Config\Option::get("fileman","google_map_api_key",""),
    "COMPONENT_TEMPLATE" => "front_map"
),
false
);