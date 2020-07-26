<?php
/**  
 * @package mod_jbvertical_tabs
 * @copyright Copyright (C) 2015 JoomlaBuff http://joomlabuff.com. All rights reserved.
 * @license GNU General Public License version 2 or later; see LICENSE.txt
 * @websites http://joomlabuff.com
 * @support Forum - http://joomlabuff.com/support
 */

defined('_JEXEC') or die;
$images  = json_decode($item->images);
$item_heading = $params->get('item_heading', 'h4');
?>
<?php if ($params->get('item_title')) : ?>
	<<?php echo $item_heading; ?> class="jbverticaltabs-title<?php echo $params->get('moduleclass_sfx'); ?>">
	<?php if ($params->get('link_titles') && $item->link != '') : ?>
		<a href="<?php echo $item->link; ?>">
			<?php echo $item->title; ?>
		</a>
	<?php else : ?>
		<?php echo $item->title; ?>
	<?php endif; ?>
	</<?php echo $item_heading; ?>>
<?php endif; ?>

<?php if($params->get('image')):?>
<?php if (isset($images->image_fulltext) && !empty($images->image_fulltext)) : ?>
	<?php $imgfloat = (empty($images->float_fulltext)) ? $params->get('float_fulltext') : $images->float_fulltext; ?>
	<div class="pull-<?php echo htmlspecialchars($imgfloat); ?> item-image"> <img
	<?php if ($images->image_fulltext_caption):
		echo 'class="caption"' . ' title="' . htmlspecialchars($images->image_fulltext_caption) . '"';
	endif; ?>
	src="<?php echo htmlspecialchars($images->image_fulltext); ?>" alt="<?php echo htmlspecialchars($images->image_fulltext_alt); ?>" itemprop="image"/> </div>
	<?php endif; ?>
<?php endif;?>
<?php if (!$params->get('intro_only')) : ?>
	<?php echo $item->afterDisplayTitle; ?>
<?php endif; ?>

<?php echo $item->beforeDisplayContent; ?>

<?php echo $item->introtext; ?>

<?php if (isset($item->link) && $item->readmore != 0 && $params->get('readmore')) : ?>
	<?php echo '<a class="readmore" href="' . $item->link . '">' . $item->linkText . '</a>'; ?>
<?php endif; ?>
