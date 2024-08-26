<?php

namespace ClickBlocks\ORMTest;

use ClickBlocks\Core,
    ClickBlocks\Cache,
    ClickBlocks\DB,
    ClickBlocks\Exceptions;

/**
 * @property int $ManagerID
 * @property datetime $ContractSigned
 * @property float $Salary
 * @property datetime $Fired
 * @property navigation $Users
 * @property navigation $Orders
 * @property navigation $Products
 */
class Managers extends \ClickBlocks\DB\BLLTable
{
   public function __construct()
   {
      parent::__construct();
      $this->addDAL(new DALManagers(), __CLASS__);
   }

   protected function _initUsers()
   {
      $this->navigators['Users'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'Users');
   }

   protected function _initOrders()
   {
      $this->navigators['Orders'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'Orders');
   }

   protected function _initProducts()
   {
      $this->navigators['Products'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'Products');
   }
}

?>