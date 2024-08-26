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
 * Responsibility of this file: page.php
 *
 * @category   MVC
 * @package    MVC
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @link       http://www.saritasa.com
 * @since      File available since Release 1.0.0
 */

namespace ClickBlocks\MVC;

use ClickBlocks\Cache\ICache;
use ClickBlocks\Core,
    ClickBlocks\Cache,
    ClickBlocks\Web,
    ClickBlocks\Web\UI\POM,
    ClickBlocks\Web\UI\Helpers;
use ClickBlocks\Core\Config;
use ClickBlocks\Core\Register;
use ClickBlocks\Web\Ajax;
use ClickBlocks\Web\CSS;
use ClickBlocks\Web\HTML;
use ClickBlocks\Web\JS;

/**
 * The parent class implements the controller's logic for site pages according to the MVC pattern.
 *
 * @category   MVC
 * @package    MVC
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @version    Release: 1.0.0
 *
 * @property POM\Body body
 * @property Core\Template $tpl
 */
class Page implements IPage
{
   /**
    * The instance of a Register class
    *
    * @var    Register
    * @access public
    */
   public $reg = null;

   /**
    * The instance of a Config class.
    *
    * @var    Config
    * @access public
    */
   public $config = null;

   /**
    * The instance of a Loader class.
    *
    * @var    Loader
    * @access public
    */
   public $loader = null;

   /**
    * The instance of a Logger class.
    *
    * @var    Logger
    * @access public
    */
   public $logger = null;

   /**
    * The instance of a Debugger class.
    *
    * @var    Debugger
    * @access public
    */
   public $debugger = null;

   /**
    * GET, POST and FILES data
    *
    * @var    array
    * @access public
    */
   public $fv = null;

   /**
    * The instance of a JS class.
    *
    * @var    JS
    * @access public
    */
   public $js = null;

   /**
    * The instance of a CSS class.
    *
    * @var    CSS
    * @access public
    */
   public $css = null;

   /**
    * The instance of a HTML class.
    *
    * @var    HTML
    * @access public
    */
   public $html = null;

   /**
    * The instance of a AJAX class.
    *
    * @var    AJAX
    * @access public
    */
   public $ajax = null;

   /**
    * The instance of a cache class implementing ICache interface.
    *
    * @var    ICache
    * @access public
    */
   public $cache = null;

   /**
    * The instance of a ClickBlocks\Web\UI\Helpers class.
    *
    * @var    \ClickBlocks\Web\UI\Helpers\XHTML
    * @access public
    */
   public $xhtml = null;

   /**
    * The instance of a WebForms\Validators class.
    *
    * @var    POM\Validators
    * @access public
    */
   public $validators = null;

   /**
    * The url of a page to which the transition will be the case if the page is not accessable.
    *
    * @var    string
    * @access public
    */
   public $noAccessURL = null;

   /**
    * The url of a page to which the transition will be the case if the user session is expired.
    *
    * @var    string
    * @access public
    */
   public $noSessionURL = null;

   /**
    * The time (in seconds) of expiration cache of a page.
    *
    * @var    integer
    * @access public
    */
   public $expired = 0;

   /**
    * The object of web-control that was the sender of an ajax-request.
    *
    * @var    IWebControl
    * @access public
    */
   public $sender = null;

   public $callBackPermissions = array('\ClickBlocks\MVC\\', '\ClickBlocks\Web\UI\POM\\');

   /**
    * The xhtml-temlate of a page.
    * @var    string
    * @access private
    */
   private $tpl = null;

   /**
    * The array of controls uniqueID
    *
    * @var    array
    * @access private
    */
   private $controls = array();

   private $vs = array();

   private $uniqueID = null;

   private $cs = null;

   private $cacheKey = null;

   /**
    * Checks whether or not the current request POST-request.
    *
    * @return boolean
    * @access public
    * @static
    */
   public static function isPOSTRequest()
   {
      return ($_SERVER['REQUEST_METHOD'] == 'POST');
   }

   /**
    * Checks whether or not the current request GET-request.
    *
    * @return boolean
    * @access public
    * @static
    */
   public static function isGETRequest()
   {
      return ($_SERVER['REQUEST_METHOD'] == 'GET');
   }

