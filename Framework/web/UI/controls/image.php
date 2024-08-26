<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core,
    ClickBlocks\Web\UI\Helpers;

class Image extends WebControl
{
   public function __construct($id = null, $src = null, $alt = null)
   {
      parent::__construct($id);
      $this->attributes['src'] = $src;
      $this->attributes['alt'] = $alt;
      $this->attributes['usemap'] = null;
      $this->attributes['ismap'] = null;
      $this->attributes['width'] = null;
      $this->attributes['height'] = null;
      $this->attributes['border'] = null;
      $this->attributes['longdesc'] = null;
      $this->attributes['align'] = null;
      $this->attributes['lowsrc'] = null;
      $this->attributes['dynsrc'] = null;
      $this->attributes['start'] = null;
      $this->attributes['loop'] = null;
      $this->attributes['loopdelay'] = null;
      $this->attributes['controls'] = null;
      $this->attributes['hspace'] = null;
      $this->attributes['vspace'] = null;
      $this->properties['autoRefresh'] = false;
      $this->properties['maxWidth'] = 0;
      $this->properties['maxHeight'] = 0;
   }

   public function redraw(array $parameters = null)
   {
      if ($this->properties['autoRefresh']) $this->update();
      return parent::redraw($parameters);
   }

   public function render()
   {
      if (!$this->properties['visible']) return $this->invisible();
      $img = new Helpers\Img();
      $attrs = $this->attributes;
      unset($attrs['uniqueID']);
      $attrs['id'] = $this->attributes['uniqueID'];
      $props = $this->properties;
      $props['showID'] = true;
      $img->setParameters(array($attrs, $props));
      return $img->render();
   }
}

?>