<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
global $arTheme, $arRegion;
if($arRegion)
	$bPhone = ($arRegion['PHONES'] ? true : false);
else
	$bPhone = ((int)$arTheme['HEADER_PHONES'] ? true : false);
?>
<footer id="footer" class="compact">
	<div class="container">
		<div class="row bottom-middle">
			<div class="maxwidth-theme">
				<div class="col-md-3 col-sm-3 copy-block">
					<div class="copy blocks">
						<?$APPLICATION->IncludeFile(SITE_DIR."include/footer/copy.php", Array(), Array(
								"MODE" => "php",
								"NAME" => "Copyright",
								"TEMPLATE" => "include_area.php",
							)
						);?>
					</div>
					<div class="print-block blocks"><?=CAllcorp2::ShowPrintLink();?></div>
					<div id="bx-composite-banner" class="blocks"></div>
				</div>
				<div class="col-md-6 col-sm-6">
					<?if($bPhone || CAllcorp2::checkContentBlock(SITE_DIR.'include/header/site-address.php') || CAllcorp2::checkContentBlock(SITE_DIR.'include/footer/site-email.php', 'PROPERTY_EMAIL_VALUE')):?>
						<?$APPLICATION->IncludeFile(SITE_DIR."include/footer/contacts-title.php", array(), array(
								"MODE" => "html",
								"NAME" => "Title",
								"TEMPLATE" => "include_area.php",
							)
						);?>
						<div class="row info">
							<?$bEmail = CAllcorp2::checkContentBlock(SITE_DIR.'include/footer/site-email.php', 'PROPERTY_EMAIL_VALUE');
							$bAddr = CAllcorp2::checkContentBlock(SITE_DIR.'include/header/site-address.php');
							$break = false;?>
							<div class="col-md-6">
								<?if($bPhone || $bEmail):?>
									<?//check phone text?>
									<?if($bPhone):?>
										<div>
											<div class="blocks phones">
												<?CAllcorp2::ShowHeaderPhones('', 'Phone_black.svg');?>
											</div>
										</div>
									<?endif?>
									<?//check email text?>
									<?if($bEmail):?>
										<div>
											<?CAllcorp2::showEmail('email blocks');?>
										</div>
									<?endif?>
								<?elseif($bAddr):?>
									<?$break = true;?>
									<?CAllcorp2::showAddress('address blocks');?>
								<?endif?>
							</div>
							<?//check address text?>
							<?if($bAddr && !$break):?>
								<div class="col-md-6">
									<?CAllcorp2::showAddress('address blocks');?>
								</div>
							<?endif?>
						</div>
					<?endif;?>
				</div>
				<div class="col-md-3 col-sm-3">
					<div class="social-block">
						<?$APPLICATION->IncludeComponent(
							"aspro:social.info.allcorp2",
							".default",
							array(
								"CACHE_TYPE" => "A",
								"CACHE_TIME" => "3600000",
								"CACHE_GROUPS" => "N",
								"COMPONENT_TEMPLATE" => ".default",
								"SOCIAL_TITLE" => GetMessage("SOCIAL_TITLE")
							),
							false
						);?>
					</div>
				</div>
			</div>
		</div>
	</div>
</footer>