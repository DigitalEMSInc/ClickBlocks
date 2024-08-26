<?php

namespace ClickBlocks\ORMTest;

use ClickBlocks\Core,
    ClickBlocks\Cache,
    ClickBlocks\DB,
    ClickBlocks\Exceptions;

/**
 * @property int $OrderID
 * @property int $CustomerID
 * @property datetime $Created
 * @property char $StatusID
 * @property int $ManagerID
 * @property navigation $Managers
 * @property navigation $Customers
 * @property navigation $LookupOrderStatuses
 * @property navigation $OrderProducts
 */
class Orders extends \ClickBlocks\DB\BLLTable
{
   public function __construct()
   {
      parent::__construct();
      $this->addDAL(new DALOrders(), __CLASS__);
   }

   protected function _initManagers()
   {
      $this->navigators['Managers'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'Managers');
   }

   protected function _initCustomers()
   {
      $this->navigators['Customers'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'Customers');
   }

   protected function _initLookupOrderStatuses()
   {
      $this->navigators['LookupOrderStatuses'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'LookupOrderStatuses');
   }

   protected function _initOrderProducts()
   {
      $this->navigators['OrderProducts'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'OrderProducts');
   }
}

?>