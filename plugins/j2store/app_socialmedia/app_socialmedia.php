<?php
/**
 * @package J2Store
 * @copyright Copyright (c)2014-17 Ramesh Elamathi / J2Store.org
 * @license GNU GPL v3 or later
 */
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');
require_once(JPATH_ADMINISTRATOR.'/components/com_j2store/library/plugins/app.php');
class plgJ2StoreApp_socialmedia extends J2StoreAppPlugin
{
	/**
	 * @var $_element  string  Should always correspond with the plugin's filename,
	 *                         forcing it to be unique
	 */
    var $_element   = 'app_socialmedia';

    /**
     * Overriding
     *
     * @param $options
     * @return unknown_type
     */
    function onJ2StoreGetAppView( $row )
    {

	   	if (!$this->_isMe($row))
    	{
    		return null;
    	}

    	$html = $this->viewList();


    	return $html;
    }

    /**
     * Validates the data submitted based on the suffix provided
     * A controller for this plugin, you could say
     *
     * @param $task
     * @return html
     */
    function viewList()
    {
    	$app = JFactory::getApplication();
    	$option = 'com_j2store';
    	$ns = $option.'.app.'.$this->_element;
    	$html = "";
    	JToolBarHelper::title(JText::_('J2STORE_APP').'-'.JText::_('PLG_J2STORE_'.strtoupper($this->_element)),'j2store-logo');
    	JToolBarHelper::apply('apply');
    	JToolBarHelper::save();
    	JToolBarHelper::back('PLG_J2STORE_BACK_TO_APPS', 'index.php?option=com_j2store&view=apps');
    	JToolBarHelper::back('J2STORE_BACK_TO_DASHBOARD', 'index.php?option=com_j2store');

    	$vars = new JObject();
    	//model should always be a plural
    	$this->includeCustomModel('AppSocialmedias');

    	$model = F0FModel::getTmpInstance('AppSocialmedias', 'J2StoreModel');

    	$data = $this->params->toArray();
    	$newdata = array();
    	$newdata['params'] = $data;
    	$form = $model->getForm($newdata);
    	$vars->form = $form;

    	$this->includeCustomTables();

    	$id = $app->input->getInt('id', '0');
    	$vars->id = $id;
    	$vars->action = "index.php?option=com_j2store&view=app&task=view&id={$id}";
    	$html = $this->_getLayout('default', $vars);
    	return $html;
    }

    /**
     * Method to get the image based on the product type
     * */
    function getImages($product){
        if (!isset($product->j2store_product_id)) {
          $ptable = F0FTable::getAninstance('Product', 'J2StoreTable');
          $ptable->load(array('product_source' =>  'com_content','product_source_id' => $product->id ));
          $product_helper = J2Store::product();
          $product = $product_helper->setId( $ptable->j2store_product_id)->getProduct();
        }

        // this is j2store products obj, get the j2store images if any
        if (!empty($product->main_image)) {
          return JURI::root().$product->main_image;
        } else if (!empty($product->thumb_image)) {
          return JURI::root().$product->thumb_image;
        } else{
          // article item, get article images if any
          $src_images = new stdClass();
          if (isset($product->source->images)) {
            $src_images = json_decode($product->source->images);
          }
          if (!empty($src_images->image_intro)) {
            return JURI::root().$src_images->image_intro;
          }elseif (!empty($src_images->image_fulltext)) {
            return JURI::root().$src_images->image_fulltext;
          }
        }
      return '';
    }

    function getProductName($product){      
        if (!isset($product->j2store_product_id)) {
          return $product->title;
        } else{
          return $product->product_name;
        }
    }

    function getProductPrice($product){      
        if (isset($product->j2store_product_id)) {
          // return formated price
          if ($this->params->get('include_price',1)) {
            $formated_price = '';
            try{
              $formated_price = J2Store::currency()->format($product->pricing->price);
            } catch(Exception $e){}
            $pre = JText::_('J2STORE_APP_SOCIALMEDIA_MESSAGE_PRICE_PRE');
            $post = JText::_('J2STORE_APP_SOCIALMEDIA_MESSAGE_PRICE_POST');
            if ($pre == 'J2STORE_APP_SOCIALMEDIA_MESSAGE_PRICE_PRE') {
              $pre = '(';
            }
            if ($post == 'J2STORE_APP_SOCIALMEDIA_MESSAGE_PRICE_POST') {
              $post = ') ';
            }
            $formated_price = (!empty($formated_price))? $pre.$formated_price.$post: '';
            return $formated_price;
          }

        } else{
          return '';
        }
    }

