<?php
/**
 * @package J2Store
 * @subpackage plg_j2store_app_emailtofriend
 * @copyright Copyright (c)2015 JoomlaBuff - joomlabuff.org
 * @license GNU GPL v3 or later
 */
/** ensure this file is being included by a parent file */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
 
$image_path = JUri::root ();
$image_type = 'thumbnail';
$main_image = "";
$baseURL = JURI::base();
$subpathURL = JURI::base(true);
		
		//replace administrator string, if present
$baseURL = str_replace('/administrator', '', $baseURL);
$subpathURL = str_replace('/administrator', '', $subpathURL);
		
//invoice url
//$url = str_replace('&amp;','&', JRoute::_('index.php?option=com_j2store&view=products&task=view&id='.$vars->product->j2store_product_id));

$url =str_replace('&amp;','&',$vars->product->product_link);
$url = str_replace('/administrator', '', $url);
$url = ltrim($url, '/');
$subpathURL = ltrim($subpathURL, '/');
if(substr($url,0,strlen($subpathURL)+1) == "$subpathURL/") $url = substr($url,strlen($subpathURL)+1);
$productURL = rtrim($baseURL,'/').'/'.ltrim($url,'/');	
	
?>
<?php echo JText::_('J2STORE_APP_EMAILTOFRIEND_MESSAGE_PRETEXT_SALUTATION');?>
	<p><?php echo JText::sprintf('J2STORE_APP_EMAILTOFRIEND_MESSAGE_PRETEXT',$vars->data['sender_name'],$vars->data['sender_email'],$vars->data['sender_email']);?></p>
	
<h2 itemprop="name" class="product-title">
	<a itemprop="url" href="<?php echo $productURL ; ?>" title="<?php echo $vars->product->product_name; ?>">
		<?php echo $vars->product->product_name; ?>
	</a>
</h2>
<div class="j2store-product-images">	 
	<?php if($image_type == 'thumbimage'):?>
	<div class="j2store-thumbnail-image">
		<?php if(JFile::exists(JPATH_SITE.'/'.JPath::clean($vars->product->thumb_image))):?>
			<a href="<?php echo $productURL ; ?>"> 
			<img
			itemprop="image" alt="<?php echo $vars->product->product_name ;?>"
			class="j2store-img-responsive j2store-product-thumb-image-<?php echo $vars->product->j2store_product_id; ?>"
			src="<?php echo $image_path.$vars->product->thumb_image;?>" />

		</a>
		 
			
		<?php endif; ?>
	</div>
	 <?php endif; ?>

	 
	<div class="j2store-mainimage">
		   <?php
		if (isset ( $vars->product->main_image ) && $vars->product->main_image) {
			$main_image = $vars->product->main_image;
		}
		?>
		   <?php if($main_image &&  JFile::exists(JPATH_SITE.'/'.$main_image)):?>

				<a href="<?php echo $productURL ; ?>"> 
				<img
				itemprop="image" alt="<?php echo $vars->product->product_name ;?>"
				class="j2store-img-responsive j2store-product-main-image-<?php echo $vars->product->j2store_product_id; ?>"
				src="<?php echo $image_path.$main_image;?>"
				width="200" />


		</a>
			<?php endif;?>	
			 		  
	</div>

</div>
<div class="email-friend-message">
	<h5><?php echo JText::_('J2STORE_APP_EMAILTOFRIEND_MESSAGE_FROM_FRIEND');?></h5>
	<?php echo $vars->data['message'];?>
</div>
