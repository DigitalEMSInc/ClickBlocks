<?php

namespace ClickBlocks\ORMTest;

use ClickBlocks\Core,
    ClickBlocks\Cache,
    ClickBlocks\DB,
    ClickBlocks\Exceptions;

class OrchestraStock extends \ClickBlocks\DB\Orchestra
{
   public function __construct()
   {
      parent::__construct('\ClickBlocks\ORMTest\Stock');
   }

   public function YourFunction()
   {
      // business logic goes here
      // ...
      
      // defining sql for data extracting. 
      $sql = '';
      
      // ...
      // business logic ends here
       
      return $this->getDataByProtocol($sql);
   }
}

?>