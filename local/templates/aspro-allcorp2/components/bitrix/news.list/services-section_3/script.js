$(document).ready(function(){
	if(BX.browser.IsIE9() && BX.browser.IsIE())
	{
		if($('.float-banners.v3 .items .item').length)
		{
			$('.float-banners.v3 .items .item').sliceHeight();
		}
	}
})