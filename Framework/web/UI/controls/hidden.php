<?php

namespace ClickBlocks\Web\UI\POM;

class Hidden extends WebControl
{
   public function __construct($id, $value = null)
   {
      parent::__construct($id);
      $this->attributes['name'] = $id;
      $this->attributes['value'] = $value;
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
      return '<input type="hidden"' . $this->getParams() . ' />';
   }
}

?>
