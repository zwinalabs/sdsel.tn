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

//assigning module id
$slider_id = 'jowl-module-jbmedia-style8-slider-'.$module->id;
 
$doc->addScript(JURI::root().'modules/mod_jb_responsive_testimonals/assets/owl-carousel/owl.carousel.min.js');
$doc->addStylesheet(JURI::root() . 'modules/mod_jb_responsive_testimonals/assets/js/google-code-prettify/prettify.css');
$doc->addStylesheet(JURI::root() . 'modules/mod_jb_responsive_testimonals/assets/owl-carousel/owl.carousel.css');
$doc->addStylesheet(JURI::root() . 'modules/mod_jb_responsive_testimonals/assets/owl-carousel/owl.theme.css');
$doc->addStylesheet(JURI::root() . 'modules/mod_jb_responsive_testimonals/assets/owl-carousel/owl.transitions.css');
$doc->addStylesheet(JURI::root() . 'modules/mod_jb_responsive_testimonals/assets/css/animate.css');

$count_items = $params->get('count_items',3);
$singleItem =($params->get('sinlgeitem',0)) ? 'true' : 'false';
$navigation = ($params->get('navigation',0)) ? 'true' : 'false';
$autoplay = ($params->get('autoscroll',1)) ? 'true' : 'false';
$pagination = ($params->get('pagination',0)) ? 'true' : 'false';
$slideSpeed = $params->get('slidespeed',200);
$pagination_speed = $params->get('pagination_speed',800);
$paginationNumber = ($params->get('paginationNumber',0)) ? 'true' : 'false';
if($singleItem == 1) {
	$count_items =  1;
} 
$transition_style =  $params->get('transition_type','fade');
 
?>
	<!-- Check your list is not empty -->
<?php if(empty($list) && count($list) <0 ) return false;?>
<script type="text/javascript">
var $ = jQuery.noConflict();
jQuery(document).ready(function($) {
    var style8owl = $("#<?php echo $slider_id;?>");
   // $('.owl-item').addClass('animated bounceOutLeft');
    style8owl.owlCarousel({
    		 items: <?php echo (int)$count_items;?>, 
         navigation:<?php echo $navigation;?>, 
       //Pagination
		pagination : <?php echo $pagination;?>,
         paginationNumbers: <?php echo $paginationNumber;?>, 
         navigationText:[ "<i class='fa fa-angle-left'></i>",
 		"<i class='fa fa-angle-right'></i>"], 
    });

  });
</script>
<div id="jb-responsive-testimonal-style8-<?php echo $module->id;?>" class="jb-responsive-module <?php echo $moduleclass_sfx; ?>">
	<div class="jb-responsive-testimonal-item-wrapper ">
		<!-- Testimonals items   -->
		<ul id="<?php echo $slider_id;?>" class="owl-carousel jb-responsive-testimonial-list">
			<?php foreach($list as $i => $item):?>	
			<li class="rt-single-list">
				<!--  Testimonal item quote -->
				<div data-quotes-content=""
						class="responsive-testimonals-quotes-item quotes-bottomright-arrow">
						<span class="responsive-testimonals-quotes-text">
							" <?php echo $item->quote; ?>"			
						</span>
				</div>
				
				<!--  Testimonal item name and company-->
				<div class="responsive-testimonals-quotes-info">
					<img alt="image" class="responsive-testimonals-quotes-image" src="<?php echo $item->image;?>" alt="<?php echo $item->name;?>">
					<span class="responsive-testimonals-quotes-author">
						<?php echo $item->name;?>			
					</span>
					<span class="responsive-testimonals-quotes-subtext">
						<?php echo $item->company ;?>				
					</span>
				</div>
			</li>
			<!-- EOF current item -->
			<?php endforeach;?>
		</ul>
	</div>
</div>
 

