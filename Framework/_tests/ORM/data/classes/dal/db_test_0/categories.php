<?php

namespace ClickBlocks\ORMTest;

use ClickBlocks\Core,
    ClickBlocks\Cache,
    ClickBlocks\DB,
    ClickBlocks\Exceptions;

/**
 * @property int $CategoryID
 * @property varchar $Name
 * @property int $ParentCategoryID
 */
class DALCategories extends \ClickBlocks\DB\DALTable
{
   public function __construct()
   {
      parent::__construct('db_test_0', 'Categories');
   }
}

?>