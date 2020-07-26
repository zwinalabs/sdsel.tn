<?php
/**
 * --------------------------------------------------------------------------------
 * App Plugin - Recaptcha for J2Store Resigister
 * --------------------------------------------------------------------------------
 * @package     Joomla 2.5 -  3.x
 * @subpackage  J2 Store
 * @author      Alagesan, J2Store <support@j2store.org>
 * @copyright   Copyright (c) 2016 J2Store . All rights reserved.
 * @license     GNU General Public License v or later
 * @link        http://j2store.org
 * --------------------------------------------------------------------------------
 *
 * */
defined('_JEXEC') or die('Restricted access');
?>
<script>
    jQuery(document).ready(function($) {
        grecaptcha.render("<?php echo $vars->id;?>",{sitekey:"<?php echo $vars->pubkey;?>", theme:"<?php echo $vars->theme;?>"});
    });
</script>
<div id="<?php echo $vars->id;?>"></div>
