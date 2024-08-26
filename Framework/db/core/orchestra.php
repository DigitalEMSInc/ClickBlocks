<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core;

class Orchestra
{
   const PROTOCOL_RAW = 0;
   const PROTOCOL_OBJECTS = 1;

   protected $objInfo = null;
   protected $orm = null;
   /**
    * @var DB
    */
   protected $db = null;
   protected $config = null;
   protected $className = null;
   protected $protocol = self::PROTOCOL_RAW;

   public function __construct($className)
   {
      $this->config = Core\Register::getInstance()->config;
      $this->orm = ORM::getInstance();
      $this->objInfo = $this->orm->getORMInfoObject();
      $this->className = $className;
      $this->db = foo(new SQLGenerator())->getDBByClassName($this->className);
   }

   public static function create($className = null)
   {
      return new static($className);
   }

   public function getProtocol()
   {
      return $this->protocal;
   }

   public function setProtocol($protocol)
   {
      $this->protocol = $protocol;
      return $this;
   }

   public function getAll($start = null, $limit = null)
   {
      return $this->getByQuery(null, $start, $limit);
   }

   public function getByQuery($where = null, $start = null, $limit = null, $method = 'rows')
   {
      $sql = foo(new SQLGenerator())->getByQuery($this->className, $where, $start, $limit);
      return $this->getDataByProtocol($sql, array(), $method);
   }

   protected function getDataByProtocol($sql, array $params = array(), $method = 'rows')
   {
      $data = call_user_func_array(array($this->db, $method), array($sql, $params));
      if ($this->protocol == self::PROTOCOL_OBJECTS)
      {
         if ($method == 'rows')
         {
            foreach ($data as $k => $row)
              $data[$k] = foo(new $this->className())->assign($row);
         }
         else if ($method == 'row')
         {
            $data = foo(new $this->className())->assign($data);
         }
      }
      return $data;
   }

   public function retrieveEntries(array $data)
   {
      $w = $p = array();
      if (is_array($data) && count($data))
      {
         $keys = array_keys($data);
         foreach($keys as $k)
         {
            $w[] = $k.' = :'.$k;
            $p[$k] = $data[$k];
         }
         if (count($w)) $w = ' WHERE ' . implode(' AND ', $w);
         else $w = '';
         $sql = 'SELECT * FROM '.foo(new $this->className())->getTableAlias();
         $result = $this->db->rows($sql . $w, $p);
         foreach($result as $value)
         {
            $tableRows[] = foo(new $this->className())->assign($value);
         }
         if(count($tableRows))
         {
            return $tableRows;
         }
      }
   }

    /**
     * @return DB
     */
    public static function getDB()
    {
        return ORM::getInstance()->getDB('db0');
    }
}