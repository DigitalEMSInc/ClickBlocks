<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core,
    ClickBlocks\Web,
    ClickBlocks\Web\UI\Helpers;

class DateTimePicker extends TextBox
{
   public function __construct($id, $value = null)
   {
      parent::__construct($id, $value);
      $this->properties['theme'] = null;
      $this->properties['buttonClass'] = null;
      $this->properties['options'] = array();
      $this->properties['tagContainer'] = 'span';
   }

   public function &__get($param)
   {
      if ($param == 'options') return $this->properties['options'];
      return parent::__get($param);
   }

   public function CSS()
   {
      if (!$this->properties['visible']) return;
      if (Web\Ajax::isAction())
      {
         $this->ajax->css('/Framework/_engine/web/js/datetimepicker/src/css/jscal2.css', 0, true);
         $this->ajax->css('/Framework/_engine/web/js/datetimepicker/src/css/border-radius.css', 0, true);
         if ($this->properties['theme']) $this->ajax->css('/Framework/_engine/web/js/datetimepicker/src/css/' . $this->properties['theme'] . '/' . $this->properties['theme'] . '.css', 0, true);
      }
      else
      {
         $this->css->add(new Helpers\Style('jscal2', null, '/Framework/_engine/web/js/datetimepicker/src/css/jscal2.css'));
         $this->css->add(new Helpers\Style('border', null, '/Framework/_engine/web/js/datetimepicker/src/css/border-radius.css'));
         if ($this->properties['theme']) $this->css->add(new Helpers\Style('theme', null, '/Framework/_engine/web/js/datetimepicker/src/css/' . $this->properties['theme'] . '/' . $this->properties['theme'] . '.css'));
      }
      return $this;
   }

   public function JS()
   {
      $this->js->addTool('datetimepicker');
      if ($this->properties['visible'])
      {
         if (Web\Ajax::isAction()) $this->ajax->script($this->getConstructor(), $this->updated, true);
         else $this->js->add(new Helpers\Script('datetimepickerinit_' . $this->attributes['uniqueID'], $this->getConstructor()), 'foot');
      }
      return $this;
   }

   public function render()
   {
      if (!$this->properties['visible']) return $this->invisible();
      $html = '<' . $this->properties['tagContainer'] . ' id="container_' . $this->attributes['uniqueID'] . '">';
      $html .= '<input type="text"';
      if ($this->properties['disabled']) $html .= ' disabled="disabled"';
      if ($this->properties['readonly']) $html .= ' readonly="readonly"';
      $html .= $this->getParams() . ' />';
      $html .= '<input type="button"';
      if ($this->properties['disabled']) $html .= ' disabled="disabled"';
      $html .= ' id="btn_' . $this->attributes['uniqueID'] . '" class="' . $this->properties['buttonClass'] . '" />';
      $html .= '</' . $this->properties['tagContainer'] . '>';
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

   private function getConstructor()
   {
      $exceptions = array('selectionType' => 1,
                          'onSelect' => 1,
                          'onChange' => 1,
                          'onTimeChange' => 1,
                          'disabled' => 1,
                          'dateInfo' => 1,
                          'onFocus' => 1,
                          'onBlur' => 1);
      $options = (array)$this->properties['options'];
      $options['inputField'] = $this->attributes['uniqueID'];
      if (!$options['inputTrigger'])
         $options['trigger'] = 'btn_' . $this->attributes['uniqueID'];
      else
         $options['trigger'] = $this->attributes['uniqueID'];
      if (!$options['onSelect']) $options['onSelect'] = 'function() {this.hide();}';
      foreach ($options as $k => &$option)
      {
         if (!isset($exceptions[$k])) $option = json_encode($option);
         $option = $k . ': ' . $option;
      }
      $options = implode(', ', $options);
      return 'Calendar.setup({' . $options . '});';
   }
}

?>