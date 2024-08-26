<?php

namespace ClickBlocks\ORMTest;

use ClickBlocks\Core,
    ClickBlocks\Cache,
    ClickBlocks\DB,
    ClickBlocks\Exceptions;

class ServiceManagers extends \ClickBlocks\DB\Service
{
   public function __construct()
   {
      parent::__construct('\ClickBlocks\ORMTest\Managers');
   }
}

?>