<?php
/**
 * ANGIE - The site restoration script for backup archives created by Akeeba Backup and Akeeba Solo
 *
 * @package   angie
 * @copyright Copyright (c)2009-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL v3 or later
 */

defined('_AKEEBA') or die();

?>
<html>
<head>
	<title>Akeeba Backup Site Restoration Script v. <?php echo AKEEBA_VERSION ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php include __DIR__ . '/php/head.php' ?>
    <script type="text/javascript">window.addEventListener('DOMContentLoaded', function(event) { akeeba.fef.menuButton(); akeeba.fef.tabs(); });</script>
</head>
<body class="akeeba-renderer-fef">
<!--[if IE]><div class="ie9"><![endif]-->

<?php if (AApplication::getInstance()->getContainer()->input->getCmd('tmpl', '') != 'component'): ?>
	<header class="akeeba-navbar">
        <div class="akeeba-maxwidth akeeba-flex">
            <!-- Branding -->
            <div class="akeeba-nav-logo">
                <a href="#">
                    <span class="aklogo-deluxe-j"></span>
                    <span class="akeeba-product-name">
                        Akeeba Backup Site Restoration Script
                        <span class="akeeba-label--red--small">v.<?php echo AKEEBA_VERSION ?></span>
                    </span>
                </a>
                <a href="#" class="akeeba-menu-button akeeba-hidden-desktop akeeba-hidden-tablet" id="mobileMenuToggler"
                   title="<?php echo AText::_('ANGIE_COMMON_MSG_TOGGLE_NAV') ?>">
                    <span class="akion-navicon-round"></span>
                </a>
            </div>

            <!-- Navigation -->
            <nav>
		        <?php include __DIR__ . '/php/buttons.php'; ?>
            </nav>
        </div>
	</header>

    <div class="akeeba-maxwidth" id="mainContent">
<?php endif; ?>

	    <?php include __DIR__ . '/php/messages.php' ?>
	    <?php echo $this->getBuffer() ?>

<?php if (AApplication::getInstance()->getContainer()->input->getCmd('tmpl', '') != 'component'): ?>
    </div>

	<footer id="akeeba-footer">
		<div class="akeeba-maxwidth">
            <div class="akeeba-container--75-25">
                <div>
                    <p class="credit">
                        Copyright &copy;2006 &ndash; <?php echo date('Y') ?> Akeeba Ltd. All rights reserved.<br/>
                        ANGIE is Free Software distributed under the
                        <a href="http://www.gnu.org/licenses/gpl.html">GNU GPL version 3</a> or any later version published by the FSF.
                    </p>
                </div>
                <div style="text-align: right">
                    <a href="https://www.akeeba.com" rel="nofollow" target="_blank" style="color: #cdcdcd">
                        <span class="aklogo-company-logo md"></span>
                    </a>
                </div>
            </div>
		</div>
	</footer>
<?php endif; ?>

<!--[if IE]></div><![endif]-->
</body>
</html>