    function displaySocialPlugins($event, $product){

      $app = JFactory::getApplication();
      if($app->isAdmin() || empty($event)) return '';
      $html ='';
      if( $this->params->get('display_event') == $event){
        $vars = new JObject();
        $vars->media_url = $this->getImages($product);
        $vars->product_name = $this->getProductName($product);
        $vars->product_price = $this->getProductPrice($product);

        if($this->params->get('display_socialmedia_in') =='product_view' &&  $app->input->get('task') =='view'){
              //here we have to check the view is product
              $html = $this->_getLayout('form', $vars);
          }elseif($this->params->get('display_socialmedia_in') =='category_view' && $app->input->get('task') !='view' ){
            //here we have to check the view is product
            $html = $this->_getLayout('form', $vars);
          }elseif($this->params->get('display_socialmedia_in') =='both'){

            $html = $this->_getLayout('form', $vars);
          }

      }

      return $html;
    }

	/**
	 * event handler to display before rendering the Price
	 * @param object $product
	 */
    public function onJ2StoreBeforeRenderingProductPrice($product){
    	return $this->displaySocialPlugins('BeforeRenderingProductPrice',$product);
    }

    /**
     * event handler to display after rendering the Price
     * @param object $product
     */
    public function onJ2StoreAfterRenderingProductPrice($product){
      return $this->displaySocialPlugins('AfterRenderingProductPrice',$product);
    }

    /**
     * event handler to display before the cart button
     * @param object $product
     */
    public function onJ2StoreBeforeAddToCartButton($product){
      return $this->displaySocialPlugins('BeforeAddToCartButton',$product);
    }

    /**
     * event handler to display after the cart button
     * @param object $product
     */
    public function onJ2StoreAfterAddToCartButton($product){
      return $this->displaySocialPlugins('AfterAddToCartButton',$product);
    }

/* Article manager events */

    public function onContentAfterTitle($context,$items,$params){
      return $this->displaySocialPlugins('afterDisplayTitle',$items);
    }

    public function onContentBeforeDisplay($context,$items,$params){
      return $this->displaySocialPlugins('beforeDisplayContent',$items);
   	}

   	public function onContentAfterDisplay($context,$items,$params){
      return $this->displaySocialPlugins('afterDisplayContent',$items);      
   	}

    // ref: https://developers.facebook.com/docs/reference/opengraph/object-type/product.group/
   public function onJ2StoreViewProductList(&$items, &$view, $params, $model){

   		$app = JFactory::getApplication();
			$document = JFactory::getDocument();
			$fb_appid = $this->params->get('facebook_app_id');
			$document->setMetaData('fb:app_id',$this->params->get('facebook_app_id',0));
      $document->setMetaData('og:type', 'product.group');

      $uri = JFactory::getURI();
      $absolute_url = $uri->toString();
      $document->setMetaData('og:url', $absolute_url);
      $document->setMetaData('og:title', $document->getTitle());

      $image_url = '';
      foreach ($items as $item) {
        $image_url = $this->getImages($item);
        $image_url = (empty($image_url)) ?'' : $image_url ;
        if(!empty($image_url)) break;
      }
      $document->setMetaData('og:image', $image_url);

   	} 

