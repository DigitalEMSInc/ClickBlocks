<?php
/**
 * ClickBlocks.PHP v. 1.0
 *
 * Copyright (C) 2010  SARITASA LLC
 * http://www.saritasa.com
 *
 * This framework is free software. You can redistribute it and/or modify
 * it under the terms of either the current ClickBlocks.PHP License
 * viewable at theclickblocks.com) or the License that was distributed with
 * this file.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY, without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * You should have received a copy of the ClickBlocks.PHP License
 * along with this program.
 *
 * Responsibility of this file: jquery.php
 *
 * @category   General
 * @package    Core
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @link       http://www.saritasa.com
 * @since      File available since Release 1.0.0
 */

namespace ClickBlocks\Web;

/**
 * This class intended for controlling through php js-code written on jquery.
 *
 * @category   General
 * @package    Core
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @version    Release: 1.0.0
 */
class JSJQuery extends JS
{
   /**
    * The array of file names of general parts of the library.
    *
    * @var    array
    * @access protected
    */
   protected $tools = array('core' => array('jquery-1.4.2.min.js'),
                            
                            'scroll' => array('core', 'jquery.scrollTo.js'),
                            'center' => array('core', 'jquery.centerIt.js'),
                            'clockpicker' => array('newcore', 'jquery.clockpick.1.2.9.min.js'),
                            'sort' => array('core', 'jquery-ui-1.8.4.custom.min.js'),
                            'tools' => array('core', 'jquery.tools.min.js'),
                            'slideshow' => array('core', 'jquery.cycle.min.js'),
                            'coinslider' => array('core', 'coin-slider/coin-slider.min.js'),
                            'newcore' => array('jquery-1.8.2.min.js'),
                            'newuicore' => array('newcore','jquery.ui.core.js'),
                            'newuiwidget' => array('newcore','jquery.ui.widget.js'),
                            'newuimouse' => array('newcore','jquery.ui.mouse.js'),
                            'newuisortable' => array('newcore','jquery.ui.sortable.js'));

   /**
    * The path to library's folder.
    *
    * @var    string
    * @access protected
    */
   protected $toolDir = '/Framework/_engine/web/js/jquery/';

   /**
    * Returns a js-code fragment of certain type.
    *
    * @param string $type
    * @param array $params
    * @return string
    * @access public
    */
   public function getCode($type, array $params = null)
   {
      switch ($type)
      {
         case 'domready':
           return '$(document).ready(function(){' . implode(endl, $params) . '});';
      }
   }
}

?>
