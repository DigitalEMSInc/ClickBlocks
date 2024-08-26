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
 * Responsibility of this file: simpleton.php 
 * 
 * @category   Helper
 * @package    Core
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @link       http://www.saritasa.com
 * @since      File available since Release 1.0.0        
 */ 

namespace ClickBlocks\Utils;

/**
 * Simpleton intended for transformation any array into simple object.
 *
 * @category   Helper 
 * @package    Core
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @version    Release: 1.0.0
 */
class Simplex implements \Iterator, \ArrayAccess, \SeekableIterator, \Countable
{
   /**
    * The array of properties of the class
    * 
    * @var    array
    * @access protected            
    */       
   protected $properties = array();
   
   
   /**
    * The position of the object property when iterating through a cycle of its properties.
    * 
    * @var    integer 
    * @access protected           
    */       
   protected $position = 0;
   
   /**
    * Constructs an object from an array.
    * 
    * @param array $properties
    * @access public            
    */       
   public function __construct(array $properties)
   {
      foreach ($properties as $key => $property)
      { 
         if (is_array($property)) $this->properties[$key] = new self($property);
         else $this->properties[$key] = $property;
      }
   }
   
   /**
    * Returns a value of a property of the class.
    * 
    * @param string $param
    * @return mixed
    * @throws ClickBlocks\Exceptions\NotExistingPropertyException      
    * @access public                
    */       
   public function __get($param)
   {
      if (isset($this->properties[$param])) return $this->properties[$param];
      throw new \Exception(err_msg('ERR_GENERAL_3', array($param, get_class($this)))); 
   }
   
   /**
    * Assigns a value of certain property of the class.
    *  
    * @param string $param
    * @param mixed $value    
    * @throws ClickBlocks\Exceptions\NotExistingPropertyException   
    * @access public                
    */       
   public function __set($param, $value)
   {
      if (isset($this->properties[$param])) $this->properties[$param] = $value;
      else throw new \Exception(err_msg('ERR_GENERAL_3', array($param, get_class($this))));  
   }
   
   /**
    * Validates whether or not there is a property of the class. 
    *
    * @param string $param
    * @return boolean
    * @access public           
    */           
   public function __isset($param)
   {
      return isset($this->properties[$param]);
   }
   
   /**
    * Returns the number of properties of the class.
    * 
    * @return integer
    * @access public            
    */       
   public function count()
   {
      return count($this->properties);
   }
   
   /**
    * Moves the internal pointer of an array of properties to new position.
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
    * Resets the internal pointer of an array of properties to zero.
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
    * Returns a value of the current property of the class.
    *
    * @return mixed
    * @access public        
    */           
   public function current() 
   {                       
      return $this->properties[$this->key()];
   }

   /**
    * Returns a key of the current element of the property array.
    *
    * @return mixed
    * @access public        
    */           
   public function key() 
   {                          
      $tmp = array_keys($this->properties);
      return $tmp[$this->position];  
   }

   /**
    * Moves forward to next element of the property array.
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
    * Checks if current position is valid.
    * 
    * @return boolean
    * @access public            
    */       
   public function valid() 
   {                         
      return isset($this->properties[$this->key()]);   
   }
   
   /**
    * Assigns a value of property to the specified offset (key).
    * 
    * @param mixed $offset
    * @param mixed $value
    * @access public                 
    */       
   public function offsetSet($offset, $value) 
   {                       
      $this->properties[$offset] = $value;
   }
   
   /**
    * Whether or not a property exists.
    * 
    * @param mixed $offset 
    * @return boolean
    * @access public                 
    */       
   public function offsetExists($offset) 
   {                            
      return isset($this->properties[$offset]);
   }
    
   /**
    * Deletes a property. 
    * 
    * @param mixed $offset
    * @return boolean
    * @access public                 
    */         
   public function offsetUnset($offset) 
   {                            
      unset($this->properties[$offset]);
   }
    
   /**
    * Returns the value of some property at specified offset (key).
    * 
    * @param mixed $offset 
    * @return mixed
    * @access public                
    */       
   public function offsetGet($offset) 
   {                    
      return $this->properties[$offset];
   }
   
   /**
    * Returns an array of the properties of the class.
    * 
    * @return array
    * @access public             
    */       
   public function getArray()
   {
      return $this->properties;
   }
}

?>