<?php
/*-------------------------------------------------------------------------
# com_layer_slider - Creative Slider
# -------------------------------------------------------------------------
# @ author    John Gera, George Krupa, Janos Biro, Balint Polgarfi
# @ copyright Copyright (C) 2017 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php
// No direct access
defined('_JEXEC') or die;

$doc = JFactory::getDocument();
$doc->addCustomTag("
<style>
	.ls-sliders-list td:first-child,
	.ls-sliders-list td:last-child,
	.ls-bulk-actions,
	.ls-plugin-settings-tabs,
	.ls-plugin-settings,
	#ls-import-samples-button,
	#ls-import-button,
	#ls-add-slider-button,
	#ls-slider-filters,
	#screen-meta-links { display: none; }
	.ls-pagination .button { padding: 8px 10px; }
</style>
<script>
	jQuery(document.documentElement).on('click', 'a[href*=\"action=edit\"]', function(e) {
		e.preventDefault();
		var id = this.href.split('&id=')[1];
		window.parent.jInsertEditorText('{creativeslider id=\"'+ id +'\"}', 'jform_articletext');
		window.parent.jModalClose();
	});
</script>
");

include dirname(__FILE__) . '/../../default.php';