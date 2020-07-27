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

defined('_JEXEC') or die;
?><?php
include dirname(__FILE__) . '/../../default.php';

if (isset(${'_GET'}['action']) && ${'_GET'}['action'] == 'edit') {
	// save slide histories
	if (get_option('save_history', false)) {
		$document->addScriptDeclaration(";lsSaveHistory = true;\n");
	}

	// check image URLs
	global $lsdb;
	$slider = json_decode($lsdb->result['data'], true);
	$img = new LS_Img();
	$fixes = $img->getWrongPaths($slider);

	if (!empty($fixes)) {
		$error = json_encode(sprintf(__('LayerSlider noticed that some image URLs are not correct. %sOpen image URL manager%s', 'LayerSlider'), '<button class="button ls-img-path-fix">', '</button>'));
		$document->addScriptDeclaration(";LS_img_path_fix = $error;\n");
		?>
		<script type="text/html" id="tmpl-ls-img-path-fix">
		<header>
			<h1>
				<?php _e('Image URL manager', 'LayerSlider') ?>
				<button class="button ls-correct-img-path" style="margin: 0 0 0 20px !important"><?php _e('Repair image URLs', 'LayerSlider') ?></button>
			</h1>
			<b class="dashicons dashicons-no"></b>
			<p style="margin-top: 60px !important; font-size: 15px">
				<?php _e('LayerSlider noticed that some image URLs are not correct. The built-in search algorithm has found the following URLs, please check them. (This problem may occur if you moved your site.)', 'LayerSlider') ?></p>
		</header>
		<div class="km-ui-modal-scrollable inner">
			<table>
				<tr style="font-size: 40px">
					<th><i class="dashicons dashicons-no" style="color:#ff4400; font-size:60px"></i></th>
					<th></th>
					<th><i class="dashicons dashicons-yes" style="color:#7EB917; font-size:60px"></i></th>
				</tr>
			<?php foreach ($fixes as $incorrect => $correct) : ?>
				<tr class="small" style="text-align: left; vertical-align: top">
					<td><?php echo $incorrect ?></td>
					<td></td>
					<td><a href="<?php echo $correct ?>" target="_blank"><?php echo $correct ?></a></td>
				</tr>
			<?php endforeach ?>
			</table>
		</div>
		</script>
		<?php
	}
} else {
	// Add Dashboard
	if (!\JPluginHelper::isEnabled('system', 'offlajnparams'))
		\JFactory::getApplication()->enqueueMessage(
			'Please enable "Offlajn Params" plugin <a href="index.php?option=com_plugins&filter_search=offlajn">here</a>!<br>If it is missing please reinstall the extension.', 'error');
	else {
		$hash = preg_match('~<hash>(.*?)</hash>~', $GLOBALS['ls_xml'], $m) ? $m[1] : '';
		$hash = strtr(call_user_func('base'.'64_encode', $hash), '+/=', '-_,');

		$generalInfo = '<script>
			jQuery(window).on("load resize", function() {jQuery(".column.left .box-content, .column.mid .box-content").height(jQuery(".column.right").height() - 31)});
			jQuery.post(location.pathname+"?task=offlajninfo&v='.LS_PLUGIN_VERSION.'", "hash='.$hash.'", function(r) {jQuery(".column.left .box-content").html(r);});
		</script>';
		$relatedNews = '<script>jQuery.get(location.pathname+"?task=offlajnnews&tag=Creative Slider", function(r) {jQuery(".column.mid .box-content").html(r)})</script>';
	}
	$supportTicketUrl = \JURI::root()."administrator/components/com_layer_slider/assets/images/support-ticket-button.png";
	$supportUsUrl = \JURI::root()."administrator/components/com_layer_slider/assets/images/support-us-button.png";
	?>
	<div class="ls-box panel dashboard" id="offlajn-dashboard">
		<h3 class="header medium">OFFLAJN Product Info</h3>
		<div class="pane-slider content" style="padding-top: 0px; border-top: medium none; padding-bottom: 0px; border-bottom: medium none; overflow: hidden;">
			<div class="column left">
				<div class="dashboard-box">
					<div class="box-title">General <b>Information</b></div>
					<div class="box-content"><?php echo @$generalInfo; ?></div>
				</div>
			</div>
			<div class="column mid">
				<div class="dashboard-box">
					<div class="box-title">Related <b>News</b></div>
					<div class="box-content"><?php echo @$relatedNews; ?></div>
				</div>
			</div>
			<div class="column right">
				<div class="dashboard-box">
					<div class="box-title">Product <b>Support</b></div>
					<div class="box-content">
						<div class="content-inner">
							If you have any problem with Creative Slider just write us and we will help ASAP!
							<div style="background-image:url('<?php echo $supportTicketUrl?>');" class="support-ticket-button">
								<a href="http://offlajn.com/contact-us.html#department=5&amp;product=49" target="_blank"></a>
							</div>
						</div>
					</div>
				</div>
				<div class="dashboard-box">
					<div class="box-title">Rate <b>Us</b></div>
					<div class="box-content">
						<div class="content-inner">
							If you use Creative Slider, please post a rating and a review at the Joomla! Extensions Directory. With this small gesture you will help the community a lot. Thank you very much!
							<div style="background-image:url('<?php echo $supportUsUrl?>');" class="support-us-button">
								<a href="https://extensions.joomla.org/extensions/extension/photos-a-images/slideshow/creative-slider" target="_blank"></a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}
