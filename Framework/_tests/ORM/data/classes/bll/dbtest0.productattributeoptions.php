<?php

namespace ClickBlocks\ORMTest;

use ClickBlocks\Core,
    ClickBlocks\Cache,
    ClickBlocks\DB,
    ClickBlocks\Exceptions;

/**
 * @property int $OptionID
 * @property int $ProductAttributeID
 * @property varchar $Value
 * @property navigation $ProductAttributes
 * @property navigation $OrderProductOptions
 */
class ProductAttributeOptions extends \ClickBlocks\DB\BLLTable
{
   public function __construct()
   {
      parent::__construct();
      $this->addDAL(new DALProductAttributeOptions(), __CLASS__);
   }

   protected function _initProductAttributes()
   {
      $this->navigators['ProductAttributes'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'ProductAttributes');
   }

   protected function _initOrderProductOptions()
   {
      $this->navigators['OrderProductOptions'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'OrderProductOptions');
   }
}

?>