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
 * Responsibility of this file: config.php
 *
 * @category   Core
 * @package    Core
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @link       http://www.saritasa.com
 * @since      File available since Release 1.0.0
 */

namespace ClickBlocks\Core;

/**
 * This class is designed for storing global configuration parameters of an application and the framework.
 *
 * Этот класс предназначен для хранения глобальных конфигурационных параметров приложения и фрэймворка.
 *
 * @category  Core
 * @package   Core
 * @copyright 2007-2010 SARITASA LLC <info@saritasa.com>
 * @version   Release: 1.0.0
 */
class Config implements \ArrayAccess
{
   private $container = [];
   
   /**
    * Constructor of this class.
    *
    * Конструктор класса.
    *
    * @access public
    */
   public function __construct()
   {
      $this->root = $_SERVER['DOCUMENT_ROOT'];
   }
   
   public function offsetSet(mixed $offset, mixed $value): void
   {
      if (is_null($offset)) {
         $this->container[] = $value;
      } else {
         $this->container[$offset] = $value;
      }
   }
   
   public function offsetGet(mixed $property): mixed
   {
      return $this->container[$property] ?? null;
   }
   
   public function offsetExists(mixed $property): bool
   {
      return isset($this->container[$property]);
   }
   
   public function offsetUnset(mixed $offset): void
   {
      unset($this->container[$offset]);
   }
   
   /**
    * Loads configuration parameters from a configuration file.
    *
    * Загружает конфигурационные параметры из файла конфигурации.
    *
    * @param string $iniFile - full path to an configuration file in php-ini format.
    * @access public
    * @return boolean returns TRUE if ini file is existed and was parsed correctly and FALSE if the file is not exist.
    */
   public function init($iniFile)
   {
      if (!is_file($iniFile)) return false;
      $data = parse_ini_file($iniFile, true);
      if ($data === false) throw new \Exception(err_msg('ERR_CONFIG_1', array($iniFile)));
      foreach ($data as $section => $properties)
      {
         if ($section != 'general' && is_array($properties)) foreach ($properties as $k => $v) $this->container[$section][$k] = $v;
         elseif ($section == 'general') foreach ($properties as $k => $v) $this->container[$k] = $v;
         else $this->container[$section] = $properties;
      }
      return true;
   }
   
   public function __set(string $name, mixed $value): void
   {
      $this->container[$name] = $value;
   }
   
   public function __get(string $name): mixed
   {
      return $this->container[$name] ?? null;
   }
}

?>
