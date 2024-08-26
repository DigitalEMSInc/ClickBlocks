<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\IO,
    ClickBlocks\Exceptions;

class ORMInfo
{
   private $info = null;
   private $config = null;

   public function __construct(array $info)
   {
      $this->info = $info;
      $this->config = Core\Register::getInstance()->config;
   }

   public function getInfo()
   {
      return $this->info;
   }

   public function getNamespace()
   {
      return $this->info['namespace'];
   }

   public function getDBAliasByDBName($dbName)
   {
      return $this->info['aliases']['dbs']['names'][$dbName];
   }

   public function getDBNameByDBAlias($dbAlias)
   {
      preg_match('/dbname=([^;]+);/i', $this->config[$dbAlias]['dsn'], $arr);
      return $arr[1];
   }

   public function getTableAliasByTableName($dbAlias, $tableName)
   {
      return $this->info['aliases']['tables']['names'][$dbAlias][$tableName];
   }

   public function getTableNameByTableAlias($dbAlias, $tableAlias)
   {
      return $this->info['aliases']['tables']['aliases'][$dbAlias][$tableAlias];
   }

   public function getFieldNameByFieldAlias($dbAlias, $tableAlias, $fieldAlias)
   {
      return $this->info['aliases']['fields']['aliases'][$dbAlias][$tableAlias][$fieldAlias];
   }

   public function getFieldAliasByFieldName($dbAlias, $tableAlias, $fieldName)
   {
      return $this->info['aliases']['fields']['names'][$dbAlias][$tableAlias][$fieldName];
   }

   public function getFieldAliasesByTableAlias($dbAlias, $tableAlias)
   {
      return array_keys($this->info['aliases']['fields']['aliases'][$dbAlias][$tableAlias]);
   }

   public function getFieldAliasesByTableName($dbAlias, $tableName)
   {
      return $this->getFieldAliasesByTableAlias($dbAlias, $this->getTableAliasByTableName($tableName));
   }

   public function getFieldsInfoByTableAlias($dbAlias, $tableAlias)
   {
      return $this->info['model'][$dbAlias]['tables'][$tableAlias]['fields'];
   }

   public function getFieldsInfoByTableName($dbAlias, $tableName)
   {
      return $this->getFieldsInfoByTableAlias($dbAlias, $this->getTableAliasByTableName($tableName));
   }

   public function getFieldNamesByTableAlias($dbAlias, $tableAlias)
   {
      return array_keys($this->getFieldsInfoByTableAlias($dbAlias, $tableAlias));
   }

   public function getFieldNamesByTableName($dbAlias, $tableName)
   {
      return array_keys($this->getFieldsInfoByTableName($dbAlias, $tableName));
   }

   public function getFieldInfoByFieldAlias($dbAlias, $tableAlias, $fieldAlias)
   {
      return $this->info['model'][$dbAlias]['tables'][$tableAlias]['fields'][$fieldAlias];
   }

   public function getFieldInfoByFieldName($dbAlias, $tableAlias, $fieldName)
   {
      return $this->getFieldInfoByFieldAlias($dbAlias, $TableAlias, $this->getFieldNameByFieldAlias($fieldName));
   }

   public function getNavigationFieldInfo($dbAlias, $tableAlias, $field)
   {
      return $this->info['model'][$dbAlias]['tables'][$tableAlias]['navigationFields'][$field];
   }

   public function getLogicFieldInfo($dbAlias, $tableAlias, $field)
   {
      return $this->info['model'][$dbAlias]['tables'][$tableAlias]['logicFields'][$field];
   }

   public function getKeyByTableAlias($dbAlias, $tableAlias)
   {
      return $this->info['model'][$dbAlias]['tables'][$tableAlias]['pk'];
   }

   public function getKeyByTableName($dbAlias, $tableName)
   {
      return $this->getKeyByTableAlias($dbAlias, $this->getTableAliasByTableName($tableName));
   }

   public function getKeyInfoByTableAlias($dbAlias, $tableAlias)
   {
      $tb = $this->info['model'][$dbAlias]['tables'][$tableAlias];
      return array_intersect_key($tb['fields'], $tb['pk']);
   }

   public function getKeyInfoByTableName($dbAlias, $tableName)
   {
      return $this->getKeyInfoByTableAlias($dbAlias, $this->getTableAliasByTableName($tableName));
   }

   public function getNavigationFieldInfoForSQL($dbAlias, $tableAlias, $field)
   {
      $nav = $this->getNavigationFieldInfo($dbAlias, $tableAlias, $field);
      $info = array();
      $info['to']['db'] = $this->getDBNameByDBAlias($nav['to']['db']);
      $info['to']['table'] = $this->getTableNameByTableAlias($nav['to']['db'], $nav['to']['table']);
      $info['to']['fields'] = array_intersect_key($this->getFieldsInfoByTableAlias($nav['to']['db'], $nav['to']['table']), $nav['to']['fields']);
      $info['to']['pk'] = $this->getKeyInfoByTableAlias($nav['to']['db'], $nav['to']['table']);
      $inherit = $this->getInheritInfoByTableAlias($nav['to']['db'], $nav['to']['table']);
      if ($inherit)
      {
         $info['to']['inherit']['db'] = $this->getDBNameByDBAlias($inherit[0]);
         $info['to']['inherit']['table'] = $this->getTableNameByTableAlias($inherit[0], $inherit[1]);
         $info['to']['inherit']['fields'] = $this->getKeyInfoByTableAlias($inherit[0], $inherit[1]);
      }
      $info['from']['db'] = $this->getDBNameByDBAlias($nav['from']['db']);
      $info['from']['table'] = $this->getTableNameByTableAlias($nav['from']['db'], $nav['from']['table']);
      $info['from']['fields'] = array_intersect_key($this->getFieldsInfoByTableAlias($nav['from']['db'], $nav['from']['table']), $nav['from']['fields']);
      $info['from']['pk'] = $this->getKeyInfoByTableAlias($nav['from']['db'], $nav['from']['table']);
      return $info;
   }

   public function getInheritInfoByTableAlias($dbAlias, $tableAlias)
   {
      return $this->info['model'][$dbAlias]['tables'][$tableAlias]['inherit'];
   }

   public function getInheritInfoByTableName($dbAlias, $tableName)
   {
      return $this->getInheritInfoByTableAlias($dbAlias, $tableName);
   }

   public function getTableAliasByClassName($class)
   {
      return $this->info['aliases']['classes']['names'][$class];
   }

   public function getClassNameByTableAlias($dbAlias, $tableAlias)
   {
      return $this->info['aliases']['classes']['tables'][$dbAlias][$tableAlias];
   }

   public function getClassParents($class)
   {
      $parent = array();
      $tb = $this->getTableAliasByClassName($class);
      $parent[] = $tb;
      $inherit = $this->getInheritInfoByTableAlias($tb[0], $tb[1]);
      if (is_array($inherit)) return array_merge($parent, $this->getClassParents($this->getClassNameByTableAlias($inherit[0], $inherit[1])));
      return $parent;
   }
}

?>