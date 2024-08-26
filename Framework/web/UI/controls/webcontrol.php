<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core,
    ClickBlocks\Utils,
    ClickBlocks\Web,
    ClickBlocks\Web\UI\Helpers;

abstract class WebControl extends Helpers\Control implements IWebControl
{
   const PHP_SIGN = 'php::';
   const ID_REG_EXP = '/^[a-zA-Z_]{1}[0-9a-zA-Z_]*$/';

   protected $page = null;
   protected $reg = null;
   protected $js = null;
   protected $css = null;
   protected $html = null;
   protected $ajax = null;
   protected $updated = false;

   public function __construct($id)
   {
      if ($id == '') throw new \Exception(err_msg('ERR_CTRL_1', array(get_class($this))));
      if (!preg_match(self::ID_REG_EXP, $id)) throw new \Exception(err_msg('ERR_CTRL_2', array(get_class($this), $id)));
      parent::__construct($id);
      $this->reg = Core\Register::getInstance();
      $this->js = Web\JS::getInstance();
      $this->css = Web\CSS::getInstance();
      $this->html = Web\HTML::getInstance();
      $this->ajax = Web\Ajax::getInstance();
      unset($this->attributes['id']);
      $this->attributes['uniqueID'] = uniqid($id);
      $this->properties = array();
      $this->properties['id'] = $id;
      $this->properties['ctrlClass'] = get_class($this);
      $this->properties['disabled'] = false;
      $this->properties['visible'] = true;
      $this->properties['parentUniqueID'] = null;
      $this->page = $this->reg->page;
   }

   public function CSS()
   {
      return $this;
   }

   public function JS()
   {
      return $this;
   }

   public function HTML()
   {
      return $this;
   }

   public function getParameters()
   {
      return array('parameters' => array($this->attributes, $this->properties),
                   'extra' => array('init' => method_exists($this, 'init'),
                                    'load' => method_exists($this, 'load'),
                                    'unload' => method_exists($this, 'unload'),
                                    'assign' => method_exists($this, 'assign'),
                                    'clean' => method_exists($this, 'clean'),
                                    'validate' => method_exists($this, 'validate')));
   }

   public function setParameters(array $parameters)
   {
      list ($this->attributes, $this->properties) = $parameters['parameters'];
      return $this;
   }

   public function parse(array &$attributes, Core\ITemplate $tpl)
   {
      foreach ($attributes as &$v) $v = $this->parseAttribute($v);
      return $this;
   }

   public function getFullID()
   {
      $parentUniqueID = $this->properties['parentUniqueID'];
      $id = $this->properties['id'];
      while (($parent = $this->page->getActualVS($parentUniqueID)) && $parent['parameters'][1]['ctrlClass'] != 'ClickBlocks\Web\UI\POM\Body')
      {
         $id = $parent['parameters'][1]['id'] . '.' . $id;
         $parentUniqueID = $parent['parameters'][1]['parentUniqueID'];
      }
      return $id;
   }

   public function getParentForm()
   {
      $parent = $this->page->getByUniqueID($this->properties['parentUniqueID']);
      if ($parent instanceof WebForm) return $parent;
      if ($parent instanceof IWebControl) return $parent->getParentForm();
      return false;
   }

   public function getParent($class = null)
   {
      $parent = $this->page->getByUniqueID($this->properties['parentUniqueID']);
      if ($class && is_a($parent, $class)) return $parent;
      if ($parent instanceof IWebControl)
      {
         if ($parent->parentUniqueID) return $parent->getParent($class);
         return $parent;
      }
      return false;
   }

   public function setParent(IPanel $parent, $id = null, $mode = Core\DOMDocumentEx::DOM_INJECT_TOP)
   {
      return $this->setParentByUniqueID($parent->uniqueID, $id, $mode);
   }

   public function setParentByUniqueID($uniqueID, $id = null, $mode = Core\DOMDocumentEx::DOM_INJECT_TOP)
   {
      $parent = $this->page->getByUniqueID($uniqueID);
      if (!$parent) throw new \Exception(err_msg('ERR_CTRL_7', array($uniqueID)));
      $this->delete();
      return $parent->inject($this, $id, $mode);
   }

