<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core;

class Service implements IService
{
   protected $config = null;
   protected $cache = null;
   protected $objectName = null;
   protected $mode = null;

   public static $onBeforeSave = null;
   public static $onBeforeInsert = null;
   public static $onBeforeUpdate = null;
   public static $onBeforeReplace = null;
   public static $onBeforeDelete = null;
   public static $onAfterSave = null;
   public static $onAfterInsert = null;
   public static $onAfterUpdate = null;
   public static $onAfterReplace = null;
   public static $onAfterDelete = null;

   public static function cleanCache()
   {
      $cache = Core\Register::getInstance()->cache;
      $key = Core\Register::getInstance()->config->siteUniqueID . '_orm_bll_objects';
      $objs = $cache->get($key);
      if (is_array($objs))
      {
         foreach ($objs as $k => $data)
         {
            if (is_array($data)) foreach ($data as $cacheID => $pk) $cache->delete($cacheID);
         }
      }
      $cache->delete($key);
   }

   public function __construct($objectName)
   {
      $this->config = Core\Register::getInstance()->config;
      $this->cache = Core\Register::getInstance()->cache;
      $this->objectName = $objectName;
   }

   public static function create($objectName = null)
   {
      return new static($objectName);
   }

   public function getObjectName()
   {
      return $this->objectName;
   }

   public function getOrchestra($protocol = Orchestra::PROTOCOL_RAW)
   {
      $info = ORM::getInstance()->getORMInfo();
      $namespace = (($info['namespace'] != '\\') ? $info['namespace'] : '') . '\\';
      $class = substr($this->objectName, strlen($namespace) + 1);
      $class = $namespace . $info['classes'][$class]['orchestra'];
      return foo(new $class())->setProtocol($protocol);
   }

   public function __get($param)
   {
      if ($param == 'db' || $param == 'cache')
      {
         $this->mode = $param;
         return $this;
      }
   }

   public function getByID($pk = null, $expire = null)
   {
      if ($this->useCache() && $this->cacheExists($pk)) return $this->cacheGet($pk);
      $tb = new $this->objectName();
      if ((int)$expire) $tb->expire = (int)$expire;
      $tb->assign($this->getRow($tb, $pk, false));
      if ($this->useCache()) $this->cacheSet($tb, $pk);
      $this->mode = null;
      return $tb;
   }

   public function updateByID($pk, array $values)
   {
      $key = $pk;
      $tb = new $this->objectName();
      $dal = $tb->getDAL();
      if (!is_array($pk)) $pk = array($dal->getKey(false) => $pk);
      if ($this->mode != 'cache')
      {
         $tpk = $pk;
         $tvalues = $values;
         $sql = foo(new SQLGenerator())->updateByID($this->objectName, $tpk, $tvalues, false);
         $dal->getDB()->execute($sql, array_merge($tvalues, $tpk));
      }
      if ($this->useCache())
      {
         if ($this->cacheExists($key)) $tb = $this->cacheGet($key);
         $tb->setValues($pk, false)->setValues($values, false);
         $this->cacheSet($tb);
      }
      $this->mode = null;
      return $this;
   }

   public function deleteByID($pk)
   {
      $tb = new $this->objectName();
      $dal = $tb->getDAL();
      if (!is_array($pk)) $pk = array($dal->getKey($isRawData) => $pk);
      if ($this->mode != 'cache')
      {
         $tpk = $pk;
         $sql = foo(new SQLGenerator())->deleteByID($this->objectName, $tpk, false);
         $dal->getDB()->execute($sql, $tpk);
      }
      if ($this->useCache())
      {
         $tb->setValues($pk, false);
         $this->cacheDelete($tb);
      }
      $this->mode = null;
      return $this;
   }

   public function save($tb, $expire = null)
   {
      $this->checkOwn($tb);
      if (self::$onBeforeSave) foo(new Core\Delegate(self::$onBeforeSave))->call(array($tb));
      if ($this->mode != 'cache' || !$tb->getDAL()->isKeyFilled()) $tb->save();
      if ($this->useCache())
      {
         if ((int)$expire) $tb->expire = (int)$expire;
         $this->cacheSet($tb);
      }
      else if (!is_null($expire)) throw new \Exception(err_msg('ERR_SVC_2', array('save')));
      $this->mode = null;
      if (self::$onAfterSave) foo(new Core\Delegate(self::$onAfterSave))->call(array($tb));
      return $this;
   }

   public function insert($tb, $expire = null)
   {
      $this->checkOwn($tb);
      if (self::$onBeforeInsert) foo(new Core\Delegate(self::$onBeforeInsert))->call(array($tb));
      $tb->insert();
      if ($this->useCache())
      {
         if ((int)$expire) $tb->expire = (int)$expire;
         $this->cacheSet($tb);
      }
      else if (!is_null($expire)) throw new \Exception(err_msg('ERR_SVC_2', array('save')));
      $this->mode = null;
      if (self::$onAfterInsert) foo(new Core\Delegate(self::$onAfterInsert))->call(array($tb));
      return $this;
   }

