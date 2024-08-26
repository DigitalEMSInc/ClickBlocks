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
 * Responsibility of this file: logexception.php 
 * 
 * @category   Core
 * @package    Core
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @link       http://www.saritasa.com
 * @since      File available since Release 1.0.0        
 */

namespace ClickBlocks\Core;

/**
 * This class is designed for an exception with a subsequent logging information about the error.
 * 
 * Этот класс предназначен для генерации исключения с последующим логированием информации об ошибке.
 *
 * @category  Core
 * @package   Core
 * @copyright 2007-2010 SARITASA LLC <info@saritasa.com>
 * @version   Release: 1.0.0
 */
class LogException extends \ErrorException
{
   /**
    * Constructor of this class.
    * 
    * Конструктор класса.
    * 
    * @param string $message   - error message.
    * @param integer $category - category of an error.
    * @param integer $severity - severity of an error.
    * @param string filename   - file name where an error occurred.
    * @param integer $lineno   - line number in the file with an error.
    * @param string $stack     - debug backtrace.
    * @access public
    */
   public function __construct($message = null, $category = Logger::LOG_CATEGORY_EXCEPTION, $severity = null, $filename = null, $lineno = null, $stack = null)
   {
      parent::__construct($message, 0, $severity, $filename, $lineno);
      $data = array();
      $data['message'] = $this->getMessage();
      $data['code'] = $severity;
      $data['severity'] = $this->getSeverityAsString();
      $data['file'] = $this->getFile();
      $data['line'] = $this->getLine();
      $data['memory'] = Logger::getMemoryUsage();
      $data['time'] = Logger::getExecutionTime();
      $data['stack'] = array_reverse(explode("\n", (!$stack) ? $this->getTraceAsString() : $stack));
      foreach ($data['stack'] as &$v) $v = substr($v, strpos($v, ' ') + 1);
      Logger::log($data, $category);
   }

   /**
    * Returns severity of an error as string.
    * 
    * Возвращает серьёзность ошибки в виде строки.
    * 
    * @return string
    * @access public
    * @final
    */
   final public function getSeverityAsString()
   {
      $severity = $this->getSeverity();
      $errorLevels = array('FATAL' => E_ERROR,
                           'WARNING' => E_WARNING,
                           'PARSE' => E_PARSE,
                           'NOTICE' => E_NOTICE,
                           'CORE_FATAL' => E_CORE_ERROR,
                           'CORE_WARNING' => E_CORE_WARNING,
                           'COMPILE_FATAL' => E_COMPILE_ERROR,
                           'COMPILE_WARNING' => E_COMPILE_WARNING,
                           'USER_ERROR' => E_USER_ERROR,
                           'USER_WARNING' => E_USER_WARNING,
                           'USER_NOTICE' => E_USER_NOTICE,
                           'STRICT' => E_STRICT,
                           'RECOVERABLE_EFATAL' => E_RECOVERABLE_ERROR,
                           'DEPRECATED' => E_DEPRECATED,
                           'USER_DEPRECATED' => E_USER_DEPRECATED);
      $tmp = array();
      foreach ($errorLevels as $level => $v) if ($severity & $v) $tmp[] = $level;
      return implode(' | ', $tmp);
   }
}

?>
