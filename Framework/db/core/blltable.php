<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Exceptions;

class BLLTable implements IBLLTable, \Serializable
{
   const BUILD_BY_DB = 0;
   const BUILD_BY_CACHE = 1;
   const BUILD_BY_NEW = 2;

   private $buildBy = self::BUILD_BY_NEW;

   protected $dal = array();
   protected $config = null;
   protected $fields = array();
   protected $navigationFields = array();
   protected $navigators = array();
   public $expire = null;
   public $navigator = null;

   protected static $services;

   public function __construct()
   {
      $this->init();
      $this->setExpire($this->config->orm['cacheBLLObjectExpire']);
   }

   public static function create()
   {
      return new static();
   }

   protected function init()
   {
      $this->config = Core\Register::getInstance()->config;
      $info = ORM::getInstance()->getORMInfo();
      $class = self::getClassName();
      $this->fields = array_merge($info['classes'][$class]['table']['fields'], $info['classes'][$class]['table']['logicFields']);
      $this->navigationFields = $info['classes'][$class]['table']['navigationFields'];
   }

   public function setExpire($value)
   {
      $this->expire = $value;
   }

   public function getExpire()
   {
      return $this->expire;
   }

   public function addDAL(IDALTable $tb, $className)
   {
      $this->dal[$className] = $tb;
      return $this;
   }

   public function getDAL($className = null)
   {
      return $this->dal[($className) ? $className : get_class($this)];
   }

   public function getDALs()
   {
      return $this->dal;
   }

   public function getField($field)
   {
      foreach ($this->dal as $dal) if (is_array($dal->getField($field))) return $dal->getField($field);
   }

   public function isFromDB()
   {
      return ($this->buildBy == self::BUILD_BY_DB);
   }

   public function isFromCache()
   {
      return ($this->buildBy == self::BUILD_BY_CACHE);
   }

   public function isFromNew()
   {
      return ($this->buildBy == self::BUILD_BY_NEW);
   }

   public function setValues($fields, $isRawData = false)
   {
      foreach ($this->dal as $dal) $dal->setValues($fields, $isRawData);
      $this->checkNavigator();
      return $this;
   }

   public function getValues($isRawData = false)
   {
      $values = $aliases = array();
      foreach ($this->dal as $dal)
      {
         $values = array_merge($values, $dal->getValues($isRawData));
         $aliases = array_merge($aliases, $dal->getAliases());
      }
      if (!$isRawData) return array_intersect_key($values, $this->fields);
      return array_intersect_key($values, $aliases);
   }

   public function assign(array $data = null)
   {
      $n = 0;
      foreach ($this->dal as $dal) $dal->assign($data);
      $this->initNavigators();
      $this->buildBy = self::BUILD_BY_DB;
      return $this;
   }

   public function __set($field, $value)
   {
      if (isset($this->fields[$field]))
      {
         $dal = $this->dal[$this->fields[$field]];
         $navObject = $this->navigator['navObject'] ?? null;
         if ($value !== $dal->{$field} && $navObject instanceof NavigationProperty)
         {
            if (!$this->navigator['navObject']->isUpdateable())
              throw new \Exception(err_msg('ERR_NAV_4', array($this->navigator['navObject']->getField())));
         }
         $dal->{$field} = $value;
         $this->checkNavigator();
         return;
      }
      else if (isset($this->navigationFields[$field]))
      {
         $info = $this->dal[$this->navigationFields[$field]]->getNavigationField($field);
         if ($info['output'] == 'object' && !is_a($value, $info['to']['bll'])) throw new \Exception(err_msg('ERR_BLL_3', array($field)));
         if ($info['output'] == 'raw' && !is_array($value)) throw new \Exception(err_msg('ERR_BLL_4', array($field)));
         if (!isset($this->navigators[$field])) $this->initNavigator($field);
         if ($info['multiplicity']) $this->navigators[$field][] = $value;
         else $this->navigators[$field][0] = $value;
         return;
      }
      throw new \Exception(err_msg('ERR_BLL_1', array($field, get_class($this))));
   }

   public function __get($field)
   {
      if (isset($this->fields[$field])) return $this->dal[$this->fields[$field]]->{$field};
      else if (isset($this->navigationFields[$field]))
      {
         if (!isset($this->navigators[$field])) $this->initNavigator($field);
         return $this->navigators[$field];
      }
      throw new \Exception(err_msg('ERR_BLL_1', array($field, get_class($this))));
   }

