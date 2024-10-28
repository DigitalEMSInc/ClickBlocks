<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Exceptions;

class DALTable implements IDALTable, \Serializable
{
   private $dbAlias = null;
   private $logicTableName = null;

   protected $db = null;
   protected $info = array();
   protected $config = null;

   protected $isInstantiated = false;
   protected $isUpdated = false;
   protected $isSaved = false;
   protected $isDeleted = false;

   public function __construct($dbAlias, $logicTableName)
   {
      $this->dbAlias = $dbAlias;
      $this->logicTableName = $logicTableName;
      $this->init();
   }

   protected function init()
   {
      $this->config = Core\Register::getInstance()->config;
      $orm = ORM::getInstance();
      $this->db = $orm->getDB($this->dbAlias);
      $info = $orm->getORMInfo();
      $this->info = $info['model'][$this->dbAlias]['tables'][$this->logicTableName];
   }

   public function getDB()
   {
      return $this->db;
   }

   public function getDBAlias()
   {
      return $this->dbAlias;
   }

   public function getDBName()
   {
      return ORM::getInstance()->getORMInfoObject()->getDBNameByDBAlias($this->dbAlias);
   }

   public function getTableAlias()
   {
      return $this->logicTableName;
   }

   public function getTable()
   {
      return $this->info['name'];
   }

   public function getField($field)
   {
      return $this->info['fields'][$field];
   }

   public function getFields()
   {
      return $this->info['fields'];
   }

   public function getLogicField($field)
   {
      return $this->info['logicFields'][$field];
   }

   public function getLogicFields()
   {
      return $this->info['logicFields'];
   }

   public function getNavigationField($field)
   {
      return $this->info['navigationFields'][$field];
   }

   public function getNavigationFields()
   {
      return $this->info['navigationFields'];
   }

   public function getFieldAlias($field)
   {
      return $this->info['aliases'][$field];
   }

   public function getFieldName($alias)
   {
      return $this->info['fields'][$field]['name'];
   }

   public function getAliases()
   {
      return $this->info['aliases'];
   }

   public function getKeyLength()
   {
      return count($this->info['pk']);
   }

   public function getKey($isRawData = false)
   {
      $pk = $this->info['pk'];
      if ($isRawData) foreach ($pk as $field) $pk[$field] = $this->info['fields'][$field]['name'];
      return ((count($pk) == 1) ? end($pk) : array_values($pk));
   }

   public function getKeyValue($isRawData = false)
   {
      $values = array();
      if ($isRawData) foreach ($this->info['pk'] as $field) $values[$this->info['fields'][$field]['name']] = (string)$this->info['fields'][$field]['value'];
      else foreach ($this->info['pk'] as $field) $values[$field] = (string)$this->info['fields'][$field]['value'];
      return ((count($values) == 1) ? (string)end($values) : $values);
   }

   public function setKeyValue($value)
   {
      if (!is_array($value)) $values = array($value);
      else $values = array_values($value);
      $n = 0; foreach ($this->info['pk'] as $field) $this->{$field} = $values[$n++];
   }

   public function isKeyFilled()
   {
      $flag = true;
      foreach ($this->info['pk'] as $field) $flag &= (
          isset($this->info['fields'][$field]['value']) && strlen($this->info['fields'][$field]['value']) != 0
      );
      return (bool)$flag;
   }

   public function __set($field, $value)
   {
      if (!isset($this->info['fields'][$field]))
      {
         if (isset($this->info['logicFields'][$field]))
         {
            if (isset($this->info['logicFields'][$field]['set']))
            {
               call_user_func_array(array($this, $this->info['logicFields'][$field]['set']['name']), array($value));
               return;
            }
         }
         throw new Exceptions\NotExistingPropertyException(err_msg('ERR_DAL_4', array($field, $this->getTable())));
      }
      if ($this->isInstantiated)
      {
         if ($this->info['fields'][$field]['autoincrement']) throw new \LogicException(err_msg('ERR_DAL_2', array($field)));
      }
      if (is_object($value)) throw new Exceptions\IncorrectValueException(err_msg('ERR_DAL_1', array($field)));
      if ($value !== ($this->info['fields'][$field]['value'] ?? null))
      {
         $this->isUpdated = true;
         $this->isSaved = false;
      }
      if (isset($this->info['fields'][$field]['setter'])) $this->info['fields'][$field]['value'] = call_user_func_array(array($this, $this->info['fields'][$field]['setter']['name']), array($value));
      else $this->info['fields'][$field]['value'] = $value;
   }

