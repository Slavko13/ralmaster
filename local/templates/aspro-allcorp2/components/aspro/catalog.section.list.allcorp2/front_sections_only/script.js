$(document).ready(function(){
	setTimeout(function(){
		if(BX.browser.IsIE9() && BX.browser.IsIE())
		{
			$('.sections_wrapper .items .item').sliceHeight({'fixWidth':1});
		}
	},100);
})