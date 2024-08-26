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
 * Responsibility of this file: progress.php 
 * 
 * @category   Helper
 * @package    Core
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @link       http://www.saritasa.com
 * @since      File available since Release 1.0.0        
 */

namespace ClickBlocks\Core;

/**
 * Provides a periodic call to the callback function when uploading a file through the extension APC 
 * as well as receive information on file that is uploaded.
 *
 * @category   Helper 
 * @package    Core
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @version    Release: 1.0.0
 */
class Progress
{
   /**
    * The instance of the Ajax class
    * 
    * @var    Ajax
    * @access protected            
    */       
   protected $ajax = null;
   
   /**
    * The array of information of uploading file.
    * 
    * @var    array
    * @access public           
    */       
   public $info = null;
   
   /**
    * Constructs a new Progress class.
    * 
    * @param string $key  unique identifier of uploading file.
    * @access public           
    */       
   public function __construct($key)
   {   
      $this->info = apc_fetch(ini_get('apc.rfc1867_prefix') . $key);
      $this->ajax = Ajax::getInstance();
   }
   
   /**
    * Returns the percent of uploading of the file.
    * 
    * @return float
    * @access public            
    */       
   public function getPercent()
   {
      if ($this->isCompleted()) return 100;
      if ($this->info['total'] > 0) return $this->info['current'] * 100 / $this->info['total'];
      return 100;
   }
   
   /**
    * Returns the APC cache information.
    * 
    * @return array
    * @access public             
    */       
   public function getCacheInfo()
   {
      return apc_cache_info();      
   }
   
   /**
    * Checks whether or not the uploading has completed.
    * 
    * @return boolean
    * @access public            
    */       
   public function isCompleted()
   {
      return ($this->isUploaded() || $this->info['current'] == $this->info['total']);
   }
   
   /**
    * Checks whether or not the uploading has canceled.
    * 
    * @return boolean
    * @access public            
    */  
   public function isCanceled()
   {
      return ($this->info['cancel_upload']); 
   }
   
   /**
    * Checks whether or not the uploading has comleted or canceled.
    * 
    * @return boolean
    * @access public            
    */       
   public function isFinished()
   {
      return ($this->isCompleted() || $this->isCanceled());
   }
   
   /**
    * Checks whether or not the uploading has uploaded successfuly.
    * 
    * @return boolean
    * @access public            
    */   
   public function isUploaded()
   {
      return $this->info['done'];
   }
   
   /**
    * Invokes a callback method for the computing of the uploading's progress.
    * 
    * @param string $id
    * @param string $callback
    * @return Progress
    * @access public                    
    */       
   public function doNext($id, $callback)
   {      
      if (!$this->isFinished()) $this->ajax->addAction('script', "ajax.progress('" . $id . "', '" . $callback . "')");
      return $this;
   }
}

?>