<?php
/**
 * @package J2Store
 * @subpackage plg_j2store_app_stockindate
 * @copyright Copyright (c)2015 JoomlaBuff - joomlabuff.org
 * @license GNU GPL v3 or later
 */
/**
 * ensure this file is being included by a parent file
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
?>
<div class="app_emailtofriend">
   <!-- Button HTML (to Trigger Modal) -->
   <a href="#emailFriendModal-<?php echo $vars->product->j2store_product_id;?>" class="app-emailtofriend-btn" data-toggle="modal"> <i class="icon icon-mail"></i> 
   	<?php echo  JText::_($vars->plugin_params->get('display_text','J2STORE_APP_EMAILTOFRIEND_MESSAGE_EMAIL_TO_FRIEND'));?>
   </a>
    
         <div class="emailtofriend-notification"></div>
          
            <!-- Modal HTML -->
            <div id="emailFriendModal-<?php echo $vars->product->j2store_product_id;?>"
               class="modal fade">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"
                           aria-hidden="true">&times;</button>
                        <h4 class="modal-title">
                        <!-- Modal title comes here -->
                           <?php echo JText::_($vars->plugin_params->get('modal_header','J2STORE_APP_EMAILTOFRIEND_MESSAGE_MODEL_TITLE'));?>
                        </h4>
                     </div>
                     <div class="modal-body">
                        <div class="form-horizontal"  id="emailtoFriendForm">
                           <input type="hidden" id="product_id" name="product_id"
                              value="<?php echo $vars->product->j2store_product_id;?>" />
                           <div class="jb-control-group">
                              <label class="control-label" for="sender_name">
                              <?php echo  JText::_('J2STORE_APP_EMAILTOFRIEND_YOUR_NAME');?>
                              </label>
                              <div class="controls">
                                 <input type="text" name="sender_name" id="sender_name"
                                    class="input" />
                              </div>
                           </div>
                           <div class="jb-control-group">
                              <label class="control-label" for="sender_email">
                              <?php echo  JText::_('J2STORE_APP_EMAILTOFRIEND_YOUR_EMAIL');?>
                              </label>
                              <div class="controls">
                                 <input type="text" id="sender_email" name="sender_email" 	class="input" />
                              </div>
                           </div>
                           <div class="jb-control-group">
                              <label class="control-label" for="receiver_email">
                              <?php echo  JText::_('J2STORE_APP_EMAILTOFRIEND_TO_EMAIL');?>
                              </label>
                              <div class="controls">
                                 <input type="text" name="receiver_email" id="receiver_email"
                                    class="input" />
                                  <div class="alert alert-info">
									<?php echo JText::_('J2STORE_APP_EMAILTOFRIEND_MULTIPLE_EMAIL_IDS_HELP');?>
								</div>
                              </div>
                             
                           </div>
                           <div class="jb-control-group">
                              <label class="control-label" for="message">
                              <?php echo  JText::_('J2STORE_APP_EMAILTOFRIEND_TO_MESSAGE');?>
                              </label>
                              <div class="controls">
                                 <textarea  name="message" id="message"></textarea>
                              </div>
                           </div>
                           
                           
                           <div class="jb-control-group">
                              <input id="emailtoFriendFormBtn" onclick="sendEmailtoFriend();" type="button"  
                                value="<?php echo JText::_('J2STORE_SEND');?>" class="btn btn-primary" />										
                              <button type="button" class="btn btn-inverse" data-dismiss="modal">
                                 <?php echo JText::_('JCANCEL');?>
                              </button>
                           </div>
                           
                        </div>
                     </div>
                     <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                        <?php echo JText::_('JCANCEL');?>
                        </button>
                     </div>
                  </div>
               </div>
            
   </div>
</div>



<script type="text/javascript">
 
/**
 * Method to validate the Recommend 
 * this product form and send email 
 */
function sendEmailtoFriend(){
	(function($){		 
	$(".j2error ,.j2success").remove();
	var	data  = { 
				'product_id' : $('#product_id').attr('value') ,				
				'sender_email': $('#sender_email').attr('value'),				
				'sender_name': $('#sender_name').attr('value'),
				'receiver_email' :$('#receiver_email').attr('value'),
				'message' :$('#message').attr('value'),
				 'option':'com_j2store',
				 'view':'apps',
				 'task' :'view',
				'id' : '<?php echo $vars->app_id ;?>',
				'appTask' : 'emailToMyFriend'
				};
	 
		$.ajax({
			url :'index.php',
			type:'post',
			data : data,
			dataType: 'json',
			success: function(json){
					$('.j2error').remove(); // remove old error messages				
				 	if(json['error']){
				 		$.each( json['error'], function( key, value ) {					 	 
							if (value) {
								$('#'+key).after('<br class="j2error" /><span class="j2error">' + value + '</span>');
							}
						});
				 		setTimeout(function() {
					 		$('.j2error').hide();
					 		
						},6000);
				 	}
				 	if(json['success']){
				 		$('#product_id').attr('value','') ;
					 	$('#sender_name').attr('value','') ;
					 	$('#sender_email').attr('value','') ;
					 	$('#receiver_email').attr('value','') ;
					 	$('#message').attr('value','') ;
				 		$('#emailFriendModal-<?php echo $vars->product->j2store_product_id;?>').modal('hide');
					 	$('.emailtofriend-notification').html('<p class="alert alert-success j2success">'+json['success']['msg']+'</p>');				 	
					 	
					 	setTimeout(function() {
					 		$('.emailtofriend-notification').hide();
					 		
						},6000);
					 	
				 	}

				 
			}

		});
		
	})(j2store.jQuery);
}
 
 
</script>
