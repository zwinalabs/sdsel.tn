<?php
/**  
 * @package mod_jbaccordian
 * @copyright Copyright (C) 2015 JoomlaBuff http://joomlabuff.com. All rights reserved.
 * @license GNU General Public License version 2 or later; see LICENSE.txt
 * @websites http://joomlabuff.com
 * @support Forum - http://joomlabuff.com/support
 */
 
defined ( '_JEXEC' ) or die ();
$document = JFactory::getDocument();
$document->addStyleSheet ( JUri::root () . 'modules/mod_jbvertical_tabs/assets/css/font-awesome.min.css' );
?>
<div class="jbacc-block <?php echo $moduleclass;?>">
	<div class="row-fluid">
		<div class="span12">
			<div class="panel-group" id="accordjbvtab">
			  <?php foreach($list as $key => $item):?>
			  <div class="panel panel-default">
			    <div class="panel-heading">
			      <h4 class="panel-title">
			        <a class="accordion-toggle" data-toggle="collapse" onclick="jQuery('#icon-uparrow-<?php echo $item->alias;?>').toggle('click');jQuery('#icon-downarrow-<?php echo $item->alias;?>').toggle('click');"
			         	 data-parent="#accordion" href="#collapse_<?php echo $key.'_'.$item->alias;?>">			           
						<span id="icon-downarrow-<?php echo $item->alias;?>" class="fa fa-arrow-down"
							  onclick="jQuery('#icon-uparrow-<?php echo $item->alias;?>').toggle('click');jQuery('#icon-downarrow-<?php echo $item->alias;?>').toggle('click');"
							  data-toggle-tag="icon-uparrow-<?php echo $item->alias;?>">
						</span>
						<span id="icon-uparrow-<?php echo $item->alias;?>" onclick="jQuery('#icon-downarrow-<?php echo $item->alias;?>').toggle('click');jQuery('#icon-uparrow-<?php echo $item->alias;?>').toggle('click');"
							 class="fa fa-arrow-up" data-toggle-tag="icon-downarrow-<?php echo $item->alias;?>" style="display:none;" >
						</span>
			          <?php echo $item->title; ?>
			        </a>
			      </h4>
			    </div>
			    <div id="collapse_<?php echo $key.'_'.$item->alias;?>" class="panel-collapse collapse">
			      <div class="panel-body">
			        <?php require JModuleHelper::getLayoutPath('mod_jbvertical_tabs', '_item'); ?>
			      </div>
			    </div>
			  </div>
			  <?php endforeach;?>
		  </div>
	</div>
</div>
</div>
 		 
