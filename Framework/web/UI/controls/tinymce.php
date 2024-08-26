<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core,
    ClickBlocks\Web,
    ClickBlocks\Web\UI\Helpers;

class TinyMCE extends WebControl
{
   public function __construct($id, $text = null)
   {
      parent::__construct($id);
      $this->properties['options'] = array();
      $this->properties['text'] = $text;
      $this->attributes['name'] = $id;
   }

   public function &__get($param)
   {
      if ($param == 'options') return $this->properties['options'];
      return parent::__get($param);
   }

   public function clean()
   {
      $this->properties['text'] = null;
      return $this;
   }

   public function assign($value)
   {
      $this->properties['text'] = $value;
      return $this;
   }

   public function validate($type, Core\IDelegate $check)
   {
      return $check($this->properties['text']);
   }

   public function JS()
   {
      $this->js->addTool('tinymce');
      if ($this->properties['visible'])
      {
         if (Web\Ajax::isAction()) $this->ajax->script($this->getConstructor(), $this->updated, true);
         else $this->js->add(new Helpers\Script('tinymceinit_' . $this->attributes['uniqueID'], $this->getConstructor()), 'foot');
      }
      return $this;
   }

   public function render()
   {
      if (!$this->properties['visible']) return $this->invisible();
      return '<textarea' . $this->getParams() . '>' . $this->properties['text'] . '</textarea>';
   }

   protected function remove($time = 0)
   {
      $this->ajax->script($this->getDestructor(), $time, true);
      return parent::remove($time);
   }

   protected function repaint()
   {
      $this->js->addTool('tinymce');
      if (!$this->properties['visible'])
      {
         $this->ajax->script($this->getDestructor(), $this->updated, true);
         parent::repaint();
      }
      else
      {
         parent::repaint();
         $this->ajax->script($this->getDestructor(), $this->updated, true);
         $this->ajax->script($this->getConstructor(), $this->updated, true);
      }
   }

   private function getConstructor()
   {
      $options = (array)$this->properties['options'];
      $options['elements'] = $this->attributes['uniqueID'];
      if (!$options['mode']) $options['mode'] = 'exact';
      if (!$options['theme']) $options['theme'] = 'advanced';
      $options = ($options) ? json_encode($options) : '';
      return 'tinyMCE.init(' . $options . ');';
   }

   private function getDestructor()
   {
      return 'if (tinyMCE.get(\'' . $this->attributes['uniqueID'] . '\')) tinyMCE.get(\'' . $this->attributes['uniqueID'] . '\').remove();';
   }
}

?>