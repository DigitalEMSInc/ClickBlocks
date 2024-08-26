<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core,
    ClickBlocks\Web,
    ClickBlocks\Web\UI\Helpers;

class TextBox extends WebControl
{
   public function __construct($id, $value = null)
   {
      parent::__construct($id);
      $this->attributes['name'] = $id;
      $this->attributes['value'] = $value;
      $this->attributes['size'] = null;
      $this->attributes['type'] = null;
      $this->attributes['placeholder'] = null;
      $this->attributes['maxlength'] = null;
      $this->attributes['onselect'] = null;
      $this->attributes['onchange'] = null;
      $this->attributes['autocorrect'] = null;
      $this->attributes['autocapitalize'] = null;
      $this->attributes['oncontextmenu'] = null;
      $this->properties['readonly'] = false;
      $this->properties['defaultValue'] = null;
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
      if ($this->properties['defaultValue'])
      {
         $value = addslashes(htmlspecialchars($this->properties['defaultValue']));
         $this->attributes['onblur'] .= ";if (!this.value) this.value = '" . $value . "';";
         $this->attributes['onfocus'] .= ";if (this.value == '" . $value . "') this.value = '';";
         if (!$this->attributes['value']) $this->attributes['value'] = $this->properties['defaultValue'];
      }
      $html = '<input type="text"';
      if ($this->properties['disabled']) $html .= ' disabled="disabled"';
      if ($this->properties['readonly']) $html .= ' readonly="readonly"';
      $html .= $this->getParams() . ' />';
      return $html;
   }
}

?>
