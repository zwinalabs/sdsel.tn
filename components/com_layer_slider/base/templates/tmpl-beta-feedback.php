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
?><?php if(!defined('LS_ROOT_FILE')) {  header('HTTP/1.0 403 Forbidden'); exit; } ?>
<?php if(strpos(LS_PLUGIN_VERSION, 'b') !== false) : ?>
<div class="ls-version-number">
	<?php echo sprintf(__('Using beta version (%s)', 'LayerSlider'), LS_PLUGIN_VERSION) ?>
	<a href="mailto:support@kreaturamedia.com?subject=LayerSlider WP (v<?php echo LS_PLUGIN_VERSION ?>) Feedback"><?php _e('Send feedback', 'LayerSlider') ?></a>
</div>
<?php endif; ?>
