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
 * @property navigation $Orders
 * @property navigation $Parent
 * @property navigation $Children
 * @property navigation $LookupUserTypes
 * @property navigation $Managers
 */
class Customers extends Users
{
   public function __construct()
   {
      parent::__construct();
      $this->addDAL(new DALCustomers(), __CLASS__);
   }

   protected function _initOrders()
   {
      $this->navigators['Orders'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'Orders');
   }

   protected function _initParent()
   {
      $this->navigators['Parent'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'Parent');
   }

   protected function _initChildren()
   {
      $this->navigators['Children'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'Children');
   }
}

?>