<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core;

class ValidatorCustom extends Validator
{
   public function __construct($id, $message = null)
   {
      parent::__construct($id, $message);
      $this->properties['serverFunction'] = null;
      $this->properties['clientFunction'] = null;
      $this->type = 'custom';
   }

   public function validate()
   {
      if (!$this->properties['serverFunction']) $flag = true;
      else
      {
         $delegate = new Core\Delegate($this->properties['serverFunction']);
         $flag = $delegate($this->properties['controls'], $this->properties['mode']);
      }
      $this->properties['isValid'] = $flag;
      $this->doAction();
      return $this;
   }

   protected function getScriptString()
   {
      if ($this->ajax->isSubmit()) $p = 'parent.';
      return $p . 'validators.add(\'' . $this->attributes['uniqueID'] . '\', {\'groups\': \'' . addslashes(implode(',', $this->groups)) . '\', \'cids\': \'' . implode(',', $this->controls) . '\', \'type\': \'' . $this->type . '\', \'order\': \'' . (int)$this->properties['order'] . '\', \'mode\': \'' . addslashes($this->properties['mode']) . '\', \'message\': \'' . addslashes($this->properties['text']) . '\', \'action\': \'' . addslashes($this->properties['action']) . '\', \'unaction\': \'' . addslashes($this->properties['unaction']) . '\', \'exparam\': {\'hiding\': ' . (int)$this->properties['hiding'] . ', \'serverFunction\': \'' . addslashes($this->properties['serverFunction']) . '\', \'clientFunction\': ' . ($this->properties['clientFunction'] ?: 'false') . '}});';
   }
}
