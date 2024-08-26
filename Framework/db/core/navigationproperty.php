<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Exceptions;

class NavigationProperty implements \Iterator, \ArrayAccess, \SeekableIterator, \Countable, \Serializable
{
   protected $config = null;
   protected $rows = array();
   protected $removed = array();
   protected $objects = null;
   protected $position = 0;
   protected $lastPosition = 0;
   protected $isExecuted = false;
   protected $bll = null;
   protected $dalClassName= null;
   protected $field = null;
   protected $info = null;

   public function __construct(IBLLTable $bll, $dalClassName, $field)
   {
      $this->dalClassName = $dalClassName;
      $this->field = $field;
      $this->initialize($bll);
      if (!$this->config['orm']['lazyLoading']) $this->execute();
   }

   public function initialize(IBLLTable $bll = null)
   {
      $this->config = Core\Register::getInstance()->config;
      if ($bll)
      {
         $this->bll = $bll;
         $this->info = $this->bll->getDAL($this->dalClassName)->getNavigationField($this->field);
      }
   }

   public function getField()
   {
      return $this->field;
   }

   public function isReadable()
   {
      return $this->info['readable'];
   }

   public function isInsertable()
   {
      return $this->info['insertable'];
   }

   public function isUpdateable()
   {
      return $this->info['updateable'];
   }

   public function isDeleteable()
   {
      return $this->info['deleteable'];
   }

   public function count(): int
   {
      if ($this->config['orm']['lazyLoading']) $this->execute();
      return count($this->rows);
   }

   public function seek($position): void
   {
      if ($this->config['orm']['lazyLoading']) $this->execute();
      $this->position = $position;
      if (!$this->valid()) throw new \Exception(err_msg('ERR_GENERAL_2', array($this->position)));
   }

   public function rewind(): void
   {
      if ($this->config['orm']['lazyLoading']) $this->execute();
      $this->position = 0;
   }

   public function current(): mixed
   {
      if ($this->config['orm']['lazyLoading']) $this->execute();
      return $this->offsetGet($this->key(), true);
   }

   public function key(): mixed
   {
      if ($this->config['orm']['lazyLoading']) $this->execute();
      $tmp = array_keys($this->rows);
      return $tmp[$this->position];
   }

   public function next(): void
   {
      if ($this->config['orm']['lazyLoading']) $this->execute();
      $this->position++;
   }

   public function valid(): bool
   {
      if ($this->config['orm']['lazyLoading']) $this->execute();
      return isset($this->rows[$this->key()]);
   }

   public function offsetSet($offset, $value, $alwaysRead = false): void
   {
      if (!$this->info['insertable'] && !$alwaysRead) throw new \Exception(err_msg('ERR_NAV_2', array($this->field)));
      if ($this->config['orm']['lazyLoading']) $this->execute();
      if (is_array($value))
      {
         if (is_null($offset)) $this->rows[] = $value;
         else $this->rows[$offset] = $value;
      }
      else if (is_a($value, $this->info['to']['bll']))
      {
         if (is_null($offset)) $this->rows[] = $value->getValues(true);
         else $this->rows[$offset] = $value->getValues(true);
      }
      else throw new \Exception(err_msg('ERR_DAL_3', array($field)));
      if (!is_null($offset)) $this->lastPosition = $offset;
      {
         end($this->rows);
         $this->lastPosition = key($this->rows);
      }
      if (isset($this->removed[$offset])) unset($this->removed[$offset]);
      if (isset($this->objects[$offset])) unset($this->objects[$offset]);
   }

   public function offsetExists($offset): bool
   {
      if ($this->config['orm']['lazyLoading']) $this->execute();
      return isset($this->rows[$offset]);
   }

   public function offsetUnset($offset): void
   {
      if (!$this->info['deleteable']) throw new \Exception(err_msg('ERR_NAV_3', array($this->field)));
      if ($this->config['orm']['lazyLoading']) $this->execute();
      if (isset($this->rows[$offset])) $this->removed[$offset] = $this->rows[$offset];
      unset($this->rows[$offset]);
      unset($this->objects[$offset]);
   }

