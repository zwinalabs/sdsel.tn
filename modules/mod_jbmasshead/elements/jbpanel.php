<?php
/**
 * @version    CVS: 1.0.0
 * @package    JB Masshead
 * @author     Priya Bose <support@joomlabuff.com>
 * @copyright  2016 www.joomlabuff.com
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */


defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.form.formfield');

require_once(dirname(__FILE__).'/../behavior.php');
class JFormFieldJapanel extends JFormField {
    protected $type = 'Japanel';
    
    protected function getInput() {
    	$func = (string) $this->element['function'];
    	if(!$func) {
    		$func = 'init';
    	}
    	
    	if(method_exists($this, $func)) {
    		call_user_func_array(array($this, $func), array());
    	}
    	return null;
    }
    
    protected function init() {
    	$doc = JFactory::getDocument();
        $path = JURI::root().$this->element['path'];
        if (!version_compare(JVERSION, '3.4', 'lt')) // joomla 3.4.x not call mootools by default
        	JHtml::_('behavior.framework', true);
        $doc->addScript($path.'jbpanel/depend.js');
        if(version_compare(JVERSION, '3.0', 'lt')) {
        	JHTML::_('JABehavior.jquery');
        	//JHTML::_('JABehavior.jquerychosen', '.pane-slider select');
        	JHTML::_('JABehavior.jquerychosen', '.form-validate select');
        	
        	$doc->addStyleSheet($path.'jbpanel/style.css');
        	$doc->addScript($path.'jbpanel/script.js');
        } else {
        	$doc->addStyleSheet($path.'jbpanel/style30.css');
        	$doc->addScript($path.'jbpanel/script30.js');
        }
        return null;
    }
    
    protected function depend() {
		$group_name = 'jform';
    	preg_match_all('/jform\\[([^\]]*)\\]/', $this->name, $matches);
		
		if(!isset($matches[1]) || empty($matches[1])){
			preg_match_all('/jaform\\[([^\]]*)\\]/', $this->name, $matches);
			$group_name = 'jaform';
		}
		
		
		$script = '';
		if(isset($matches[1]) && !empty($matches[1])) {
			foreach ($this->element->children() as $option){
				$elms = preg_replace('/\s+/', '', (string)$option[0]);
				$script .= "
					JADepend.inst.add('".$option['for']."', {
						val: '".$option['value']."',
						elms: '".$elms."',
						group: '".$group_name . '[' . @$matches[1][0] . ']'."'
					});";
			}
		}
		if(!empty($script)) {
			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration("
			$(window).addEvent('load', function(){
				".$script."
			});");
		}
    }
}