   public function getXHTML()
   {
      return '<' . strtolower(Utils\PHPParser::getClassName(get_class($this)))  . $this->getXHTMLParams() . ' />';
   }

   public function getValidators()
   {
      return Validators::getInstance()->getValidators($this->attributes['uniqueID']);
   }

   public function __get($param)
   {
      if ($param == 'page') return $this->page;
      return parent::__get($param);
   }

   public function __set($param, $value)
   {
      if ($param == 'uniqueID') throw new \Exception(err_msg('ERR_CTRL_4', array(get_class($this), $this->getFullID())));
      if ($param == 'parentUniqueID' && $this->properties['parentUniqueID'] != '') throw new \Exception(err_msg('ERR_CTRL_13', array(get_class($this), $this->getFullID())));
      if ($param == 'id' && $this->properties['id'] != $value)
      {
         if (!preg_match(self::ID_REG_EXP, $value)) throw new \Exception(err_msg('ERR_CTRL_2', array(get_class($this), $value)));
         if ($this->properties['parentUniqueID'] != '')
         {
            $pvs = $this->page->getActualVS($this->properties['parentUniqueID']);
            foreach ($pvs['controls'] as $uniqueID => $v)
            {
               $vs = $this->page->getActualVS($uniqueID);
               if ($vs['parameters'][1]['id'] != $this->properties['id'] && $vs['parameters'][1]['id'] == $value)
               throw new \Exception(err_msg('ERR_CTRL_5', array(get_class($this), $this->getFullID(), $this->page->getByUniqueID($uniqueID))));
            }
         }
      }
      parent::__set($param, $value);
   }

   /**
    * Sets a lot of parameters at once
    * before:
    *
    * $leftButtons->get('editInBott')->class = 'pop-gray-btn mt10 ml10';
    * $leftButtons->get('editInBott')->value = 'CANCEL';
    * $leftButtons->get('editInBott')->onclick = 'location.href="/incident/view?ID='.$incidentId.'"';
    *
    * after:
    * $leftButtons->get('editInBott')->setParams(array(
    *     'class' => 'pop-gray-btn mt10 ml10',
    *     'value' => 'CANCEL',
    *     'onclick' => 'location.href="/incident/view?ID='.$incidentId.'"',
    * ));
    *
    * @param array $params
    * @return $this
    * @throws \Exception
    */
   public function setParams(array $params)
   {
      foreach($params as $param => $value) {
         if(property_exists($this, $param)) {
            $this->$param = $value;
         }
         else {
            $this->__set($param, $value);
         }
      }

      return $this;
   }

   /**
    * Initializes parameter(s) in the widget.
    *
    * E.g.
    * // from
    * $widget->visible = true;
    * // to
    * $widget->setVar('visible', true);
    *
    * // from
    * $widget->visible = true;
    * $widget->options = $options;
    *
    * // to
    * $widget->setVar(array(
    *    'visible' => true,
    *    'options' => $options,
    * ));
    *
    * @param array|string $name
    * @param mixed $value
    * @return self
    * @throws \Exception
    */
   public function setParameter($name, $value = null)
   {
      if(is_array($name)) {
         foreach($name as $name => $value) {
            $this->__set($name, $value);
         }
      }
      else {
         $this->__set($name, $value);
      }

      return $this;
   }

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

   public function update($mode = true)
   {
      if ($mode !== false) $mode = ($mode === true) ? 0 : (int)$mode;
      $this->updated = $mode;
      return $this;
   }

   public function redraw(array $parameters = null)
   {
      if (!$this->properties['parentUniqueID'] && !($this instanceof Body)) return;
      $new = $this->getParameters();
      if ($this->updated !== false || $parameters != $new['parameters']) $this->repaint();
      return $this;
   }

   public function delete($time = 0)
   {
      $parent = $this->page->getByUniqueID($this->properties['parentUniqueID']);
      if (!$parent) throw new \Exception(err_msg('ERR_CTRL_6', array($this->getFullID())));
      return $parent->deleteByUniqueID($this->attributes['uniqueID'], $time);
   }

