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
<div class="modjbvt-block <?php echo $moduleclass_sfx;?>">
	<div class="row row-fluid">
		<div class="span12 col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<!-- required for floating -->
			<!-- Nav tabs -->
			<ul class="nav nav-pills nav-stacked nav-tabs">
				<!-- 'tabs-right' for right tabs -->  
				  <?php foreach($list as $key => $item):?>
				  <?php $class="jbvt-hlist"; 
				   $registry = new JRegistry;
					$registry->loadString($item->attribs);	
			 		$content_menuid  = isset($registry['menuid']) ? $registry['menuid'] : '';  
			 	  
			 	 			 		
			 	    if($content_menuid == $current_menu_id){
				   	 $class ='active';			 
				   	}?>
				  <?php // if($key == 1):?>
				  <?php // $class =' active';?>
				  <?php // endif;?>
				    <li class="jbvt-hlist brand<?php echo $class;?>">
				    	<a href="#jbvt_<?php echo $module->id;?>_<?php echo $item->alias; ?>" data-toggle="tab">
					   	 <?php echo $item->title; ?>
					    </a>
					</li>				     
				    <?php endforeach;?>
				  </ul>
				  <!-- Tab panes -->
			<div class="tab-content">    
		     <?php foreach($list as $key => $item):?>
			       <?php   $registry = new JRegistry;
					$registry->loadString($item->attribs);	
			 		$content_menuid  = isset($registry['menuid']) ? $registry['menuid'] : '';  
			 	  
			 	 			 		
			 	    if($content_menuid == $current_menu_id){
				   	 $class ='active';			 
				   	}?>
			 
		      <div class="tab-pane <?php echo $class?>" id="jbvt_<?php echo $module->id;?>_<?php echo $item->alias; ?>">
		      	<?php require JModuleHelper::getLayoutPath('mod_jbvertical_tabs', '_item'); ?>        
		   	 </div>
     <?php endforeach;?> 
     </div>
		</div>
	</div>
</div>



