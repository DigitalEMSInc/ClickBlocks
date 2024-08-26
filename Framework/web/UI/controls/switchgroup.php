<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core;

class SwitchGroup extends Panel
{
   public function __construct($id, $group = null)
   {
      parent::__construct($id);
      $this->properties['group'] = $group;
   }

   public function add(IWebControl $ctrl)
   {
      if (!($ctrl instanceof RadioButton) && !($ctrl instanceof CheckBox)) throw new \Exception(err_msg('ERR_CTRL_15'));
      $ctrl->name = ($this->properties['group'] ?: $this->attributes['uniqueID']) . '[]';
      return parent::add($ctrl);
   }

   public function __set($param, $value)
   {
      if ($param == 'values') return $this->checked($value);
      if ($param == 'group' && $value != $this->properties['group'])
      {
         foreach ($this as $ctrl) $ctrl->name = ($value ?: $this->attributes['uniqueID']) . '[]';
      }
      parent::__set($param, $value);
   }

   public function __get($param)
   {
      if ($param == 'values')
      {
         $tmp = array();
         foreach ($this->controls as $uniqueID => $v)
         {
            $vs = $this->page->getActualVS($uniqueID);
            if ($vs['parameters'][1]['checked']) $tmp[] = $vs['parameters'][0]['value'];
         }
         return $tmp;
      }
      return parent::__get($param);
   }

   public function clean()
   {
      $this->checked(false);
      return $this;
   }

   public function validate($type, Core\IDelegate $check)
   {
      switch ($type)
      {
         case 'required':
           return $check($this->isChecked('OR'));
      }
      return true;
   }

   public function checked($values, $flag = true)
   {
      if (is_bool($values))
      {
         foreach ($this as $ctrl) $ctrl->checked = $values;
      }
      else if (is_array($values))
      {
         if (is_array(end($values)))
         {
            foreach ($this as $ctrl)
            {
               $ctrl->checked = (bool)$values[$ctrl->uniqueID]['state'];
               $ctrl->value = $values[$ctrl->uniqueID]['value'];
            }
         }
         else
         {
            foreach ($this as $ctrl)
              if (in_array($ctrl->value, $values))
                $ctrl->checked = $flag;
         }
      }
      else
      {
         foreach ($this as $ctrl)
         {
            $ctrl->checked = ($ctrl->value == $values);
         }
      }
      return $this;
   }

   public function isChecked($mode = 'OR')
   {
      switch ($mode)
      {
         case 'AND':
           $flag = true;
           foreach ($this->controls as $uniqueID => $v)
           {
              $vs = $this->page->getActualVS($uniqueID);
              $flag &= $vs['parameters'][1]['checked'];
           }
           break;
         case 'OR':
           $flag = false;
           foreach ($this->controls as $uniqueID => $v)
           {
              $vs = $this->page->getActualVS($uniqueID);
              $flag |= $vs['parameters'][1]['checked'];
           }
           break;
         case 'XOR':
           $n = 0;
           foreach ($this->controls as $uniqueID => $v)
           {
              $vs = $this->page->getActualVS($uniqueID);
              if ($vs['parameters'][1]['checked']) $n++;
           }
           $flag = ($n == 1);
           break;
      }
      return $flag;
   }
}

?>
