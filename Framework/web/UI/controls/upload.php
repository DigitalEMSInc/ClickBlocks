<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core,
    ClickBlocks\Web\UI\Helpers;

class Upload extends WebControl
{
   public function __construct($id, $callBack = null)
   {
      parent::__construct($id);
      $this->attributes['value'] = null;
      $this->attributes['size'] = null;
      $this->attributes['onchange'] = null;
      $this->attributes['onload'] = null;
      $this->attributes['onunload'] = null;
      $this->properties['isProgress'] = false;
      $this->properties['callBack'] = $callBack;
      $this->properties['callBackProgress'] = null;
      $this->properties['autoCreationForm'] = true;
      $this->properties['classContainer'] = null;
      $this->properties['styleContainer'] = null;
      $this->properties['rfc1867Name'] = ini_get('apc.rfc1867_name');
      $this->properties['tag'] = 'span';
   }

   public function clean()
   {
      $this->attributes['value'] = null;
      return $this;
   }

   public function assign($value)
   {
      $this->attributes['value'] = (is_array($value)) ? $value['name'] : $value;
      return $this;
   }

   public function validate($type, Core\IDelegate $check)
   {
      return $check($this->attributes['value']);
   }

   public function render()
   {
      if (!$this->properties['visible']) return $this->invisible();
      $mouseover = $this->attributes['onmouseover'];
      $change = $this->attributes['onchange'];
      $html = '<' . $this->properties['tag'] . ' id="container_' . $this->attributes['uniqueID'] . '" style="' . htmlspecialchars($this->properties['styleContainer']) . '" class="' . htmlspecialchars($this->properties['classContainer']) . '">';
      if ($this->properties['callBack'])
      {
         if ($this->properties['isProgress'] && $this->properties['callBackProgress'])
         {
            $html .= '<input type="hidden" id="ajax_progress_key_' . $this->attributes['uniqueID'] . '" name="rfc1867Name" value="' . uniqid() . '"></input>';
            $this->attributes['onchange'] .= ';ajax.submitProgress(\'' . addslashes($this->properties['callBack']) . '\', \'frame_' . $this->attributes['uniqueID'] . '\', \'' . addslashes($this->attributes['callBackProgress']) . '\')';
         }
         else
         {
            $this->attributes['onchange'] .= ';ajax.submit(\'' . addslashes($this->properties['callBack']) . '\', \'frame_' . $this->attributes['uniqueID'] . '\')';
         }
         $frame = new Helpers\IFrame('frame_' . $this->attributes['uniqueID']);
         $frame->addStyle('display', 'none');
         $frame->onload = 'ajax.hideLoader();';
         $html .= $frame->render();
      }
      if ($this->properties['autoCreationForm']) $this->attributes['onmouseover'] .= ';ajax._getFormByTarget(\'frame_\' + this.id)';
      $this->attributes['name'] = $this->attributes['uniqueID'];
      $html .= '<input type="file"';
      if ($this->properties['disabled']) $html .= ' disabled="disabled"';
      $html .= $this->getParams() . ' />' . $this->properties['text'] . '</' . $this->properties['tag'] . '>';
      unset($this->attributes['name']);
      $this->attributes['onmouseover'] = $mouseover;
      $this->attributes['onchange'] = $change;
      return $html;
   }

   protected function repaint()
   {
      $this->updated = $this->updated ?: 200;
      parent::repaint();
   }

   protected function getRepaintID()
   {
      return 'container_' . $this->attributes['uniqueID'];
   }
}

?>
