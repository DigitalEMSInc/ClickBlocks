<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core,
    ClickBlocks\Web,
    ClickBlocks\Web\UI\Helpers;

class ColorPicker extends TextBox
{
   const THEME_DEFAULT = 0;
   const THEME_ADVANCED = 1;

   public function __construct($id, $value = null, $theme = self::THEME_DEFAULT)
   {
      parent::__construct($id, $value);
      $this->attributes['maxlength'] = 7;
      $this->properties['theme'] = $theme;
      $this->properties['classContainer'] = null;
      $this->properties['styleContainer'] = null;
      $this->properties['tag'] = 'span';
   }

   public function CSS()
   {
      if (Web\Ajax::isAction()) $this->ajax->css('/Framework/_engine/web/js/colorpicker/js_color_picker_v2.css', 0, true);
      else $this->css->add(new Helpers\Style('colorpicker', null, '/Framework/_engine/web/js/colorpicker/js_color_picker_v2.css'));
      return $this;
   }

   public function JS()
   {
      $this->js->addTool('colorpicker');
      if ($this->properties['visible'])
      {
         $script = 'if (document.getElementById(\'' . $this->attributes['uniqueID'] . '\')) setInterval(function(){setColorForFormfield(\'' . $this->attributes['uniqueID'] . '\', \'' . $this->attributes['uniqueID'] . '\');}, 200);';
         if (Web\Ajax::isAction()) $this->ajax->script($script, $this->updated, true);
         else $this->js->add(new Helpers\Script('colorpicker_' . $this->attributes['uniqueID'], $script), 'foot');
      }
      return $this;
   }

   public function render()
   {
      if (!$this->properties['visible']) return $this->invisible();
      $html = '<' . $this->properties['tag'] . ' id="container_' . $this->attributes['uniqueID'] . '" style="' . htmlspecialchars($this->properties['styleContainer']) . '" class="' . htmlspecialchars($this->properties['classContainer']) . '">';
      switch ($this->properties['theme'])
      {
         case self::THEME_DEFAULT:
           $html .= parent::render() . '<input type="button" value="Color picker" onclick="showColorPicker(this, document.getElementById(\'' . $this->attributes['uniqueID'] . '\'))">';
           break;
         case self::THEME_ADVANCED:
           $this->attributes['style'] .= 'width:82px;font-size:12px;height:17px;border:0px;padding:2px 0 0 2px;margin:0;vertical-align:top;';
           $html .= '<div style="width:103px;width:100px;width:100px;height:20px;border:1px solid #7F9DB9;padding:0;margin:0;">';
           $html .= parent::render();
           $html .= '<img style="padding-right:1px;padding-top:1px" width="15" height="18" src="/Framework/_engine/web/js/colorpicker/images/select_arrow.gif" onmouseover="this.src=\'/Framework/_engine/web/js/colorpicker/images/select_arrow_over.gif\'" onmouseout="this.src=\'/Framework/_engine/web/js/colorpicker/images/select_arrow.gif\'" onclick="showColorPicker(this, document.getElementById(\'' . $this->attributes['uniqueID'] . '\'))" />';
           $html .= '</div>';
           break;
      }
      $html .= '</' . $this->properties['tag'] . '>';
      return $html;
   }

   protected function repaint()
   {
      parent::repaint();
      $this->CSS();
      $this->JS();
   }

   protected function getRepaintID()
   {
      return 'container_' . $this->attributes['uniqueID'];
   }
}

?>