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
 */
class DALProductAttributes extends \ClickBlocks\DB\DALTable
{
   public function __construct()
   {
      parent::__construct('db_test_0', 'ProductAttributes');
   }
}

?>