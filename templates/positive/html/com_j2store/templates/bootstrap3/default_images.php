<?php
/**
 * @package J2Store
 * @copyright Copyright (c)2014-17 Ramesh Elamathi / J2Store.org
 * @license GNU GPL v3 or later
 */

// No direct access
defined('_JEXEC') or die;
$image_path = JUri::root();
$image_type = $this->params->get('list_image_type', 'thumbnail');
$main_image="";
?>
<?php if($this->params->get('list_show_image', 1)): ?>
<div class="j2store-product-images">

	<?php if($image_type == 'thumbimage'):?>
	<div class="j2store-thumbnail-image">
		<?php if(JFile::exists(JPATH_SITE.'/'.JPath::clean($this->product->thumb_image))):?>
			
			<?php if($this->params->get('list_image_link_to_product', 1)): ?>
				<a href="<?php echo $this->product->product_link; ?>">
			<?php endif;?>	
			<img itemprop="image" alt="<?php echo $this->product->product_name ;?>" title="<?php echo $this->product->product_name ;?>" class="j2store-img-responsive j2store-product-thumb-image-<?php echo $this->product->j2store_product_id; ?>"  src="<?php echo $image_path.$this->product->thumb_image;?>" />
			
			<?php if($this->params->get('list_image_link_to_product', 1)): ?>
				</a>
			<?php endif;?>
		<?php elseif(!empty($this->product->thumb_image)):?>
			<?php echo J2Store::product()->displayImage($this->product,array('type'=>'Thumb','params' => $this->params)); ?>
		<?php endif; ?>
		
		<figcaption class="overlay">
                                    <div class="box">
                                        <div class="content">
                                            <form action="<?php echo $this->product->cart_form_action; ?>"
		method="post" class="j2store-addtocart-form"
		id="j2store-addtocart-form-<?php echo $this->product->j2store_product_id; ?>"
		name="j2store-addtocart-form-<?php echo $this->product->j2store_product_id; ?>"
		data-product_id="<?php echo $this->product->j2store_product_id; ?>"
		data-product_type="<?php echo $this->product->product_type; ?>"
		enctype="multipart/form-data">

<?php $cart_type = $this->params->get('list_show_cart', 1); ?>
<?php if($cart_type == 1) : ?>
	<?php echo $this->loadTemplate('options'); ?>
	<?php echo $this->loadTemplate('cart'); ?>

<?php elseif( ($cart_type == 2 && count($this->product->options)) || $cart_type == 3 ):?>
<!-- we have options so we just redirect -->
	<a href="<?php echo $this->product->product_link; ?>" class="<?php echo $this->params->get('choosebtn_class', 'btn btn-success'); ?>"><?php echo JText::_('J2STORE_VIEW_PRODUCT_DETAILS'); ?></a>
<?php else: ?>
	<?php echo $this->loadTemplate('cart'); ?>
<?php endif; ?>

</form>
                                        </div>
                                    </div>
                                </figcaption>
	</div>
	 <?php endif; ?>

	<?php if($image_type == 'mainimage'):?>
	<div class="j2store-mainimage">
		   <?php
	      if(isset($this->product->main_image) && $this->product->main_image){
	      	$main_image = $this->product->main_image;
	      }
	      ?>
		   <?php if($main_image &&  JFile::exists(JPATH_SITE.'/'.$main_image)):?>
		   	
		   	<?php if($this->params->get('list_image_link_to_product', 1)): ?>
				<a href="<?php echo $this->product->product_link; ?>">
			<?php endif;?>	
			
		  	 <img itemprop="image"
		  	 	alt="<?php echo $this->product->product_name ;?>"
				title="<?php echo $this->product->product_name ;?>"
		  	 	class="j2store-img-responsive j2store-product-main-image-<?php echo $this->product->j2store_product_id; ?>"
		  	 	src="<?php echo $image_path.$main_image;?>"
		  	 	width="<?php echo (int) $this->params->get('list_image_thumbnail_width', '200'); ?>"
		  	 />
		  	 
		  	<?php if($this->params->get('list_image_link_to_product', 1)): ?>
				</a>
			<?php endif;?>
		   <?php elseif(!empty($this->product->main_image)):?>
			   <?php echo J2Store::product()->displayImage($this->product,array('type'=>'Main','params' => $this->params)); ?>
		   <?php endif; ?>
	</div>
	 <?php endif; ?>
</div>
<?php endif; ?>