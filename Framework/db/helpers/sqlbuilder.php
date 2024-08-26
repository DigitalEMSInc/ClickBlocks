<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core;

class SQLBuilder
{
   private $dsn = array();

   public function __construct(array $dsn)
   {
      $this->setDSN($dsn);
   }

   public function setDSN(array $dsn)
   {
      $this->dsn = $dsn;
   }

   public function wrap($str)
   {
      switch ($this->dsn['engine'])
      {
         case 'mysql':
           return '`' . str_replace('`', '``', $str) . '`';
         case 'mssql':
           return '[' . strtr($str, array('[' => '[[', ']' => ']]')) . ']';
      }
   }

   public function insert($table, array &$data)
   {
      if ($this->dsn['engine'] == 'mssql')
      {
         $keys = $values = $tmp = array();
         foreach ($data as $k => $v)
         {
            if (!$this->isSQL($v))
            {
               $values[] = (is_numeric($v) || $v == '') ? '?' : 'N?';
               $tmp[] = $v;
            }
            else
            {
               $values[] = $v;
               unset($data[$k]);
            }
            $keys[] = $this->wrap($k);
         }
         $data = $tmp;
      }
      else
      {
         $keys = $values = array();
         foreach ($data as $k => $v)
         {
            if (!$this->isSQL($v)) $values[] = ':' . $k;
            else
            {
               $values[] = $v;
               unset($data[$k]);
            }
            $keys[] = $this->wrap($k);
         }
      }
      return 'INSERT INTO ' . $this->wrap($table) . ' (' . join(', ', $keys) . ') VALUES (' . join(', ', $values) . ')';
   }

   public function update($table, array &$data, &$where = null)
   {
      if ($w = $this->joinWhere($where, 'AND')) $w = ' WHERE ' . $w;
      return 'UPDATE ' . $this->wrap($table) . ' SET ' . $this->joinSet($data) . $w;
   }

   public function replace($table, array &$data, &$where = null)
   {
      if ($w = $this->joinWhere($where, 'AND')) $w = ' WHERE ' . $w;
      return 'REPLACE ' . $this->wrap($table) . ' SET ' . $this->joinSet($data) . $w;
   }

   public function delete($table, &$where = null)
   {
      if ($w = $this->joinWhere($where, 'AND')) $w = ' WHERE ' . $w;
      return 'DELETE FROM ' . $this->wrap($table) . $w;
   }

   public function getSQL($type, $param = null)
   {
      switch ($type)
      {
         case 'ShowDataBases':
           switch ($this->dsn['engine'])
           {
              case 'mysql':
                return 'SHOW DATABASES';
              case 'mssql':
                return 'EXEC sp_databases';
           }
           break;
         case 'ShowTables':
           switch ($this->dsn['engine'])
           {
              case 'mysql':
                return 'SHOW TABLES FROM ' . $this->wrap($this->dsn['dbname']);
              case 'mssql':
                return 'USE ' . $this->wrap($this->dsn['dbname']) . '; SELECT TABLE_NAME FROM INFORMATION_SCHEMA.Tables;';
           }
           break;
        case 'ShowFields':
           switch ($this->dsn['engine'])
           {
              case 'mysql':
                return 'SHOW FULL COLUMNS FROM ' . $this->wrap($param);
              case 'mssql':
                return "SELECT C.COLUMN_NAME AS Field, C.COLUMN_DEFAULT AS DefaultValue, C.DATA_TYPE AS Type, C.IS_NULLABLE AS isNullable, C.CHARACTER_MAXIMUM_LENGTH AS MaxLength, columnproperty(object_id(T.TABLE_NAME), C.COLUMN_NAME, 'IsIdentity') AS PK FROM INFORMATION_SCHEMA.Tables AS T
                        INNER JOIN INFORMATION_SCHEMA.Columns AS C ON T.TABLE_NAME = C.TABLE_NAME
                        WHERE T.TABLE_NAME = '" . str_replace("'", "''", $param) . "' ORDER BY C.ORDINAL_POSITION";
           }
           break;
         case 'ShowCreateOperator':
           switch ($this->dsn['engine'])
           {
              case 'mysql':
                return 'SHOW CREATE TABLE ' . $this->wrap($param);
              case 'mssql':
                return '';
           }
         case 'CreateTable':
           switch ($this->dsn['engine'])
           {
              case 'mysql':
                $ff = $pk = array();
                foreach ($param[1] as $k => $field)
                {
                   $ff[] = $this->getFieldDefinition($field);
                   if (isset($param[2][$k])) $pk[] = $this->wrap($field['name']);
                }
                if (count($pk)) $ff[] = ' PRIMARY KEY (' . implode(', ', $pk) . ')';
                return 'CREATE TABLE ' . $this->wrap($param[0]) . '(' . implode(', ', $ff) . ')ENGINE=' . (($param[3]) ? $param[3] : 'InnoDB') . ' CHARACTER SET \'' . (($param[4]) ? $param[4] : 'utf8') . '\'';
              case 'mssql':
                return '';
           }
           break;
         case 'AddField':
           switch ($this->dsn['engine'])
           {
              case 'mysql':
                return 'ALTER TABLE ' . $this->wrap($param[0]) . ' ADD COLUMN ' . $this->getFieldDefinition($param[1]);
              case 'mssql':
                return '';
           }
           break;
         case 'DeleteField':
           switch ($this->dsn['engine'])
           {
              case 'mysql':
                return 'ALTER TABLE ' . $this->wrap($param[0]) . ' DROP COLUMN ' . $this->wrap($param[1]);
              case 'mssql':
                return '';
           }
           break;
         case 'ChangeField':
           switch ($this->dsn['engine'])
           {
              case 'mysql':
                return 'ALTER TABLE ' . $this->wrap($param[0]) . ' CHANGE COLUMN ' . $this->wrap($param[1]) . ' ' . $this->getFieldDefinition($param[2]);
              case 'mssql':
                return '';
           }
           break;
      }
   }

