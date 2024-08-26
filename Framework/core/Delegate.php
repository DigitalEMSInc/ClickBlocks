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
 * Responsibility of this file: delegate.php
 *
 * @category   Core
 * @package    Core
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @link       http://www.saritasa.com
 * @since      File available since Release 1.0.0
 */

namespace ClickBlocks\Core;

class Delegate implements IDelegate
{
   private $callback = null;

   protected $namespace = null;
   protected $class = null;
   protected $method = null;
   protected $isStatic = false;
   protected $type = 'function';
   protected $cid = null;

   public function __construct($callback)
   {
      $this->callback = htmlspecialchars_decode($callback);
      $func = explode('::', $this->callback);
      if (count($func) > 1)
      {
         $this->class = $func[0];
         $this->method = $func[1];
         $this->type = 'static';
         $this->isStatic = true;
      }
      else
      {
         $func = explode('->', $this->callback);
         if (count($func) > 1)
         {
            $this->class = $func[0];
            $this->method = $func[1];
            $this->type = 'method';
         }
         else $this->method = $this->callback;
      }
      if ($this->type == 'function')
      {
         $k = strrpos($this->method, '\\');
         if ($k !== false) $this->namespace = substr($this->method, 0, $k + 1);
         if ($this->namespace[0] != '\\') $this->namespace = '\\' . $this->namespace;
         return;
      }
      if (!strlen($this->class))
      {
         $this->class = get_class(Register::getInstance()->page);
         $this->namespace = '\ClickBlocks\MVC\\';
      }
      else
      {
         $class = explode('@', $this->class);
         if (count($class) > 1)
         {
            $this->class = $class[0];
            $this->cid = $class[1];
            $this->type = 'control';
         }
         $k = strrpos($this->class, '\\');
         if ($k !== false) $this->namespace = substr($this->class, 0, $k + 1);
         if ($this->namespace[0] != '\\') $this->namespace = '\\' . $this->namespace;
      }
   }

   public function in($callback)
   {
      if ($this->type == 'function')
      {
         if ($callback == $this->method) return true;
         $k = strrpos($callback, '\\');
         if ($k !== false) $namespace = substr($callback, 0, $k + 1);
         if ($this->namespace == $namespace && $namespace == $callback) return true;
      }
      else
      {
         $data = explode($this->isStatic ? '::' : '->', $callback);
         if ($this->class == $data[0] && $this->method == $data[1]) return true;
         $k = strrpos($data[0], '\\');
         if ($k !== false) $namespace = substr($data[0], 0, $k + 1);
         if ($this->namespace == $namespace && $namespace == $callback || $data[1] == '' && $data[0] == $this->class) return true;
      }
      return false;
   }

   public function __invoke()
   {
      return $this->call(func_get_args());
   }

   public function call(array $params)
   {
      switch ($this->type)
      {
         case 'function':
           return call_user_func_array($this->method, $params);
         case 'static':
           return call_user_func_array(array($this->class, $this->method), $params);
         case 'method':
           if (get_class(Register::getInstance()->page) == $this->class) $class = Register::getInstance()->page;
           else
           {
              $class = new \ReflectionClass($this->class);
              if (is_array($params['construct']))
              {
                 $class = $class->newInstanceArgs($params['construct']);
                 unset($params['construct']);
                 $params = array_pop($params);
              }
              else $class = $class->newInstance();
           }
           return call_user_func_array(array($class, $this->method), $params);
         case 'control':
           $ctrl = Register::getInstance()->page->getByUniqueID($this->cid);
           if (!$ctrl) $ctrl = Register::getInstance()->page->get($this->cid);
           if ($ctrl === false) throw new \Exception(err_msg('ERR_EVN_1', array($this->cid)));
           if ($this->isStatic) return call_user_func_array(array($this->class, $this->method), $params);
           return call_user_func_array(array($ctrl, $this->method), $params);
      }
   }

   public function __toString()
   {
      return $this->callback;
   }
}

?>
