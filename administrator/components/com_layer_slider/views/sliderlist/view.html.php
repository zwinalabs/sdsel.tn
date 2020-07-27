<?php
/*-------------------------------------------------------------------------
# com_layer_slider - Creative Slider
# -------------------------------------------------------------------------
# @ author    John Gera, George Krupa, Janos Biro, Balint Polgarfi
# @ copyright Copyright (C) 2017 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class Layer_sliderViewSliderlist extends JViewLegacy {

    /**
     * Display the view
     */
    public function display($tpl = null) {
        // Check for errors.
        $errors = $this->get('Errors');
        if (!empty($errors)) {
            throw new Exception(implode("\n", $errors));
        }

        $GLOBALS['ls_screen'] = (object) array(
            'id' => 'toplevel_page_layerslider',
            'base' => 'toplevel_page_layerslider'
        );

        // simulate wp page
        ${'_GET'}['page'] = 'layerslider';

        parent::display($tpl);
    }

}
