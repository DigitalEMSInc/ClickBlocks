<?php

namespace ClickBlocks\ORMTest;

use ClickBlocks\Core,
    ClickBlocks\Cache,
    ClickBlocks\DB,
    ClickBlocks\Exceptions;

/**
 * @property char $TypeID
 * @property varchar $TypeName
 */
class DALLookupUserTypes extends \ClickBlocks\DB\DALTable
{
   public function __construct()
   {
      parent::__construct('db_test_0', 'LookupUserTypes');
   }
}

?>