    // refernce : https://developers.facebook.com/docs/reference/opengraph/object-type/product/
   	public function onJ2StoreViewProduct($product, $view){
   		$document = JFactory::getDocument();
      // get any available image
        $image_url = $this->getImages($product);
        $image_url = (empty($image_url)) ?'' : $image_url ;

   		//make sure facebook share is enabled
   		if($this->params->get('show_facebook_icon',1)){
	   		$document->setMetaData('fb:app_id',$this->params->get('facebook_app_id',0));
	   		$document->setMetaData('og:type', 'product');
      //  $document->setMetaData('og:locale', 'product');
        $document->setMetaData('og:title', $product->product_name);
	   		
  	   	if($image_url ){
            $document->setMetaData('og:image', $image_url);
        }
			  //$document->setMetaData('product:description', strip_tags($document->getDescription()));

	   		//price with tax
	   		$document->setMetaData('product:original_price:amount', $product->pricing->base_price);
	   		$document->setMetaData('product:original_price:currency', J2Store::currency()->getCode() );

	   		//without tax
	   		$document->setMetaData('product:pretax_price:amount', $product->pricing->base_price);
	   		$document->setMetaData('product:pretax_price:currency',J2Store::currency()->getCode());

	   		$document->setMetaData('product:weight:value', $product->variant->weight);
	   		$document->setMetaData('product:weight:units', $product->variant->weight_unit);

	   		//sale prices
	   		$document->setMetaData('product:sale_price:amount', $product->pricing->price);
	   		$document->setMetaData('product:sale_price:currency', J2Store::currency()->getCode());

	  /*  		$document->setMetaData('product:sale_price_dates:start', 'product');
	   		$document->setMetaData('product:sale_price_dates:stop', 'product');
 */
	   		$availability = $product->variant->availability ? 'InStock':'OutOfStock';
	   		$document->setMetaData('product:availability',$availability);
	   		$document->setMetaData('product:brand',$product->manufacturer );

	   		$document->setMetaData('product:category',$product->source->category_title);
	   		/* $document->setMetaData('product:color', 'product'); */
	   		$document->setMetaData('product:isbn', $product->variant->upc);
	   		$document->setMetaData('product:upc', $product->variant->upc);
   		}

   		if($this->params->get('twitter_meta_tags',1)){

   			if($this->params->get('twitter_add_card_meta',1)){
	   			// Must be set to a value of “product” // product card deprecated so using summary
	   			$document->setMetaData('twitter:card', $this->params->get('twitter_summary_card_type','summary'));//twitter_summary_card_type

	   			// The Twitter @username the card should be attributed to. Required
	   			$document->setMetaData('twitter:site',$this->params->get('twitter_site_account'));
	   			$document->setMetaData('twitter:creator', $this->params->get('twitter_personal_account'));
	   			
	   			$formated_price = '';
	   			try{
					$formated_price = J2Store::currency()->format($product->pricing->price);
				} catch(Exception $e){}
	   			
	   			$formated_price = (!empty($formated_price))? ' ( '.$formated_price.' ) ': '';
	   			
	   			$document->setMetaData('twitter:title', $product->product_name . $formated_price);
				
	   			if($image_url ){
		   			$document->setMetaData('twitter:image',  $image_url);
		   			if ($this->params->get('twitter_product_image_width') != 0)
					   {
							$document->setMetaData('twitter:image:width',$this->params->get('twitter_product_image_width',50));
					   }
			    }

				if(!empty($product->product_short_desc)){
            $anoffset = $this->params->get('twitter_product_description_limit',100);
            $pos=strlen($product->product_short_desc);
            if (strlen($product->product_short_desc) > $anoffset ){
	   				  $pos=strpos($product->product_short_desc, ' ', $anoffset);
            }
	   				$short_desc = strip_tags( substr($product->product_short_desc,0,$pos ) ) ; 
	   				$document->setMetaData('twitter:description', $short_desc);
	   			}
	   			
			    /* 
			     * these tags are depricated in the new twitter api
			     * $document->setMetaData('twitter:label1', 'price');
	   			$document->setMetaData('twitter:data1', $product->pricing->price);

	   			if(!empty($product->product_short_desc)){
	   				$pos=strpos($product->product_short_desc, ' ', $this->params->get('twitter_product_description_limit',100));
	   				$document->setMetaData('twitter:label2', 'description');
	   				$document->setMetaData('twitter:data1', substr($product->product_short_desc,0,$pos ));
	   			}*/
   			}
   		}
   }

   	public static function getSocialBookMarkUri( $uri = null )
   	{
   		$type = 1;
   		if( $uri === null )
   			return $uri = JFactory::getUri()->__toString();
   	}

}

