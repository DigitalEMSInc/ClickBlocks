<?php

namespace ClickBlocks\Web\UI\POM;

class ValidatorRequired extends Validator
{
   public function __construct($id, $message = null)
   {
      parent::__construct($id, $message);
      $this->type = 'required';
   }

   public function validate()
   {
      switch ($this->properties['mode'])
      {
         case 'AND':
           $flag = true;
           foreach ($this->properties['controls'] as $uniqueID) $flag &= $this->validateControl($uniqueID);
           break;
         case 'OR':
           $flag = false;
           foreach ($this->properties['controls'] as $uniqueID) $flag |= $this->validateControl($uniqueID);
           break;
         case 'XOR':
           $n = 0;
           foreach ($this->properties['controls'] as $uniqueID) if ($this->validateControl($uniqueID)) $n++;
           $flag = ($n == 1);
           break;
      }
      $this->properties['isValid'] = $flag;
      $this->doAction();
      return $this;
   }

   public function check($value)
   {
      return ($value != '');
   }
}
