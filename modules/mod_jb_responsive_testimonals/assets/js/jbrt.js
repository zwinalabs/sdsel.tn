


/**
 * On hover will display the quote
 *
 */
function showDefaultTestimonalInfo(elem){
	var $ = jQuery.noConflict();
	$('.jbt-avatar').removeClass('active');
	$('.jbt-quote-info').hide();
	var current_li =$(elem).data('li_id');
	var current_item =$(elem).data('quote_id');
	
	$('#'+current_li).addClass('active');
	$('#'+current_item).show();

}
