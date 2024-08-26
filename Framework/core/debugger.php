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
 * Responsibility of this file: debugger.php
 *
 * @category   Core
 * @package    Core
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @link       http://www.saritasa.com
 * @since      File available since Release 1.0.0
 */

namespace ClickBlocks\Core;

/**
 * This class is designed for handling bugs and showing error messages.
 *
 * Этот класс предназначен для перехвата ошибок и показа сообщений об ошибках.
 *
 * @category  Core
 * @package   Core
 * @copyright 2007-2010 SARITASA LLC <info@saritasa.com>
 * @version   Release: 1.0.0
 */
class Debugger
{
   /**
    * The instance of this class.
    *
    * Экземпляр класса.
    *
    * @var object $instance
    * @access private
    */
   private static $instance = null;

   private static $output = null;
   private static $eval = null;
   private static $isParseError = false;

   /**
    * will handle exception in ClickBlocks way (redirect to bug page) if set to true, otherwise lets exceptions fly thru
    * @var bool
    */
   public static $handleExceptions = true;

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
    * @access private
    */
   private function __construct()
   {
      ini_set('display_errors', 1);
      ini_set('html_errors', 0);
      ob_start(array('\ClickBlocks\Core\Debugger', 'errorFatal'));
      set_exception_handler(array('\ClickBlocks\Core\Debugger', 'exceptionHandler'));
      self::setErrorReporting(E_ALL & ~E_NOTICE);
   }

