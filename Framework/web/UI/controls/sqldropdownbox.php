<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core,
    ClickBlocks\DB;

class SQLDropDownBox extends DropDownBox
{
   public function __construct($id, $value = null, $sql = null)
   {
      parent::__construct($id, $value);
      $this->properties['dbAlias'] = 'db0';
      $this->sql = $sql;
   }

   public function __set($param, $value)
   {
      if ($param == 'sql') $this->setupOptionsBySQL($value);
      else parent::__set($param, $value);
   }

   protected function setupOptionsBySQL($sql)
   {
      if (!$sql) return;
      $this->properties['options'] = DB\ORM::getInstance()->getDB($this->dbAlias)->couples($sql, array(), \PDO::FETCH_NUM);
   }
}

?>