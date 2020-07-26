<?php
/**
 * @package   T3 Blank
 * @copyright Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>




<!-- FOOTER -->
<footer id="t3-footer" class="wrap footer t3-footer">

	<?php if ($this->checkSpotlight('footnav', 'footer-1, footer-2, footer-3, footer-4, footer-5, footer-6')) : ?>
		<!-- FOOT NAVIGATION -->
		<div class="container">
			<?php $this->spotlight('footnav', 'footer-1, footer-2, footer-3, footer-4, footer-5, footer-6') ?>
		</div>
		<!-- //FOOT NAVIGATION -->
	<?php endif ?>

	<div class="t3-copyright">
		<div class="container">
			<div class="row">
				<!-- //COPYRIGHTS -->
				<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
					<div class="<?php echo $this->getParam('t3-rmvlogo', 1) ? 'col-md-8' : 'col-md-12' ?> copyright <?php $this->_c('footer') ?>">
						<jdoc:include type="modules" name="<?php $this->_p('footer') ?>" />
					</div>
				</div>
				<!-- //FOOTER MENU -->
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
					<?php if ($this->getParam('t3-rmvlogo', 1)): ?>
						<div class="col-md-4 poweredby text-hide">
							<a class="t3-logo t3-logo-color" href="http://t3-framework.org" title="<?php echo JText::_('T3_POWER_BY_TEXT') ?>"
							   target="_blank" <?php echo method_exists('T3', 'isHome') && T3::isHome() ? '' : 'rel="nofollow"' ?>><?php echo JText::_('T3_POWER_BY_HTML') ?></a>
						</div>
					<?php endif; ?>
					<?php if ($this->countModules('footer-info')) : ?>
						<div class="<?php $this->_c('footer-info') ?>">
							<jdoc:include type="modules" name="<?php $this->_p('footer-info') ?>" style="raw" />
						</div>
						<!-- //FOOTER MENU -->
					<?php endif ?>
				</div>
			</div>
		</div>
	</div>

</footer>
<!-- //FOOTER -->