   public function joinSet(array &$data)
   {
      if ($this->dsn['engine'] == 'mssql')
      {
         $p = $tmp = array(); $rows = $data;
         foreach ($rows as $k => $v)
         {
            if (!$this->isSQL($v))
            {
               $p[] = $this->wrap($k) . ((is_numeric($v) || $v == '') ? ' = ?' : ' = N?');
               $tmp[] = $v;
            }
            else
            {
               $p[] = $this->wrap($k) . ' = ' . $v;
               unset($data[$k]);
            }
         }
         $data = $tmp;
      }
      else
      {
         $p = array(); $rows = $data;
         foreach ($rows as $k => $v)
         {
            if (!$this->isSQL($v)) $p[] = $this->wrap($k) . ' = :' . $k;
            else
            {
               $p[] = $this->wrap($k) . ' = ' . $v;
               unset($data[$k]);
            }
         }
      }
      return join(', ', $p);
   }

   public function joinWhere(&$where, $del)
   {
      if ($this->dsn['engine'] == 'mssql')
      {
         if (is_array($where))
         {
            $p = $tmp = array(); $rows = $where;
            foreach ($rows as $k => $v)
            {
               if (is_array($v))
               {
                  $p[] = '(' . $this->joinWhere($v, ($del == 'AND') ? 'OR' : 'AND') . ')';
                  $tmp[] = $v;
               }
               else if (is_numeric($k))
               {
                  $p[] = $v;
                  $tmp[] = $v;
               }
               else if (!$this->isSQL($v))
               {
                  $tmp[] = $v;
                  $p[] = $this->wrap($k) . ((is_numeric($v) || $v == '') ? ' = ?' : ' = N?');
               }
               else
               {
                  $p[] = $this->wrap($k) . ' = ' . $v;
                  unset($where[$k]);
               }
            }
            $w = join(' ' . $del . ' ', $p);
            $where = $tmp;
         }
         else if (is_string($where)) $w = $where;
      }
      else
      {
         if (is_array($where))
         {
            $p = array(); $rows = $where;
            foreach ($rows as $k => $v)
            {
               if (is_array($v)) $p[] = '(' . $this->joinWhere($v, ($del == 'AND') ? 'OR' : 'AND') . ')';
               else if (is_numeric($k)) $p[] = $v;
               else if (!$this->isSQL($v)) $p[] = $this->wrap($k) . ' = :' . $k;
               else
               {
                  $p[] = $this->wrap($k) . ' = ' . $v;
                  unset($where[$k]);
               }
            }
            $w = join(' ' . $del . ' ', $p);
         }
         else if (is_string($where)) $w = $where;
      }
      return $w;
   }

   public function isSQL($str)
   {
      return in_array(strtoupper($str ?? ''), array('NULL', 'NOW()', 'CURRENT_TIMESTAMP'));
   }

   public static function getPHPDataType($type)
   {
      switch ($type)
      {
         case 'varchar':
         case 'char':
         case 'text':
         case 'tinytext':
         case 'mediumtext':
         case 'longtext':
         case 'tinyblob':
         case 'blob':
         case 'mediumblob':
         case 'longblob':
         case 'enum':
         case 'set':
         case 'binary':
         case 'varbinary':
           return 'string';
         case 'int':
         case 'tinyint':
         case 'smallint':
         case 'mediumint':
         case 'bigint':
         case 'year':
         case 'bit':
           return 'int';
         case 'decimal':
         case 'float':
         case 'double':
           return 'float';
         case 'time':
         case 'datetime':
         case 'date':
         case 'timestamp':
           return 'dt';
      }
   }

   private function getFieldDefinition($field)
   {
      $f = $this->wrap($field['name']);
      $f .= ' ' . strtoupper($field['type']);
      if (strlen($field['length'])) $f .= '(' . $field['length'] . (($field['precision']) ? ', ' . $field['precision'] : '') . ')';
      if ($field['unsigned']) $f .= ' UNSIGNED';
      if (!$field['null']) $f .= ' NOT NULL';
      if ($field['autoincrement']) $f .= ' AUTO_INCREMENT';
      if (strlen($field['default'])) $f .= ' DEFAULT \'' . addslashes($field['default']) . '\'';
      else if ($field['null']) $f .= ' DEFAULT NULL';
      return $f;
   }
}

?>