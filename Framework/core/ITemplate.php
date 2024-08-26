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
 * Responsibility of this file: template.php
 *
 * @category   Core
 * @package    Core
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @link       http://www.saritasa.com
 * @since      File available since Release 1.0.0
 */

namespace ClickBlocks\Core;

/**
 * Interface for classes are able to work with templates.
 *
 * Интерфейс для классов, умеющих работать с шаблонами.
 *
 * @category  Core
 * @package   Core
 * @copyright 2007-2010 SARITASA LLC <info@saritasa.com>
 * @version   Release: 1.0.0
 */
interface ITemplate extends \ArrayAccess, \IteratorAggregate, \Countable
{
   /**
    * Adds new variable in the current template.
    *
    * Добавляет переменную в текущий шаблон.
    *
    * @param string $name - variable name.
    * @param mixed $value - variable value.
    * @access public
    */
   public function __set($name, $value);

   /**
    * Returns variable value of the current template.
    *
    * Получает значение переменной текущего шаблона.
    *
    * @param string $name - variable name.
    * @return mixed
    * @access public
    */
   public function &__get($name);

   /**
    * Sets new tenplate.
    *
    * Устанавливает новый шаблон.
    *
    * @param string $key      - unique identifier of a template.
    * @param string $template
    * @param string $parent   - unique identifier if a parent template.
    * @param string $var      - name of php-variable in the parent template.
    * @access public
    */
   public function setTemplate($key, $template, $parent = null, $var = null);

   /**
    * Returns an array with information about a template.
    * Format of the array following: array('template' => ..., 'parent' => ..., 'sibs' => array(...), 'var' => ...)
    *
    * Возвращает массив с информацией о шаблоне.
    * Формат массива следующий: array('template' => ..., 'parent' => ..., 'sibs' => array(...), 'var' => ...)
    *
    * @param string $key - unique identifier of a template.
    * @return string
    * @access public
    */
   public function getTemplate($key);

   /**
    * Deletes a template.
    *
    * Удаляет шаблон.
    *
    * @param string $key - unique identifier of a template.
    * @access public
    */
   public function deleteTemplate($key);

   /**
    * Removes current template variables.
    *
    * Удаляет переменные текущего шаблона.
    *
    * @access public
    */
   public function clean();

   /**
    * Returns a rendered template.
    *
    * Возвращает отрендеренный шаблон.
    *
    * @param string $key - unique identifier of a template.
    * @return string
    * @access public
    */
   public function render($key = null);

   /**
    * Push a rendered template to a browser.
    *
    * Выдаёт отрендеренный шаблон в браузер.
    *
    * @param string $key - unique identifier of a template.
    * @access public
    */
   public function show($key = null);
}

?>
