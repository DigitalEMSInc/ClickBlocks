<?php

namespace ClickBlocks\ORMTest;

use ClickBlocks\Core,
    ClickBlocks\Cache,
    ClickBlocks\DB,
    ClickBlocks\Exceptions;

/**
 * @property int $CategoryID
 * @property varchar $Name
 * @property int $ParentCategoryID
 * @property navigation $Parent
 * @property navigation $Categories
 * @property navigation $LookupProductAttributes
 * @property navigation $Products
 */
class Categories extends \ClickBlocks\DB\BLLTable
{
   public function __construct()
   {
      parent::__construct();
      $this->addDAL(new DALCategories(), __CLASS__);
   }

   protected function _initParent()
   {
      $this->navigators['Parent'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'Parent');
   }

   protected function _initCategories()
   {
      $this->navigators['Categories'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'Categories');
   }

   protected function _initLookupProductAttributes()
   {
      $this->navigators['LookupProductAttributes'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'LookupProductAttributes');
   }

   protected function _initProducts()
   {
      $this->navigators['Products'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'Products');
   }
}

?>