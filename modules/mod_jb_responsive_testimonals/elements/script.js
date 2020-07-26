function addJBMedia(){
	
	var $ = jQuery.noConflict();

		//increament the
	    //var clone = image_div.clone();
	    
	    var counter = $( ".jbmedia ").length;
	    counter++;
	    console.log(counter);
	    var clone = $( ".jbmedia:first ").clone();
		//need to change the input name
		clone.find('input').each(function(){
			$(this).attr("name", jQuery(this).attr("name").replace(jQuery(this).attr("name").match(/\[[0-9]+\]/), "["+counter+"]"));
	    	$(this).attr("value",'');
	    	//$(this).removeAttr("id",'');
	 		$(this).attr("id",'jform_params_input_jbmedia_'+jQuery(this).data("input")+'_'+counter);
		});
		
		clone.find('textarea').each(function(){
			$(this).attr("name", jQuery(this).attr("name").replace(jQuery(this).attr("name").match(/\[[0-9]+\]/), "["+counter+"]"));
	    	$(this).attr("value",'');
	    	//$(this).removeAttr("id",'');
	 		$(this).attr("id",'jform_params_input_jbmedia_'+jQuery(this).data("input")+'_'+counter);
		});
		
	    clone.find('.jbmedia-media').each(function(){
	    	$(this).attr("name", jQuery(this).attr("name").replace(jQuery(this).attr("name").match(/\[[0-9]+\]/), "["+counter+"]"));
	    	$(this).attr("value",'');
	    	$(this).removeAttr("id",'');
	 		$(this).attr("id",'jform_params_input_jbmedia_'+jQuery(this).data("input")+'_'+counter);
	 		$(this).attr("image_id",'input-additional-image-'+counter);
		  });
	    
		clone.find('.jbmedia-btn').each(function(){
			$(this).attr('href','index.php?option=com_media&view=images&tmpl=component&asset=1&author=673&fieldid=jform_params_input_jbmedia_image_'+counter+'&folder=');			
		});
		
		clone.find('.jbmedia-remove-btn').each(function(){
		    var removeId ="jform_params_input_jbmedia_image_"+counter;
		    // data-image_input
		    $(this).attr("data-image_input",'jform_params_input_jbmedia_image_'+counter);
		    var onclick = "jInsertFieldValue('','"+$(this).data('image_input') + "')";
			$(this).attr('onclick',onclick +';return false;');
		})

		 //to chang label id
		// var new_html = image_div.before(clone);

			//now it is placed just of the image div so remove the element
		 var processed_html =  clone.remove();

		 //get the newly added tbody and insert after the additional-image-0
		  $(processed_html).appendTo($('.jbmedia-main-wrapper'));

		// intialize squeeze box again for edit button to work
		window.parent.SqueezeBox.initialize({});
		window.parent.SqueezeBox.assign($$('a.modal'), {
			parse: 'rel'
		});

}
jQuery(document).ready(function(){
	
	jQuery('.jbmedia-main-wrapper').closest('.controls').removeClass('controls');
});

function deleteJBMedia(element){	 
	jQuery(element).closest('.jbmedia').remove();
}