<?php
/**
 * @version		2.6.x
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2014 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die;


$document = JFactory::getDocument();
$app = JFactory::getApplication();
$current_tmpl = $app->getTemplate();

$document->addScript("templates/".$current_tmpl."/js/prettySocial.min.js");
$document->addScriptDeclaration("
	jQuery(document).ready(function($) {
		$('.prettySocial').prettySocial();
	});
	");


// Define default image size (do not change)
K2HelperUtilities::setDefaultImage($this->item, 'itemlist', $this->params);

?>

<!-- Start K2 Item Layout -->
<div class="catItemView single_news_post group<?php echo ucfirst($this->item->itemGroup); ?><?php echo ($this->item->featured) ? ' catItemIsFeatured' : ''; ?><?php if($this->item->params->get('pageclass_sfx')) echo ' '.$this->item->params->get('pageclass_sfx'); ?>">
	<div class="row-remove">
		<?php if($this->item->params->get('catItemImage') && !empty($this->item->image)): ?>
	  <!-- Item Image -->
	  <div class="catItemImageBlock">

			  <?php if(isset($this->item->editLink)): ?>
				<!-- Item edit link -->
				<span class="catItemEditLink">
					<a class="modal btn btn-primary btn-sm" rel="{handler:'iframe',size:{x:990,y:550}}" href="<?php echo $this->item->editLink; ?>">
						<i class="fa fa-edit"></i> <?php echo JText::_('K2_EDIT_ITEM'); ?>
					</a>
				</span>
			<?php endif; ?>
			
		  
		   <div class="catItemImage">
				<div class="img_container">
					<a href="<?php echo $this->item->link; ?>" title="<?php if(!empty($this->item->image_caption)) echo K2HelperUtilities::cleanHtml($this->item->image_caption); else echo K2HelperUtilities::cleanHtml($this->item->title); ?>">
						<img class="img-responsive" src="<?php echo $this->item->image; ?>" alt="<?php if(!empty($this->item->image_caption)) echo K2HelperUtilities::cleanHtml($this->item->image_caption); else echo K2HelperUtilities::cleanHtml($this->item->title); ?>"  />
					</a>
				</div>
			</div>
		  
		  
	  </div>
	<?php endif; ?>
	
	<div class="content-box">
		<div class="catItemContentBlock">
			<div class="catItemHeader">
		
			<?php if(
			  $this->item->params->get('catItemHits') ||
			  $this->item->params->get('catItemCategory') ||
			  $this->item->params->get('catItemTags') ||
			  $this->item->params->get('catItemAttachments')
			  ): ?>
		 

			 <div class="title_container">
			  <?php if($this->item->params->get('catItemTitle')): ?>
			  <!-- Item title -->
			  <h3 class="catItemTitle">

				<?php if ($this->item->params->get('catItemTitleLinked')): ?>
					<a href="<?php echo $this->item->link; ?>">
					<?php echo $this->item->title; ?>
				</a>
				<?php else: ?>
				<?php echo $this->item->title; ?>
				<?php endif; ?>

				<?php if($this->item->params->get('catItemFeaturedNotice') && $this->item->featured): ?>
				<!-- Featured flag -->
				<span>
					<sup>
						<?php echo JText::_('K2_FEATURED'); ?>
					</sup>
				</span>
				<?php endif; ?>
			  </h3>
			  <?php endif; ?>
			</div>
	  

		<div class="catItemMeta">
			<?php if($this->item->params->get('catItemAuthor')): ?>
			<span class="catItemAuthor">
				<?php if(isset($this->item->author->link) && $this->item->author->link): ?>
				<a rel="author" href="<?php echo $this->item->author->link; ?>">
					<i class="fa fa-user"></i>
					<?php echo $this->item->author->name; ?>
				</a>
				<?php else: ?>
				<?php echo $this->item->author->name; ?>
				<?php endif; ?>
			</span>
			<?php endif; ?>

			<?php if($this->item->params->get('catItemDateCreated')): ?>
			<span class="catItemDateCreated">
				<a href="#">
					<i class="fa fa-calendar"></i>
				<?php echo JHTML::_('date', $item->created, JText::_('F, d Y')); ?>
				</a>
			</span>
			<?php endif; ?>
			
			<?php if($this->item->params->get('catItemCategory')): ?>
				<!-- Item category name -->
				<span class="catItemCategory">
					<a href="<?php echo $this->item->category->link; ?>"><i class="fa fa-folder-o"></i> <?php echo $this->item->category->name; ?></a>
					
				</span>
				<?php endif; ?>

			<?php if($this->item->params->get('catItemCommentsAnchor') && ( ($this->item->params->get('comments') == '2' && !$this->user->guest) || ($this->item->params->get('comments') == '1')) ): ?>
			<span class="catItemCommentsLink">
				<?php if(!empty($this->item->event->K2CommentsCounter)): ?>
					<?php echo $this->item->event->K2CommentsCounter; ?>
				<?php else: ?>
					<?php if($this->item->numOfComments > 0): ?>
					<a href="<?php echo $this->item->link; ?>#itemCommentsAnchor">
						<i class="fa fa-comment"></i> <?php echo $this->item->numOfComments; ?> <?php echo ($this->item->numOfComments>1) ? JText::_('K2_COMMENTS') : JText::_('K2_COMMENT'); ?>
					</a>
					<?php else: ?>
					<a href="<?php echo $this->item->link; ?>#itemCommentsAnchor">
						<i class="fa fa-comment"></i> <?php echo JText::_('K2_BE_THE_FIRST_TO_COMMENT'); ?>
					</a>
					<?php endif; ?>
				<?php endif; ?>
			</span>
			<?php endif; ?>
			
				

				<?php if($this->item->params->get('catItemHits')): ?>
				<!-- Item Hits -->
				<span class="catItemHitsBlock">
					<span class="catItemHits">
						<i class="fa fa-eye"></i> <?php echo $this->item->hits; ?>  <?php echo JText::_('K2_TIMES'); ?>
					</span>
				</span>
				<?php endif; ?>

				

			  

			  <?php if($this->item->params->get('catItemAttachments') && count($this->item->attachments)): ?>
			  <!-- Item attachments -->
			  <div class="catItemAttachmentsBlock">
				  <span><?php echo JText::_('K2_DOWNLOAD_ATTACHMENTS'); ?></span>
				  <ul class="catItemAttachments">
				    <?php foreach ($this->item->attachments as $attachment): ?>
				    <li>
					    <a title="<?php echo K2HelperUtilities::cleanHtml($attachment->titleAttribute); ?>" href="<?php echo $attachment->link; ?>">
					    	<?php echo $attachment->title ; ?>
					    </a>
					    <?php if($this->item->params->get('catItemAttachmentsCounter')): ?>
					    <span>(<?php echo $attachment->hits; ?> <?php echo ($attachment->hits==1) ? JText::_('K2_DOWNLOAD') : JText::_('K2_DOWNLOADS'); ?>)</span>
					    <?php endif; ?>
				    </li>
				    <?php endforeach; ?>
				  </ul>
			  </div>
			  <?php endif; ?>

		 
		  <?php endif; ?>
			
			
		</div>
		
	


  </div>

  <!-- Plugins: AfterDisplayTitle -->
  <?php echo $this->item->event->AfterDisplayTitle; ?>

  <!-- K2 Plugins: K2AfterDisplayTitle -->
  <?php echo $this->item->event->K2AfterDisplayTitle; ?>

	<?php if($this->item->params->get('catItemRating')): ?>
	<!-- Item Rating -->
	<div class="catItemRatingBlock">
		<span><?php echo JText::_('K2_RATE_THIS_ITEM'); ?></span>
		<div class="itemRatingForm">
			<ul class="itemRatingList">
				<li class="itemCurrentRating" id="itemCurrentRating<?php echo $this->item->id; ?>" style="width:<?php echo $this->item->votingPercentage; ?>%;"></li>
				<li><a href="#" data-id="<?php echo $this->item->id; ?>" title="<?php echo JText::_('K2_1_STAR_OUT_OF_5'); ?>" class="one-star">1</a></li>
				<li><a href="#" data-id="<?php echo $this->item->id; ?>" title="<?php echo JText::_('K2_2_STARS_OUT_OF_5'); ?>" class="two-stars">2</a></li>
				<li><a href="#" data-id="<?php echo $this->item->id; ?>" title="<?php echo JText::_('K2_3_STARS_OUT_OF_5'); ?>" class="three-stars">3</a></li>
				<li><a href="#" data-id="<?php echo $this->item->id; ?>" title="<?php echo JText::_('K2_4_STARS_OUT_OF_5'); ?>" class="four-stars">4</a></li>
				<li><a href="#" data-id="<?php echo $this->item->id; ?>" title="<?php echo JText::_('K2_5_STARS_OUT_OF_5'); ?>" class="five-stars">5</a></li>
			</ul>
			<div id="itemRatingLog<?php echo $this->item->id; ?>" class="itemRatingLog"><?php echo $this->item->numOfvotes; ?></div>
		</div>
	</div>
	<?php endif; ?>

  <div class="catItemBody">

	  <!-- Plugins: BeforeDisplayContent -->
	  <?php echo $this->item->event->BeforeDisplayContent; ?>

	  <!-- K2 Plugins: K2BeforeDisplayContent -->
	  <?php echo $this->item->event->K2BeforeDisplayContent; ?>


	  <?php if($this->item->params->get('catItemIntroText')): ?>
	  <!-- Item introtext -->
	  <div class="catItemIntroText">
	  	<?php echo $this->item->introtext; ?>
	  </div>
	  <?php endif; ?>


	  <?php if($this->item->params->get('catItemExtraFields') && count($this->item->extra_fields)): ?>
	  <!-- Item extra fields -->
	  <div class="catItemExtraFields">
	  	<h4><?php echo JText::_('K2_ADDITIONAL_INFO'); ?></h4>
	  	<ul>
			<?php foreach ($this->item->extra_fields as $key=>$extraField): ?>
			<?php if($extraField->value != ''): ?>
			<li class="<?php echo ($key%2) ? "odd" : "even"; ?> type<?php echo ucfirst($extraField->type); ?> group<?php echo $extraField->group; ?>">
				<?php if($extraField->type == 'header'): ?>
				<h4 class="catItemExtraFieldsHeader"><?php echo $extraField->name; ?></h4>
				<?php else: ?>
				<span class="catItemExtraFieldsLabel"><?php echo $extraField->name; ?></span>
				<span class="catItemExtraFieldsValue"><?php echo $extraField->value; ?></span>
				<?php endif; ?>
			</li>
			<?php endif; ?>
			<?php endforeach; ?>
			</ul>
	  </div>
	  <?php endif; ?>

	  <!-- Plugins: AfterDisplayContent -->
	  <?php echo $this->item->event->AfterDisplayContent; ?>

	  <!-- K2 Plugins: K2AfterDisplayContent -->
	  <?php echo $this->item->event->K2AfterDisplayContent; ?>

	  <?php if($this->item->params->get('catItemVideo') && !empty($this->item->video)): ?>
  <!-- Item video -->
  <div class="catItemVideoBlock">
  	<h3><?php echo JText::_('K2_RELATED_VIDEO'); ?></h3>
		<?php if($this->item->videoType=='embedded'): ?>
		<div class="catItemVideoEmbedded">
			<?php echo $this->item->video; ?>
		</div>
		<?php else: ?>
		<span class="catItemVideo"><?php echo $this->item->video; ?></span>
		<?php endif; ?>
  </div>
  <?php endif; ?>

  <?php if($this->item->params->get('catItemImageGallery') && !empty($this->item->gallery)): ?>
  <!-- Item image gallery -->
  <div class="catItemImageGallery">
	  <h4><?php echo JText::_('K2_IMAGE_GALLERY'); ?></h4>
	  <?php echo $this->item->gallery; ?>
  </div>
  <?php endif; ?>
  
   
  
  <?php if ($this->item->params->get('catItemReadMore')): ?>
	<!-- Item "read more..." link -->
	<div class="catItemReadMore">
		<a class="btn btn-secondary" href="<?php echo $this->item->link; ?>">
			<?php echo JText::_('K2_READ_MORE'); ?>
		</a>
	</div>
	<?php endif; ?>

	

	

	
	<?php if($this->item->params->get('catItemDateModified')): ?>
		<!-- Item date modified -->
		<?php if($this->item->modified != $this->nullDate && $this->item->modified != $this->item->created ): ?>
		<span class="catItemDateModified">
			<?php echo JText::_('K2_LAST_MODIFIED_ON'); ?> <?php echo JHTML::_('date', $this->item->modified, JText::_('K2_DATE_FORMAT_LC')); ?>
		</span>
		<?php endif; ?>
		<?php endif; ?>

	  <!-- Plugins: AfterDisplay -->
	  <?php echo $this->item->event->AfterDisplay; ?>

	  <!-- K2 Plugins: K2AfterDisplay -->
	  <?php echo $this->item->event->K2AfterDisplay; ?>

		</div>
		<?php if($this->item->params->get('catItemTags') && count($this->item->tags)): ?>
	  <!-- Item tags -->
	  <div class="catItemTagsBlock">
		  <ul class="catItemTags clearfix">
		    <?php foreach ($this->item->tags as $tag): ?>
		    <li><a href="<?php echo $tag->link; ?>"><?php echo $tag->name; ?></a></li>
		    <?php endforeach; ?>
		  </ul>
	  </div>
	  <?php endif; ?>
	</div>
	</div>
	<!-- Content box ends -->
	<!-- End K2 Item Layout -->
	</div>	
 </div>

  

  
