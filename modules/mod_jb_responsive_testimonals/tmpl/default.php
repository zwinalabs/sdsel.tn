<?php
/**  
 * Joomla Buff Responsive Testimonals
 * @package      mod_jb_responsive_testimonals
 * @copyright    Copyright (C) 2014 - 2019 JoomlaBuff http://joomlabuff.com. All rights reserved.
 * @license      GNU General Public License version 2 or later; see LICENSE.txt
 * @websites     http://joomlabuff.com
 * @support      Forum - http://joomlabuff.com/forum/index.html
 */
defined ( '_JEXEC' ) or die ();
?>
<!-- Check your list is not empty -->
<?php if(empty($list) && count($list) <0 ) return false;?>
<!-- Testimonals items   -->
<div id="jb-responsive-testimonal-default-<?php echo $module->id;?>" class="jbt-style-1 <?php echo $moduleclass_sfx; ?>">
	<div class="jb-responsive-testimonal-item-wrapper ">
			<!-- Loop the testimonals items -->
		<ul class="jb-responsive-testimonial-list">
				<?php foreach($list as $i => $item):?>	
					<!--  current item -->
					<!-- // set the first item as default active  -->						
					<?php if($i ==0): ?>
					<li	id="jb-testimonial-list-image-<?php echo $module->id;?>-<?php echo $i;?>" class="jbt-avatar active"> 
					<?php else:?>
					<li id="jb-testimonial-list-image-<?php echo $module->id;?>-<?php echo $i;?>" class="jbt-avatar"> 
					<?php endif;?>		
					<!-- Testimonal image -->
					 <img class="jb-rt-image jb-rt-owl-image"
							onmouseover="showDefaultTestimonalInfo(this);"
							data-li_id="jb-testimonial-list-image-<?php echo $module->id;?>-<?php echo $i;?>"
							data-quote_id="jb-testimonial-quote-wrapper-<?php echo $module->id;?>-<?php echo $i;?>"
							title="<?php echo $item->name;?>" src="<?php echo $item->image;?>"
							alt="<?php echo $item->name;?>" />

					</li>
					<!-- EOF current item -->
					<?php endforeach;?>
				</ul>
			<?php foreach($list as $qi => $qitem):?>  
				<!--  current item -->
				<!-- // set the first item as default active  -->	
				<?php if($qi ==0): ?>
				<div id="jb-testimonial-quote-wrapper-<?php echo $module->id;?>-<?php echo $qi;?>" class="jbt-quote-info active" style="display: block;">
				<?php else:?>
				<div id="jb-testimonial-quote-wrapper-<?php echo $module->id;?>-<?php echo $qi;?>" class="jbt-quote-info" style="display: none;">
				<?php endif;?>
					<!--  Testimonal item quote -->
					<p id="jb-responsive-testimonial-bq-<?php echo $module->id;?>-<?php echo $qi;?>" class="jbt-quote">
						<span><?php echo $qitem->quote; ?></span>
					</p>
					<p class="jb-responsive-testimonal-head">
						<!-- Testimonal item name -->
						<span class="jbt-avatar-name"><?php echo $qitem->name;?></span> ,
						<!-- Testimonal item company  --> 
						<span class="jbt-avatar-company"> <?php echo $qitem->company ;?></span>
					</p>
			</div>
			<!-- EOF current item -->
			<?php endforeach;?>
		</div>
	</div>
</div>
<!-- EOF testimonal items -->
 


