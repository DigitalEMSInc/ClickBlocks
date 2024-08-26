<?php

namespace ClickBlocks\Plugins;

use ClickBlocks\Core,
    ClickBlocks\WebForms;

class Brick extends WebForms\Panel
{
   protected static $IDs = array();

   public function __construct($id, $expire = 0)
   {
      $n = 1; $new = $id;
      while (isset(self::$IDs[$new]))
      {
         $new = $id . '_' . $n;
         $n++;
      }
      self::$IDs[$new] = true;
      parent::__construct($new, $expire);
      $this->properties['manage'] = true;
      $this->properties['plugin'] = null;
   }
}
