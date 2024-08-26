<?php

namespace ClickBlocks\ORMTest;

use ClickBlocks\Core,
    ClickBlocks\Cache,
    ClickBlocks\DB,
    ClickBlocks\Exceptions;

/**
 * @property int $OrderProductOptionID
 * @property int $OrderID
 * @property int $ProductID
 * @property int $SupplierID
 * @property int $ProductAttributeID
 * @property int $OptionID
 * @property navigation $Suppliers
 * @property navigation $OrderProducts
 * @property navigation $ProductAttributeOptions
 * @property navigation $ProductAttributes
 * @property navigation $Products
 */
class OrderProductOptions extends \ClickBlocks\DB\BLLTable
{
   public function __construct()
   {
      parent::__construct();
      $this->addDAL(new DALOrderProductOptions(), __CLASS__);
   }

   protected function _initSuppliers()
   {
      $this->navigators['Suppliers'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'Suppliers');
   }

   protected function _initOrderProducts()
   {
      $this->navigators['OrderProducts'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'OrderProducts');
   }

   protected function _initProductAttributeOptions()
   {
      $this->navigators['ProductAttributeOptions'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'ProductAttributeOptions');
   }

   protected function _initProductAttributes()
   {
      $this->navigators['ProductAttributes'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'ProductAttributes');
   }

   protected function _initProducts()
   {
      $this->navigators['Products'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'Products');
   }
}

?>