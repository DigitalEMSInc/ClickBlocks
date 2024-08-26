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
 */
class DALUsers extends \ClickBlocks\DB\DALTable
{
   public function __construct()
   {
      parent::__construct('db_test_0', 'Users');
   }

   protected function _setterLastActivity($value)
   {
      return ($value) ? Core\DT::format($value, 'm/d/Y H:i:s', 'Y-m-d H:i:s') : null;
   }

   protected function _getterLastActivity($value)
   {
      return ($value) ? Core\DT::format($value, 'Y-m-d H:i:s', 'm/d/Y H:i:s') : null;
   }

   protected function _getFullName()
   {
      return $this->FirstName . ' ' . $this->LastName;
   }

   protected function _setFullName($value)
   {
      list($this->FirstName, $this->LastName) = explode(' ', $value);
   }
}

?>