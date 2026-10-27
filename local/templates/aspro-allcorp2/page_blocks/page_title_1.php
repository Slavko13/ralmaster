<?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/page-title-1.css');?>

<section class="page-top maxwidth-theme <?CAllcorp2::ShowPageProps('TITLE_CLASS');?>">	
	<div class="row">
		<div class="col-md-12 page-title-wrapper-v1">
			<?$APPLICATION->IncludeComponent("bitrix:breadcrumb", "corp", array(
				"START_FROM" => "0",
				"PATH" => "",
				"SITE_ID" => SITE_ID
				),
				false
			);?>
			<div class="page-top-main">
				<h1 id="pagetitle"><?$APPLICATION->ShowTitle(false)?></h1>
			</div>
		</div>
	</div>
</section>