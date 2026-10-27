$(document).ready(function(){
	if(BX.browser.IsIE9() && BX.browser.IsIE())
	{
		$('.news_block.portfolio .items').each(function(){
			if($(this).find('.s_1').length || $(this).find('.s_2').length)
			{
				var item_block = $(this).find('.item.sliced:last');
				$(this).find('.item.sliced').sliceHeight({item: item_block});
			}
			else
			{
				$(this).find('.item').sliceHeight({callback: setCustomHeight});
			}
		})
	}
});

setCustomHeight = function(){
	$('.news_block .items .custom > .item').sliceHeight();
}