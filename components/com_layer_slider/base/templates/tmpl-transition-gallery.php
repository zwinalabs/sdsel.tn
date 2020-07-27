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
<script type="text/html" id="tmpl-ls-transition-modal">
	<div id="ls-transition-window">
		<header>
			<h1><?php _e('Choose a slide transition to import', 'LayerSlider') ?></h1>
			<b class="dashicons dashicons-no"></b>
			<div id="transitionmenu" class="filters buildermenu">
				<span><?php _e('Show Transitions:', 'LayerSlider') ?></span>
				<ul>
					<li class="active"><?php _e('2D', 'LayerSlider') ?></li>
					<li><?php _e('3D', 'LayerSlider') ?></li>
				</ul>
			</div>
		</header>
		<div class="km-ui-modal-scrollable inner">
			<div id="ls-transitions-list">

				<!-- 2D -->
				<section data-tr-type="2d_transitions"></section>

				<!-- 3D -->
				<section data-tr-type="3d_transitions"></section>
			</div>
		</div>
	</div>
</script>