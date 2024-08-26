<?php

namespace ClickBlocks\ORMTest;

use ClickBlocks\Core,
    ClickBlocks\Cache,
    ClickBlocks\DB,
    ClickBlocks\Exceptions;

class ServiceProducts extends \ClickBlocks\DB\Service
{
   public function __construct()
   {
      parent::__construct('\ClickBlocks\ORMTest\Products');
   }
}

?>