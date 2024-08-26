<?php

namespace ClickBlocks\Web;

/**
 * This class intended for controlling through php js-code written on mootools. 
 *
 * @category   General 
 * @package    Core
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @version    Release: 1.0.0
 */
class JSMootools extends JS
{
   /**
    * The array of file names of general parts of the library.
    * 
    * @var    array
    * @access protected            
    */   
   protected $tools = array('core' => array('mootools-1.2.4-core-nc.js'));
   
   /**
    * The path to library's folder.
    * 
    * @var    string
    * @access protected            
    */     
   protected $toolDir = '/Framework/_engine/web/js/mootools/';
   
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
           return 'window.addEvent(\'domready\', function(){' . implode(endl, $params) . '});';
      }
   }
}

?>
