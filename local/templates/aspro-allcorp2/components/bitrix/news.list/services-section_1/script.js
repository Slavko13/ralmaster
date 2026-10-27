$(document).ready(function(){
	if(BX.browser.IsIE9() && BX.browser.IsIE())
	{
		if($('.catalog .item.slice-item').length)
		{
			$('.catalog .item.slice-item').sliceHeight({'fixWidth':1});
		}
	}
})