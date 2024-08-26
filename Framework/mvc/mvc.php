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
 * Responsibility of this file: mvc.php
 *
 * @category   MVC
 * @package    MVC
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @link       http://www.saritasa.com
 * @since      File available since Release 1.0.0
 */

namespace ClickBlocks\MVC;

use ClickBlocks\Core,
    ClickBlocks\Utils,
    ClickBlocks\Web;

/**
 * This class intended for the controling of Page classes.
 *
 * Класс разработан для управления классами страниц.
 *
 * @category   MVC
 * @package    MVC
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @version    Release: 1.0.0
 * @abstract
 */
abstract class MVC
{
   /**
    * The instance of ClickBlocks\Core\Register class.
    *
    * Экземпляр класса ClickBlocks\Core\Register.
    *
    * @var object
    * @access protected
    */
   protected $reg = null;

   /**
    * The instance of URI class.
    *
    * Экземпляр класса URI.
    *
    * @var object
    * @access protected
    */
   protected $uri = null;

   /**
    * The array as a result of merging of GET, POST and FILES data.
    *
    * Массив как результат соединения массивов GET, POST и FILES.
    *
    * @var array
    * @access protected
    */
   protected $fv = null;

   /**
    * Constructs a new MVC object.
    *
    * Конструктор класса.
    *
    * @access public
    */
   public function __construct()
   {
      $this->reg = Core\Register::getInstance();
      $this->reg->fv = $this->fv = array_merge($_GET, $_POST, $_FILES);
      $this->reg->uri = $this->uri = new Utils\URI();
      if ($this->reg->config->isLocked)
      {
         $config = $this->reg->config;
         if (strpos($_SERVER['REQUEST_URI'], $config->unlockKey) !== false || $_COOKIE[md5($config->unlockKey)])
         {
            setcookie(md5($config->unlockKey), true, time() + $config->unlockKeyExpire);
         }
         else
         {
            exit((!is_file(Core\IO::dir($config->lockedPage))) ? MSG_GENERAL_0 : file_get_contents(Core\IO::dir($config->lockedPage)));
         }
      }
   }

   /**
    * Returns the instance of a Page class that associated with a page's URL.
    *
    * Возвращает экземпляр страничного класса, ассоциированного с некоторым URL.
    *
    * @return IPage
    * @access public
    * @abstract
    */
   abstract public function getPage();

   /**
    * Verifies the first time whether the request happened to a page.
    *
    * Проверяет первый ли раз была запрошена страница.
    *
    * @return boolean
    * @access public
    * @static
    */
   public static function isFirstRequest()
   {
      return (Page::isGETRequest() && !Web\Ajax::isAction());
   }

   /**
    * Launches the sequence of calls of page-class's methods.
    *
    * Запускает последовательность вызовов методов страничного класса.
    *
    * @param IPage $page
    * @access public
    */
   public function execute(IPage $page = null)
   {
      $page = ($page) ?: $this->getPage();
      if (!($page instanceof IPage)) throw new \Exception(err_msg('ERR_GENERAL_6', array(get_class($this))));
      $this->reg->page = $page;
      if (!$page->access())
      {
         if ($page->noAccessURL) Web\JS::goURL($page->noAccessURL);
         return;
      }
      if (self::isFirstRequest())
      {
         if ($page->cacheExists())
         {
            echo $page->cacheGet();
            return;
         }
         $page->preparse();
         $page->parse();
         $page->init();
         $page->load();
         $page->unload();
         $page->show();
      }
      else
      {
         $page->input();
         $page->assign();
         $page->load();
         $page->process();
         $page->unload();
         $page->redraw();
         $page->perform();
      }
   }
}

?>
