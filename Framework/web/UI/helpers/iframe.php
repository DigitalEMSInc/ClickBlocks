<?php

namespace ClickBlocks\Web\UI\Helpers;

use ClickBlocks\Core,
    ClickBlocks\Web;

class IFrame extends Control
{
   public function __construct($id = null)
   {
      parent::__construct($id);
      $this->attributes['name'] = $id;
      $this->attributes['src'] = null;
      $this->attributes['onload'] = null;
      $this->attributes['width'] = null;
      $this->attributes['height'] = null;
      $this->attributes['scrolling'] = null;
      $this->attributes['align'] = null;
      $this->attributes['marginwidth'] = null;
      $this->attributes['marginheight'] = null;
      $this->attributes['frameborder'] = null;
   }
   
   public function render()
   {
      return '<iframe' . $this->getParams() . '></iframe>';
   }
}

?>
