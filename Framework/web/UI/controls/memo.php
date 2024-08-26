<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core;

class Memo extends WebControl
{
   public function __construct($id, $text = null, $cols = null, $rows = null)
   {
      parent::__construct($id);
      $this->attributes['name'] = $id;
      $this->attributes['cols'] = $cols;
      $this->attributes['rows'] = $rows;
      $this->attributes['wrap'] = null;
      $this->attributes['onselect'] = null;
      $this->attributes['onchange'] = null;
      $this->attributes['autocorrect'] = null;
      $this->attributes['autocapitalize'] = null;
      $this->attributes['maxlength'] = null;
      $this->attributes['placeholder'] = null;
      $this->properties['readonly'] = false;
      $this->properties['text'] = $text;
   }

   public function clean()
   {
      $this->properties['text'] = null;
      return $this;
   }

   public function assign($value)
   {
      $this->properties['text'] = $value;
      return $this;
   }

   public function validate($type, Core\IDelegate $check)
   {
      return $check($this->properties['text']);
   }

   public function render()
   {
      if (!$this->properties['visible']) return $this->invisible();
      $html = '<textarea';
      if ($this->properties['disabled']) $html .= ' disabled="disabled"';
      if ($this->properties['readonly']) $html .= ' readonly="readonly"';
      $html .= $this->getParams() . '>' . htmlspecialchars($this->properties['text']) . '</textarea>';
      return $html;
   }
}

?>
