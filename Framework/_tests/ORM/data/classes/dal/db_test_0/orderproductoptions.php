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
 */
class DALOrderProductOptions extends \ClickBlocks\DB\DALTable
{
   public function __construct()
   {
      parent::__construct('db_test_0', 'OrderProductOptions');
   }
}

?>