   public function __get($field)
   {
      if (!isset($this->info['fields'][$field]))
      {
         if (isset($this->info['logicFields'][$field]))
         {
            if (isset($this->info['logicFields'][$field]['get']))
            {
               return call_user_func_array(array($this, $this->info['logicFields'][$field]['get']['name']), array());
            }
         }
         throw new \Exception(err_msg('ERR_DAL_4', array($field, $this->getTable())));
      }
      if (isset($this->info['fields'][$field]['getter'])) return call_user_func_array(array($this, $this->info['fields'][$field]['getter']['name']), array($this->info['fields'][$field]['value']));
      return $this->info['fields'][$field]['value'] ?? null;
   }

   public function __isset($field)
   {
      return isset($this->info['fields'][$field]) || isset($this->info['logicFields'][$field]);
   }

   public function setValues(array $fields, $isRawData = false)
   {
      if ($isRawData)
      {
         foreach ($fields as $k => $v)
         {
            if (isset($this->info['aliases'][$k]))
              $this->{$this->info['aliases'][$k]} = $v;
         }
      }
      else
      {
         foreach ($fields as $k => $v)
         {
            if (isset($this->info['fields'][$k]))
              $this->{$k} = $v;
         }
      }
      return $this;
   }

   public function getValues($isRawData = false)
   {
      $values = array();
      if ($isRawData) foreach ($this->info['fields'] as $k => $v) $values[$v['name']] = $this->{$k};
      else foreach ($this->info['fields'] as $k => $v) $values[$k] = $this->{$k};
      return $values;
   }

   public function assign(array $data = null)
   {
      if (is_array($data) && count($data))
      {
         foreach ($data as $k => $v)
         {
            $field = $this->info['aliases'][$k] ?? null;
            if (!$field) continue;
            $fieldinfo = &$this->info['fields'][$field];
            $fieldinfo['value'] = $v;
            $fieldinfo['hasChanged'] = false;
            if (isset($this->info['fields'][$field]['get'])) $this->info['fields'][$field]['value'] = call_user_func_array(array($this, $this->info['fields'][$field]['get']['name']), array($this->info['fields'][$field]['value']));
         }
         $this->isInstantiated = true;
      }
      else
      {
         foreach ($this->info['fields'] as $field => $info)
         {
            $this->info['fields'][$field]['value'] = $info['default'];
            if (isset($this->info['fields'][$field]['get'])) $this->info['fields'][$field]['value'] = call_user_func_array(array($this, $this->info['fields'][$field]['get']['name']), array($this->info['fields'][$field]['value']));
         }
         $this->isInstantiated = false;
      }
   }

   public function getAll($where = null, $orderBy = null, $limit = null)
   {
      if ($w = $this->db->sql->joinWhere($where, 'AND')) $w = ' WHERE ' . $w;
      switch ($this->db->getEngine())
      {
         case 'mysql':
           $sql = 'SELECT * FROM ' . $this->db->wrap($this->getTable()) . $w . (($orderBy) ? ' ORDER BY ' . $orderBy : '') . (($limit) ? ' LIMIT ' . $limit : '');
           break;
         case 'mssql':
           $sql = 'SELECT ' . (($limit) ? ' TOP ' . $limit : '') . ' * FROM ' . $this->db->wrap($this->getTable()) . $w . (($orderBy) ? ' ORDER BY ' . $orderBy : '');
           break;
      }
      return $this->db->rows($sql, is_array($where) ? $where : array());
   }

   public function deleteAll()
   {
      return $this->db->execute('TRUNCATE TABLE ' . $this->db->sql->wrap($this->getTable()));
   }

   public function isNumericField($field)
   {
      $type = SQLBuilder::getPHPDataType($this->info['fields'][$field]['type']);
      return ($type == 'int' || $type == 'float');
   }

