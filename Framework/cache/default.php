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
 * Responsibility of this file: default.php
 *
 * @category   Cache
 * @package    Core
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @link       http://www.saritasa.com
 * @since      File available since Release 1.0.0
 */

namespace ClickBlocks\Cache;

/**
 * The class designed for caching data in session.
 *
 * Класс предназначен для кэширования данных в сессии.
 *
 * @category  Cache
 * @package   Core
 * @copyright 2007-2010 SARITASA LLC <info@saritasa.com>
 * @version   Release: 1.0.0
 */
class CacheDefault implements ICache
{
   /**
    * The namespace for a cache into the $_SESSION array.
    *
    * Пространство имён для кэша в массиве $_SESSION.
    *
    * @var string $ns
    * @access private
    */
   private $ns = null;

   /**
    * Constructor of this class.
    *
    * Конструктор класса.
    *
    * @param string $namespace - namespace for a cache into $_SESSION array.
    * @access public
    */
   public function __construct($namespace = '__CACHE__')
   {
      $this->ns = $namespace;
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
      $_SESSION[$this->ns][$key] = array('content' => serialize($content), 'expire' => (int)$expire + time());
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
      return unserialize($_SESSION[$this->ns][$key]['content']);
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
   $v = $_SESSION[$this->ns][$key];
      return ($v === null || (int)$v['expire'] < time());
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
      return isset($_SESSION);
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
      unset($_SESSION[$this->ns][$key]);
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
      unset($_SESSION[$this->ns]);
   }

   /**
    * The garbage collector that cleans session from expired cached data.
    *
    * Сборщик мусора, который очищает сессию от закэшированных данных, время кэширования которых истекло.
    *
    * @access public
    */
   public function gc()
   {
      if (is_array($_SESSION[$this->ns])) foreach ($_SESSION[$this->ns] as $key => $data)
      {
         if ($this->isExpired($key)) $this->delete($key);
      }
   }
}

?>