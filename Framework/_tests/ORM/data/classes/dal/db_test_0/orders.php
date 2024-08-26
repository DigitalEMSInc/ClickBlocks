<?php

namespace ClickBlocks\ORMTest;

use ClickBlocks\Core,
    ClickBlocks\Cache,
    ClickBlocks\DB,
    ClickBlocks\Exceptions;

/**
 * @property int $OrderID
 * @property int $CustomerID
 * @property datetime $Created
 * @property char $StatusID
 * @property int $ManagerID
 */
class DALOrders extends \ClickBlocks\DB\DALTable
{
   public function __construct()
   {
      parent::__construct('db_test_0', 'Orders');
   }
}

?>