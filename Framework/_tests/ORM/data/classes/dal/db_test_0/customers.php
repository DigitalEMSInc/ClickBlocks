<?php

namespace ClickBlocks\ORMTest;

use ClickBlocks\Core,
    ClickBlocks\Cache,
    ClickBlocks\DB,
    ClickBlocks\Exceptions;

/**
 * @property int $CustomerID
 * @property varchar $Phone
 * @property varchar $Fax
 * @property varchar $Country
 * @property int $ParentCustomerID
 */
class DALCustomers extends \ClickBlocks\DB\DALTable
{
   public function __construct()
   {
      parent::__construct('db_test_0', 'Customers');
   }
}

?>