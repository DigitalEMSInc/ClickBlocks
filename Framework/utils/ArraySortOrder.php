<?php
/**
 * ClickBlocks.PHP v. 1.0
 *
 * Copyright (C) 2010  SARITASA LLC
 * http://www.saritasa.com
 *
 * This framework is free software. You can redistribute it and/or modify
 * it under the terms of either the current ClickBlocks.PHP License
 * (viewable at theclickblocks.com) or the License that was distributed with
 * this file.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY, without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * You should have received a copy of the ClickBlocks.PHP License
 * along with this program.
 *
 * Responsibility of this file: sortorders.php
 *
 * @category   Helper
 * @package    Core
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @link       http://www.saritasa.com
 * @since      File available since Release 1.0.0
 */

namespace ClickBlocks\Utils;

/**
 * Extends the functionality of the array as a design language.
 *
 * @category   Helper
 * @package    Core
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @version    Release: 1.0.0
 */
class ArraySortOrder implements \Iterator, \ArrayAccess, \SeekableIterator, \Countable
{
   /**
    * Some array has an arbitrary structure.
    *
    * @var    array
    * @access protected
    */
   protected $array = null;

   /**
    * The internal array pointer.
    *
    * @var    integer
    * @access protected
    */
   protected $position = 0;

   /**
    * Constructs an instance of new class.
    *
    * @param array $array
    * @access public
    */
   public function __construct(array $array = array())
   {
      $this->array = $array;
   }

   /**
    *  Returns the count of elements of an array.
    *
    *  @return integer
    *  @access public
    */
   public function count()
   {
      return count($this->array);
   }

   /**
    * Seek to position.
    *
    * @param integer $position
    * @throws OutOfBoundsException
    * @access public
    */
   public function seek($position)
   {
      $this->position = $position;
      if (!$this->valid()) throw new \Exception(err_msg('ERR_GENERAL_2', array($this->position)));
   }

   /**
    * Rewind array back to the start.
    *
    * @return boolean
    * @access public
    */
   public function rewind()
   {
      $this->position = 0;
      return true;
   }

   /**
    * Return current array entry.
    *
    * @return mixed
    * @access public
    */
   public function current()
   {
      return $this->array[$this->key()];
   }

   /**
    * Return current array key
    *
    * @return mixed
    * @access public
    */
   public function key()
   {
      $tmp = array_keys($this->array);
      return $tmp[$this->position];
   }

   /**
    * The iterator to the next entry.
    *
    * @return boolean
    * @access public
    */
   public function next()
   {
      $this->position++;
      return true;
   }

   /**
    * Checks if the array contains any more entries.
    *
    * @return boolean
    * @access public
    */
   public function valid()
   {
      return isset($this->array[$this->key()]);
   }

   /**
    * Assigns a value to the specified offset.
    *
    * @param mixed $offset
    * @param mixed $value
    * @access public
    */
   public function offsetSet($offset, $value)
   {
      $this->array[$offset] = $value;
   }

   /**
    * Whether or not an offset exists.
    *
    * @param mixed $offset
    * @return boolean
    * @access public
    */
   public function offsetExists($offset)
   {
      return isset($this->array[$offset]);
   }

    /**
     * Deletes an element of the array.
     *
     * @param mixed $offset
     * @access public
     */
   public function offsetUnset($offset)
   {
      unset($this->array[$offset]);
   }

    /**
     * Returns the value at specified offset.
     *
     * @param mixed $offset
     * @return mixed
     * @access public
     */
   public function offsetGet($offset)
   {
      return $this->array[$offset];
   }

   /**
    * Assigns new order of array elements.
    *
    * @param array $orders
    * @access public
    */
   public function setOrder(array $orders)
   {
      if (count($orders) != count($this->array)) return;
      $tmp = array();
      foreach ($orders as $v) $tmp[$v] = $this->array[$v];
      $this->array = $tmp;
      $this->rewind();
   }

   /**
    * Returns the array of sequence numbers of elements.
    *
    * @return array
    * @access public
    */
   public function getOrder()
   {
      return array_keys($this->array);
   }

   /**
    * Returns the array.
    *
    * @return array
    * @access public
    */
   public function getArray()
   {
      return $this->array;
   }

   /**
    * Deletes the last element of the array.
    *
    * @access public
    */
   public function deleteLast()
   {
      $this->delete(count($this->array) - 1);
   }

   /**
    * Deletes the first element of the array
    *
    * @access public
    */
   public function deleteFirst()
   {
      $this->delete(0);
   }

   /**
    * Deletes an element of the array that located before an element with the certain number.
    *
    * @param integer $n
    * @access public
    */
   public function deleteBefore($n)
   {
      $this->delete($n - 1);
   }

   /**
    * Deletes an element of the array that located after an element with the certain number.
    *
    * @param integer $n
    * @access public
    */
   public function deleteAfter($n)
   {
      $this->delete($n + 1);
   }

   /**
    * Deletes an element of the array with the certain number.
    *
    * @param integer $n
    * @access public
    */
   public function delete($n)
   {
      $tmp = array_keys($this->array);
      if (!isset($tmp[$n])) return false;
      $n = $tmp[$n];
      unset($this->array[$n]);
      if (!is_numeric($n)) return;
      $tmp = array();
      foreach ($this->array as $k => $v)
      {
         if (is_numeric($k) && $k > $n) $tmp[$k - 1] = $v;
         else $tmp[$k] = $v;
      }
      $this->array = $tmp;
      return true;
   }

   /**
    * Adds new element of the array into the end of it.
    *
    * @param mixed $value
    * @param mixed $key
    * @access public
    */
   public function injectLast($value, $key = null)
   {
      $this->inject(count($this->array), $value, $key);
   }

   /**
    * Adds new element of the array into the start of it.
    *
    * @param mixed $value
    * @param mixed $key
    * @access public
    */
   public function injectFirst($value, $key = null)
   {
      $this->inject(0, $value, $key);
   }

   /**
    * Adds new element of the array before other element.
    *
    * @param integer $n
    * @param mixed $value
    * @param mixed $key
    * @access public
    */
   public function injectBefore($n, $value, $key = null)
   {
      $this->inject($n - 1, $value, $key);
   }

   /**
    * Adds new element of the array after other element.
    *
    * @param integer $n
    * @param mixed $value
    * @param mixed $key
    * @access public
    */
   public function injectAfter($n, $value, $key = null)
   {
      $this->inject($n, $value, $key);
   }

   /**
    * Adds new element of the array into the certain position.
    *
    * @param integer $n
    * @param mixed $value
    * @param mixed $key
    */
   public function inject($n, $value, $key = null)
   {
      if ($n > count($this->array)) $n = count($this->array);
      if ($n < 0) $n = 0;
      $key = (is_null($key)) ? count($this->array) : $key;
      $p1 = array_slice($this->array, 0, $n, true);
      $p2 = array_slice($this->array, $n, null, true);
      $this->array = $p1 + array($key => $value) + $p2;
   }
}

?>