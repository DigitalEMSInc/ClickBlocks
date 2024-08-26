<?php

namespace ClickBlocks\Web;

use ClickBlocks\Core,
    ClickBlocks\Web\UI\Helpers,
    ClickBlocks\Web\UI\POM;

class Ajax
{
   /**
    * The flag of a caching.
    *
    * @var    boolean
    * @access public
    */
   public $noCache = true;

   public $contentType = 'text/html';

   /**
    * The variable keeping js-code of inquiry answers.
    *
    * @var    array
    * @access protected
    */
   protected $actions = null;

   /**
    * The instance of the js-manager object.
    *
    * @var    object
    * @access protected
    */
   protected $js = null;

   /**
    * The instance of the Register object.
    *
    * @var    object
    * @access protected
    */
   protected $reg = null;

   private $parent = null;

   /**
    * The instance of the class.
    *
    * @var    object
    * @access protected
    */
   private static $instance = null;

   /**
    * Ajax::__construct()
    *
    * Initialization of the Ajaxtor js-object.
    *
    * @access private
    * @since  Method available since Release 1.0.0
    */
   private function __construct()
   {
      $this->reg = Core\Register::getInstance();
      $this->js = JS::getInstance();
      $this->parent = isset($this->reg->fv['ajaxsubmit']) ? 'parent.' : '';
      $this->actions = array('top' => array(), 'bottom' => array());
   }

   /**
    * Ajax::getInstance()
    *
    * Getting of the object of the class.
    *
    * @return object
    * @access public
    * @static
    * @since  Method available since Release 3.0.0
    */
   public static function getInstance()
   {
      if (self::$instance === null) self::$instance = new self();
      return self::$instance;
   }

   /**
    * Ajax::doit()
    *
    * Execution of ajax-requests.
    *
    * @param  array $fv
    * @access public
    * @since  Method available since Release 1.0.0
    */
   public function doit(array $fv = null)
   {
      if (!$fv)
      {
         $fv = $this->reg->fv;
         $fv = ($fv) ?: array_merge($_GET, $_POST, $_FILES);
      }
      if (Ajax::isAction() && $fv['ajaxfunc'])
      {
         if ($fv['ajaxargs']) $fv['ajaxargs'] = $this->getAjaxArguments($fv['ajaxargs']);
         ob_start();
         $func = new Core\Delegate($fv['ajaxfunc']);
         $func($fv['ajaxargs']);
         $cnt = trim(ob_get_contents());
         ob_end_clean();
         if ($cnt != '') $this->addAction('alert', $cnt);
         $this->perform();
      }
      return $this;
   }

   /**
    * Ajax::perform()
    *
    * Execution of ajax-responses.
    *
    * @access public
    * @since  Method available since Release 2.1.0
    */
   public function perform()
   {
      $actions = implode(';', $this->actions['top']) . ((count($this->actions['bottom'])) ? ';' : '') . implode(';', $this->actions['bottom']);
      if ($actions != '') $actions .= ';' . $this->parent . 'ajax.initViewStates(true)';
      if (Ajax::isAction())
      {
         header('Content-type: ' . $this->contentType . '; charset=' . $this->reg->config->charset);
         if ($this->noCache)
         {
            header('Cache-Control: no-store, no-cache, must-revalidate');
            header('Cache-Control: post-check=0, pre-check=0', false);
            header('Pragma: no-cache');
         }
         if (!Ajax::isAjaxRequest()) echo JS::script($actions);
         else echo $actions;
         exit;
      }
      else if (strlen($actions) > 1) $this->js->add(new Helpers\Script(null, $actions), 'foot');
      return $this;
   }

   public function getActions()
   {
      return $this->actions;
   }

   public function setActions(array $actions = array())
   {
      $this->actions = $actions;
      return $this;
   }

   /**
    * Ajax::isAjaxRequest()
    *
    * Check of that there was ajax-request.
    * Returns true if it was also false otherwise.
    *
    * @return boolean
    * @access public
    * @since  Method available since Release 2.2.1
    */
   public static function isAction()
   {
      $fv = (Core\Register::getInstance()->fv) ?: array_merge($_GET, $_POST, $_FILES);
      return ($fv['ajaxfunc'] || $fv['ajaxsubmit']);
   }

   public static function isAjaxRequest()
   {
      return ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
   }

   /**
    * Ajax::isSubmit()
    *
    * Check of that there was ajax-submit a form.
    * Returns true if it was also false otherwise.
    *
    * @return boolean
    * @access public
    * @since  Method available since Release 1.3.1
    */
   public static function isSubmit()
   {
      $fv = (Core\Register::getInstance()->fv) ?: array_merge($_GET, $_POST, $_FILES);
      return isset($fv['ajaxsubmit']);
   }

   protected function addAction($action, $time = 0, $place = false)
   {
      if ($time > 0) $action = $this->parent . 'setTimeout(function(){' . $action . ';}, ' . (int)$time . ')';
      $this->actions[($place) ? 'top' : 'bottom'][] = $action;
      return $this;
   }

   public function alert($text, $time = 0, $place = false)
   {
      return $this->addAction($this->parent . 'Ajax.action(\'alert\', \'' . $this->replaceBreakup($text) . '\')', $time, $place);
   }

