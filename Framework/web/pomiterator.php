<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core;

class POMIterator implements \Iterator
{
   private $controls = array();
   private $page = null;

   public function __construct(array $controls)
   {
      $this->controls = ($controls) ? array_combine($controls, $controls) : array();
      $this->page = Core\Register::getInstance()->page;
   }

   public function rewind()
   {
      return $this->page->getByUniqueID(reset($this->controls));
   }

   public function current()
   {
      return $this->page->getByUniqueID(current($this->controls));
   }

   public function key()
   {
      return key($this->controls);
   }

   public function next()
   {
      return $this->page->getByUniqueID(next($this->controls));
   }

   public function valid()
   {
      return (key($this->controls) !== null);
   }
}

?>
