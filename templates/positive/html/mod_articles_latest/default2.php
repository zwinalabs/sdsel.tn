<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_latest
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<ul class="latestnews<?php echo $moduleclass_sfx; ?>">
<?php foreach ($list as $item) : ?>
	<li itemscope itemtype="https://schema.org/Article">
	<div class="media">
		<a class="media-left pull-left" href="<?php echo $item->link; ?>" itemprop="url">
		<img src="<?php echo json_decode($item->images)->image_intro; ?>"/>
		</a>
		<div class="media-body">
				<a href="<?php echo $item->link; ?>" itemprop="url"><h4> <?php echo $item->title; ?> </h4></a>
		<span><i class="fa fa-calendar"></i> <?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC4')); ?></span>
		</div>
	</div>
	</li>
<?php endforeach; ?>
</ul>
