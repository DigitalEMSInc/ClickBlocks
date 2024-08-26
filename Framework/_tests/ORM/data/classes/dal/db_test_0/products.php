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
 */
class DALProducts extends \ClickBlocks\DB\DALTable
{
   public function __construct()
   {
      parent::__construct('db_test_0', 'Products');
   }
}

?>