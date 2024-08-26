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
 */
class DALLookupProductAttributes extends \ClickBlocks\DB\DALTable
{
   public function __construct()
   {
      parent::__construct('db_test_0', 'LookupProductAttributes');
   }
}

?>