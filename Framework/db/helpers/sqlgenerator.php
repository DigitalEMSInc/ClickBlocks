<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Utils;

class SQLGenerator
{
   private $objInfo = null;
   private $orm = null;

   public function __construct()
   {
      $this->orm = ORM::getInstance();
      $this->objInfo = $this->orm->getORMInfoObject();
   }

   public function getDBByClassName($class)
   {
      $tb = $this->objInfo->getTableAliasByClassName(Utils\PHPParser::getClassName($class));
      return $this->orm->getDB($tb[0]);
   }

   public function getRow($class, array &$apk, $isRawData = false)
   {
      $class = Utils\PHPParser::getClassName($class);
      $tb = $this->objInfo->getTableAliasByClassName($class);
      $db = $this->orm->getDB($tb[0]);
      $NN = ($db->getEngine() == 'mssql') ? 'N' : '';
      $pk = array_keys($apk);
      $w = $t = $k = $tmp = array();
      foreach ($this->objInfo->getClassParents($class) as $n => $data)
      {
         $dbName = $db->wrap($this->objInfo->getDBNameByDBAlias($data[0]));
         if ($db->getEngine() == 'mssql') $dbName .= '.[dbo]';
         $table = $db->wrap($this->objInfo->getTableNameByTableAlias($data[0], $data[1]));
         $pk1 = array();
         $t[] = $dbName . '.' . $table . ' AS t' . $n;
         foreach ($this->objInfo->getKeyByTableAlias($data[0], $data[1]) as $keyAlias)
         {
            $key = $this->objInfo->getFieldNameByFieldAlias($data[0], $data[1], $keyAlias);
            $kk = (!$isRawData) ? $keyAlias : $key;
            if (in_array($kk, $pk))
            {
               $w[] = 't' . $n . '.' . $db->wrap($key) . ' = ' . ((is_numeric($apk[$kk]) || $apk[$kk] == '') ? '' : $NN) . '?';
               $tmp[] = $apk[$kk];
            }
            $pk1[] = $key;
         }
         if (isset($pk2) && is_array($pk2))
         {
            foreach ($pk1 as $i => $key) $k[] = 't' . $n . '.' . $db->wrap($key) . ' = t' . ($n - 1) . '.' . $db->wrap($pk2[$i]);
         }
         $pk2 = $pk1;
      }
      $w = array_merge($k, $w);
      $apk = $tmp;
      switch ($db->getEngine())
      {
         case 'mysql':
           return 'SELECT * FROM ' . implode(', ', array_reverse($t)) . ((count($w)) ? ' WHERE ' . implode(' AND ', $w) : '') . ' LIMIT 1';
         case 'mssql':
           return 'SELECT TOP 1 * FROM ' . implode(', ', array_reverse($t)) . ((count($w)) ? ' WHERE ' . implode(' AND ', $w) : '');
       }
   }

   public function updateByID($class, array &$apk, array &$values, $isRawData = false)
   {
      $class = Utils\PHPParser::getClassName($class);
      $tb = $this->objInfo->getTableAliasByClassName($class);
      $db = $this->orm->getDB($tb[0]);
      $NN = ($db->getEngine() == 'mssql') ? 'N' : '';
      $w = $t = $k = $tmp = array();
      $pk = array_keys($apk);
      $parents = $this->objInfo->getClassParents($class);
      foreach ($parents as $n => $data)
      {
         $dbName = $db->wrap($this->objInfo->getDBNameByDBAlias($data[0]));
         if ($db->getEngine() == 'mssql') $dbName .= '.[dbo]';
         $table = $db->wrap($this->objInfo->getTableNameByTableAlias($data[0], $data[1]));
         $pk1 = array();
         $t[$n] = $dbName . '.' . $table;
         foreach ($this->objInfo->getKeyByTableAlias($data[0], $data[1]) as $keyAlias)
         {
            $key = $this->objInfo->getFieldNameByFieldAlias($data[0], $data[1], $keyAlias);
            $kk = (!$isRawData) ? $keyAlias : $key;
            if (in_array($kk, $pk))
            {
               $w[] = $t[$n] . '.' . $db->wrap($key) . ' = ' . ((is_numeric($apk[$kk]) || $apk[$kk] == '') ? '' : $NN) . '?';
               $tmp[] = $apk[$kk];
            }
            $pk1[] = $key;
         }
         if (isset($pk2) && is_array($pk2))
         {
            foreach ($pk1 as $i => $key) $k[] = $t[$n] . '.' . $db->wrap($key) . ' = ' . $t[$n - 1] . '.' . $db->wrap($pk2[$i]);
         }
         $pk2 = $pk1;
      }
      $w = array_merge($k, $w);
      $apk = $tmp;
      $v = $tmp = array();
      foreach ($values as $field => $value)
      {
         foreach ($parents as $n => $data)
         {
            $fieldName = (!$isRawData) ? $this->objInfo->getFieldNameByFieldAlias($data[0], $data[1], $field) : $field;
            if ($fieldName)
            {
               $v[] = $t[$n] . '.' . $db->wrap($fieldName) . ' = ' . ((is_numeric($value) || $value == '') ? '' : $NN) . '?';
               $tmp[] = $value;
            }
         }
      }
      $values = $tmp;
      return 'UPDATE ' . implode(', ', array_reverse($t)) . ' SET ' . implode(', ', $v) . ' WHERE ' . implode(' AND ', $w);
   }

