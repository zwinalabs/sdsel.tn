<?php
/**  
 * @package mod_jbvertical_tabs
 * @copyright Copyright (C) 2015 JoomlaBuff http://joomlabuff.com. All rights reserved.
 * @license GNU General Public License version 2 or later; see LICENSE.txt
 * @websites http://joomlabuff.com
 * @support Forum - http://joomlabuff.com/support
 */
defined ( '_JEXEC' ) or die ();


?>
<div class="modjbvt-block single-service-page <?php echo $moduleclass_sfx;?>">
	<div class="row">
		<div class="span3 col-xs-12 col-sm-3 col-md-3 col-lg-3"> 
			<div class="service-link-widget tab_nav_holder">	
			<ul class=" nav nav-list nav-pills nav-stacked tabs-left">
				 <!--  tabs - right for right tabs -->  
				  <?php foreach($list as $key => $item):
				    $registry = new JRegistry;
					$registry->loadString($item->attribs);	
			 		$content_menuid  = isset($registry['menuid']) ? $registry['menuid'] : '';  
			 	    $class=""; 
			 	 			 		
			 	    if($content_menuid == $current_menu_id){
				   	 $class ='active';			 
				   	}?>
				   	 <?php if($key ==0 && $content_menuid == $current_menu_id ):?>
				    <li class="jbvt-vlist brand active">
						<a href="#jbvt_<?php echo $module->id;?>_<?php echo $item->alias; ?>" data-toggle="tab">
							<?php echo $item->title; ?>
						</a>
					</li>	
					<?php else:?>
					<li class="jbvt-vlist brand <?php echo $class;?>">
						<a href="#jbvt_<?php echo $module->id;?>_<?php echo $item->alias; ?>" data-toggle="tab">
							<?php echo $item->title; ?>
						</a>
					</li>
					<?php endif;?>			
					     
				    <?php endforeach;?>
	  		</ul>
	  		</div>
		</div>
		<div class="span8 col-xs-12 col-sm-9 col-md-9 col-lg-9">
			<!-- Tab panes -->
			<div class="tab-content">    
			     <?php foreach($list as $key => $item):  
				    $registry = new JRegistry;
					$registry->loadString($item->attribs);	
			 		$content_menuid  = isset($registry['menuid']) ? $registry['menuid'] : '';  
			 		$class="";
		 
			 		if($content_menuid == $current_menu_id){
					  	$class='active'; 
			 		};?>
			 		 <?php if($key ==0 && $content_menuid == $current_menu_id ):?>
				      <div class="tab-pane active" id="jbvt_<?php echo $module->id;?>_<?php echo $item->alias; ?>">
				      	<?php require JModuleHelper::getLayoutPath('mod_jbvertical_tabs', '_item'); ?>        
				   	 </div>
				    <?php else:?>
				   	 <div class="tab-pane <?php echo $class;?>" id="jbvt_<?php echo $module->id;?>_<?php echo $item->alias; ?>">
				      	<?php require JModuleHelper::getLayoutPath('mod_jbvertical_tabs', '_item'); ?>        
				   	 </div>
				   	 <?php endif;?>
			    <?php endforeach;?> 
	     	</div>
		</div>
	</div>
</div>

