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
 * Responsibility of this file: cache.php
 *
 * @category   Cache
 * @package    Core
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @link       http://www.saritasa.com
 * @since      File available since Release 1.0.0
 */

namespace ClickBlocks\Cache;

use ClickBlocks\Core;

/**
 * All classes for caching should implement ICache interface.
 *
 * Интерфейс ICache должны реализовывать все классы для работы с кэшэм.
 *
 * @category  Cache
 * @package   Core
 * @copyright 2007-2010 SARITASA LLC <info@saritasa.com>
 * @version   Release: 1.0.0
 */
interface ICache
{
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
   public function set($key, $content, $expire);

   /**
    * Gets a value from a cache.
    *
    * Получает значение из кэша.
    *
    * @param string $key - unique identifier of a cache.
    * @return mixed
    * @access public
    */
   public function get($key);

   /**
    * Deletes a cache associated with certain unique identifier.
    *
    * Удаляет кэш, связанный с определённым идентификатором.
    *
    * @param string $key - unique identifier of a cache.
    * @access public
    */
   public function delete($key);

   /**
    * Checks whether or not a cache is expired.
    *
    * Проверяет истёк ли срок годности кэша или нет.
    *
    * @param string $key - unique identifier of a cache.
    * @return boolean    - the method returns TRUE if a cache is expired and FALSE otherwise.
    */
   public function isExpired($key);

   /**
    * Checks whether or not the given type of a cache memory is available for usage.
    *
    * Проверяет доступен ли для использования данный тип кэша.
    *
    * @return boolean    - the method returns TRUE if the given type of a cache memory is available and FALSE otherwise.
    * @access public
    * @static
    */
   public static function isAvailable();

   /**
    * Cleans entire cache memory of the given type.
    *
    * Очищает всю кэш память данного типа.
    *
    * @access public
    */
   public function clean();
}

/**
 * The class contains only static factory method is used for getting of an instance of the object of a given type of a cache.
 *
 * Класс содержит единственный статический фабричный метод, используемый для получения экземпляра объекта заданного типа кэша.
 *
 * @category  Cache
 * @package   Core
 * @copyright 2007-2010 SARITASA LLC <info@saritasa.com>
 * @version   Release: 1.0.0
 */
class Cache
{
   /**
    * Returns an instance of the object of a given type of a cache memory.
    *
    * Возвращает экземпляр объекта заданного типа кэша.
    *
    * @param string $type  - type of a cache memory.
    * @param array $params - parameters of the constructor of a given cache type class.
    * @return ICache       - instance of an object that implements ICache interface.
    * @access public
    * @static
    */
   public static function factory($type, array $params = null)
   {
      switch ($type)
      {
         case 'Laravel':
           require_once(__DIR__ . '/laravel.php');
           break;
         case 'Redis':
           require_once(__DIR__ . '/redis.php');
           break;
         case 'Memory':
           require_once(__DIR__ . '/memory.php');
           break;
         case 'File':
           require_once(__DIR__ . '/file.php');
           break;
         case 'Alternative':
           require_once(__DIR__ . '/alternative.php');
           break;
         case 'EAccelerator':
           require_once(__DIR__ . '/eaccelerator.php');
           break;
         case 'X':
           require_once(__DIR__ . '/xcache.php');
           break;
         case 'Default':
           require_once(__DIR__ . '/default.php');
           break;
         default:
           throw new \Exception(err_msg('ERR_CACHE_1', array($type)));
      }
      $cache = 'ClickBlocks\Cache\Cache' . $type;
      if (class_exists($cache, false))
      {
         if (call_user_func($cache . '::isAvailable'))
         {
            switch ($type)
            {
               case 'Redis':
                 return new $cache(
                     $params['host'],
                     !empty($params['port']) ? $params['port'] : 6379,
                     !empty($params['timeout']) ? $params['timeout'] : 0,
                     !empty($params['password']) ? $params['password'] : null,
                     !empty($params['database']) ? $params['database'] : 0 );
               case 'Memory':
                 return new $cache($params['host'], $params['port'], $params['compress']);
               case 'File':
                 return new $cache($params['dir']);
               default:
                 return new $cache();
            }
         }
         throw new \Exception(err_msg('ERR_CACHE_2', array(Core\Register::getInstance()->config->cache['type'])));
      }
      throw new \Exception(err_msg('ERR_GENERAL_1', array($cache)));
   }
}

?>