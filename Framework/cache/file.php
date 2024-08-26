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
 * Responsibility of this file: file.php 
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
 * The class designed for caching data in files. 
 * 
 * Класс предназначен для кэширования данных в файлах.
 *
 * @category  Cache
 * @package   Core
 * @copyright 2007-2010 SARITASA LLC <info@saritasa.com>
 * @version   Release: 1.0.0
 */
class CacheFile implements ICache
{  
   /**
    * Directory for storing of cached data.
    * 
    * Папка для хранения кэшированных данных.
    * 
    * @var string $dir - full path to a cache directory.
    * @access private
    */
   private $dir = null;
   
   /**
    * Constructor of this site.
    * 
    * Конструктор класса.
    * 
    * @param string $dir - full path to a cache directory
    * @access public
    */
   public function __construct($dir = null)
   {
      $this->setCacheDirectory($dir ?: 'cache');
   }
   
   /**
    * Sets new cache directory.
    * 
    * Устанавливает новую папку для кэширования.
    * 
    * @param string $dir - full path to a cache directory.
    * @access public
    */
   public function setCacheDirectory($dir)
   {
      $dir = Core\IO::dir($dir);
      if (!is_dir($dir)) throw new \Exception(err_msg('ERR_GENERAL_5', array($dir)));
      if (!is_writable($dir)) throw new \Exception(err_msg('ERR_CACHE_3', array($dir)));
      if (strlen($dir) && $dir[strlen($dir) - 1] != '/') $dir .= '/';
      $this->dir = $dir;
   }
   
   /**
    * Gets the current cache directory.
    * 
    * Получение текущей папки для кэширования.
    * 
    * @return string
    * @access public
    */
   public function getCacheDirectory()
   {
      return $this->dir;
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
      $key = md5($key);
      file_put_contents($this->dir . $key, serialize($content));
      file_put_contents($this->dir . $key . '_expire', (int)$expire);
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
      $file = $this->dir . md5($key);
      if (is_file($file)) return unserialize(file_get_contents($file));
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
      $file = $this->dir . md5($key);
      if (!is_file($file))
      {
         if (is_file($file . '_expire')) unlink($file . '_expire');
         return true;
      }
      if (!is_file($file . '_expire')) 
      {
         if (is_file($file)) unlink($file);
         return true;
      }
      if (!($mtime = filemtime($file))) return true;      
      if ($mtime + (int)file_get_contents($file . '_expire') < time())
      {
         unlink($file);
         unlink($file . '_expire');
         return true;
      }
      return false;
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
      return true;
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
      $file = $this->dir . md5($key);
      if (is_file($file)) unlink($file);
      if (is_file($file . '_expire')) unlink($file . '_expire');
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
      Core\IO::deleteFiles($this->dir);
   }
   
   /**
    * The garbage collector that removes all files are contained expired cached data.
    * 
    * Сборщик мусора, который удаляет все файлы с закэшированными данными, срок годности которых истёк.
    * 
    * @access public
    */
   public function gc()
   {
      foreach (glob($this->dir . '/*') as $file)
      {
         if (substr($file, -7) == '_expire') continue;
         if (!is_file($file . '_expire'))
         {
            unlink($file);
            continue;
         }
         if (!($mtime = filemtime($file)) || $mtime + (int)file_get_contents($file . '_expire') < time())
         {
            unlink($file);
            unlink($file . '_expire');
         }
      }
   }
}

?>
