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
 * Responsibility of this file: iterator.php 
 * 
 * @category   Core
 * @package    Core
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @link       http://www.saritasa.com
 * @since      File available since Release 1.0.0        
 */

namespace ClickBlocks\Core;

/**
 * This class implements standart spl-interface \Iterator.
 * 
 * Класс реализует стандартный spl-интерфейс \Iterator.
 *
 * @category  Core
 * @package   Core
 * @copyright 2007-2010 SARITASA LLC <info@saritasa.com>
 * @version   Release: 1.0.0
 */
class BaseIterator implements \Iterator
{
   /**
    * Some array.
    * 
    * Некоторый массив.
    * 
    * @var array $vars
    * @access private
    */
   private $vars = array();
   
   /**
    * Constructor of this class.
    * 
    * Конструктор класса.
    * 
    * @param array $vars
    * @access public
    */
   public function __construct(array $vars) 
   {
      $this->vars = $vars;
   }

   /**
    * Resets internal array pointer to the starting point.
    * 
    * Сбрасывает внутренний указатель массива в начальную позицию.
    * 
    * @return mixed - returns the value of the first array element, or FALSE if the array is empty. 
    * @access public
    */
   public function rewind() 
   {    
      return reset($this->vars);
   }

   /**
    * Returns value of current element of an array.
    * 
    * Возвращает значение текущего элемента массива.
    * 
    * @return mixed
    * @access public
    */
   public function current() 
   {
      return current($this->vars);
   }

   /**
    * Returns key of an current array element.
    * 
    * Возвращает ключ текущего элемента массива.
    * 
    * @return mixed - returns NULL in failure case and string value otherwise.
    * @access public
    */
   public function key() 
   {
      return key($this->vars);   
   }
 
   /**
    * Returns next array element.
    * 
    * Возвращает следующий элемент массива.
    * 
    * @return mixed - returns next array element or FALSE if there are no more elements.
    * @access public
    */
   public function next() 
   {
      return next($this->vars);
   }

   /**
    * Returns TRUE if the end of array is not reached yet and FALSE otherwise.
    * 
    * Возвращает TRUE если конец массива не достигнут и FALSE в противном случае.
    * 
    * @return boolean
    * @access public
    */
   public function valid() 
   {
      return (key($this->vars) !== null);   
   }
}
