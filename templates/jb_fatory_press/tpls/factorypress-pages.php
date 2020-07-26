<?php
/** 
 *------------------------------------------------------------------------------
 * @package       T3 Framework for Joomla!
 *------------------------------------------------------------------------------
 * @copyright     Copyright (C) 2004-2013 JoomlArt.com. All Rights Reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 * @authors       JoomlArt, JoomlaBamboo, (contribute to this project at github 
 *                & Google group to become co-author)
 * @Google group: https://groups.google.com/forum/#!forum/t3fw
 * @Link:         http://t3-framework.org 
 *------------------------------------------------------------------------------
 */


defined('_JEXEC') or die;
?>

<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>"
	  class='<jdoc:include type="pageclass" />'>

<head>
	<jdoc:include type="head" />
	<?php $this->addCss('sppagebuilder') ?>
	<?php $this->addCss('responsive') ?>
	
	<?php $this->loadBlock('head') ?>
</head>

<body>

<div class="t3-wrapper"> <!-- Need this wrapper for off-canvas menu. Remove if you don't use of-canvas -->

  <?php $this->loadBlock('topbar') ?>
  
  <?php $this->loadBlock('header') ?>

  <?php $this->loadBlock('mainnav') ?>
  
  <?php $this->loadBlock('slideshow') ?>
  
  <?php $this->loadBlock('masthead') ?>
  
  <?php $this->loadBlock('navhelper') ?>
 
  <?php $this->loadBlock('mainbody-blank') ?>

  
  <?php $this->loadBlock('footer') ?>

</div>


<!-- ==================== Style Switcher ==================== -->
		
<section id="style-switcher" class="switcher">
  <div class="switch_btn">
		<button><i class="fa fa-cog fa-spin"></i></button>
  </div>
  
  <div class="switch_menu">
	<h5 class="title">Style Switcher</h5>
		
		<div class="switch_body">
			<div class="switcher_container">
				<div class="layout-style">
					<h5>Layout Style</h5>

					  <select id="layout-style">
						<option value="1">Wide</option>
						<option value="2">Boxed</option>
					  </select>
				</div>
				<h5>Predefined Colors</h5>
				<ul class="colors" id="colors">
				  <li><a href="index.php" class="color2"></a></li> 
				  <li><a href="index.php/red" class="color3" ></a></li>
				  <li><a href="index.php/green" class="color4"></a></li>
				  <li><a href="index.php/blue" class="color5"></a></li>
				  
				</ul>
				<h5>Background Image</h5>
				<ul class="colors bg" id="bg">
				  <li><a href="#" class="bg1"></a></li>
				  <li><a href="#" class="bg2"></a></li>
				  <li><a href="#" class="bg3"></a></li>
				  <li><a href="#" class="bg4"></a></li>
				  <li><a href="#" class="bg5"></a></li>
				  <li><a href="#" class="bg6"></a></li>
				  <li><a href="#" class="bg7"></a></li>
				  <li><a href="#" class="bg8"></a></li>
				  <li><a href="#" class="bg9"></a></li>
				</ul>
			</div>
		</div>
	</div>
</section>

</body>

</html>
