<?php

namespace ClickBlocks\Web\UI\POM;

class HyperLink extends WebControl
{
   public function __construct($id, $href = null, $text = null)
   {
      parent::__construct($id);
      $this->attributes['type'] = null;
      $this->attributes['href'] = $href;
      $this->attributes['target'] = null;
      $this->attributes['hreflang'] = null;      
      $this->properties['text'] = $text;
   }
   
   public function render()
   {
      if (!$this->properties['visible']) return $this->invisible();
      return '<a' . $this->getParams() . '>' . $this->properties['text'] . '</a>';
   }
}

?>
