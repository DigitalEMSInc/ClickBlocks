<?php

namespace ClickBlocks\ORMTest;

use ClickBlocks\Core,
    ClickBlocks\Cache,
    ClickBlocks\DB,
    ClickBlocks\Exceptions;

/**
 * @property char $StatusID
 * @property varchar $Status
 */
class DALLookupOrderStatuses extends \ClickBlocks\DB\DALTable
{
   public function __construct()
   {
      parent::__construct('db_test_0', 'LookupOrderStatuses');
   }
}

?>