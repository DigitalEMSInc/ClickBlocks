<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core;

class RadioButton extends WebControl
{
   public function __construct($id, $value = null, $caption = null)
   {
      parent::__construct($id);
      $this->attributes['name'] = $id;
      $this->attributes['value'] = $value;
      $this->attributes['onchange'] = null;
      $this->properties['align'] = 'right';
      $this->properties['checked'] = false;
      $this->properties['caption'] = $caption;
      $this->properties['readonly'] = false;
      $this->properties['tagContainer'] = 'span';
      $this->properties['classContainer'] = null;
      $this->properties['styleContainer'] = null;
   }

   public function clean()
   {
      $this->properties['checked'] = false;
      return $this;
   }

   public function assign($value)
   {
      if (is_array($value))
      {
         $this->attributes['value'] = $value['value'];
         $this->properties['checked'] = (bool)$value['state'];
      }
      else if (is_bool($value)) $this->properties['checked'] = $value;
      else $this->properties['checked'] = ($this->attributes['value'] == $value);
      return $this;
   }

   public function validate($type, Core\IDelegate $check)
   {
      switch ($type)
      {
         case 'required':
           return $check($this->attributes['value']);
      }
      return true;
   }

   public function render()
   {
      if (!$this->properties['visible']) return $this->invisible();
      if ($this->properties['caption'] != '') $label = '<label for="' . $this->attributes['uniqueID'] . '">' . $this->properties['caption'] . '</label>';
      $html = '<' . $this->properties['tagContainer'] . ' id="container_' . $this->attributes['uniqueID'] . '" style="' . htmlspecialchars($this->properties['styleContainer']) . '" class="' . htmlspecialchars($this->properties['classContainer']) . '">';
      if ($this->properties['align'] == 'left') $html .= $label;
      $html .= '<input type="radio"';
      if ($this->properties['checked']) $html .= ' checked="checked"';
      if ($this->properties['disabled']) $html .= ' disabled="disabled"';
      if ($this->properties['readonly']) $html .= ' readonly="readonly"';
      $html .= $this->getParams() . ' />';
      if ($this->properties['align'] == 'right') $html .= $label;
      $html .= '</' . $this->properties['tagContainer'] . '>';
      return $html;
   }

   protected function getRepaintID()
   {
      return 'container_' . $this->attributes['uniqueID'];
   }
}

?>
