<?
$sSectionName = "Металлообработка";

$rmServicesPath = parse_url((string)($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH);
$rmIsServicesRoot = rtrim((string)$rmServicesPath, '/') === '/services';

$arDirProperties = Array(
   "MENU" => $rmIsServicesRoot ? "N" : "Y",
   "MENU_SHOW_ELEMENTS" => "Y",
   "MENU_SHOW_SECTIONS" => "Y"
);

if ($rmIsServicesRoot) {
   $arDirProperties["HIDETITLE"] = "Y";
   $arDirProperties["FULLWIDTH"] = "Y";
}
?>