   public function deleteByID($class, array &$apk, $isRawData = false)
   {
      $class = Utils\PHPParser::getClassName($class);
      $tb = $this->objInfo->getTableAliasByClassName($class);
      $db = $this->orm->getDB($tb[0]);
      $NN = ($db->getEngine() == 'mssql') ? 'N' : '';
      $w = $t = $tmp = array();
      $pk = array_keys($apk);
      foreach ($this->objInfo->getClassParents($class) as $n => $data)
      {
         $dbName = $db->wrap($this->objInfo->getDBNameByDBAlias($data[0]));
         if ($db->getEngine() == 'mssql') $dbName .= '.[dbo]';
         $table = $db->wrap($this->objInfo->getTableNameByTableAlias($data[0], $data[1]));
         $pk1 = array();
         foreach ($this->objInfo->getKeyByTableAlias($data[0], $data[1]) as $keyAlias)
         {
            $key = $this->objInfo->getFieldNameByFieldAlias($data[0], $data[1], $keyAlias);
            $kk = (!$isRawData) ? $keyAlias : $key;
            $t[] = 't' . $n;
            if (in_array($kk, $pk))
            {
               $w[] = 't' . $n . '.' . $db->wrap($key) . ' = ' . ((is_numeric($apk[$kk]) || $apk[$kk] == '') ? '' : $NN) . '?';
               $tmp[] = $apk[$kk];
            }
            $pk1[] = $key;
         }
         if (is_array($pk2))
         {
            $k = array();
            foreach ($pk1 as $i => $key) $k[] = 't' . $n . '.' . $db->wrap($key) . ' = t' . ($n - 1) . '.' . $db->wrap($pk2[$i]);
            $j[] = 'INNER JOIN ' . $dbName . '.' . $table . ' AS t' . $n . ' ON ' . implode(' AND ', $k);
         }
         $pk2 = $pk1;
      }
      $apk = $tmp;
      $dbName = $db->wrap($this->objInfo->getDBNameByDBAlias($tb[0]));
      if ($db->getEngine() == 'mssql') $dbName .= '.[dbo]';
      $table = $db->wrap($this->objInfo->getTableNameByTableAlias($tb[0], $tb[1]));
      $sql = 'DELETE ' . implode(', ', $t) . ' FROM ' . $dbName . '.' . $table . ' AS t0' . endl;
      if (count($j)) $sql .= implode(endl, $j) . endl;
      $sql .= 'WHERE ' . implode(' AND ', $w);
      return $sql;
   }

   public function getByQuery($class, $where = null, $start = null, $limit = null)
   {
      $class = Utils\PHPParser::getClassName($class);
      $tb = $this->objInfo->getTableAliasByClassName($class);
      $db = $this->orm->getDB($tb[0]);
      $t = $k = array();
      foreach ($this->objInfo->getClassParents($class) as $n => $data)
      {
         $dbName = $db->wrap($this->objInfo->getDBNameByDBAlias($data[0]));
         if ($db->getEngine() == 'mssql') $dbName .= '.[dbo]';
         $table = $db->wrap($this->objInfo->getTableNameByTableAlias($data[0], $data[1]));
         $pk1 = array();
         foreach ($this->objInfo->getKeyByTableAlias($data[0], $data[1]) as $keyAlias)
         {
            $key = $this->objInfo->getFieldNameByFieldAlias($data[0], $data[1], $keyAlias);
            $t[] = $dbName . '.' . $table . ' AS t' . $n;
            $pk1[] = $key;
         }
         if (is_array($pk2))
         {
            foreach ($pk1 as $i => $key) $k[] = 't' . $n . '.' . $db->wrap($key) . ' = t' . ($n - 1) . '.' . $db->wrap($pk2[$i]);
         }
         $pk2 = $pk1;
      }
      $where = trim($where);
      if ($where) $k[] = $where;
      if (count($k)) $where = implode(' AND ', $k);
      if ($where)
      {
         if (strtolower(substr($where, 0, 5)) != 'order') $where = ' WHERE ' . $where;
         else $where = ' ' . $where;
      }
      if ((int)$limit)
      {
         if ((int)$start) $l = ' LIMIT ' . (int)$start . ', ' . (int)$limit;
         else $l = ' LIMIT ' . (int)$limit;
      }
      return 'SELECT * FROM ' . implode(', ', array_reverse($t)) .  $where . $l;
   }
}

?>