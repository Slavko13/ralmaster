<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
//define global template name for body class
global $bannerTemplate, $bBigBannersIndexClass;
$bannerTemplate = $this->__templateName;
if($bBigBannersIndexClass)
	$bannerTemplate = '';
?>