   /**
    * Constructs a new Page.
    *
    * @param string $xml
    * @param string $xslt
    * @access public
    */
   public function __construct($template = null)
   {
      $this->reg = Core\Register::getInstance();
      $this->fv = $this->reg->fv;
      $this->config = $this->reg->config;
      $this->cache = $this->reg->cache;
      $this->loader = $this->reg->loader;
      $this->logger = $this->reg->logger;
      $this->debugger = $this->reg->debugger;
      $this->ajax = $this->reg->ajax = Web\Ajax::getInstance();
      $this->js = Web\JS::getInstance();
      $this->css = Web\CSS::getInstance();
      $this->html = Web\HTML::getInstance();
      $this->validators = POM\Validators::getInstance();
      $this->tpl = $template;
      $this->cs = $this->cache;
      $this->noSessionURL = $this->reg->uri->scheme . '://' . $this->reg->uri->getSource();
      $this->cacheKey = md5($this->tpl . $this->reg->uri->getURI());
   }

   /**
    * Returns the unique ID of a page.
    *
    * @return string
    * @access public
    */
   public function getUniqueID()
   {
      return $this->uniqueID;
   }

   /**
    * Returns the page-html from its cache.
    *
    * @return string
    * @access public
    */
   public function cacheGet()
   {
      if ($this->expired) return $this->cache->get($this->cacheKey);
   }

   /**
    * Validates whether or not the cache of a page has expired.
    *
    * @return boolean
    * @access public
    */
   public function cacheExists()
   {
      if ($this->expired) return !$this->cache->isExpired($this->cacheKey);
      return false;
   }

   /**
    * Sets the new page's cache.
    *
    * @param string $html
    * @return Page
    * @access public
    */
   public function cacheSet($html)
   {
      if ($this->expired) $this->cache->set($this->cacheKey, $html, $this->expired);
      return $this;
   }

   public function count()
   {
      return count($this->controls);
   }

   public function lockValidator($uniqueID, $flag = false)
   {
      if (isset($this->vs['validators'][$uniqueID])) $this->vs['validators'][$uniqueID] = $flag;
   }
   
   public function validatorIsLocked($uniqueID)
   {
      return !$this->vs['validators'][$uniqueID];
   }

   public function offsetSet($uniqueID, $ctrl)
   {
      if (!($ctrl instanceof POM\IWebControl)) throw new \Exception(err_msg('ERR_PG_2'));
      $this->vs[$uniqueID] = $ctrl->getParameters();
      $this->controls[$uniqueID] = $ctrl;
      if ($ctrl instanceof POM\IValidator && !isset($this->vs['validators'][$uniqueID])) $this->vs['validators'][$uniqueID] = true;
   }

   public function offsetExists($uniqueID)
   {
      return isset($this->controls[$uniqueID]);
   }

   public function offsetUnset($uniqueID)
   {
      unset($this->vs[$uniqueID]);
      unset($this->vs['validators'][$uniqueID]);
      unset($this->controls[$uniqueID]);
   }

   public function offsetGet($uniqueID)
   {
      return $this->vs[$uniqueID];
   }

   public function __get($param)
   {
      if ($param == 'tpl') return $this->tpl;
      if ($param == 'head') return $this->xhtml ? $this->xhtml->head : null;
      if ($param == 'body') return ($this->xhtml && $this->xhtml->body) ? $this->xhtml->body : $this->getByUniqueID($this->uniqueID);
      throw new \Exception(err_msg('ERR_GENERAL_3', array($param, get_class($this))));
   }

   public function getValidators()
   {
      $vals = array();
      foreach ((array)$this->vs['validators'] as $uniqueID => $flag) if ($flag) $vals[] = $uniqueID;
      return $vals;
   }

   public function getActualVS($uniqueID)
   {
      if (isset($this->controls[$uniqueID])) return $this->controls[$uniqueID]->getParameters();
      return $this->vs[$uniqueID];
   }

   public function getByUniqueID($uniqueID)
   {
      if (isset($this->controls[$uniqueID])) return $this->controls[$uniqueID];
      if (isset($this->vs[$uniqueID]))
      {
         $ctrl = $this->vs[$uniqueID];
         $this->controls[$uniqueID] = $ctrl = foo(new $ctrl['parameters'][1]['ctrlClass']($ctrl['parameters'][1]['id']))->setParameters($ctrl);
         return $ctrl;
      }
      return false;
   }

