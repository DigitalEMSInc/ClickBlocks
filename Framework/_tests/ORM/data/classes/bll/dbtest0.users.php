<?php

namespace ClickBlocks\ORMTest;

use ClickBlocks\Core,
    ClickBlocks\Cache,
    ClickBlocks\DB,
    ClickBlocks\Exceptions;

/**
 * @property int $UserID
 * @property char $TypeID
 * @property varchar $FirstName
 * @property varchar $LastName
 * @property varchar $Email
 * @property varchar $Password
 * @property datetime $Created
 * @property datetime $Updated
 * @property datetime $Deleted
 * @property datetime $LastActivity
 * @property custom $FullName
 * @property navigation $LookupUserTypes
 * @property navigation $Customers
 * @property navigation $Managers
 */
class Users extends \ClickBlocks\DB\BLLTable
{
   public function __construct()
   {
      parent::__construct();
      $this->addDAL(new DALUsers(), __CLASS__);
   }

   protected function _initLookupUserTypes()
   {
      $this->navigators['LookupUserTypes'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'LookupUserTypes');
   }

   protected function _initCustomers()
   {
      $this->navigators['Customers'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'Customers');
   }

   protected function _initManagers()
   {
      $this->navigators['Managers'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'Managers');
   }
}

?>