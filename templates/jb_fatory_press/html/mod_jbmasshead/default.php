<?php
/**
 * ------------------------------------------------------------------------
 * JA Events II template
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
 */

defined('_JEXEC') or die('Restricted access');
?>
<div class="jamasshead inner-banner <?php echo $params->get('moduleclass_sfx','')?>" <?php if(isset($masshead['params']['background'])): ?> style="background-image: url(<?php echo $masshead['params']['background'] ?>)" <?php endif; ?>>
	<div class="container text-center">
		<h2 class="jamasshead-title"><?php echo $masshead['title']; ?></h2>
		<div class="jamasshead-description"><?php echo $masshead['description']; ?></div>
	</div>
</div>	
