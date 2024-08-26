<?php

namespace ClickBlocks\Web;

use ClickBlocks\Core,
    ClickBlocks\Web\UI\Helpers;

class CSS
{
   private static $instance = null;
  
   protected $css = array();
   protected $cssUrlBase;

   private function __construct()
   {
      $this->css['link'] = array();
      $this->css['style'] = array();
   }

   public static function getInstance()
   {
      if (self::$instance === null) self::$instance = new CSS();
      return self::$instance;
   }

   public function add(Helpers\Style $obj, $type = 'link')
   {
      if (!isset($this->css[$type])) throw new \Exception(err_msg('ERR_CSS_1', array($type)));
      $this->css[$type][$obj->id] = $obj;
      return $this;
   }

   /**
    * Helper for adding CSS File
    *
    * Before:
    * $this->css->add(new Helpers\Style($id, null, \CB::url('css').'/'.$file), 'link');
    *
    * After:
    * $this->css->addFile($id, $file);
    *
    * @param string $id ID of JS block
    * @param string $file a JS file
    * @param bool $includeRoot if true then uses CSS base path as prefix to $file
    * @param string|null $media the value of media
    * @return CSS
    */
   public function addFile($id, $file = null, $includeRoot = true, $media = null)
   {
      if($file && $includeRoot) {
         $file = $this->getCssUrlBase().'/'.$file;
      }

      $this->add(new Helpers\Style($id, null, $file, $media));

      return $this;
   }

   /**
    * Returns CSS base path
    *
    * @return string
    */
   public function getCssUrlBase()
   {
      return $this->cssUrlBase ?: ($this->cssUrlBase = Core\IO::url('css'));
   }


   public function set($id, Helpers\Style $obj, $type = 'link')
   {
      if (!isset($this->css[$type])) throw new \Exception(err_msg('ERR_CSS_1', array($type)));
      $this->css[$type][$id] = $obj;
      return $this;
   }

   public function get($id, $type = 'link')
   {
      if (!isset($this->css[$type])) throw new \Exception(err_msg('ERR_CSS_1', array($type)));
      return $this->css[$type][$id];
   }

   public function delete($id, $type = 'link')
   {
      if (!isset($this->css[$type])) throw new \Exception(err_msg('ERR_CSS_1', array($type)));
      unset($this->css[$type][$id]);
      return $this;
   }

   public function render($type = 'link')
   {
      if (!isset($this->css[$type])) throw new \Exception(err_msg('ERR_CSS_1', array($type)));
      foreach ($this->css[$type] as $obj) $html .= $obj->render();
      return $html;
   }

   public static function style($style)
   {
      return foo(new Helpers\Style(null, $style))->render();
   }

   public static function link($src, $charset = null)
   {
      if ($src[0] == '/') $src = Core\IO::url('css') . $src;
      $obj = new Helpers\Style();
      $obj->href = $src;
      $obj->charset = $charset;
      return $obj->render();
   }
}

?>