   public function update($tb)
   {
      $this->checkOwn($tb);
      if (self::$onBeforeUpdate) foo(new Core\Delegate(self::$onBeforeUpdate))->call(array($tb));
      if ($this->mode != 'cache') $tb->update();
      if ($this->useCache()) $this->cacheSet($tb);
      $this->mode = null;
      if (self::$onAfterUpdate) foo(new Core\Delegate(self::$onAfterUpdate))->call(array($tb));
      return $this;
   }

   public function replace($tb)
   {
      $this->checkOwn($tb);
      if (self::$onBeforeReplace) foo(new Core\Delegate(self::$onBeforeReplace))->call(array($tb));
      if ($this->mode != 'cache') $tb->replace();
      if ($this->useCache()) $this->cacheSet($tb);
      $this->mode = null;
      if (self::$onAfterReplace) foo(new Core\Delegate(self::$onAfterReplace))->call(array($tb));
      return $this;
   }

   public function delete(&$tb)
   {
      $this->checkOwn($tb);
      if (self::$onBeforeDelete) foo(new Core\Delegate(self::$onBeforeDelete))->call(array($tb));
      if ($this->mode != 'cache') $tb->delete();
      if ($this->useCache()) $this->cacheDelete($tb);
      $this->mode = null;
      if (self::$onAfterDelete) foo(new Core\Delegate(self::$onAfterDelete))->call(array($tb));
      $tb = null;
      return $this;
   }

   protected function cacheExists($pk)
   {
      return !$this->cache->isExpired($this->getCacheKey($pk));
   }

   protected function cacheGet($pk)
   {
      $str = $this->cache->get($this->getCacheKey($pk));
      $str = preg_replace('/buildBy";i:[\d]+/', 'buildBy";i:' . BLLTable::BUILD_BY_CACHE, $str);
      $str = str_replace('isInstantiated";b:0', 'isInstantiated";b:1', $str);
      $str = str_replace('isUpdated";b:1', 'isUpdated";b:0', $str);
      $str = str_replace('isSaved";b:1', 'isSaved";b:0', $str);
      $str = str_replace('isDeleted";b:1', 'isDeleted";b:0', $str);
      return unserialize($str);
   }

   protected function cacheSet($tb)
   {
      if (!$tb->getDAL()->isKeyFilled()) return;
      $pk = $tb->getDAL()->getKeyValue();
      $this->cache->set($this->getCacheKey($pk), serialize($tb), $tb->expire);
      $this->saveObjectIDInCache($pk);
   }

   protected function cacheDelete($tb)
   {
      if (!$tb->getDAL()->isKeyFilled()) return;
      $key = $this->getCacheKey($tb->getDAL()->getKeyValue());
      $this->cache->delete($key);
      $this->deleteObjectIDFromCache($key);
   }

   protected function getRow($tb, $pk = null, $isRawData = false)
   {
      if (!$pk) return array();
      $dal = $tb->getDAL();
      if (!is_array($pk) && $dal->getKeyLength() > 1) throw new \Exception(err_msg('ERR_SVC_1', array($this->objectName)));
      if (!is_array($pk)) $pk = array($dal->getKey($isRawData) => $pk);
      $sql = foo(new SQLGenerator())->getRow($this->objectName, $pk, $isRawData);
      return $dal->getDB()->row($sql, $pk);
   }

   private function saveObjectIDInCache($pk)
   {
      $key = $this->getObjectsCacheKey();
      $objs = $this->cache->get($key);
      $objs[get_class($this)][$this->getCacheKey($pk)] = $pk;
      $this->cache->set($key, $objs, $this->config['orm']['cacheBLLObjectExpire']);
   }

   private function deleteObjectIDFromCache($cacheID)
   {
      $key = $this->getObjectsCacheKey();
      $objs = $this->cache->get($key);
      if (is_array($objs)) {
         $class = get_class($this);
         $obj = array_key_exists($class, $objs) ? $objs[$class] : null;
         if ($obj && array_key_exists($cacheID, $obj)) {
            unset($obj[$cacheID]);
            $objs[$class] = $obj;
         }
      }
      $this->cache->set($key, $objs, $this->config['orm']['cacheBLLObjectExpire']);
   }

   private function getCacheKey($pk)
   {
      if (!is_array($pk)) $pk = (string)$pk;
      else foreach ($pk as $k => $v) $pk[$k] = (string)$v;
      return $this->config->siteUniqueID . '_orm_bll_object_' . $this->objectName . '_' . md5(serialize($pk));
   }

   private function getObjectsCacheKey()
   {
      return $this->config->siteUniqueID . '_orm_bll_objects';
   }

   private function checkOwn($tb)
   {
      if ($this->objectName != '\\' . get_class($tb)) throw new \Exception(err_msg('ERR_SVC_3', array(get_class($tb), get_class($this))));
   }

   private function useCache()
   {
      return (($this->config['orm']['useCacheForDataObjects'] || $this->mode == 'cache') && $this->mode != 'db');
   }
}

?>
