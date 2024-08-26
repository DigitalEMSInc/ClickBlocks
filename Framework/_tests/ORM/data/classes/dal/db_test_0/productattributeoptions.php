<?php

namespace ClickBlocks\ORMTest;

use ClickBlocks\Core,
    ClickBlocks\Cache,
    ClickBlocks\DB,
    ClickBlocks\Exceptions;

/**
 * @property int $OptionID
 * @property int $ProductAttributeID
 * @property varchar $Value
 */
class DALProductAttributeOptions extends \ClickBlocks\DB\DALTable
{
   public function __construct()
   {
      parent::__construct('db_test_0', 'ProductAttributeOptions');
   }
}

?>