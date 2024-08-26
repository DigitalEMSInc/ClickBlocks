<?php

namespace ClickBlocks\Web\UI\POM;

class ValidatorCompare extends Validator
{
   public function __construct($id, $message = null)
   {
      parent::__construct($id, $message);
      $this->type = 'compare';
   }

   public function validate()
   {
      switch ($this->properties['mode'])
      {
         case 'AND':
           $flag = true;
           foreach ($this->properties['controls'] as $uid1)
           {
              $ctrl1 = $this->page->getByUniqueID($uid1);
              if ($ctrl1 === false) throw new \Exception(err_msg('ERR_VAL_1', array($uid1)));
              foreach ($this->properties['controls'] as $uid2)
              {
                 $ctrl2 = $this->page->getByUniqueID($uid2);
                 if ($ctr2 === false) throw new \Exception(err_msg('ERR_VAL_1', array($uid2)));
                 $flag &= ($this->getActualValue($ctrl1) == $this->getActualValue($ctrl2));
              }
           }
           break;
         case 'OR':
           $flag = false;
           foreach ($this->properties['controls'] as $uid1)
           {
              $ctrl1 = $this->page->getByUniqueID($uid1);
              if ($ctrl1 === false) throw new \Exception(err_msg('ERR_VAL_1', array($uid1)));
              foreach ($this->properties['controls'] as $uid2)
              {
                 $ctrl2 = $this->page->getByUniqueID($uid2);
                 if ($ctrl2 === false) throw new \Exception(err_msg('ERR_VAL_1', array($uid2)));
                 if ($ctrl1->id != $ctrl2->id) $flag |= ($this->getActualValue($ctrl1) == $this->getActualValue($ctrl2));
                 if ($flag) break;
              }
              if ($flag) break;
           }
           break;
         case 'XOR':
           $n = 0;
           $controls = array_values($this->properties['controls']);
           $c = count($controls);
           for ($i = 0; $i < $c; $i++)
           {
              $uid1 = $controls[$i];
              $ctrl1 = $this->page->getByUniqueID($uid1);
              if ($ctrl1 === false) throw new \Exception(err_msg('ERR_VAL_1', array($uid1)));
              for ($j = $i + 1; $j < $c; $j++)
              {
                 $uid2 = $controls[$j];
                 $ctrl2 = $this->page->getByUniqueID($uid2);
                 if ($ctrl2 === false) throw new \Exception(err_msg('ERR_VAL_1', array($uid2)));
                 if ($ctrl1->id != $ctrl2->id && $this->getActualValue($ctrl1) == $this->getActualValue($ctrl2)) $n++;
              }
           }
           $flag = ($n == 1);
           break;
      }
      $this->properties['isValid'] = $flag;
      $this->doAction();
      return $this;
   }

   private function getActualValue(IWebControl $ctrl)
   {
      if (!method_exists($ctrl, 'validate')) return;
      if (isset($ctrl->value)) $value = $ctrl->value;
      else if (isset($ctrl->text)) $value = $ctrl->text;
      return $value;
   }
}

