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

class Layer_sliderViewCustomStylesEditor extends JViewLegacy {

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
            'id' => 'layerslider_page_ls-style-editor',
            'base' => 'layerslider_page_ls-style-editor'
        );

        // simulate wp page
        ${'_GET'}['page'] = 'ls-style-editor';

        parent::display($tpl);
    }

}
