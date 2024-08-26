<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core,
    ClickBlocks\Utils,
    ClickBlocks\MVC,
    ClickBlocks\Web,
    ClickBlocks\Web\UI\Helpers;

class Repeater extends Panel
{
   private $sections = null;
   private $section = null;

   public function __construct($id)
   {
      parent::__construct($id);
      $this->properties['sections'] = new Utils\ArraySortOrder();
      $this->properties['count'] = 0;
      $this->properties['order'] = array();
      $this->properties['source'] = array();
   }

   public function offsetGet($uniqueID)
   {
      if ($this->section === null)
      {
         $ctrl = $this->getByUniqueID($uniqueID);
         if ($ctrl !== false) return $ctrl;
         if ((string)$uniqueID == (string)(int)$uniqueID)
         {
            $this->section = $uniqueID;
            return $this;
         }
         return false;
      }
      $ctrl = $this->get($uniqueID . '_' . $this->section);
      $this->section = null;
      return $ctrl;
   }

   public function __set($param, $value)
   {
      if ($param == 'source' || $param == 'sections') return;
      if ($param == 'count' && $value != $this->properties['count']) $this->setCount($value);
      if ($param == 'order')
      {
         $this->properties['sections']->setOrder($value);
         $this->properties['order'] = $this->properties['sections']->getOrder();
      }
      parent::__set($param, $value);
   }

   public function __get($param)
   {
      if ($param == 'order') $this->properties['order'] = $this->properties['sections']->getOrder();
      return parent::__get($param);
   }

   public function init()
   {
      $count = $this->properties['count'];
      $this->properties['count'] = 0;
      for ($i = 0; $i < $count; $i++) $this->put($i);
   }

   public function add(IWebControl $ctrl)
   {
      if (Web\XHTMLParser::isParsing())
      {
         $this->properties['source'][] = $ctrl->uniqueID;
         parent::add($ctrl);
         $this->controls[$ctrl->uniqueID] = false;
         if ($ctrl instanceof IValidator) $this->page->lockValidator($ctrl->uniqueID, false);
         return $this;
      }
      return parent::add($ctrl);
   }

   public function putLast()
   {
      $this->put($this->properties['count']);
      return $this;
   }

   public function putFirst()
   {
      $this->put(0);
      return $this;
   }

   public function putBefore($n)
   {
      $this->put($n - 1);
      return $this;
   }

   public function putAfter($n)
   {
      $this->put($n);
      return $this;
   }

   public function put($n)
   {
      if ($n > count($this->properties['sections'])) $n = count($this->properties['sections']);
      if ($n < 0) $n = 0;
      $ids = array();
      foreach ($this->properties['source'] as $uniqueID)
      {
         $ctrl = $this->getByUniqueID($uniqueID);
         $ctrl = $ctrl->copy();
         if ($ctrl instanceof Repeater) $ctrl->init();
         $ctrl->id .= '_' . uniqid();
         if (isset($ctrl->name)) $ctrl->name .= '_' . uniqid();
         $ids[$uniqueID] = $ctrl->uniqueID;
         parent::add($ctrl);
      }
      foreach ($ids as $uniqueID)
      {
         $ctrl = $this->getByUniqueID($uniqueID);
         if ($ctrl instanceof IValidator)
         {
            $controls = $ctrl->controls;
            $parentFullID = $this->getFullID();
            foreach ($controls as $cID)
            {
               $fullID = $this->page->getByUniqueID($cID)->getFullID();
               $fullID = substr($fullID, strpos($fullID, $parentFullID) + strlen($parentFullID) + 1);
               $obj = $this->get($fullID);
               if ($obj !== false)
               {
                  $controls[] = $ids[$obj->uniqueID];
                  $key = array_search($obj->uniqueID, $this->properties['source']);
                  unset($controls[array_search($this->properties['source'][$key], $controls)]);
               }
            }
            $ctrl->controls = $controls;
         }
      }
      $this->properties['sections']->inject($n, $ids);
      $this->properties['count']++;
      $this->properties['order'] = $this->properties['sections']->getOrder();
      $this->adjustNames();
      return $this;
   }

   public function deleteFirst()
   {
      $this->deleteItem(0);
      return $this;
   }

   public function deleteLast()
   {
      $this->deleteItem($this->properties['count'] - 1);
      return $this;
   }

   public function deleteItem($n)
   {
      $order = $this->properties['sections']->getOrder();
      foreach ((array)$this->properties['sections'][$n] as $uniqueID)
      {
         $this->getByUniqueID($uniqueID)->delete();
      }
      if (!$this->properties['sections']->delete($n)) return;
      $this->properties['order'] = $this->properties['sections']->getOrder();
      $this->properties['count']--;
      $this->adjustNames();
      return $this;
   }

   public function getInnerHTML()
   {
      $template = (string)$this->tpl;
      foreach ($this->properties['order'] as $n)
      {
         $ids = (array)$this->properties['sections'][$n];
         $this->tpl = strtr($template, $ids);
         foreach ($ids as $uniqueID) $this->tpl->{$uniqueID} = $this->getByUniqueID($uniqueID)->render();
         $this->tpl->count = $n;
         $this->tpl->uniqueID = $this->attributes['uniqueID'];
         $html .= $this->tpl->render();
      }
      $this->tpl = $template;
      return $html;
   }

   public function getXHTML()
   {
      $tag = strtolower(Utils\PHPParser::getClassName(get_class($this)));
      $xml = '<' . $tag . $this->getXHTMLParams() . '>';
      $temp = (string)$this->tpl;
      foreach ($this->controls as $uniqueID => $flag) if (!$flag) $temp = str_replace('<?=$' . $uniqueID . ';?>', $this->getByUniqueID($uniqueID)->getXHTML(), $temp);
      $xml .= $temp;
      return $xml .= '</' . $tag . '>';
   }

   protected function setCount($count)
   {
      if (Web\XHTMLParser::isParsing()) return;
      if ($count < 0) $count = 0;
      if ($count < $this->properties['count']) for ($i = $this->properties['count']; $i >= $count; $i--) $this->deleteItem($i);
      else if ($count > $this->properties['count']) for ($i = $this->properties['count'] + 1; $i <= $count; $i++) $this->put($i);
   }

   protected function adjustNames()
   {
      foreach ($this->properties['order'] as $n)
      {
         foreach ($this->properties['sections'][$n] as $uniqueID)
         {
            $ctrl = $this->getByUniqueID($uniqueID);
            $k = strrpos($ctrl->id, '_');
            if ($k !== false) $ctrl->id = substr($ctrl->id, 0, $k + 1) . $n;
            if (isset($ctrl->name))
            {
               $k = strrpos($ctrl->name, '_');
               if ($k !== false) $ctrl->name = substr($ctrl->name, 0, $k + 1) . $n;
            }
         }
      }
   }

   protected function getXHTMLParams()
   {
      $source = $this->properties['source'];
      $sections = $this->properties['sections'];
      $this->properties['source'] = '';
      $this->properties['sections'] = '';
      $params = parent::getXHTMLParams();
      $this->properties['source'] = $source;
      $this->properties['sections'] = $sections;
      return $params;
   }
}

?>