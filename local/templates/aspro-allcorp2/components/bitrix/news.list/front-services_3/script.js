$(document).ready(function(){
	if((BX.browser.IsIE9() && BX.browser.IsIE()) || BX.browser.IsMac())
	{
		$('.float-banners.v3 .items .item').sliceHeight();
	}
});