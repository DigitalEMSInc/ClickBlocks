<?php

namespace ClickBlocks\ORMTest;

use ClickBlocks\Core,
    ClickBlocks\Cache,
    ClickBlocks\DB,
    ClickBlocks\Exceptions;

/**
 * @property char $StatusID
 * @property varchar $Status
 * @property navigation $Orders
 */
class LookupOrderStatuses extends \ClickBlocks\DB\BLLTable
{
   public function __construct()
   {
      parent::__construct();
      $this->addDAL(new DALLookupOrderStatuses(), __CLASS__);
   }

   protected function _initOrders()
   {
      $this->navigators['Orders'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'Orders');
   }
}

?>