   public function insert($html, $id, $time = 0, $place = false)
   {
      return $this->addAction($this->parent . 'Ajax.action(\'insert\', \'' . addslashes($this->delBreakup($html)) . '\', \'' . $id . '\')', $time, $place);
   }

   public function replace($html, $id, $time = 0, $place = false)
   {
      return $this->addAction($this->parent . 'Ajax.action(\'replace\', \'' . addslashes($this->delBreakup($html)) . '\', \'' . $id . '\')', $time, $place);
   }

   public function inject($html, $id, $mode = 'top', $time = 0, $place = false)
   {
      return $this->addAction($this->parent . 'Ajax.action(\'inject\', \'' . addslashes($this->delBreakup($html)) . '\', \'' . $id . '\', \'' . $mode . '\')');
   }

   public function remove($id, $time = 0, $place = false)
   {
      return $this->addAction($this->parent . 'Ajax.action(\'remove\', \'' . $id . '\')', $time, $place);
   }

   public function redirect($url, $time = 0, $place = false)
   {
      return $this->addAction($this->parent . 'Ajax.action(\'redirect\', \'' . addslashes($this->delBreakup($url)) . '\')', $time, $place);
   }

   public function reload($time = 0, $place = false)
   {
      return $this->addAction($this->parent . 'Ajax.action(\'reload\')', $time, $place);
   }

   public function message($message, $id, $expire, $time = 0, $place = false)
   {
      return $this->addAction($this->parent . 'Ajax.action(\'message\', \'' . addslashes($this->delBreakup($message)) . '\', \'' . $id . '\', \'' . (int)$expire . '\')', $time, $place);
   }

   public function tool($tool, $time = 0, $place = false)
   {
      return $this->addAction($this->parent . 'Ajax.action(\'tool\', \'' . $this->replaceBreakup($tool) . '\')', $time, $place);
   }

   public function css($src, $time = 0, $place = false)
   {
      return $this->addAction($this->parent . 'Ajax.action(\'css\', \'' . $this->replaceBreakup($src) . '\')', $time, $place);
   }

   public function display($id, $display, $time = 0, $place = false)
   {
      return $this->addAction($this->parent . 'Ajax.action(\'display\', \'' . $id . '\', \'' . $display . '\')', $time, $place);
   }

   public function script($script, $time = 0, $place = false)
   {
      return $this->addAction($this->delBreakup($script), $time, $place);
   }

   public static function ajaxObject($obj)
   {
      return '<ajaxobject>' . addslashes(serialize($obj)) . '</ajaxobject>';
   }

   public static function ajaxArray(array $obj)
   {
      $js = array();
      foreach ($obj as $k => $v)
      {
         if (is_bool($v)) $v = (int)$v;
         else if (is_object($v)) $v = "'" . self::ajaxObject($v) . "'";
         else if (is_array($v)) $v = self::ajaxArray($v);
         else if (!is_numeric($v)) $v = "'" . addslashes($v) . "'";
         if (is_int($k)) $js[] = $v;
         else $js[] = "'" . addslashes($k) . "': " . $v;
      }
      $js = (count($js)) ? implode(', ', $js) : '';
      return '[' . $js . ']';
   }

   public static function ajaxCall($method, array $params = null, $sender = null)
   {
      $args = array();
      if ($params) foreach ($params as $param)
      {
         if (is_bool($param)) $param = (int)$param;
         else if (is_object($param)) $param = "'" . self::ajaxObject($param) . "'";
         else if (is_array($param)) $param = self::ajaxArray($param);
         else if (is_string($param)) $param = (substr($param, 0, 4) == 'js::') ? substr($param, 4) : "'" . addslashes($param) . "'";
         $args[] = $param;
      }
      if ($method instanceof Core\IDelegate) $method = $method->__toString();
      $method = str_replace('\\', '\\\\', $method);
      if (is_null($sender)) $function = "'" . $method . "'";
      else
      {
         if ($sender instanceof POM\IWebControl) $sender = $sender->getClientID();
         $function = "['" . $method . "', '" . $sender . "']";
      }
      $args = (count($args)) ? ', ' . implode(', ', $args) : '';
      return htmlspecialchars('ajax.doit(' . $function . $args . ');');
   }

   /**
    * Ajax::getAjaxArgument()
    *
    * Getting of parameters' values sent to the function.
    *
    * @param  string $args
    * @return array
    * @access public
    * @since  Method available since Release 1.0.0
    */
   public function getAjaxArguments($args)
   {
      return json_decode((string)$args, true);
   }

   /**
    * Ajax::delBreakup()
    *
    * Removing of breakup symbols from a string.
    *
    * @param  string $str
    * @return string
    * @access private
    * @since  Method available since Release 1.0.0
    */
   private function delBreakup($str)
   {
      return strtr($str, array("\r" => '&#0013;', "\n" => '&#0010;'));
   }

   /**
    * Ajax::replaceBreakup()
    *
    * Screening of breakup symbols of a string, single quote and backslash.
    *
    * @param  string $str
    * @return string
    * @access private
    * @since  Method available since Release 1.0.0
    */
   private function replaceBreakup($str)
   {
      return strtr($str, array("\\" => "\\\\", "'" => "\'", "\r" => "\\r", "\n" => "\\n"));
   }
}

?>
