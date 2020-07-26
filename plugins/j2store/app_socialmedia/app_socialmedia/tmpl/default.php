<?php
/**
 * @package J2Store
 * @copyright Copyright (c)2014-17 Ramesh Elamathi / J2Store.org
 * @license GNU GPL v3 or later
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.framework');
JHtml::_('behavior.modal');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('script', 'media/j2store/js/j2store.js', false, false);
?>

  <script type="text/javascript">
  Joomla.submitbutton = function(pressbutton) {
		if(pressbutton == 'save' || pressbutton == 'apply') {
			document.adminForm.task ='view';
			document.getElementById('appTask').value = pressbutton;
		}
		if(pressbutton == 'cancel') {
			Joomla.submitform('cancel');
		}
		var atask = jQuery('#appTask').val();

		Joomla.submitform('view');
  }
  </script>

<div class="j2store-configuration">
<form action="<?php echo $vars->action; ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal form-validate">
<?php echo J2Html::hidden('option','com_j2store');?>
<?php echo J2Html::hidden('view','apps');?>
<?php echo J2Html::hidden('app_id',$vars->id);?>
<?php echo J2Html::hidden('appTask', '', array('id'=>'appTask'));?>
<?php echo J2Html::hidden('task', 'view', array('id'=>'task'));?>

<?php echo JHtml::_('form.token'); ?>
<?php

        $fieldsets = $vars->form->getFieldsets();
        $shortcode = $vars->form->getValue('text');
        $tab_count = 0;

        foreach ($fieldsets as $key => $attr)
        {

	             if ( $tab_count == 0 )
	            {

	               echo JHtml::_('bootstrap.startTabSet', 'apps', array('active' => $key));

	            }

	            	echo JHtml::_('bootstrap.addTab', 'apps', $attr->name, JText::_($attr->label, true));

	            ?>
	         <?php  if(J2Store::isPro() != 1 && isset($attr->ispro) && $attr->ispro ==1 ) : ?>
				<?php echo J2Html::pro(); ?>
			<?php else: ?>

	            <div class="row-fluid">
	                <div class="span12">
	                    <?php
	                    $layout = '';
	                    $style = '';
	                    $fields = $vars->form->getFieldset($attr->name);
	                    foreach ($fields as $key => $field)
	                    {
	                    	$pro = $field->getAttribute('pro');
	                    ?>
	                        <div class="control-group <?php echo $layout; ?>" <?php echo $style; ?>>
	                            <div class="control-label"><?php echo $field->label; ?></div>
	                            <?php if(J2Store::isPro() != 1 && $pro ==1 ): ?>
	                            	<?php echo J2Html::pro(); ?>
	                            <?php else: ?>
	                            	<div class="controls"><?php echo $field->input; ?>
	                            	<br />
	                            	<small class="muted"><?php echo JText::_($field->description); ?></small>
	                            <?php endif; ?>

	                            </div>
	                        </div>
	                    <?php
	                    }
	                    ?>
	                </div>
	            </div>
	            <?php endif; ?>
	            <?php
	            echo JHtml::_('bootstrap.endTab');
	            $tab_count++;

        }
        ?>
 </form>
</div>
