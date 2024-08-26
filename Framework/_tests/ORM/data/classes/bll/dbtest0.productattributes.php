<?php

namespace ClickBlocks\ORMTest;

use ClickBlocks\Core,
    ClickBlocks\Cache,
    ClickBlocks\DB,
    ClickBlocks\Exceptions;

/**
 * @property int $ProductAttributeID
 * @property int $ProductID
 * @property int $AttributeID
 * @property navigation $LookupProductAttributes
 * @property navigation $Products
 * @property navigation $OrderProductOptions
 * @property navigation $ProductAttributeOptions
 */
class ProductAttributes extends \ClickBlocks\DB\BLLTable
{
   public function __construct()
   {
      parent::__construct();
      $this->addDAL(new DALProductAttributes(), __CLASS__);
   }

   protected function _initLookupProductAttributes()
   {
      $this->navigators['LookupProductAttributes'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'LookupProductAttributes');
   }

   protected function _initProducts()
   {
      $this->navigators['Products'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'Products');
   }

   protected function _initOrderProductOptions()
   {
      $this->navigators['OrderProductOptions'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'OrderProductOptions');
   }

   protected function _initProductAttributeOptions()
   {
      $this->navigators['ProductAttributeOptions'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'ProductAttributeOptions');
   }
}

?>