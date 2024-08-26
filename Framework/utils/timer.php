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
 * @category   Helper
 * @package    Core
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @link       http://www.saritasa.com
 * @since      File available since Release 1.0.0
 */

namespace ClickBlocks\Utils;

/**
 * @property array $times Array of times
 */
class Timer 
{
   private $timers = array();
   private $times = array();
   
   public function __construct() { }
      
   public function __get($name) {
      if ($name == 'times') return $this->getTimes();
      return null;
   }

   /**
    * Start new timer; Timers work in stack, eg.:<br/> 
    * $timer->start('All program');<br/> 
    * &nbsp;$timer->Start('Load data');<br/> 
    * &nbsp;&nbsp;$timer->Start('Select from DB');<br/> 
    * &nbsp;&nbsp;$timer->Stop();<br/> 
    * &nbsp;&nbsp;$timer->Start('Format data');<br/> 
    * &nbsp;&nbsp;$timer->Stop();<br/> 
    * &nbsp;$timer->Stop(); // measures 'Load data' time <br/> 
    * &nbsp;$timer->Start(...);<br/> 
    * &nbsp;$timer->Stop();<br/> 
    * timerStop();
    * @uses microtime(true)
    * @param string  $descr  text message to be associated with timer
    */
   public function start($descr = 'Some action') 
   {
       $this->timers[] = array('time'=>  microtime(true), 'desc'=>$descr);
   }

   /**
    * Stops current (last in stack) timer, measures time interval and puts it in $this->timers[$description] = $seconds;
    */
   public function stop() 
   {
      end($this->timers);
      $key = key($this->timers);
      if ($key === NULL)
        return;
      $timer = $this->timers[$key];
      $elapsed = round(microtime(true) - $timer['time'], 5);
      $this->times[$timer['desc']] = $elapsed;
      unset($this->timers[$key]);
   }
   
   public function getTimes()
   {
      return $this->times;
   }
}

?>
