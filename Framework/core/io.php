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
 * Responsibility of this file: io.php 
 * 
 * @category   Core
 * @package    Core
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @link       http://www.saritasa.com
 * @since      File available since Release 1.0.0        
 */

namespace ClickBlocks\Core;

/**
 * This class is container for common static functions for working with input/output system.
 * 
 * Класс является контейнером статических функций для работы с системой ввода-вывода.
 *
 * @category  Core
 * @package   Core
 * @copyright 2007-2010 SARITASA LLC <info@saritasa.com>
 * @version   Release: 1.0.0
 */
class IO
{
   /**
    * Returns a full path to a folder on its alias.
    * 
    * Возвращает полный путь к папке по её псевдониму.
    * 
    * @param string $folder - given directory or alias of it.
    * @return string
    * @access public
    * @static
    */
   public static function dir($folder)
   {
      $config = Register::getInstance()->config;
      $dir = $config->dirs[$folder] ?: $folder;
      if (substr($dir, 0, strlen($config->root)) != $config->root) $dir = $config->root . $dir;
      return $dir;
   }

   /**
    * Returns relative URL to a folder on its alias.
    * 
    * Возвращает относительный URL к заданной папке по её псевдониму.
    * 
    * @param string $folder - given directory or alias of it.
    * @return string
    * @access public
    * @static
    */
   public static function url($folder)
   {
      $config = Register::getInstance()->config;
      $url = $config->dirs[$folder] ?: $folder;
      if (substr($url, 0, 4) == 'http') return $url;
      return $config->baseUrl . $url;
   }

   /**
    * Creates recursively neccessary directories.
    * 
    * Рекурсивно создаёт необходимые директории.
    * 
    * @param string $path
    * @return boolean - returns TRUE if all directories are successfuly created and FALSE otherwise.
    * @access public
    * @static
    */
   public static function createDirectories($path)
   {
      if (strlen($path) == 0) return false;
      if (strlen($path) < 3 || is_dir($path) || dirname($path) == $path) return true;
      return (self::createDirectories(dirname($path)) and mkdir($path, 0775) and chmod($path, 0775));
   }

   /**
    * Deletes a folder and all its contents.
    * 
    * Удаляет папку вместе со всем её содержимым.
    * 
    * @param string $path
    * @access public
    * @static
    */
   public static function deleteDirectories($path)
   {
      if (!is_dir($path)) return false;
      $dir = opendir($path);
      while (($file = readdir($dir)) !== false)
      {
         if ($file != '.' && $file != '..')
         {
            $item = $path . '/' . $file;
            if (is_dir($item)) self::deleteDirectories($item);
            else unlink($item);
         }
      }
      closedir($dir);
      return @rmdir($path);
   }

   /**
    * Searches files in given a directory by specified mask.
    * 
    * Ищет файлы в заданной папке по определённой маске.
    * 
    * @param string $mask
    * @param string $path
    * @param string $text         - some text containing in the file.
    * @param boolean $isRecursion - if value of this parameter is TRUE the search will be recursive and not recursive otherwise.
    * @return array
    * @access public
    * @static
    */
   public static function searchFiles($mask, $path, $text = null, $isRecursion = false)
   {
      $dir = opendir($path);
      $files = array();
      while (($file = readdir($dir)) !== false)
      {
         if ($file != '.' && $file != '..' && $file != '.svn')
         {
            $item = $path . '/' . $file;
            if (is_file($item))
            {
               if (preg_match($mask, $file) && (strlen($text) == 0 || preg_match($text, file_get_contents($item)))) $files[] = $item;
            }
            elseif (is_dir($item) && $isRecursion)
            {
               $files2 = self::searchFiles($mask, $item, $text, $isRecursion);
               $files = array_merge($files, $files2);
            }
         }
      }
      closedir($dir);
      return $files;
   }

   /**
    * Deletes all files from given directory.
    * 
    * Удаляет все файлы из заданной директории.
    * 
    * @param string $path
    * @param array $files - an array of files which will not be deleted.
    * @access public
    * @static
    */
   public static function deleteFiles($path, array $files = null)
   {
      if (!is_dir($path)) return false;
      if ($files === null) $files = array();
      $dir = opendir($path);
      while (($file = readdir($dir)) !== false)
      {
         if ($file != '.' && $file != '..')
         {
            $item = $path . '/' . $file;
            if (is_file($item) && !in_array($file, $files)) unlink($item);
         }
      }
      closedir($dir);
   }
   
   /**
    * Provides a file path to standard view.
    * 
    * Приводит путь к файлу к стандартному виду.
    * 
    * @param string $file
    * @return string 
    * @access public
    * @static
    */
   public static function normalizeFileName($file)
   {
      return str_replace((DIRECTORY_SEPARATOR == '\\') ? '/' : '\\', DIRECTORY_SEPARATOR, $file);  
   }

   /**
    * returns relative path relatively to first argument
    * i.e. for "/var/bin/1/func/" and "/var/bin/2/another/" it return "../../2/another/"
    * if $isFiles is true - dirpathes will be treated as filepathes
    * @param  [type] $from    [description]
    * @param  [type] $to      [description]
    * @param  [type] $isFiles [description]
    * @return [type]          [description]
    */
   public static function getDirRelativePath($from, $to, $isFiles = false)
   {
      $from = self::normalizeFileName($from);
      $to   = self::normalizeFileName($to);
      if ($isFiles) {
         $toFileName = '/'.basename($to);
         $from = dirname($from);
         $to = dirname($to);
      }
      $from = array_filter(explode(DIRECTORY_SEPARATOR, trim($from, '/ \\')));
      $to   = array_filter(explode(DIRECTORY_SEPARATOR, trim($to, '/ \\')));

      while (isset($from[0]) && isset($to[0]) && $from[0] === $to[0]) {
         array_shift($from);
         array_shift($to);
      }
      return ($from ? implode('/', array_fill(0, count($from), '..')) : '') . ($to ? '/'.implode('/', $to) : '') . $toFileName;
   }

    public static function makeTmpFileName($keySeed = null)
    {
        return self::dir('temp').'/'.md5(time().json_encode((array)$keySeed));
    }
}

?>
