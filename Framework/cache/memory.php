<?php
/**
 * ClickBlocks.PHP v. 1.0
 *
 * Copyright (C) 2010  SARITASA LLC
 * http://www.saritasa.com
 *
 * This framework is free software. You can redistribute it and/or modify
 * it under the terms of either the current ClickBlocks.PHP License
 * viewable at theclickblocks.com) or the License that was distributed with
 * this file.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY, without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * You should have received a copy of the ClickBlocks.PHP License
 * along with this program.
 *
 * Responsibility of this file: memory.php
 *
 * @category   Cache
 * @package    Core
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @link       http://www.saritasa.com
 * @since      File available since Release 1.0.0
 */

namespace ClickBlocks\Cache;

/**
 * The class designed for caching data in memory.
 *
 * Класс предназначен для кэширования данных в оперативной памяти.
 *
 * @category  Cache
 * @package   Core
 * @copyright 2007-2010 SARITASA LLC <info@saritasa.com>
 * @version   Release: 1.0.0
 */
class CacheMemory implements ICache
{
   /**
    * Determines whether the data will be compressed before placing in the cache.
    *
    * Определяет, будут ли данные сжиматься перед помещением их в кэш.
    *
    * @var integer $compress
    * @access private
    */
   private $compress = null;

   /**
    * The instance of \Memcache class.
    *
    * Экземпляр класса \Memcache.
    *
    * @var object $memobj
    * @access private
    */
   private $memobj = null;

   /**
    * Constructor of this site.
    *
    * Конструктор класса.
    *
    * @param string $host      - host for a memcache connection.
    * @param integer $port     - port for a memcache connection.
    * @param boolean $compress - if value of this parameter is TRUE any data will be compressed before placing in a cache, otherwise data will not be compressed.
    * @access public
    */
   public function __construct($host = null, $port = null, $compress = true)
   {
      $this->memobj = new \Memcache();
      $this->memobj->connect($host ?: '127.0.0.1', $port ?: 11211);
      $this->compress = ($compress) ? MEMCACHE_COMPRESSED : 0;
   }

   /**
    * Gets the memcache object.
    *
    * Получает объект memcache.
    *
    * @return object
    * @access public
    */
   public function getMemcacheObject()
   {
      return $this->memobj;
   }

   /**
    * Puts a content into a cache.
    *
    * Помещает содержимое в кэш.
    *
    * @param string $key     - unique identifier of a cache.
    * @param mixed $content  - arbitrary value.
    * @param integer $expire - expiration time of a cache.
    * @access public
    */
   public function set($key, $content, $expire)
   {
      $expire = (int)$expire;
      $key = md5($key);
      $content = serialize($content);
      if (strlen($content) < 1000000) $this->memobj->set($key, $content, $this->compress, $expire);
      else
      {
        foreach (str_split($content, 1000000) as $n => $part)
        {
          $this->memobj->set($key . $n, $part, $this->compress, $expire);
        }
        $this->memobj->set($key, array($key => $n), $this->compress, $expire);
      }
   }

   /**
    * Gets a value from a cache.
    *
    * Получает значение из кэша.
    *
    * @param string $key - unique identifier of a cache.
    * @return mixed
    * @access public
    */
   public function get($key)
   {
      $key = md5($key);
      $res = $this->memobj->get($key);
      if (is_array($res) && is_int($res[$key]))
      {
        for ($i = 0, $j = $res[$key], $res = ''; $i <= $j; $i++)
        {
          $res .= $this->memobj->get($key . $i);
        }
      }
      return unserialize($res);
   }

   /**
    * Checks whether or not a cache is expired.
    *
    * Проверяет истёк ли срок годности кэша или нет.
    *
    * @param string $key - unique identifier of a cache.
    * @return boolean    - the method returns TRUE if a cache is expired and FALSE otherwise.
    */
   public function isExpired($key)
   {
      return ($this->memobj->get(md5($key)) === false);
   }

   /**
    * Checks whether or not the given type of a cache memory is available for usage.
    *
    * Проверяет доступен ли для использования данный тип кэша.
    *
    * @return boolean    - the method returns TRUE if the given type of a cache memory is available and FALSE otherwise.
    * @access public
    * @static
    */
   public static function isAvailable()
   {
      return extension_loaded('memcache');
   }

   /**
    * Deletes a cache associated with certain unique identifier.
    *
    * Удаляет кэш, связанный с определённым идентификатором.
    *
    * @param string $key - unique identifier of a cache.
    * @access public
    */
   public function delete($key)
   {
      $key = md5($key);
      $res = $this->memobj->get($key);
      if (is_array($res) && is_int($res[$key]))
      {
        for ($i = 0, $j = $res[$key]; $i <= $j; $i++)
        {
          $this->memobj->delete($key . $i);
        }
      }
      else $this->memobj->delete($key);
   }

   /**
    * Closes the current connection with memcache.
    *
    * Закрывает текущее соединение с memcache.
    *
    * @access public
    */
   public function __destruct()
   {
      $this->memobj->close();
   }

   /**
    * Cleans entire cache memory of the given type.
    *
    * Очищает всю кэш память данного типа.
    *
    * @access public
    */
   public function clean()
   {
      $this->memobj->flush();
   }
}

?>
