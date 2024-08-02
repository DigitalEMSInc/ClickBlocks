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
 * Responsibility of this file: logger.php
 *
 * @category   Core
 * @package    Core
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @link       http://www.saritasa.com
 * @since      File available since Release 1.0.0
 */

namespace ClickBlocks\Core;

use ClickBlocks\Utils;

/**
 * This class is designed for logging error information and also for profiling php-code.
 *
 * Этот класс предназначен для логирования информации об ошибках, а также для профилирования кода.
 *
 * @category  Core
 * @package   Core
 * @copyright 2007-2010 SARITASA LLC <info@saritasa.com>
 * @version   Release: 1.0.0
 */
class Logger
{
   /**
    * These constants determine thr category of an exception.
    *
    * Константы определяющие категорию исключения.
    */
   const LOG_CATEGORY_EXCEPTION = 1;
   const LOG_CATEGORY_USER_EXCEPTION = 2;
   const LOG_CATEGORY_SQL_EXCEPTION = 4;
   const LOG_CATEGORY_TRACK = 8;
   const LOG_CATEGORY_ALL = 15;

   /**
    * The instance of this class.
    *
    * Экземпляр класса.
    *
    * @var object $instance
    * @access private
    */
   private static $instance;


   private static $time = array();

   /**
    * Clones an object of this class. The private method '__clone' doesn't allow to clone an instance of the class.
    *
    * Клонирует объект данного класса. При этом скрытый метод __clone не позволяет клонировать объект.
    *
    * @access private
    */
   private function __clone(){}

   /**
    * Constructor of this class.
    *
    * Конструктор класса.
    *
    * @param float $time - the number of seconds an appropriate starting point of a script.
    * @access private
    */
   private function __construct($time = null)
   {
      self::$time['script_execution_time'] = ($time) ?: microtime(true);
   }

   /**
    * Returns an instance of this class.
    *
    * Возвращает экземпляр этого класса.
    *
    * @return object
    * @access public
    * @static
    */
   public static function getInstance($time = null)
   {
      if (self::$instance === null) self::$instance = new self($time);
      return self::$instance;
   }

   /**
    * Starts profiling code.
    *
    * Начинает профилирование кода.
    *
    * @param string $key - unique identifier of profiling point.
    * @access public
    * @static
    */
   public static function pStart($key)
   {
      self::$time[$key] = microtime(true);
   }

   /**
    * Ends profiling php-code.
    *
    * Заканчивает профилирование кода.
    *
    * @param string $key - unique identifier of profiling point.
    * @return float - the execution time of a block php-code.
    * @access public
    * @static
    */
   public static function pStop($key)
   {
      return number_format(microtime(true) - self::$time[$key], 6);
   }

   /**
    * Returns the size of used memory.
    *
    * Возвращает размер использованной памяти.
    *
    * @return integer - the size of used memory.
    * @access public
    * @static
    */
   public static function getMemoryUsage()
   {
      return memory_get_usage();
   }

   /**
    * Returns the execution time of a script.
    *
    * Возвращает время выполнения скрипта.
    *
    * @return float - the execution time.
    * @access public
    * @static
    */
   public static function getExecutionTime()
   {
      return number_format(microtime(true) - self::$time['script_execution_time'], 6);
   }

   /**
    * Returns the execution time of a script from a moment of request senfing to the server.
    *
    * Возвращает время работы скрипта от момента посыла запроса на срервер.
    *
    * @return float - the request time.
    * @access public
    * @static
    */
   public static function getRequestTime()
   {
      return number_format(microtime(true) - $_SERVER['REQUEST_TIME'], 6);
   }

   /**
    * Writes a log in a file (by default).
    *
    * Записывает в файл лог.
    *
    * @param mixed $data       - log data.
    * @param integer $category - the category of an exception or mistake.
    * @access public
    * @static
    */
   public static function log($data, $category = self::LOG_CATEGORY_EXCEPTION)
   {
      $config = Register::getInstance()->config;
      if (!$config->isLog) return;
      if ($config->customLogMethod && class_exists('\ClickBlocks\Core\Delegate', false))
      {
         $log = new Delegate($config->customLogMethod);
         $log($data, $category);
      }
      else
      {
         $path = IO::dir('log') . '/' . date('Y F');
         IO::createDirectories($path);
         $file = $path . '/' . date('d (l)') . '.log';
         $info = array('IP' => $_SERVER['REMOTE_ADDR'] ?? null,
                       'ID' => session_id(),
                       'time' => date('m/d/Y H:i:s'),
                       'category' => $category,
                       'url' => (substr($_SERVER['SERVER_PROTOCOL'] ?? '', 0, 5) == 'HTTPS' ? 'https://' : 'http://') . ($_SERVER['HTTP_HOST'] ?? '') . ($_SERVER['REQUEST_URI'] ?? ''),
                       'SESSION' => $_SESSION,
                       'COOKIE' => $_COOKIE,
                       'GET' => $_GET,
                       'POST' => $_POST,
                       'FILES' => $_FILES,
                       'data' => $data);
         $log = (file_exists($file)) ? unserialize(file_get_contents($file)) : array();
         $log[] = $info;
         if (is_writable($file)) {
             file_put_contents($file, serialize($log));
         }
      }
   }

   /**
    * Returns log information for given period.
    *
    * Возвращает информацию из лога за данный период.
    *
    * @param string $from      - date of the start of period in format m/d/Y.
    * @param string $to        - date of the end of period in format m/d/Y.
    * @param integer $category - category of an exception.
    * @return array            - log information.
    * @access public
    * @static
    */
   public static function getLog($from = null, $to = null, $category = self::LOG_CATEGORY_ALL)
   {
      $log = array();
      $path = IO::dir('log');
      if ($from)
      {
         $dayFrom = Utils\DT::getDay($from, 'm/d/Y');
         $from = Utils\DT::format($from, 'm/d/Y', 'm/1/Y');
      }
      if ($to)
      {
         $dayTo = Utils\DT::getDay($to, 'm/d/Y');
         $to = Utils\DT::format($to, 'm/d/Y', 'm/1/Y');
      }
      $dir = opendir($path);
      while (($item = readdir($dir)) !== false)
      {
         if ($item != '.' && $item != '..' && $item != '.svn')
         {
            $folder = $path . '/' . $item;
            if (is_dir($folder))
            {
               $flag = 1;
               $dt = Utils\DT::getMonth($item, 'Y F') . '/1/' . Utils\DT::getYear($item, 'Y F');
               if ($from) $flag &= (int)(Utils\DT::compare($dt, $from, 'm/1/Y') >= 0);
               if ($to) $flag &= (int)(Utils\DT::compare($to, $dt, 'm/1/Y') >= 0);
               if ($flag)
               {
                  $logdir = opendir($folder);
                  while (($file = readdir($logdir)) !== false)
                  {
                     if ($file != '.' && $file != '..' && $file != '.svn')
                     {
                        $logfile = $folder . '/' . $file;
                        if (is_file($logfile))
                        {
                           $flag = 1;
                           $day = trim(substr($file, 0, strpos($file, '(')));
                           if ($from) $flag &= ($day >= $dayFrom);
                           if ($to) $flag &= ($day <= $dayTo);
                           if ($flag)
                           {
                              $data = unserialize(file_get_contents($logfile));
                              foreach ($data as $info) if ($info['category'] & $category) $log[] = $info;
                           }
                        }
                     }
                  }
                  closedir($logdir);
               }
            }
         }
      }
      closedir($dir);
      return $log;
   }
}

?>
