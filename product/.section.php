<?
$sSectionName = "Продукция";

$rmProductPath = parse_url((string)($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH);
$rmIsProductRoot = rtrim((string)$rmProductPath, '/') === '/product';

$arDirProperties = Array(
   "MENU" => $rmIsProductRoot ? "N" : "Y",
   "MENU_SHOW_ELEMENTS" => "N",
   "MENU_SHOW_SECTIONS" => "Y"
);

if ($rmIsProductRoot) {
   $arDirProperties["HIDETITLE"] = "Y";
   $arDirProperties["FULLWIDTH"] = "Y";
}
?>