   public function isTextField($field)
   {
      return (SQLBuilder::getPHPDataType($this->info['fields'][$field]['type']) == 'string');
   }

   public function isDateOrTimeField($field)
   {
      return (SQLBuilder::getPHPDataType($this->info['fields'][$field]['type']) == 'dt');
   }

   public function getLength($field)
   {
      return (int)$this->info['fields'][$field]['length'];
   }

   public function getPrecision($field)
   {
      return (int)$this->info['fields'][$field]['precision'];
   }

   public function save()
   {
      $res = null;
      if (!$this->isInstantiated) $res = $this->insert();
      else if ($this->isUpdated) $res = $this->update();
      $this->isSaved = true;
      return (int)$res;
   }

   public function insert()
   {
      if (count($this->info['pk']) == 1 && $this->info['fields'][end($this->info['pk'])]['autoincrement'])
      {
         if ($this->isKeyFilled()) throw new \LogicException(err_msg('ERR_DAL_5', array($this->getTable())));
         $this->info['fields'][end($this->info['pk'])]['value'] = $this->db->insert($this->getTable(), $this->getData());
      }
      else
      {
         if (!$this->isKeyFilled()) throw new \LogicException(err_msg('ERR_DAL_6', array($this->getTable())));
         $this->db->insert($this->getTable(), $this->getData());
      }
      $res = $this->db->affectedRows;
      $this->isUpdated = false;
      $this->isInstantiated = true;
      return (int)$res;
   }

   public function replace()
   {
      //if (!$this->isKeyFilled()) throw new \LogicException(err_msg('ERR_DAL_6', array($this->getTable())));
      return (int)$this->db->replace($this->getTable(), $this->getData(true));
   }

   public function update()
   {
      $res = 0;
      if (!$this->isKeyFilled()) throw new \LogicException(err_msg('ERR_DAL_6', array($this->getTable())));
      if ($this->isUpdated) $res = $this->db->update($this->getTable(), $this->getData(), $this->getKeyData());
      $this->isUpdated = false;
      return (int)$res;
   }

   public function delete()
   {
      if (!$this->isKeyFilled()) throw new \LogicException(err_msg('ERR_DAL_6', array($this->getTable())));
      $this->isDeleted = true;
      return (int)$this->db->delete($this->getTable(), $this->getKeyData());
   }

   public function __toString()
   {
      return 'Table: ' . $this->getTable();
   }

   public function serialize()
   {
      $data = get_object_vars($this);
      $data['info'] = $this->getValues(false);
      unset($data['db']);
      unset($data['config']);
      return serialize($data);
   }

   public function unserialize($data)
   {
      $data = unserialize($data);
      $values = $data['info'];
      foreach ($data as $k => $v) $this->{$k} = $v;
      $this->init();
      $flag = $this->isInstantiated;
      $this->isInstantiated = false;
      $this->setValues($values, false);
      $this->isInstantiated = $flag;
   }
   
   public function __serialize()
   {
      return $this->serialize();
   }
   
   public function __unserialize($data)
   {
      return $this->unserialize($data);
   }

   protected function getData($isReplace = false)
   {
      $p = array();
      foreach ($this->info['fields'] as $alias => $value)
      {
         $field = $value['name'];
         if (!$isReplace && $value['autoincrement']) continue;
         if (isset($value['set'])) $value['value'] = call_user_func_array(array($this, $value['set']['name']), array($value['value']));
         $len = $this->getLength($alias) + ($this->getPrecision($alias) ? $this->getPrecision($alias)+1 : 0);
         if (($this->isNumericField($alias) || $this->isDateOrTimeField($alias)) && !strlen($value['value'] ?? ''))
         {
            if (strlen($value['default'])) $p[$field] = $value['default'];
            else $p[$field] = 'NULL';
         }
         else
         {
            $p[$field] = $value['value'] ?? null;
            if ($len > 0 && $len < strlen($value['value'] ?? '')) throw new \Exception(err_msg('ERR_DAL_7', array($alias)));
         }
      }
      return $p;
   }

   protected function getKeyData()
   {
      $values = array();
      foreach ($this->info['pk'] as $field) $values[$this->info['fields'][$field]['name']] = $this->info['fields'][$field]['value'];
      return $values;
   }
}

?>
