<?php

namespace ClickBlocks\ORMTest;

use ClickBlocks\Core,
    ClickBlocks\Cache,
    ClickBlocks\DB,
    ClickBlocks\Exceptions;

/**
 * @property int $AttributeID
 * @property int $CategoryID
 * @property varchar $Attribute
 * @property navigation $Categories
 * @property navigation $ProductAttributes
 */
class LookupProductAttributes extends \ClickBlocks\DB\BLLTable
{
   public function __construct()
   {
      parent::__construct();
      $this->addDAL(new DALLookupProductAttributes(), __CLASS__);
   }

   protected function _initCategories()
   {
      $this->navigators['Categories'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'Categories');
   }

   protected function _initProductAttributes()
   {
      $this->navigators['ProductAttributes'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'ProductAttributes');
   }
}

?>