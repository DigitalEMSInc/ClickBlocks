<?php

namespace ClickBlocks\Web\UI\Helpers;

use ClickBlocks\Core,
    ClickBlocks\Web;

class Meta extends Control
{
   public function __construct($id = null, $name = null, $content = null, $httpequiv = null)
   {
      parent::__construct($id);
      $this->attributes['name'] = $name;
      $this->attributes['content'] = $content;
      $this->attributes['scheme'] = null;
      $this->attributes['http-equiv'] = $httpequiv;      
   }
   
   public function render()
   {
      return '<meta' . $this->getParams() . ' />';
   }
}

?>
