<?php

namespace ClickBlocks\Web\UI\Helpers;

use ClickBlocks\Core,
    ClickBlocks\Web;

class StaticText extends Control
{
   public function __construct($id = null, $text = null)
   {
      parent::__construct($id);      
      $this->properties['text'] = $text;
      $this->properties['tag'] = 'span';
   }

   public function render()
   {
      return '<' . $this->properties['tag'] . $this->getParams() . '>' . $this->properties['text'] . '</' . $this->properties['tag'] . '>';
   }
}

?>
