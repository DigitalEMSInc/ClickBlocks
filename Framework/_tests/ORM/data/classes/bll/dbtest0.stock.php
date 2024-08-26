<?php

namespace ClickBlocks\ORMTest;

use ClickBlocks\Core,
    ClickBlocks\Cache,
    ClickBlocks\DB,
    ClickBlocks\Exceptions;

/**
 * @property int $ProductID
 * @property int $SupplierID
 * @property int $Quantity
 * @property float $Price
 * @property navigation $Suppliers
 * @property navigation $Products
 * @property navigation $OrderProducts
 */
class Stock extends \ClickBlocks\DB\BLLTable
{
   public function __construct()
   {
      parent::__construct();
      $this->addDAL(new DALStock(), __CLASS__);
   }

   protected function _initSuppliers()
   {
      $this->navigators['Suppliers'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'Suppliers');
   }

   protected function _initProducts()
   {
      $this->navigators['Products'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'Products');
   }

   protected function _initOrderProducts()
   {
      $this->navigators['OrderProducts'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'OrderProducts');
   }
}

?>