<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core,
    ClickBlocks\Utils,
    ClickBlocks\MVC,
    ClickBlocks\Web,
    ClickBlocks\Web\UI\Helpers;

class PopUp extends Panel
{
   public function __construct($id)
   {
      parent::__construct($id);
      $this->attributes['class'] = 'popup';
      $this->properties['shadow'] = true;
      $this->properties['updateOnShow'] = true;
   }

   public function init()
   {
      $this->js->addTool('controls');
      $this->js->add(new Helpers\Script('popup', null, Core\IO::url('js') . '/popup.js'), 'link');
      $this->tpl->uniqueID = $this->attributes['uniqueID'];
      $click = 'popup.hide(\'' . $this->attributes['uniqueID'] . '\');';
      if ($this->get('closePopup')) $this->get('closePopup')->onclick = $click;
      if ($this->get('btnNo')) $this->get('btnNo')->onclick = $click;
      if ($this->get('btnCancel')) $this->get('btnCancel')->onclick = $click;
   }

   public function show($opacity = 0.5, $centre = true, $fadeTime = 0, $closeTime = 0, $time = 0)
   {
      if ($this->ajax->isSubmit()) $p = 'parent.';
      $script = $p . 'popup.show(\'' . $this->attributes['uniqueID'] . '\', ' . (float)$opacity . ', \'' . (int)$centre . '\', ' . (int)$fadeTime . ', ' . (int)$closeTime . ');';
      if (Web\Ajax::isAction()) $this->ajax->script($script, $time);
      else $this->js->add(new Helpers\Script(null, $script), 'foot');
      if ($this->properties['updateOnShow']) $this->update();
   }

   public function hide($time = 0)
   {
      if ($this->ajax->isSubmit()) $p = 'parent.';
      $script = $p . 'popup.hide(\'' . $this->attributes['uniqueID'] . '\');';
      if (Web\Ajax::isAction()) $this->ajax->script($script, $time);
      else $this->js->add(new Helpers\Script(null, $script), 'foot');
      $this->update();
   }

   public function JS()
   {
      parent::JS();
      $script = 'popup.initialize(\'' . $this->attributes['uniqueID'] . '\', ' . (int)$this->properties['shadow'] . ');';
      if (Web\Ajax::isAction())
      {
         if ($this->ajax->isSubmit()) $p = 'parent.';
         $this->ajax->script($script, $this->updated, true);
      }
      else $this->js->add(new Helpers\Script('popupinit_' . $this->attributes['uniqueID'], $script), 'foot');
      return $this;
   }
}

?>