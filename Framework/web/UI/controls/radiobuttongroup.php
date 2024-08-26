<?php

namespace ClickBlocks\Web\UI\POM;

class RadioButtonGroup extends SwitchGroup
{
   public function add(IWebControl $ctrl)
   {
      if (!($ctrl instanceof RadioButton)) throw new \Exception(err_msg('ERR_CTRL_9'));
      return parent::add($ctrl);
   }

   public function __get($param)
   {
      if ($param == 'value') return $this->values[0];
      return parent::__get($param);
   }
}

?>