<?php

namespace ClickBlocks\Web;

use ClickBlocks\Core,
    ClickBlocks\Web\UI\Helpers;

abstract class JS
{
   private static $instance = null;

   protected $js = array();
   protected $jsUrlBase;
   protected $core = array(
       'ajax' => array('ajax.js'),
       'controls' => array('controls.js'),
       'validators' => array('validators.js?v=1'),
       'autofill' => array('ajax', 'controls', 'autofill.js'),
       'uploadbutton' => array('controls', 'uploadbutton.js'),
       'tinymce' => array('tinymce/jscripts/tiny_mce/tiny_mce.js'),
       'colorpicker' => array('controls', 'colorpicker/js_color_picker_v2.js'),
       'raphael' => array('raphael.js'),
       'json' => array('json.js'),
       'imgeditor' => array('ajax', 'controls', 'raphael', 'imgeditor/imgeditor.js'),
       'datetimepicker' => array('datetimepicker/src/js/jscal2.js', 'datetimepicker/src/js/lang/en.js'),
   );
   protected $coreDir = '/Framework/_engine/web/js/';

   private function __construct()
   {
      $this->js['domready'] = array();
      $this->js['link'] = array();
      $this->js['head'] = array();
      $this->js['foot'] = array();
   }

   public static function getInstance()
   {
      if (self::$instance === null)
      {
         $provider = Core\Register::getInstance()->config->jsProvider;
         self::$instance = new $provider();
      }
      return self::$instance;
   }

   public function add(Helpers\Script $obj, $type = 'domready')
   {
      if (!isset($this->js[$type])) throw new \Exception(err_msg('ERR_JS_1', array($type)));
      $this->js[$type][$obj->id] = ($obj->src) ? $obj->render() : $obj->text;
      return $this;
   }

   /**
    * Helper for adding Js
    *
    * Before:
    * $this->js->add(new Helpers\Script($id, $code, \CB::url('js') . '/' . $file), $target);
    *
    * After:
    * $this->js->addHelper($id, $code, $file, true);
    *
    *
    * @param string $id ID of JS block
    * @param string|null $code a JS code
    * @param string|null $file a JS file
    * @param bool $includeRoot if true then uses JS base path as prefix to $file
    * @param string|null $target a target of JS-block
    * @return JS
    */
   public function addHelper($id, $code, $file = null, $includeRoot = true, $target = null)
   {
      $target = $target ?: ($file ? 'link' : 'domready');

      if($file && $includeRoot) {
         $file = $this->getJsUrlBase().'/'.$file;
      }

      $this->add(new Helpers\Script($id, $code, $file), $target);

      return $this;
   }

   /**
    * Returns JS base path
    *
    * @return string
    */
   public function getJsUrlBase()
   {
      return $this->jsUrlBase ?: ($this->jsUrlBase = Core\IO::url('js'));
   }

   /**
    * Helper for adding JS File
    *
    * Before:
    * $this->js->add(new Helpers\Script($id, null, \CB::url('js') . '/' . $file), 'link');
    *
    * After:
    * $this->js->addFile($id, $file);
    *
    * @param string $id ID of JS block
    * @param string $file a JS file
    * @param bool $includeRoot if true then uses JS base path as prefix to $file
    * @return JS
    */
   public function addFile($id, $file, $includeRoot = true)
   {
      return $this->addHelper($id, null, $file, $includeRoot);
   }

   /**
    * Helper for adding JS Code
    *
    * Before:
    * $this->js->add(new Helpers\Script($id, $code), $target);
    *
    * After:
    * $this->js->addCode($id, $code, $target);
    *
    * @param string $id ID of JS block
    * @param string $code a JS code
    * @param string|null $target a target of JS code
    * @return JS
    */
   public function addCode($id, $code, $target = 'domready')
   {
      return $this->addHelper($id, $code, null, true, $target);
   }

