<?php

namespace ClickBlocks\ORMTest;

use ClickBlocks\Core,
    ClickBlocks\Cache,
    ClickBlocks\DB,
    ClickBlocks\Exceptions;

/**
 * @property int $SupplierID
 * @property varchar $Name
 * @property navigation $OrderProductOptions
 * @property navigation $OrderProducts
 * @property navigation $Stock
 */
class Suppliers extends \ClickBlocks\DB\BLLTable
{
   public function __construct()
   {
      parent::__construct();
      $this->addDAL(new DALSuppliers(), __CLASS__);
   }

   protected function _initOrderProductOptions()
   {
      $this->navigators['OrderProductOptions'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'OrderProductOptions');
   }

   protected function _initOrderProducts()
   {
      $this->navigators['OrderProducts'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'OrderProducts');
   }

   protected function _initStock()
   {
      $this->navigators['Stock'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'Stock');
   }
}

?>