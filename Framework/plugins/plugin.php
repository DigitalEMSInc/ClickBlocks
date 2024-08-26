<?php

namespace ClickBlocks\Plugins;

use ClickBlocks\Core,
    ClickBlocks\Exceptions;

class Plugin
{
   protected static $params = null;
   protected $config = null;
   protected $page = null;

   public function __construct($xml)
   {
      $this->config = Core\Register::getInstance()->config;
      $this->page = Core\Register::getInstance()->page;
      $this->parseXML($xml);
   }
   
   public function getVersion()
   {
   }
   
   public function getAuthor()
   {
   }
   
   public function getURL()
   {
   }
   
   public function getBrickXSLTPath($class) 
   {
      $class = strtolower(Core\PHPParser::getClassName($class)); 
      $xslt = self::$params->xpath('Bricks/Brick[translate(@Class, \'ABCDEFGHIJKLMNOPQRSTUVWXYZ\', \'abcdefghijklmnopqrstuvwxyz\')="' . $class . '"]/XSLT');
      return '/Framework/_engine/plugins/' . Core\PHPParser::getClassName($this) . '/bricks/' . $xslt[0];
   }
   
   public function getBrickXMLPath($class)
   {
      $class = strtolower(Core\PHPParser::getClassName($class)); 
      $xml = self::$params->xpath('Bricks/Brick[translate(@Class, \'ABCDEFGHIJKLMNOPQRSTUVWXYZ\', \'abcdefghijklmnopqrstuvwxyz\')="' . $class . '"]/XML');
      return '/Framework/_engine/plugins/' . Core\PHPParser::getClassName($this) . '/bricks/' . $xml[0];  
   }
   
   public function getBrickHTML($class)
   {
      $dir = dirname(__FILE__) . '/LogInOut/bricks/';
      $xslt = $this->getBrickXSLTPath($class);
      $xml = $this->getBrickXMLPath($class);
      return Core\HTML::XMLToHTML($dir . $xml, $dir . $xslt);    
   }
   
   protected function parseXML($xml)
   {
      if (!self::$params) self::$params = simplexml_load_file($xml);
   }
}

?>