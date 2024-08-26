<?php

namespace ClickBlocks\Web\UI\Helpers;

use ClickBlocks\Core,
    ClickBlocks\Web;

class Video extends Control
{
   public function __construct($id = null)
   {
      parent::__construct($id);
      $this->attributes['data'] = null;
      $this->attributes['classid'] = 'clsid:D27CDB6E-AE6D-11cf-96B8-444553540000';
      $this->attributes['codetype'] = null;
      $this->attributes['archive'] = null;
      $this->attributes['codebase'] = null;
      $this->attributes['declare'] = null;
      $this->attributes['standby'] = null;
      $this->attributes['usemap'] = null;
      $this->attributes['ismap'] = null;
      $this->attributes['align'] = null;
      $this->attributes['width'] = null;
      $this->attributes['height'] = null;
      $this->attributes['hspace'] = null;
      $this->attributes['vspace'] = null;
      $this->attributes['border'] = null;
      $this->attributes['tabindex'] = null;
      $this->attributes['wmode'] = 'transparent';
      $this->properties['movie'] = null;
      $this->properties['allowfullscreen'] = true;
      $this->properties['media'] = null;
   }

   public function render()
   {
      $html .= '<object' . $this->getParams() . '>';
      $html .= '<param name="movie" value="' . htmlspecialchars($this->properties['movie']) . '" />';
      $html .= '<param name="allowfullscreen" value="' . (($this->properties['allowfullscreen']) ? 'true' : 'false') . '" />';
      $html .= '<param name="allowscriptaccess" value="always" />';
      $html .= '<param name="flashvars" value="file=' . htmlspecialchars(addslashes($this->properties['media'])) . '" />';
      $html .= '<param name="wmode" value="' . htmlspecialchars($this->attributes['wmode']) . '" />';
      $html .= '<embed height="' . htmlspecialchars($this->attributes['height']) . '" width="' . htmlspecialchars($this->attributes['width']) . '" type="application/x-shockwave-flash" name="' . htmlspecialchars($this->attributes['id']) . '" src="' . htmlspecialchars($this->properties['movie']) . '" quality="high" allowscriptaccess="always" allowfullscreen="' . (($this->properties['allowfullscreen']) ? 'true' : 'false') . '" flashvars="file=' . htmlspecialchars(addslashes($this->properties['media'])) . '" wmode="' . htmlspecialchars($this->attributes['wmode']) . '"/>';
      $html .= '</object>';
      return $html;
   }
}

?>
