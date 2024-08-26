<?php

namespace ClickBlocks\Web\UI\Helpers;

use ClickBlocks\Core,
    ClickBlocks\Web;

class Link extends Control
{
   public function __construct($id = null, $href = null, $text = null)
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
      return '<a' . $this->getParams() . '>' . $this->properties['text'] . '</a>';
   }
}

?>