   public function offsetGet($offset, $alwaysRead = false): mixed
   {
      if (!$this->info['readable'] && !$alwaysRead) throw new \Exception(err_msg('ERR_NAV_1', array($this->field)));
      $this->lastPosition = $offset;
      if ($this->config['orm']['lazyLoading']) $this->execute();
      if ($this->info['output'] == 'raw') return $this->rows[$offset];
      if (isset($this->objects[$offset])) return $this->objects[$offset];
      $bll = new $this->info['to']['bll']();
      $bll->assign(array_key_exists($offset, $this->rows) ? (array)$this->rows[$offset] : []);
      $bll->navigator = array('navObject' => $this, 'offset' => $offset);
      $this->objects[$offset] = $bll;
      return $bll;
   }

   public function __set($param, $value)
   {
      $obj = $this[$this->lastPosition];
      if (is_object($obj)) $obj->{$param} = $value;
      else $obj[$param] = $value;
   }

   public function __get($param)
   {
      $obj = $this[$this->lastPosition];
      if (is_object($obj)) return $obj->{$param};
      return $obj[$param];
   }

   public function __call($method, $params)
   {
      $obj = $this[$this->lastPosition];
      if (is_object($obj)) return call_user_func_array(array($obj, $method), $params);
      throw new \BadMethodCallException(err_msg('ERR_NAV_5', array($method, $this->field)));
   }

   public function __invoke($page = 0, $pagesize = 0)
   {
      return $this->limit($page, $pagesize);
   }

   public function serialize()
   {
      $data = get_object_vars($this);
      unset($data['objects']);
      unset($data['info']);
      unset($data['config']);
      unset($data['bll']);
      return serialize($data);
   }

   public function unserialize($data)
   {
      $data = unserialize($data);
      foreach ($data as $k => $v) $this->{$k} = $v;
      $this->initialize();
   }
   
   public function __serialize()
   {
      return $this->serialize();
   }
   
   public function __unserialize($data)
   {
      return $this->unserialize($data);
   }

   public function limit($page = 0, $pagesize = 0)
   {
      if ($this->config['orm']['lazyLoading']) $this->execute();
      if ($pagesize == 0) $rows = $this->rows;
      else $rows = array_slice(array_values($this->rows), $page * $pagesize, $pagesize);
      if ($this->info['output'] == 'object') foreach ($rows as $k => $row) $rows[$k] = $this[$k];
      return $rows;
   }

   public function save()
   {
      foreach ($this->removed as $k => $item) $this->doAction($k, $item, 'delete');
      foreach ($this->rows as $k => $row) $this->doAction($k, $row, 'save');
   }