   /**
    * Helper for adding JS Code to init a variable
    *
    * Before:
    * $this->js->add(new Helpers\Script($id, $name . '=' . json_encode($value)), $target);
    *
    * After:
    * $this->js->addCode($id, $name, $value, $target);
    *
    * @param string $id ID of JS block
    * @param string $name a name of variable
    * @param mixed $value a value of variable
    * @param string|null $target a target of JS code
    * @return JS
    */
   public function addInit($id, $name, $value, $target = 'domready')
   {
      return $this->addCode($id, $name.' = '.json_encode($value).';', $target);
   }



   public function set($id, Helpers\Script $obj, $type = 'domready')
   {
      if (!isset($this->js[$type])) throw new \Exception(err_msg('ERR_JS_1', array($type)));
      $this->js[$type][$id] = ($obj->src) ? $obj->render() : $obj->text;
      return $this;
   }

   public function get($id, $type = 'domready')
   {
      if (!isset($this->js[$type])) throw new \Exception(err_msg('ERR_JS_1', array($type)));
      return $this->js[$type][$id];
   }

   public function delete($id, $type = 'domready')
   {
      if (!isset($this->js[$type])) throw new \Exception(err_msg('ERR_JS_1', array($type)));
      unset($this->js[$type][$id]);
      return $this;
   }

   public function render($type = 'domready')
   {
      if (!isset($this->js[$type])) throw new \Exception(err_msg('ERR_JS_1', array($type)));
      if (count($this->js[$type]) == 0) return;
      if ($type == 'domready')
      {
         return self::script($this->getCode('domready', $this->js[$type]));
      }
      else if ($type == 'foot') $js = implode(endl, array_reverse($this->js[$type]));
      else $js = implode(endl, $this->js[$type]);
      return ($type == 'link') ? $js : self::script($js);
   }

   public function addTool($tool, $dir = null)
   {
      if (isset($this->tools[$tool])) foreach ($this->tools[$tool] as $tool) $this->addTool($tool, $this->toolDir);
      else if (isset($this->core[$tool])) foreach ($this->core[$tool] as $tool) $this->addTool($tool, $this->coreDir);
      else
      {
         if (!Ajax::isAction())
         {
            $obj = new Helpers\Script($tool);
            $obj->src = (($dir) ? $dir : $this->toolDir) . $tool;
            $this->add($obj, 'link');
            if ($tool == 'ajax.js') $this->add(new Helpers\Script('ajax_init_view_states', 'ajax.initViewStates();'), 'foot');
         }
         else if ($tool != 'ajax.js')
         {
            $ajax = Ajax::getInstance();
            $ajax->tool(($dir ?: $this->toolDir) . $tool, 0, true);
         }
      }
      return $this;
   }

   public function addTools(array $files)
   {
      foreach ($files as $file) $this->addTool($file);
      return $this;
   }

   abstract public function getCode($type, array $params = null);

   public static function script($script)
   {
      return foo(new Helpers\Script(null, $script))->render();
   }

   public static function link($src, $charset = null)
   {
      if ($src[0] == '/') $src = Core\IO::url('js') . $src;
      $obj = new Helpers\Script();
      $obj->src = $src;
      $obj->charset = $charset;
      return $obj->render();
   }

   public static function goURL($url, $isNewWindow = false)
   {
      if (!$url) return;
      if (Ajax::isAjaxRequest())
      {
         echo ($isNewWindow) ? 'window.open("' . $url . '");' : 'window.location.assign("' . $url . '");';
         exit;
      }
      if ($isNewWindow) echo JS::script('window.open("' . $url . '");');
      else
      {
         try
         {
            header('Location: ' . $url);
         }
         catch (Exception $ex)
         {
            echo self::script('window.location.assign("' . $url . '");');
         }
      }
      exit;
   }

   public static function reload()
   {
      if (Ajax::isAjaxRequest()) echo 'window.location.reload(true);';
      else echo self::script('window.location.reload(true);');
      exit;
   }
}

?>
