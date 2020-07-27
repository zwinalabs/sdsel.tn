<?php
/*-------------------------------------------------------------------------
# com_creative_slider - Creative Slider
# -------------------------------------------------------------------------
# @ author    John Gera, George Krupa, Janos Biro, Balint Polgarfi
# @ copyright Copyright (C) 2018 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
namespace CreativeSlider;
use stdClass, ZipArchive;
defined('_JEXEC') or die;
?><?php if(!defined('LS_ROOT_FILE')) {  header('HTTP/1.0 403 Forbidden'); exit; }

	$time = time();
	$installed = get_option('ls-date-installed', 0);
	$level = get_option('ls-share-displayed', 1);

	switch($level){
		case 1:
			$time = $time-60*60*24*14;
			$odds = 100;
			break;

		case 2:
			$time = $time-60*60*24*30*2;
			$odds = 200;
			break;

		case 3:
			$time = $time-60*60*24*30*6;
			$odds = 300;
			break;

		default:
			$time = $time-60*60*24*30*6;
			$odds = 1000;
			break;
	}

	if($installed && $time > $installed) {
		if(mt_rand(1, $odds) == 3) {
			update_option('ls-share-displayed', ++$level);
?>
<div class="ls-overlay" data-manualclose="true"></div>
<div id="ls-share-template" class="ls-modal ls-box">
	<h3>
		<?php _e('Enjoy using LayerSlider?', 'LayerSlider') ?>
		<a href="#" class="dashicons dashicons-no-alt"></a>
	</h3>
	<div class="inner desc">
		<?php _e('If so, please consider recommending it to your friends on your favorite social network!', 'LayerSlider'); ?>
	</div>
	<div class="inner">
		<a href="https://www.facebook.com/sharer/sharer.php?u=http://creativeslider.demo.offlajn.com" target="_blank">
			<i class="dashicons dashicons-facebook-alt"></i> <?php _e('Share', 'LayerSlider') ?>
		</a>

		<a href="http://www.twitter.com/share?url=http%3A%2F%2Fcreativeslider.demo.offlajn.com&amp;text=Check%20out%20Creative%20Slider%2C%20an%20awesome%20%23slider%20%23extension%20for%20%23Joomla!&amp;via=offlajn" target="_blank">
			<i class="dashicons dashicons-twitter"></i> <?php _e('Tweet', 'LayerSlider') ?>
		</a>

		<a href="https://plus.google.com/share?url=http://creativeslider.demo.offlajn.com" target="_blank">
			<i class="dashicons dashicons-googleplus"></i> +1
		</a>
	</div>
</div>
<?php } } ?>