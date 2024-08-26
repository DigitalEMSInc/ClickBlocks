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
 * Responsibility of this file: eaccelerator.php
 *
 * @category   Cache
 * @package    Core
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @link       http://www.saritasa.com
 * @since      File available since Release 1.0.0
 */

namespace ClickBlocks\Cache;

/**
 * The class designed for caching data with eAccelerator php-extension.
 *
 * Класс предназначен для кэширования данных с помощью eaAccelerator расширения PHP.
 *
 * @category  Cache
 * @package   Core
 * @copyright 2007-2010 SARITASA LLC <info@saritasa.com>
 * @version   Release: 1.0.0
 */
class CacheEAccelerator implements ICache
{
   /**
    * Constructor of this class.
    *
    * Конструктор класса.
    *
    * @access public
    */
   public function __construct()
   {
      if ((int)ini_get('eaccelerator.enable') == 0) throw new \Exception(err_msg('ERR_CACHE_7'));
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
      eaccelerator_put($key, serialize($content), (int)$expire);
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
      return unserialize(eaccelerator_get($key));
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
      return (eaccelerator_get($key) === null);
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
      return extension_loaded('eaccelerator');
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
      eaccelerator_rm($key);
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
      eaccelerator_gc();
      foreach (eaccelerator_list_keys() as $k) eaccelerator_rm(substr($k['name'], 1));
   }
}

?>
