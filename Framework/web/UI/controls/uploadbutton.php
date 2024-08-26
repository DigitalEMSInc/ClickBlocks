<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core,
    ClickBlocks\Web,
    ClickBlocks\Web\UI\Helpers;

class UploadButton extends Upload
{
   public function __construct($id, $text = null, $callBack = null)
   {
      parent::__construct($id, $callBack);
      $this->properties['text'] = $text;
      $this->properties['tag'] = 'div';
      $this->properties['x'] = 0;
      $this->properties['y'] = 0;
   }

   public function JS()
   {
      if (!$this->properties['visible']) return;
      $this->js->addTool('uploadbutton');
      $this->js->add(new Helpers\Script('uploadbutton_' . $this->attributes['uniqueID'], 'uploadbutton.initialize(\'' . $this->attributes['uniqueID'] . '\', ' . (int)$this->properties['x'] . ', ' . (int)$this->properties['y'] . ');'), 'foot');
   }

   public function render()
   {
      $mouseover = $this->attributes['onmouseover'];
      $auto = $this->properties['autoCreationForm'];
      $this->attributes['style'] = 'position:absolute;width:60px;top:-5000px;z-index:1;filter:alpha(opacity:0);opacity:0;';
      $this->attributes['size'] = 1;
      if ($this->properties['autoCreationForm'])
      {
         $this->properties['autoCreationForm'] = false;
         $this->attributes['onmouseover'] .= ';ajax._getFormByTarget(\'frame_\' + this.id); uploadbutton.initialize(\'' . $this->attributes['uniqueID'] . '\', ' . (int)$this->properties['x'] . ', ' . (int)$this->properties['y'] . ');';
      }
      $html = parent::render();
      $this->attributes['onmouseover'] = $mouseover;
      $this->properties['autoCreationForm'] = $auto;
      return $html;
   }

   protected function repaint()
   {
      $this->updated = $this->updated ?: 200;
      if (Web\Ajax::isSubmit()) $parent = 'parent.';
      $this->js->addTool('uploadbutton');
      $this->ajax->script($parent . 'setTimeout("uploadbutton.initialize(\'' . $this->attributes['uniqueID'] . '\', ' . (int)$this->properties['x'] . ', ' . (int)$this->properties['y'] . ')", ' . ($this->updated + 100) . ')', 0, true);
      parent::repaint();
   }
}

?>
