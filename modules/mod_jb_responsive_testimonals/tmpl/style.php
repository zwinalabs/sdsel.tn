<?php
/**  
 * Joomla Buff Responsive Testimonals
 * @package      mod_jb_responsive_testimonals
 * @copyright    Copyright (C) 2014 - 2019 JoomlaBuff http://joomlabuff.com. All rights reserved.
 * @license      GNU General Public License version 2 or later; see LICENSE.txt
 * @websites     http://joomlabuff.com
 * @support      Forum - http://joomlabuff.com/forum/index.html
 */
defined('_JEXEC') or die;
?>
<!-- Check your list is not empty -->
<?php if(empty($list) && count($list) <0 ) return false;?>
<!-- Testimonals items   -->
<div id="jb-responsive-testimonal-style-<?php echo $module->id;?>" class="jbt-style-2<?php echo $moduleclass_sfx; ?>">				
		<div class="jb-responsive-testimonal-item-wrapper ">
			<!-- Loop the testimonals items -->
			<?php foreach($list as $i => $item):?>  
					<!--  current item -->
					<!-- // set the first item as default active  -->	
				<?php if($i ==0): ?>
				<div id="jb-testimonial-quote-wrapper-<?php echo $module->id;?>-<?php echo $i;?>" class="jbt-quote-info" style="display:block;">
				<?php else:?>
				<div id="jb-testimonial-quote-wrapper-<?php echo $module->id;?>-<?php echo $i;?>"  class="jbt-quote-info" style="display:none;">
				<?php endif;?>
					<!--  Testimonal item quote -->
					<p id="jb-responsive-testimonial-bq-<?php echo $module->id;?>-<?php echo $i;?>" class="jbt-quote">
						<span><?php echo $item->quote; ?></span>
					</p>
					<p class="jb-responsive-testimonal-head">
							<!-- Testimonal item name -->
						<span class="jbt-avatar-name"><?php echo $item->name;?></span> ,
						<!-- Testimonal item company  -->
						<span class="jbt-avatar-company"> <?php echo $item->company ;?></span>
					</p>
				</div>
				<!-- EOF current item -->
			<?php endforeach;?>
			
			<ul class="jb-responsive-testimonial-list">
			<?php foreach($list as $i => $item):?>	
			<!--  current item -->
		 	<!-- // set the first item as default active  -->
			<?php $active_class =($i ==0) ? 'active' :''; ?>
				<li id="jb-testimonial-list-image-<?php echo $module->id;?>-<?php echo $i;?>" class="jbt-avatar <?php echo $active_class;?>" > 
			 		<!-- Testimonal image -->
			 		<img class="jb-rt-avator-image" onmouseover="showDefaultTestimonalInfo(this);" 
			 			data-li_id="jb-testimonial-list-image-<?php echo $module->id;?>-<?php echo $i;?>" 
			 			data-quote_id="jb-testimonial-quote-wrapper-<?php echo $module->id;?>-<?php echo $i;?>"
			 			title="<?php echo $item->name;?>" src="<?php echo $item->image;?>" alt="<?php echo $item->name;?>" />
				</li>
			<!-- EOF current item -->
			<?php endforeach;?>
			</ul>
	</div>
</div>
 