   public static function setErrorReporting($errorLevel)
   {
      error_reporting($errorLevel);
      restore_error_handler();
      set_error_handler(array('\ClickBlocks\Core\Debugger', 'errorHandler'), $errorLevel);
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
   public static function getInstance()
   {
      if (self::$instance === null) self::$instance = new self();
      return self::$instance;
   }

   public static function setEvalCode($code, $isParseError = false)
   {
      if (self::$isParseError) return;
      self::$isParseError = $isParseError;
      self::$eval['code'] = $code;
      try
      {
         throw new \Exception();
      }
      catch (\Exception $e)
      {
         self::$eval['stack'] = $e->getTraceAsString();
      }
  }

   /**
    * Non-fatal error handler.
    *
    * Обработчик простых ошибок.
    *
    * @param integer $errno   - number of an error.
    * @param string $errstr   - error message.
    * @param string $errfile  - file where an error occurred.
    * @param integer $errline - line in the file where an error occurred.
    * @access public
    * @static
    */
   public static function errorHandler($errno, $errstr, $errfile, $errline)
   {
      throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
   }

   /**
    * Fatal error handler.
    *
    * Обработчик фатальных ошибок.
    *
    * @param string $html - entire output data sent to a browser.
    * @access public
    * @static
    */
   public static function errorFatal($html)
   {
      if (preg_match('/(Fatal|Parse) error:(.*) in (.*) on line (\d+)/', $html, $res))
      {
         $res[2] = ucfirst(ltrim($res[2]));
         $e = new LogException($res[2], Logger::LOG_CATEGORY_EXCEPTION, 1, $res[3], $res[4]);
         if (strpos($res[3], 'eval()\'d') !== false)
         {
            self::errorMessage(Logger::LOG_CATEGORY_EXCEPTION, self::$eval['stack'], $res[2], 'the code (see below)', $res[4], self::getCodeFragment('', $res[4], self::$eval['code']), true);
         }
         else
         {
            self::errorMessage(Logger::LOG_CATEGORY_EXCEPTION, $e->getTraceAsString(), $res[2], $res[3], $res[4], self::getCodeFragment($res[3], $res[4]), true);
         }
      }
      return (self::$output) ?: $html;
   }

   /**
    * Exception handler.
    *
    * Обработчик исключений.
    *
    * @param object $e         - instance of \Exception class.
    * @param integer $category - category of an exception.
    * @access public
    * @static
    */
   public static function exceptionHandler(\Throwable $e, $category = Logger::LOG_CATEGORY_EXCEPTION)
   {
      if (!self::$handleExceptions) {
         throw $e;
      }

      restore_error_handler();
      restore_exception_handler();
      $msg = ucfirst(ltrim($e->getMessage()));
      $trace = $e->getTrace();
      new LogException($msg, $category, $e->getCode(), $e->getFile(), $e->getLine(), $e->getTraceAsString());
      if (self::$eval && (strpos($trace[0]['file'], 'eval()\'d') !== false || strpos($trace[1]['file'], 'eval()\'d') !== false))
      {
         $msg = str_replace(' : eval()\'d', '', $msg);
         $fragment = self::$eval['code'];
         if (preg_match('/on line (\d+)/', $trace[0]['args'][1]))
         {
            $file = $trace[0]['file'];
            $msg = preg_replace('/called in ([^ ]+) code/', 'called in <b>the code (see below)</b>', $msg);
            $frag1 = self::getCodeFragment('', $trace[1]['line'], $fragment);
            if (strpos($file, 'eval()\'d') !== false)
            {
               $frag2 = self::getCodeFragment('', $trace[0]['line'], $fragment);
               $fragment = $frag1 . $frag2;
               $file = 'the second code (see below)';
            }
            else
            {
               $frag2 = self::getCodeFragment($file, $trace[0]['line']);
               $fragment = $frag1 . '<b style="font-size: 14px;">File: ' . $file . '</b>' .  $frag2;
            }
         }
         else
         {
            $fragment = self::getCodeFragment('', $e->getLine(), $fragment);
            $file = 'the code (see below)';
         }
         self::errorMessage(Logger::LOG_CATEGORY_EXCEPTION, $e->getTraceAsString(), $msg, $file, $e->getLine(), $fragment);
      }
      else
      {
         $arg = $trace[0]['args'][1];
         if ((!is_object($arg) || (is_object($arg) && method_exists($arg, '__toString'))) && preg_match('/on line (\d+)/', (string)$arg))
         {
            $msg = preg_replace('/called in ([^ ]+)/', 'called in <b>' . $trace[1]['file'] . '</b>', $msg);
            $frag1 = self::getCodeFragment($trace[1]['file'], $trace[1]['line']);
            $frag2 = self::getCodeFragment($trace[0]['file'], $trace[0]['line']);
            $fragment = '<b style="font-size: 14px;">File: ' . $trace[1]['file'] . '</b>' . $frag1 . '<b style="font-size: 14px;">File: ' . $trace[0]['file'] . '</b>' .  $frag2;
         }
         else
         {
            $fragment = self::getCodeFragment($e->getFile(), $e->getLine());
         }
         self::errorMessage($category, $e->getTraceAsString(), $msg, $e->getFile(), $e->getLine(), $fragment);
      }
   }

   /**
    * Returns error message or redirects to the debug/bug page.
    *
    * Возвращает сообщение об ошибке, либо перенаправляет на страницу с отладочной информацией об ошибке или на страницу сообщения об ошибке.
    *
    * @param integer $category - category of an error.
    * @param string $stack     - debug backtrace.
    * @param string $error     - error message.
    * @param string $file      - file where an error occurred.
    * @param integer $line     - line number in the file where an error occurred.
    * @param string $fragment  - fragment of the wrong code.
    * @param boolean $isFatal  - determines whether or not this method was called from fatal error handler.
    * @return string
    * @access private
    * @static
    */
   private static function errorMessage($category, $stack, $error, $file, $line, $fragment, $isFatal = false)
   {
      $config = Register::getInstance()->config;
      $stack = htmlspecialchars($stack);
      if (php_sapi_name() === 'cli')
      {
         $data = null;
         if ($config->customDebugMethod && class_exists('\ClickBlocks\Core\Delegate', false))
         {
            $debug = new Delegate($config->customDebugMethod);
            $data = "\n" . $debug($category, $stack, $error, $file, $line, $fragment, $isFatal);
         }
         if ($isFatal) $output = $fragment . "\n" . $data;
         else $output = $error . "\n" . $fragment . "\n" . $stack . "\n" . $data;
         echo strip_tags(html_entity_decode($output));
         exit;
      }
      if ($config->isDebug)
      {
         $vars = array('[{stack}]' => $stack, '[{errMessage}]' => $error, '[{errFile}]' => $file, '[{errLine}]' => $line, '[{fragment}]' => $fragment, '[{data}]' => '');
         if ($config->customDebugMethod && class_exists('\ClickBlocks\Core\Delegate', false))
         {
            $debug = new Delegate($config->customDebugMethod);
            $vars['[{data}]'] = $debug($category, $stack, $error, $file, $line, $fragment, $isFatal);
         }
         if (!is_file(IO::dir($config->debugPage)))
         {
            self::$output = $error . ' in <b>' . $file . '</b> on line ' . $line . '<br /><br />' . $fragment . '<b style="font-size: 14px;">Stack Trace:</b><pre>' . $stack . '</pre>' . $data;
            return;
         }
         $_SESSION['bug_content'] = strtr(file_get_contents(IO::dir($config->debugPage)), $vars);
         $output = self::redirect(IO::url('templates') . '/debug/bug.php', true);
      }
      else
      {
         if (!is_file(IO::dir($config->bugPage))) $output = ERR_GENERAL_0;
         else $output = self::redirect(IO::url($config->bugPage));
      }
      self::$output = $output;
   }

   /**
    * Returns js-script for redirect to given URL.
    *
    * Возвращает js для перенаправления по заданному URL.
    *
    * @param string $url          - given URL.
    * @param boolean $isNewWindow - determines whether or not to open a new window.
    * @return string
    * @access private
    * @static
    */
   private static function redirect($url, $isNewWindow = false)
   {
      if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
      {
         if ($isNewWindow) return 'window.open(\'' . addslashes($url) . '\')';
         return 'window.location.asign(\'' . addslashes($url) . '\')';
      }
      else
      {
         if ($isNewWindow) return '<script type="text/javascript">window.open(\'' . addslashes($url) . '\');</script>';
         return '<script type="text/javascript">window.location.assign(\'' . addslashes($url) . '\');</script>';
      }
   }

   private static function getCodeFragment($file, $line, $fragment = '')
   {
      if ($fragment == '' && is_file($file) && is_readable($file)) $fragment = file_get_contents($file);
      $fragment = str_replace("\r\n", "\n", $fragment);
      $lines = explode("\n", $fragment);
      $l = $line - 11;
      if ($l < 0)
      {
         $ln = 1;
         $l = 0;
      }
      else $ln = $line - 10;
      $lines = array_slice($lines, $l, 21);
      foreach ($lines as $k => &$str)
      {
         $str = htmlspecialchars(rtrim(str_pad(($k + $ln) . '.', 6, ' ') . $str));
         if ($line == $k + $ln) $str = '<b style="background-color: red; color: white;"><i>' . str_pad($str, 100, ' ',  STR_PAD_RIGHT). '</i></b>';
      }
      array_unshift($lines, $lines[] = str_repeat('-', 100));
      return '<pre style="margin-top: 0px;">' . implode("\n", $lines) . '</pre>';
   }
}

?>
