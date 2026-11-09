<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?
global $USER;
$backUrl = ralmaster_safe_local_path($_REQUEST['backurl'] ?? '', SITE_DIR.'cabinet/');

/*if($_GET["auth_service_error"]){
	LocalRedirect(SITE_DIR.'cabinet/');
}*/
if(!$USER->IsAuthorized()){?>
	<?if(isset($_REQUEST['backurl']) && $_REQUEST['backurl']) // fix ajax url
	{
		if($backUrl != $_SERVER['REQUEST_URI'])
		{
			$_SERVER['QUERY_STRING'] = '';
			$_SERVER['REQUEST_URI'] = $backUrl;
			//$APPLICATION->reinitPath();
			$APPLICATION->sDocPath2 = GetPagePath(false, true);
      		$APPLICATION->sDirPath = GetDirPath($APPLICATION->sDocPath2);
		}
	}?>
	<div id="wrap_ajax_auth" class="form popup">
		<div class="form-header">
			<div class="text"><div class="title"><?=\Bitrix\Main\Localization\Loc::getMessage('AUTHORIZE_TITLE');?></div></div>
		</div>
		<?
		$APPLICATION->IncludeComponent(
			"bitrix:system.auth.form",
			"main",
			Array(
				"REGISTER_URL" => SITE_DIR."cabinet/registration/?register=yes",
				"PROFILE_URL" => SITE_DIR."cabinet/",
				"FORGOT_PASSWORD_URL" => SITE_DIR."cabinet/forgot-password/?forgot-password=yes",
				"AUTH_URL" => SITE_DIR."cabinet/",
				"SHOW_ERRORS" => "Y",
				"POPUP_AUTH" => "Y",
				"AJAX_MODE" => "Y",
				"BACKURL" => ((isset($_REQUEST['backurl']) && $_REQUEST['backurl']) ? $backUrl : "")
			)
		);?>
	</div>
<?}
elseif(isset($_REQUEST['backurl']) && is_string($_REQUEST['backurl']) && $_REQUEST['backurl'] !== ''){
	LocalRedirect($backUrl);
}
else{
	$referer = (string)($_SERVER['HTTP_REFERER'] ?? '');
	if(strpos($referer, SITE_DIR.'cabinet/') === false && strpos($referer, SITE_DIR.'ajax/form.php') === false){
		$APPLICATION->ShowHead();
		?>
		<script>
			jsAjaxUtil.ShowLocalWaitWindow( 'id', 'wrap_ajax_auth', true );
			BX.reload(false)
		</script>
		<?
	}
	else{
		LocalRedirect(SITE_DIR.'cabinet/');
	}
}
