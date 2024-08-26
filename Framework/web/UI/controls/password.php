<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core;

class Password extends WebControl
{
   public function __construct($id, $value = null)
   {
      parent::__construct($id);
      $this->attributes['name'] = $id;
      $this->attributes['value'] = $value;
      $this->attributes['size'] = null;
      $this->attributes['maxlength'] = null;
      $this->attributes['onselect'] = null;
      $this->attributes['onchange'] = null;
      $this->attributes['autocorrect'] = null;
      $this->attributes['autocapitalize'] = null;
      $this->attributes['placeholder'] = null;
      $this->properties['readonly'] = false;
   }

   public function clean()
   {
      $this->attributes['value'] = null;
      return $this;
   }

   public function assign($value)
   {
      $this->attributes['value'] = $value;
      return $this;
   }

   public function validate($type, Core\IDelegate $check)
   {
      return $check($this->attributes['value']);
   }

   public function render()
   {
      if (!$this->properties['visible']) return $this->invisible();
      $html = '<input type="password"';
      if ($this->properties['disabled']) $html .= ' disabled="disabled"';
      if ($this->properties['readonly']) $html .= ' readonly="readonly"';
      $html .= $this->getParams() . ' />';
      return $html;
   }
}

?>
