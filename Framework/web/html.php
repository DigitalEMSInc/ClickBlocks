<?php

namespace ClickBlocks\Web;

use ClickBlocks\Core;

class HTML
{
   private static $instance = null;

   protected $html = array();

   private function __construct()
   {
      $this->html['top'] = array();
      $this->html['bottom'] = array();
   }

   public static function getInstance()
   {
      if (self::$instance === null) self::$instance = new HTML();
      return self::$instance;
   }

   public function add($html, $type = 'top')
   {
      if (!isset($this->html[$type])) throw new \Exception(err_msg('ERR_HTML_1', array($type)));
      $this->html[$type][md5($html)] = $html;
      return $this;
   }

   public function set($html, $id = null, $type = 'top')
   {
      if (!isset($this->html[$type])) throw new \Exception(err_msg('ERR_HTML_1', array($type)));
      if ($id === null) $this->add($html, $type);
      else $this->html[$type][$id] = $html;
      return $this;
   }

   public function get($id, $type = 'top')
   {
	  if (!isset($this->html[$type])) throw new \Exception(err_msg('ERR_HTML_1', array($type)));
      return $this->html[$type][$id];
   }

   public function delete($id, $type = 'top')
   {
	  if (!isset($this->html[$type])) throw new \Exception(err_msg('ERR_HTML_1', array($type)));
      unset($this->html[$type][$id]);
      return $this;
   }

   public function render($type = 'top')
   {
      if (!isset($this->html[$type])) throw new \Exception(err_msg('ERR_HTML_1', array($type)));
      return implode('', $this->html[$type]);
   }
   
   public static function parse($file, &$template)
   {
	  return foo(new XHTMLParser())->parse($file, $template);
   }
}

?>
