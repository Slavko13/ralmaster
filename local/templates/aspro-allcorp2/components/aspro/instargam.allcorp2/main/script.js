$(document).ready(function() {
	$.ajax({
		url: arAllcorp2Options['SITE_DIR']+'include/mainpage/comp_instagramm.php',
		data: {'AJAX_REQUEST_INSTAGRAM': 'Y', 'SHOW_INSTAGRAM': arAllcorp2Options['THEME']['INSTAGRAMM_INDEX']},
		type: 'POST',
		success: function(html){
			$('.instagram_ajax').html(html).addClass('loaded');
			$('.instagram_ajax .instagram .item').sliceHeight();
			InitFlexSlider();
			var eventdata = {action:'instagrammLoaded'};
			BX.onCustomEvent('onCompleteAction', [eventdata]);
			$('.instagram_ajax .instagram .item .desc').mCustomScrollbar({setTop: 0});
		}
	});
});