   public function replaceByUniqueID($uniqueID, POM\IWebControl $ctrl)
   {
      $ctrl = $this->getByUniqueID($uniqueID);
      $parent = $this->getByUniqueID($ctrl->parentUniqueID);
      if (!$parent) throw new \Exception(err_msg('ERR_CTRL_6', array($ctrl->getFullID())));
      return $parent->replaceByUniqueID($uniqueID, $ctrl);
   }

   public function deleteByUniqueID($uniqueID, $time = 0)
   {
      $ctrl = $this->getByUniqueID($uniqueID);
      $parent = $this->getByUniqueID($ctrl->parentUniqueID);
      if (!$parent) throw new \Exception(err_msg('ERR_CTRL_6', array($ctrl->getFullID())));
      return $parent->deleteByUniqueID($uniqueID, $time);
   }

   public function cleanByUniqueID($uniqueID = null, $isRecursion = false)
   {
      $vs = $this->getActualVS(($uniqueID) ?: $this->uniqueID);
      if ($vs['extra']['clean']) $this->getByUniqueID($uniqueID)->clean();
      $controls = $vs['controls'];
      if ($controls) foreach ($controls as $uniqueID => $v)
      {
         $vs = $this->getActualVS($uniqueID);
         if ($vs['controls'] && $isRecursion) $this->cleanByUniqueID($uniqueID, true);
         if (!$vs['extra']['clean']) continue;
         $this->getByUniqueID($uniqueID)->clean();
      }
      return $this;
   }

   public function get($id, $isRecursion = true)
   {
      return $this->body->get($id, $isRecursion);
   }

   public function replace($id, IWebControl $ctrl, $isRecursion = true)
   {
      return $this->body->replace($id, $ctrl, $isRecursion);
   }

   public function delete($id, $isRecursion = true)
   {
      return $this->body->delete($id, $isRecursion);
   }

   public function clean($id, $isSearchRecursion = true, $isCleanRecursion = false)
   {
      $ctrl = $this->get($id, $isSearchRecursion);
      return (!$ctrl) ? false : $this->cleanByUniqueID($ctrl->uniqueID, $isCleanRecursion);
   }

   public function methodExists($uniqueID, $method)
   {
      return $this->vs[$uniqueID]['extra'][$method];
   }

   /**
    * Checks accessability of a page.
    *
    * @return boolean
    * @access public
    */
   public function access()
   {
      return true;
   }

   /**
    * Preparing to the parsing of a page.
    *
    * @access public
    */
   public function preparse(){}

   /**
    * Parses the templates of this page.
    *
    * @access public
    */
   public function parse()
   {
      $this->xhtml = new Helpers\XHTML();
      $this->xhtml->head = new Helpers\Head();
      if ($this->tpl)
      {
         if (!$this->config->alwaysParse && !$this->cs->isExpired($this->cacheKey))
         {
            $res = $this->cs->get($this->cacheKey);
            $this->vs = $res[0];
            $this->tpl = $res[1];
            $this->uniqueID = $res[2];
            $this->xhtml->body = $this->getByUniqueID($this->uniqueID);
         }
         else
         {
            $body = $this->html->parse($this->tpl, $this->tpl);
            $this->uniqueID = $body->uniqueID;
            $this->xhtml->body = $body;
            if (!$this->config->alwaysParse) $this->cs->set($this->cacheKey, array($this->vs, $this->tpl, $body->uniqueID), 2592000);
         }
      }
      else
      {
         $body = new POM\Body('page');
         $this->xhtml->body = $body;
         $this[$body->uniqueID] = $body;
         $this->uniqueID = $body->uniqueID;
         $this->tpl = new Core\Template();
         $this->tpl->setTemplate($body->uniqueID, '');
      }
   }

   public function input()
   {
      if ($this->fv['ajaxargs'] && $this->fv['ajaxfunc'])
      {
         $this->fv['ajaxargs'] = $this->ajax->getAjaxArguments($this->fv['ajaxargs']);
         $this->fv = array_merge($this->fv, (array)$this->fv['ajaxargs'][0]);
         $this->fv['ajaxargs'] = (array)$this->fv['ajaxargs'][1];
      }
      $this->uniqueID = $this->fv['ajaxkey'];
      $this->reg->fv = $this->fv;
   }

