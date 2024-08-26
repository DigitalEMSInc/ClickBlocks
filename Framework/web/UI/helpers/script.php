<?php

namespace ClickBlocks\Web\UI\Helpers;

use ClickBlocks\Core,
    ClickBlocks\Web;

class Script extends Control
{
   public function __construct($id = null, $script = null, $src = null)
   {
      parent::__construct($id);
      $this->attributes['type'] = 'text/javascript';
      $this->attributes['charset'] = null;
      $this->attributes['src'] = $src;
      $this->properties['text'] = $script; 
   }
   
   public function render()
   {
      return '<script id="'.$this->attributes['id'].'"' . $this->getParams() . '>' . $this->properties['text'] . '</script>'; 
   }
}

?>
