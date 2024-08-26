<?php

namespace ClickBlocks\Web\UI\POM;

class ImageButton extends WebControl
{
   public function __construct($id, $src = null, $alt = null)
   {
      parent::__construct($id);
      $this->attributes['src'] = $src;
      $this->attributes['alt'] = $alt;
      $this->attributes['usemap'] = null;
      $this->attributes['ismap'] = null;
   }

   public function render()
   {
      if (!$this->properties['visible']) return $this->invisible();
      $html .= '<input type="image"';
      if ($this->properties['disabled']) $html .= ' disabled="disabled"';
      $html .= $this->getParams() . ' />';
      return $html;
   }
}

?>
