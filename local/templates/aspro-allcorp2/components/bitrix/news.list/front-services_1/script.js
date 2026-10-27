$(document).ready(function(){
	if((BX.browser.IsIE9() && BX.browser.IsIE()) || BX.browser.IsMac())
	{
		$('.float-banners.v2 .items .item').sliceHeight({fixWidth: 1});
	}
});