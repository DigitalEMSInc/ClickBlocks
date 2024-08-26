<?php

namespace ClickBlocks\ORMTest;

use ClickBlocks\Core,
    ClickBlocks\Cache,
    ClickBlocks\DB,
    ClickBlocks\Exceptions;

/**
 * @property char $TypeID
 * @property varchar $TypeName
 * @property navigation $Users
 */
class LookupUserTypes extends \ClickBlocks\DB\BLLTable
{
   public function __construct()
   {
      parent::__construct();
      $this->addDAL(new DALLookupUserTypes(), __CLASS__);
   }

   protected function _initUsers()
   {
      $this->navigators['Users'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'Users');
   }
}

?>