<?php
/**
 * @package J2Store
 * @copyright Copyright (c)2014-17 Ramesh Elamathi / J2Store.org
 * @license GNU GPL v3 or later
 *
 * Bootstrap 2 layout of product detail
 */
// No direct access
defined('_JEXEC') or die;
$columns = $this->params->get('item_related_product_columns', 3);
$total = count($this->up_sells); $counter = 0;
$upsell_image_width = $this->params->get('item_product_upsell_image_width', '100');
?>

<div class="row product-upsells-container">
	<div class="col-sm-12">
		<h3><?php echo JText::_('J2STORE_RELATED_PRODUCTS_UPSELLS'); ?></h3>
				<?php foreach($this->up_sells as $upsell_product):?>
					<?php
						$upsell_product->product_link = JRoute::_('index.php?option=com_j2store&view=products&task=view&id='.$upsell_product->j2store_product_id);
						if(!empty($upsell_product->addtocart_text)) {
							$cart_text = JText::_($upsell_product->addtocart_text);
						} else {
							$cart_text = JText::_('J2STORE_ADD_TO_CART');
						}
					
					?>
					
					<?php $rowcount = ((int) $counter % (int) $columns) + 1; ?>
					<?php if ($rowcount == 1) : ?>
						<?php $row = $counter / $columns; ?>
					
					<div class="upsell-product-row <?php echo 'row-'.$row; ?> row">
					<?php endif;?>
					<div class="col-sm-<?php echo round((12 / $columns));?> upsell-product product-<?php echo $upsell_product->j2store_product_id;?> <?php echo $upsell_product->params->get('product_css_class','');?>">
	<div class="upsell-product-image-content">
							<div class="upsell-product-image j2store-product-images">
							<?php
								$thumb_image = '';
								if(isset($upsell_product->thumb_image) && $upsell_product->thumb_image){
		      					$thumb_image = $upsell_product->thumb_image;
		      					}
	
		      				?>
			   				<?php if(isset($thumb_image) &&  JFile::exists(JPATH::clean(JPATH_SITE.'/'.$thumb_image))):?>
			   					<img title="<?php echo $upsell_product->product_name ;?>" alt="<?php echo $upsell_product->product_name ;?>" class="j2store-product-thumb-image-<?php echo $upsell_product->j2store_product_id; ?>"  src="<?php echo JUri::root().JPath::clean($thumb_image);?>" width="<?php echo intval($upsell_image_width);?>"/>
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
							
			<div class="content-box">
			
				<div class="inner-box">
							<h3 class="upsell-product-title product-title">
								<a href="<?php echo $upsell_product->product_link; ?>">
									<?php echo $upsell_product->product_name; ?>
								</a>
							</h3>
				
							<?php
							$this->singleton_product = $upsell_product;
							$this->singleton_params = $this->params;
							echo $this->loadAnyTemplate('site:com_j2store/products/price');
							?>
				</div>
				
				<div class="price-box">

						<?php if( J2Store::product()->canShowCart($this->params) ): ?>
							<?php if(count($upsell_product->options) || $upsell_product->product_type == 'variable'): ?>
								<a class="<?php echo $this->params->get('choosebtn_class', 'btn btn-success'); ?>"
									href="<?php echo $upsell_product->product_link; ?>"><i class="fa fa-shopping-cart"></i>
									<?php echo JText::_('J2STORE_CART_CHOOSE_OPTIONS'); ?>
								</a>
							<?php else: ?>
							<?php
								$this->singleton_product = $upsell_product;
								$this->singleton_params = $this->params;
								$this->singleton_cartext = $cart_text;
								echo $this->loadAnyTemplate('site:com_j2store/products/cart');
							?>
							<?php endif; ?>
						<?php endif; ?>
				</div>
			</div>
	</div>
					</div>
				<?php $counter++; ?>
				<?php if (($rowcount == $columns) or ($counter == $total)) : ?>
					</div>
				<?php endif; ?>
			<?php endforeach;?>
	</div>
</div>