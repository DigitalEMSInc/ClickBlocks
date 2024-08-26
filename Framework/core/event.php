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
 * Responsibility of this file: event.php 
 * 
 * @category   Core
 * @package    Core
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @link       http://www.saritasa.com
 * @since      File available since Release 1.0.0        
 */

namespace ClickBocks\Core;

class Event implements IEvent
{
   protected $delegates = array();

   public function __construct(IDelegate $delegate)
   {
      $this->addDelegate($delegate);
   }

   public function add(IDelegate $delegate)
   {
      $this->delegates[(string)$delegate] = $delegate;
   }

   public function delete(IDelegate $delegate)
   {
      unset($this->delegates[(string)$delegate]);
   }

   public function __invoke()
   {
      return $this->call(func_get_args());
   }

   public function call(array $params)
   {
      foreach ($this->delegates as $delegate) $res = $delegate->call($params);
      return $res;
   }
}

?>
