<?php
/**
 * @package   T3 Blank
 * @copyright Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

if (is_array($this->getParam('skip_component_content')) && 
  in_array(JFactory::getApplication()->input->getInt('Itemid'), $this->getParam('skip_component_content'))) 
return;
?>

<div class="home">

	<?php if ($this->countModules('service-tab')) : ?>
		<!-- SERVICE-TAB -->
		<div class="wrap t3-sl t3-sl-2 <?php $this->_c('service-tab') ?>">
			<div class="container">
				<jdoc:include type="modules" name="<?php $this->_p('service-tab') ?>" style="raw" />
			</div>
		</div>
		<!-- //SERVICE-TAB -->
	<?php endif ?>
	

	<?php if ($this->countModules('home-3')) : ?>
		<!-- HOME SL 3 -->
		<div class="wrap t3-sl t3-sl-3 <?php $this->_c('home-3') ?>">
			<div class="container">
				<jdoc:include type="modules" name="<?php $this->_p('home-3') ?>" style="raw" />
			</div>
		</div>
		<!-- //HOME SL 3 -->
	<?php endif ?>
	
	<?php $this->loadBlock('mainbody') ?>
	
	<?php if ($this->countModules('service-bottom')) : ?>
		<!-- SERVICE-BOTTM -->
		<div class="wrap  t3-sl t3-sl-5 <?php $this->_c('service-bottom') ?>">
			<div class="container">
				<jdoc:include type="modules" name="<?php $this->_p('service-bottom') ?>" style="raw" />
			</div>
		</div>
		<!-- //SERVICE-BOTTOM -->
	<?php endif ?>

	<?php if ($this->countModules('home-4')) : ?>
		<!-- HOME SL 4 -->
		<div class="wrap t3-sl t3-sl-4 <?php $this->_c('home-4') ?>">
			<div class="container">
				<jdoc:include type="modules" name="<?php $this->_p('home-4') ?>" style="raw" />
			</div>
		</div>
		<!-- //HOME SL 4 -->
	<?php endif ?>

	
	
	
	

</div>
