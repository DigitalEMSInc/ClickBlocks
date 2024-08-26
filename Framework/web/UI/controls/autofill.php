<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core,
    ClickBlocks\Web,
    ClickBlocks\Web\UI\Helpers;

class AutoFill extends TextBox
{
   public function __construct($id, $value = null)
   {
      parent::__construct($id, $value);
      $this->properties['callBack'] = null;
      $this->properties['listClass'] = null;
      $this->properties['classContainer'] = null;
      $this->properties['styleContainer'] = null;
      $this->properties['x'] = 0;
      $this->properties['y'] = 0;
   }

   public function render()
   {
      if (!$this->properties['visible']) return $this->invisible();
      $html = '<span id="container_' . $this->attributes['uniqueID'] . '" style="' . htmlspecialchars($this->properties['styleContainer']) . '" class="' . htmlspecialchars($this->properties['classContainer']) . '">';
      $html .= parent::render();
      $html .= '<div id="list_' . $this->attributes['uniqueID'] . '" style="display:none;" class="' . htmlspecialchars($this->properties['listClass']) . '"></div></span>';
      return $html;
   }

   public function JS()
   {
      $this->js->addTool('autofill');
      if (Web\Ajax::isAction())
      {
         if ($this->ajax->isSubmit()) $p = 'parent.';
         $this->ajax->script($p . 'autofill.initialize(\'' . $this->attributes['uniqueID'] . '\')', $this->updated + 100, true);
      }
      else $this->js->add(new Helpers\Script('autofillinit_' . $this->attributes['uniqueID'], 'autofill.initialize(\'' . $this->attributes['uniqueID'] . '\');'), 'foot');
      return $this;
   }

   public function search($value, $update = true)
   {
      if (!$this->properties['callBack']) return;
      $method = new Core\Delegate($this->properties['callBack']);
      $res = $method($value, $this->attributes['uniqueID']);
      $this->ajax->insert($res, 'list_' . $this->attributes['uniqueID'], true);
      if ($update)
      {
         if ($res) $this->ajax->script('autofill.showList(\'' . $this->attributes['uniqueID'] . '\', ' . (int)$this->properties['x'] . ', ' . (int)$this->properties['y'] . ')', true);
         else $this->ajax->script('autofill.hideList(\'' . $this->attributes['uniqueID'] . '\')', true);
      }
   }

   protected function repaint()
   {
      parent::repaint();
      if (!$this->properties['visible']) return;
      $this->JS();
   }

   protected function getRepaintID()
   {
      return 'container_' . $this->attributes['uniqueID'];
   }
}

?>
