<?php

namespace ClickBlocks\Web\UI\POM;

class CheckBoxGroup extends SwitchGroup
{  
   public function add(IWebControl $ctrl)
   {
      if (!($ctrl instanceof CheckBox)) throw new \Exception(err_msg('ERR_CTRL_8'));
      return parent::add($ctrl);
   }
}

?>
