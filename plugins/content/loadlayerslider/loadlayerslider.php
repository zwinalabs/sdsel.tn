<?php
/*-------------------------------------------------------------------------
# loadlayerslider - Creative Slider content plugin
# -------------------------------------------------------------------------
# @ author    Janos Biro, Balint Polgarfi
# @ copyright Copyright (C) 2017 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php
defined('_JEXEC') or die;

class PlgContentLoadLayerSlider extends JPlugin
{
  public function onContentPrepare($context, &$article, &$params, $page = 0)
  {
    // Don't run this plugin when the content is being indexed
    if ($context == 'com_finder.indexer') {
      return true;
    }

    // Simple performance check to determine whether bot should process further
    if (strpos($article->text, '{creativeslider ') !== false || strpos($article->text, '{layerslider ') !== false) {
      jimport('joomla.application.module.helper');
      $article->text = preg_replace_callback('/{(?:layer|creative)slider\s+id="?(.+?)"?}/', array($this, 'onShortcodeMatch'), $article->text);
    }

    if (strpos($article->text, '{ls-navigate') !== false) {
      $pattern = '/{ls-navigate\s+id="(.+?)"\s+action="?(.+?)"?}(.+?){\/ls-navigate}/';
      $replacement = '<a class="ls-navigate" href="javascript:;" onclick="jQuery(\'#layerslider_$1\').layerSlider(parseInt(\'$2\')||\'$2\')">$3</a>';
      $article->text = preg_replace($pattern, $replacement, $article->text);
    }
  }

  protected function onShortcodeMatch($shortcode)
  {
    $attribs = array('style' => 'none');
    $module = JModuleHelper::getModule('mod_layer_slider', 'LayerSlider'.rand());
    $module->params = 'slider='.$shortcode[1];
    return JModuleHelper::renderModule($module, $attribs);
  }
}