   public function __isset($field)
   {
      return (isset($this->fields[$field]) || isset($this->navigationFields[$field]));
   }

   public function __call($method, $params)
   {
      if (isset($this->navigationFields[$method])) return call_user_func_array(array($this->navigators[$method], 'limit'), $params);
      foreach ($this->dal as $dal)
      {
         try
         {
            return call_user_func_array(array($dal, $method), $params);
         }
         catch(\Exception $e){}
      }
      throw new \Exception(err_msg('ERR_BLL_2', array($method, get_class($this))));
   }

   public function serialize()
   {
      $data = get_object_vars($this);
      unset($data['fields']);
      unset($data['navigationFields']);
      unset($data['config']);
      unset($data['navigator']);
      return serialize($data);
   }

   public function unserialize($data)
   {
      $data = unserialize($data);
      foreach ($data as $k => $v) $this->{$k} = $v;
      $this->init();
      foreach ($this->navigationFields as $field => $class)
      {
         if (!isset($this->navigators[$field]))
         {
            if ($this->isFromCache()) $this->initNavigator($field);
         }
         else $this->navigators[$field]->initialize($this);
      }
   }

   public function isCalledFromService()
   {
      $trace = debug_backtrace();
      return ($trace[2]['object'] instanceof Service && $trace[2]['object']->getObjectName() == '\\' . get_class($this));
   }

   public function save()
   {
      if (!$this->isCalledFromService()) {
         self::getService()->save($this);
         return true;
      }
      $res = 0;
      foreach ($this->dal as $dal)
      {
         if (!empty($ID)) $dal->setKeyValue($ID);
         $res += $dal->save();
         $ID = $dal->getKeyValue(false);
      }
      $this->saveNavigators();
      return $res;
   }

   public function insert()
   {
      if (!$this->isCalledFromService())  {
         return self::getService()->insert($this);
      }
      $res = 0;
      foreach ($this->dal as $dal)
      {
         if (isset($ID)) $dal->setKeyValue($ID);
         $res += $dal->insert();
         $ID = $dal->getKeyValue(false);
      }
      return $res;
   }

   public function delete()
   {
      if (!$this->isCalledFromService()) {
         self::getService()->delete($this);
         return true;
      }
      $res = 0;
      foreach (array_reverse($this->dal) as $dal) $res += $dal->delete();
      return $res;
   }

   public function update()
   {
      if (!$this->isCalledFromService()) {
         self::getService()->update($this);
         return true;
      }
      $res = 0;
      foreach ($this->dal as $dal) $res += $dal->update();
      return $res;
   }

   public function replace()
   {
      if (!$this->isCalledFromService()) {
         self::getService()->replace($this);
         return true;
      }
      foreach ($this->dal as $dal) $res += $dal->replace();
      return $res;
   }

   protected function saveNavigators()
   {
      foreach ($this->navigators as $key => $navigator) $navigator->save();
   }

   protected function initNavigators()
   {
      foreach ($this->navigationFields as $field => $class) $this->initNavigator($field);
   }

   protected function initNavigator($field)
   {
      $data = $this->dal[$this->navigationFields[$field]]->getNavigationField($field);
      if (!isset($this->navigators[$field])) $this->{$data['init']['name']}();
   }

   protected function checkNavigator()
   {
      if (!is_array($this->navigator)) return;
      $this->navigator['navObject']->offsetSet($this->navigator['offset'], $this, true);
      $this->navigator = null;
   }

   public static function getClassName()
   {
      $class = get_called_class();
      $k = strrpos($class, '\\');
      if ($k !== false)
         $class = substr($class, $k + 1);
      return $class;
   }

   public static function getServiceClassName()
   {
      return '\ClickBlocks\DB\Service'.self::getClassName();
   }

   /**
    * @return Service
    */
   public static function getService()
   {
      $service = &self::$services[self::getServiceClassName()];
      if (!$service) {
         $className = self::getServiceClassName();
         $service = new $className;
      }
      return $service;
   }

   public function assignByID($ID)
   {
       return self::getService()->getByID($ID);
   }
   
   public function __serialize()
   {
      return $this->serialize();
   }
   public function __unserialize($data)
   {
      $this->unserialize($data);
   }
}

?>
