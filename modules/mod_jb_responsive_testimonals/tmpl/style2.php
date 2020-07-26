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
<div id="jb-responsive-testimonal-style2-<?php echo $module->id;?>" class="jb-responsive-module<?php echo $moduleclass_sfx; ?>">				
	<div class="jb-responsive-testimonal-item-wrapper ">
		<ul class="jb-responsive-testimonial-list">
			<?php foreach($list as $i => $item):?>	
				<!--  current item -->			
				<li id="jb-testimonial-list-image-<?php echo $module->id;?>-<?php echo $i;?>" class="col-xs-12 col-sm-2 col-md-1 jb-list-box" > 
					<blockquote>
						<!--  Testimonal item quote -->
						<span id="jb-responsive-testimonial-bq-<?php echo $module->id;?>-<?php echo $i;?>" class="jb-responsive-testimonal-quote text-center">
							" <?php echo $item->quote; ?>"
						</span>
						<small>
							<span>							
								<!--  Testimonal item name and company-->
								<?php echo $item->name;?> .,						
								<?php echo $item->company ;?>
							</span>
						</small>
				</blockquote>
			</li>
			<!-- EOF current item -->
			<?php endforeach;?>
		</ul>
	</div>
</div>
<!-- EOF items -->
	


