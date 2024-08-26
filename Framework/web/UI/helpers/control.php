<?php

namespace ClickBlocks\Web\UI\Helpers;

use ClickBlocks\Core;

abstract class Control implements IControl
{
   protected $attributes = array('id' => null,
                                 'style' => null,
                                 'class' => null,
                                 'title' => null,
                                 'lang' => null,
                                 'dir' => null,
                                 'onfocus' => null,
                                 'onblur' => null,
                                 'onclick' => null,
                                 'ondblclick' => null,
                                 'onmousedown' => null,
                                 'onmouseup' => null,
                                 'onmouseover' => null,
                                 'onmousemove' => null,
                                 'onmouseout' => null,
                                 'onkeypress' => null,
                                 'onkeydown' => null,
                                 'onkeyup' => null);
   protected $properties = array('showID' => false);


   public function __construct($id = null)
   {
      $this->attributes['id'] = ($id === null) ? uniqid('c'.\ClickBlocks\Core\Register::getInstance()->controlCounter++) : $id;
   }

   public function __set($param, $value)
   {
      if (array_key_exists($param, $this->attributes)) $this->attributes[$param] = $value;
      else if (array_key_exists($param, $this->properties)) $this->properties[$param] = $value;
      else throw new \Exception(err_msg('ERR_GENERAL_3', array($param, get_class($this))));
   }

   public function __get($param)
   {
      if (array_key_exists($param, $this->attributes)) return $this->attributes[$param];
      else if (array_key_exists($param, $this->properties)) return $this->properties[$param];
      else throw new \Exception(err_msg('ERR_GENERAL_3', array($param, get_class($this))));
   }

   public function getParameters()
   {
      return array($this->attributes, $this->properties);
   }

   public function setParameters(array $parameters)
   {
      list ($this->attributes, $this->properties) = $parameters;
      return $this;
   }

   public function __isset($param)
   {
      return (array_key_exists($param, $this->attributes) || array_key_exists($param, $this->properties));
   }

   public function __unset($param)
   {
      unset($this->attributes[$param]);
      unset($this->properties[$param]);
   }

   public function __toString()
   {
      try
      {
         return $this->render();
      }
      catch (\Exception $e)
      {
         Core\Debugger::exceptionHandler($e);
      }
   }

   abstract public function render();

   public function addClass($class)
   {
      if (!$this->hasClass($class)) $this->class = trim($this->class . ' ' . $class);
      return $this;
   }

   public function removeClass($class)
   {
      $this->class = str_replace($class, '', $this->class);
      $this->class = trim(str_replace(array('  ', '   '), ' ', $this->class));
      return $this;
   }

   public function replaceClass($class1, $class2)
   {
      $this->removeClass($class1)->addClass($class2);
   }

   public function toggleClass($class1, $class2 = null)
   {
      if (!$this->hasClass($class1)) $this->replaceClass($class2, $class1);
      else $this->replaceClass($class1, $class2);
      return $this;
   }

   public function hasClass($class)
   {
      return (strpos($this->class, $class) !== false);
   }

   public function addStyle($style, $value)
   {
      if (!$this->hasStyle($style)) $this->style = trim($this->style . $style . ':' . $value . ';');
      else $this->setStyle($style, $value);
      return $this;
   }

   public function setStyle($style, $value)
   {
      $this->style = preg_replace('/' . $style . ' *:[^;]*;*/', $style . ':' . $value . ';', $this->style);
      return $this;
   }

   public function getStyle($style)
   {
      preg_match('/' . $style . ' *:([^;]*);*/', $this->style, $arr);
      return $arr[1];
   }

   public function removeStyle($style)
   {
      $this->style = preg_replace('/' . $style . ' *:[^;]*;*/', '', $this->style);
      $this->style = trim(str_replace(array('  ', '   '), ' ', $this->style));
      return $this;
   }

   public function toggleStyle($style, $value)
   {
      if (!$this->hasStyle($style)) $this->addStyle($style, $value);
      else $this->removeStyle($style);
      return $this;
   }

   public function hasStyle($style)
   {
      return (strpos($this->style, $style) !== false);
   }

   protected function getParams()
   {
      $tmp = array(); $attr = $this->attributes;
      if (!$this->showID) unset($attr['id']);
      foreach ($attr as $k => $v) if ($v != '') $tmp[] = $k . '="' . htmlspecialchars($v) . '"';
      return (count($tmp)) ? ' ' . implode(' ', $tmp) : '';
   }
}

?>
