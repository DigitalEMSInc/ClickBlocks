<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core;

class DropDownBox extends WebControl
{
   public function __construct($id, $value = null)
   {
      parent::__construct($id);
      $this->attributes['name'] = $id;
      $this->attributes['size'] = null;
      $this->attributes['onchange'] = null;
      $this->properties['multiple'] = false;
      $this->properties['value'] = $value;
      $this->properties['defaultValue'] = null;
      $this->properties['options'] = array();
   }

   public function &__get($param)
   {
      if ($param == 'options') return $this->properties['options'];
      return parent::__get($param);
   }

   public function clean()
   {
      $this->properties['value'] = null;
      return $this;
   }

   public function assign($value)
   {
      $this->properties['value'] = $value;
      return $this;
   }

   public function validate($type, Core\IDelegate $check)
   {
      switch ($type)
      {
         case 'required':
           return $check($this->properties['value']);
      }
      return true;
   }

   public function render()
   {
      if (!$this->properties['visible']) return $this->invisible();
      $html = '<select';
      if ($this->properties['disabled']) $html .= ' disabled="disabled"';
      if ($this->properties['multiple']) $html .= ' multiple="multiple"';
      $html .= $this->getParams() . '>';
      $html .= $this->getOptionsHTML();
      $html .= '</select>';
      return $html;
   }

   public function getOptionsHTML()
   {
      if (!is_array($this->properties['options'])) return $this->properties['options'];
      $ops = $this->properties['options'];
      if ($this->properties['defaultValue'] != '') $ops = array('' => $this->properties['defaultValue']) + $ops;
      foreach ($ops as $key => $option)
      {
         if (is_array($option))
         {
            $html .= '<optgroup label="' . htmlspecialchars($key) . '">';
            foreach ($option as $k => $opt) $html .= $this->getOptionHTML($k, $opt);
            $html .= '</optgroup>';
         }
         else $html .= $this->getOptionHTML($key, $option);
      }
      return $html;
   }

   protected function getOptionHTML($key, $option)
   {
      if (is_array($this->properties['value']) && in_array($key, $this->properties['value']) || (string)$this->properties['value'] === (string)$key) $sel = ' selected="selected"';
      return '<option' . $sel . ' value="' . htmlspecialchars($key) . '">' . $option . '</option>';
   }
}

?>
