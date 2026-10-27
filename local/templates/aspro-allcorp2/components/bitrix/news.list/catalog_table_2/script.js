$(document).ready(function(){
	setBasketItemsClasses();
});

startActions = function(){
	setBasketItemsClasses();
	setHoverHeight();
	//$('body .catalog.item-views.table.catalog_table_2 .item .title').sliceHeight();
	//$('body .catalog.item-views.table .item .cont').sliceHeight({autoHeightBlock: '.cont_inner', classNull: '.props_wrapper'});
	//$('body .catalog.item-views.table.catalog_table_2 .item .slice_price').sliceHeight();
	//$('body .catalog.item-views.table.catalog_table_2 .item').sliceHeight({classNull: '.desktop-wrapper', autoHeightBlock: '.cont_inner', callback: setHoverHeight});
}

setHoverHeight = function(el){
	$('.catalog_table_2 .item').each(function(){
		var _this = $(this);
			// price_pos = _this.find('.inner-wrap > .text > .foot').position(),
			// top_wrapper_padding = parseInt(_this.find('.inner-wrap').css('padding-top')),
			// top_container_padding = parseInt(_this.find('.inner-wrap > .text').css('padding-top'));
		_this.parent().addClass('sliced');
		_this.addClass('sliced');
		//_this.find('.cont_inner').height(price_pos.top-top_wrapper_padding);
	})
}

BX.addCustomEvent('onWindowResize', function(eventdata) {
	ignoreResize.push(true);
	setHoverHeight();
	ignoreResize.pop();
});