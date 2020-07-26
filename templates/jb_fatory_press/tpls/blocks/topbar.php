<?php
/**
 * @package   jb_creativ
 * @copyright Copyright (C) 2015 - 2023 Open Source Matters, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */
 
 defined('_JEXEC') or die;
 ?>
 
 <!-- TOPBAR -->
 <?php if ($this->countModules('topbar-left') || $this->countModules('topbar-right') ) : ?>
 <div id="jb-topbar" class="wrap top-bar">
	<div class="container">
		<div class="row">
		<?php if ($this->countModules('topbar-left')) : ?>
			<div class="col-xs-12 col-sm-6 col-md-2  text-left t3-topbar-1  <?php $this->_c('topbar-left') ?>">
				<jdoc:include type="modules" name="<?php $this->_p('topbar-left') ?>" style="T3Xhtml" />
			</div>
		<?php endif ?>
		
		<?php if ($this->countModules('topbar-right')) : ?>
			<div class="col-xs-12 col-sm-12 col-md-10  top-bar-right pull-right <?php $this->_c('topbar-right') ?>">
				<jdoc:include type="modules" name="<?php $this->_p('topbar-right') ?>" style="T3Xhtml" />
			</div>
		<?php endif ?>
		</div>
	</div>
</div>
<?php endif ?>
 <!-- //TOPBAR -->