   protected function execute()
   {
      if ($this->isExecuted) return;
      $this->isExecuted = true;
      $dal = $this->bll->getDAL($this->dalClassName);
      $db = $dal->getDB();
      $info = ORM::getInstance()->getORMInfoObject()->getNavigationFieldInfoForSQL($dal->getDBAlias(), $dal->getTableAlias(), $this->field);
      $fromDBName = $db->wrap($info['from']['db']);
      $toDBName = $db->wrap($info['to']['db']);
      $toInheritDB = $db->wrap(array_key_exists('inherit', $info['to']) ? $info['to']['inherit']['db'] : '');
      if ($db->getEngine() == 'mssql')
      {
         $fromDBName .= '.[dbo]';
         $toDBName .= '.[dbo]';
         $toInheritDB .= '.[dbo]';
      }
      $params = $w = array();
      if ($info['from']['table'] == $info['to']['table'])
      {
         foreach ($info['from']['fields'] as $alias => $data)
         {
            $toField = current($info['to']['fields']);
            $params[] = $this->bll->{$alias};
            $w[] = 't1.' . $db->wrap($toField['name']) . ' = ?';
         }
         if (!$info['to']['inherit'])
         {
            $sql = 'SELECT t1.* FROM ' . $fromDBName . '.' . $db->wrap($info['from']['table']) . ' AS t1 WHERE ' . implode(' AND ', $w);
            $ff = true;
         }
         else
         {
            $inheritJoins = array();
            foreach ($info['from']['pk'] as $alias => $data)
            {
               $toField = current($info['to']['inherit']['fields']);
               $inheritJoins[] = 't1.' . $db->wrap($data['name']) . ' = t2.' . $db->wrap($toField['name']);
            }
            $sql = 'SELECT t2.*, t1.* FROM ' . $fromDBName . '.' . $db->wrap($info['from']['table']) . ' AS t1
                    INNER JOIN ' . $toInheritDB . '.' . $db->wrap($info['to']['inherit']['table']) . ' AS t2 ON ' . implode(' AND ', $inheritJoins) . '
                    WHERE ' . implode(' AND ', $w);
         }
      }
      else
      {
         $joins = array();
         foreach ($info['from']['fields'] as $alias => $data)
         {
            $toField = current($info['to']['fields']);
            $params[] = $this->bll->{$alias};
            $joins[] = 't2.' . $db->wrap($data['name']) . ' = t1.' . $db->wrap($toField['name']);
            $w[] = 't2.' . $db->wrap($data['name']) . ' = ?';
         }
         if (!array_key_exists('inherit', $info['to']) || !$info['to']['inherit'])
         {
            $sql = 'SELECT t1.* FROM ' . $toDBName . '.' . $db->wrap($info['to']['table']) . ' AS t1
                    INNER JOIN ' . $fromDBName . '.' .  $db->wrap($info['from']['table']) . ' AS t2 ON ' . implode(' AND ', $joins) . '
                    WHERE ' . implode(' AND ', $w);
         }
         else if ($info['to']['inherit'])
         {
            if ($info['from']['db'] == $info['to']['inherit']['db'] && $info['from']['table'] == $info['to']['inherit']['table'])
            {
               $sql = 'SELECT t2.*, t1.* FROM ' . $toDBName . '.' . $db->wrap($info['to']['table']) . ' AS t1
                       INNER JOIN ' . $fromDBName . '.' .  $db->wrap($info['from']['table']) . ' AS t2 ON ' . implode(' AND ', $joins) . '
                       WHERE ' . implode(' AND ', $w);
            }
            else
            {
               $inheritJoins = array();
               foreach ($info['to']['fields'] as $alias => $data)
               {
                  $toField = current($info['to']['inherit']['fields']);
                  $inheritJoins[] = 't1.' . $db->wrap($data['name']) . ' = t3.' . $db->wrap($toField['name']);
               }
               $sql = 'SELECT t3.*, t1.* FROM ' . $toDBName . '.' . $db->wrap($info['to']['table']) . ' AS t1
                       INNER JOIN ' . $toInheritDB . '.' . $db->wrap($info['to']['inherit']['table']) . ' AS t3 ON ' . implode(' AND ', $inheritJoins) . '
                       INNER JOIN ' . $fromDBName . '.' .  $db->wrap($info['from']['table']) . ' AS t2 ON ' . implode(' AND ', $joins) . '
                       WHERE ' . implode(' AND ', $w);
            }
         }
      }
      if (!$this->info['multiplicity'] && $db->getEngine() == 'mysql') $sql .= ' LIMIT 1';
      $this->rows = $db->rows($sql, $params);
   }

   protected function doAction($key, array $row, $action)
   {
      $bll = new $this->info['to']['bll']();
      $bll->setValues($row, true);
      $flag = $bll->getDAL()->isKeyFilled();
      $fields = array_values($this->info['to']['fields']);
      foreach (array_values($this->info['from']['fields']) as $k => $field)
      {
         $bll->{$fields[$k]} = $this->bll->{$field};
      }
      $service = new $this->info['to']['service']();
      switch ($action)
      {
         case 'delete':
           $service->delete($bll);
           unset($this->removed[$key]);
           return;
         case 'save':
           if ($flag)
           {
              if ($this->info['updateable']) $service->update($bll);
           }
           else
           {
              if ($this->info['insertable']) $service->insert($bll);
           }
           break;
      }
      $this->rows[$key] = $bll->getValues(true);
      if ($this->info['output'] != 'raw') $this->objects[$key] = $bll;
   }
}

?>
