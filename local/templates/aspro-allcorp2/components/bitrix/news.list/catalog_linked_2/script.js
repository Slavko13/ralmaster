$(document).ready(function(){
	setBasketItemsClasses();

	//$('.table.linked .item .title').sliceHeight({sliceEqualLength: true});
	//$('.table.linked .item .cont').sliceHeight({autoHeightBlock: '.cont_inner', classNull: '.props_wrapper'});
	// $('.table.linked .item .slice_price').sliceHeight({sliceEqualLength: true});
	//$('.table.linked .item').sliceHeight({classNull: '.desktop-wrapper', autoHeightBlock: '.cont_inner', callback: setHoverHeight, sliceEqualLength: true});
})

setHoverHeight = function(){
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