   public function assign()
   {
      $this->loadViewState();
      $this->loadTemplate();
      if (!is_array($this->vs)) Web\JS::goURL($this->noSessionURL);
      foreach ($this->vs as $vs)
      {
         $value = $this->fv[$this->uniqueID][$vs['parameters'][0]['uniqueID']];
         if ($value === null || !$vs['extra']['assign']) continue;
         $this[$vs['parameters'][0]['uniqueID']] = foo(new $vs['parameters'][1]['ctrlClass']($vs['parameters'][1]['id']))->setParameters($vs)->assign($value);
      }
   }

   /**
    * The initialization of a page.
    * This method invokes only time for GET request to the page.
    *
    * @access public
    */
   public function init()
   {
      $this->invokeMethod('init', $this->uniqueID);
   }

   /**
    * The loading of a page.
    *
    * @access public
    */
   public function load()
   {
      $this->invokeMethod('load', $this->uniqueID);
   }

   public function process()
   {
      if ($this->fv['ajaxfunc'] == '') return;
      $func = new Core\Delegate($this->fv['ajaxfunc']);
      if (!$this->checkCallBackPermissions($func)) return;
      ob_start();
      $func->call($this->fv['ajaxargs']);
      $cnt = trim(ob_get_contents());
      ob_end_clean();
      if ($cnt != '') $this->ajax->alert($cnt);
   }

   /**
    * Unloding of a page.
    *
    * @access public
    */
   public function unload()
   {
      $this->invokeMethod('unload', $this->uniqueID);
   }

   public function redraw()
   {
      foreach ($this->controls as $ctrl)
      {
         $ctrl->redraw($this->vs[$ctrl->uniqueID]['parameters']);
         $this->vs[$ctrl->uniqueID] = $ctrl->getParameters();
      }
   }

   /**
    * Executes all ajax-actions.
    *
    * @access public
    */
   public function perform()
   {
      $this->saveViewState();
      $this->saveTemplate();
      $this->ajax->perform();
   }

   /**
    * Renders a page and outputs its html to the browser.
    *
    * @access public
    */
   public function show()
   {
      echo $this->render();
      foreach ($this->controls as $ctrl) $this->vs[$ctrl->uniqueID] = $ctrl->getParameters();
      $this->saveViewState();
      $this->saveTemplate();
   }

   /**
    * Renders a page.
    *
    * @return string
    * @access public
    */
   public function render()
   {
      $html = $this->xhtml->render();
      $this->cacheSet($html);
      return $html;
   }

   /**
    * Renders a page.
    *
    * @return string
    * @access public
    */
   public function __toString()
   {
      try
      {
         return $this->render();
      }
      catch (\Exception $ex)
      {
         Core\Debugger::exceptionHandler($ex);
      }
   }

   protected function invokeMethod($method, $uniqueID)
   {
      $vs = $this->getActualVS($uniqueID);
      if ($vs['controls'])
      {
         foreach ($vs['controls'] as $uid => $v)
         {
            $this->invokeMethod($method, $uid);
         }
      }
      if ($this->methodExists($uniqueID, $method))
      {
         $this->getByUniqueID($uniqueID)->{$method}();
      }
   }

   protected function saveTemplate()
   {
      $this->cache->set('page_tpl_' . $this->uniqueID, $this->tpl, ini_get('session.gc_maxlifetime'));
   }

   protected function loadTemplate()
   {
      $this->tpl = $this->cache->get('page_tpl_' . $this->uniqueID);
   }

   protected function saveViewState()
   {
      $this->cache->set('page_vs_' . $this->uniqueID, $this->vs, ini_get('session.gc_maxlifetime'));
   }

   protected function loadViewState()
   {
      $this->vs = $this->cache->get('page_vs_' . $this->uniqueID);
   }

   private function checkCallBackPermissions(Core\Delegate $func)
   {
      foreach ($this->callBackPermissions as $permission) if ($func->in($permission)) return true;
      if ($this->config->isDebug) throw new \Exception(err_msg('ERR_GENERAL_9', array($func)));
      return false;
   }

   public function method($method, array $params = array(), $isStatic = false)
   {
      return Web\Ajax::ajaxCall((($isStatic) ? '::' : '->') . $method, $params);
   }
}

?>