   public function copy()
   {
      $obj = foo(new Web\XHTMLParser())->parse($this->getXHTML(), $tpl);
      if ($obj instanceof IPanel)
      {
         $parentFullID = $this->getFullID();
         foreach (Web\XHTMLParser::getValidators() as $uniqueID)
         {
            $validator = $this->page->getByUniqueID($uniqueID);
            foreach ($validator->controls as &$uID)
            {
               $fullID = $this->page->getByUniqueID($uID)->getFullID();
               $fullID = substr($fullID, strpos($fullID, $parentFullID) + strlen($parentFullID) + 1);
               $ctrl = $obj->get($fullID);
               if ($ctrl !== false) $uID = $ctrl->uniqueID;
            }
         }
         $this->page->tpl->setTemplate($obj->uniqueID, (string)$tpl[$obj->uniqueID]);
         foreach ($obj->getControls() as $uniqueID => $flag) $this->copyTemplates($obj, $obj->getByUniqueID($uniqueID), $tpl, $this->page->tpl);
      }
      return $obj;
   }

   public function method($method, array $params = array(), $isStatic = false)
   {
      return Web\Ajax::ajaxCall($this->properties['ctrlClass'] . '@' . $this->attributes['uniqueID'] . (($isStatic) ? '::' : '->') . $method, $params);
   }

   protected function copyTemplates($ctrl, $child, Core\ITemplate $template, Core\ITemplate $tpl)
   {
      if ($child instanceof IPanel)
      {
         $tpl->setTemplate($child->uniqueID, (string)$template[$child->uniqueID], $ctrl->uniqueID);
         foreach ($child->getControls() as $uniqueID => $flag) $this->copyTemplates($child, $child->getByUniqueID($uniqueID), $template, $tpl);
      }
   }

   protected function parseAttribute($v)
   {
      $v = Web\XHTMLParser::exe(Web\XHTMLParser::decodePHPTags($v));
      if (substr((string)$v, 0, 6) == '\\' . self::PHP_SIGN) $v = substr($v, 1);
      else if (substr((string)$v, 0, 5) == self::PHP_SIGN) $v = Web\XHTMLParser::evil(substr($v, 5));
      return $v;
   }

   protected function invisible()
   {
      $span = new Helpers\StaticText($this->getRepaintID());
      $span->showID = true;
      $span->addStyle('display', 'none');
      return $span->render();
   }

   protected function getParams()
   {
      $tmp = array('runat="server"');
      $uniqueID = $this->attributes['uniqueID'];
      unset($this->attributes['uniqueID']);
      $this->attributes['id'] = $uniqueID;
      foreach ($this->attributes as $k => $v)
      {
         if ($v == '') continue;
         $tmp[] = $k . '="' . htmlspecialchars((string)$v) . '"';
      }
      $this->attributes['uniqueID'] = $uniqueID;
      unset($this->attributes['id']);
      return ' ' . implode(' ', $tmp);
   }

   protected function getXHTMLParams()
   {
      $tmp = array();
      $params = $this->attributes + $this->properties;
      unset($params['uniqueID']);
      unset($params['parentUniqueID']);
      unset($params['ctrlClass']);
      foreach ($params as $k => $v)
      {
         $v = ((is_array($v) || is_object($v)) ? self::PHP_SIGN : '') . $this->rollup($v);
         if ($v == '') continue;
         $tmp[] = $k . '="' . $v . '"';
      }
      return (($tmp) ? ' ' : '') . implode(' ', $tmp);
   }

   protected function rollup($value, $key = null)
   {
      if (is_array($value))
      {
         $vals = array();
         foreach ($value as $k => $v)
         {
            if (is_array($v)) $vals[] = $this->rollup($v, $k);
            else $vals[] = "'" . addslashes($k) . "' => '" . addslashes($v) . "'";
         }
         return ($key ? ("'" . addslashes($key) . "'" . ' => ') : '') . 'array(' . implode(',', $vals) . ')';
      }
      else if (is_bool($value)) return htmlspecialchars((int) $value);
      else if (is_object($value)) return htmlspecialchars('unserialize(\'' . addslashes(serialize($value)) . '\')');
      return htmlspecialchars($value);
   }

   protected function repaint()
   {
      $this->ajax->replace($this->render(), $this->getRepaintID(), $this->updated, true);
   }

   protected function remove($time = 0)
   {
      $this->ajax->remove($this->getRepaintID(), $time, true);
   }

   protected function getRepaintID()
   {
      return $this->attributes['uniqueID'];
   }
}

?>
