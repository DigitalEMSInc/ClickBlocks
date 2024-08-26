<?php

namespace ClickBlocks\ORMTest;

use ClickBlocks\Core,
    ClickBlocks\Cache,
    ClickBlocks\DB,
    ClickBlocks\Exceptions;

/**
 * @property int $ProductID
 * @property varchar $Name
 * @property varchar $Description
 * @property int $CategoryID
 * @property int $ManagerID
 * @property navigation $Managers
 * @property navigation $Categories
 * @property navigation $OrderProductOptions
 * @property navigation $OrderProducts
 * @property navigation $ProductAttributes
 * @property navigation $Stock
 */
class Products extends \ClickBlocks\DB\BLLTable
{
   public function __construct()
   {
      parent::__construct();
      $this->addDAL(new DALProducts(), __CLASS__);
   }

   protected function _initManagers()
   {
      $this->navigators['Managers'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'Managers');
   }

   protected function _initCategories()
   {
      $this->navigators['Categories'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'Categories');
   }

   protected function _initOrderProductOptions()
   {
      $this->navigators['OrderProductOptions'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'OrderProductOptions');
   }

   protected function _initOrderProducts()
   {
      $this->navigators['OrderProducts'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'OrderProducts');
   }

   protected function _initProductAttributes()
   {
      $this->navigators['ProductAttributes'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'ProductAttributes');
   }

   protected function _initStock()
   {
      $this->navigators['Stock'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'Stock');
   }
}

?>