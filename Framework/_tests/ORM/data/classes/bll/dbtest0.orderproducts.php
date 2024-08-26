<?php

namespace ClickBlocks\ORMTest;

use ClickBlocks\Core,
    ClickBlocks\Cache,
    ClickBlocks\DB,
    ClickBlocks\Exceptions;

/**
 * @property int $OrderID
 * @property int $ProductID
 * @property int $SupplierID
 * @property int $Quantity
 * @property navigation $Suppliers
 * @property navigation $Orders
 * @property navigation $Products
 * @property navigation $Stock
 * @property navigation $OrderProductOptions
 */
class OrderProducts extends \ClickBlocks\DB\BLLTable
{
   public function __construct()
   {
      parent::__construct();
      $this->addDAL(new DALOrderProducts(), __CLASS__);
   }

   protected function _initSuppliers()
   {
      $this->navigators['Suppliers'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'Suppliers');
   }

   protected function _initOrders()
   {
      $this->navigators['Orders'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'Orders');
   }

   protected function _initProducts()
   {
      $this->navigators['Products'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'Products');
   }

   protected function _initStock()
   {
      $this->navigators['Stock'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'Stock');
   }

   protected function _initOrderProductOptions()
   {
      $this->navigators['OrderProductOptions'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'OrderProductOptions');
   }
}

?>