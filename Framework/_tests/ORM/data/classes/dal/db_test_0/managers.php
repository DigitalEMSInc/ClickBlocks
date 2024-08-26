<?php

namespace ClickBlocks\ORMTest;

use ClickBlocks\Core,
    ClickBlocks\Cache,
    ClickBlocks\DB,
    ClickBlocks\Exceptions;

/**
 * @property int $ManagerID
 * @property datetime $ContractSigned
 * @property float $Salary
 * @property datetime $Fired
 */
class DALManagers extends \ClickBlocks\DB\DALTable
{
   public function __construct()
   {
      parent::__construct('db_test_0', 'Managers');
   }
}

?>