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
?><?php if(!defined('LS_ROOT_FILE')) { header('HTTP/1.0 403 Forbidden'); exit; } ?>
<script type="text/html" id="ls-static-layer-item-template">
	<li>
		<a href="#" class="dashicons dashicons-redo ls-icon-jump" data-help="<?php _e('Click this icon to jump to the slide where this layer was added on, so you can quickly edit its settings.', 'LayerSlider') ?>"></a>
		<div class="ls-sublayer-thumb"></div>
		<span class="ls-sublayer-title"><?php echo sprintf(__('Layer #%d', 'LayerSlider'), '1') ?></span>
	</li>
</script>
