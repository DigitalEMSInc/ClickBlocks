<?php

namespace ClickBlocks\Web\UI\Helpers;

use ClickBlocks\Core,
    ClickBlocks\Web;

class Style extends Control
{
   public function __construct($id = null, $style = null, $href = null, $media = null)
   {
      parent::__construct($id);
      $this->attributes['type'] = 'text/css';
      $this->attributes['charset'] = null;
      $this->attributes['href'] = $href;
      $this->attributes['hreflang'] = null;
      $this->attributes['rel'] = null;
      $this->attributes['rev'] = null;
      $this->attributes['target'] = null;
      $this->attributes['media'] = $media;
      $this->properties['text'] = $style;
      $this->properties['conditions'] = null;
   }

   public function render()
   {
      if (strlen($this->attributes['href']))
      {
         if (!$this->attributes['rel']) $this->attributes['rel'] = 'stylesheet';
         if (!$this->properties['conditions']) return '<link' . $this->getParams() . ' />';
         return '<!--[' . $this->properties['conditions'] . ']><link ' . $this->getParams() . ' /><![endif]-->';
      }
      return '<style' . $this->getParams() . '>' . $this->properties['text'] . '</style>';
   }
}

?>
