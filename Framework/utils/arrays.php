<?php

namespace ClickBlocks\Core;

class Arrays
{
   public static function swap(array &$array, $key1, $key2)
   {
      $keys = array_flip(array_keys($array));
      return array_merge(array_slice($array, 0, $keys[$key1]), array_reverse(array_slice($array, $keys[$key1], $keys[$key2])) ,array_slice($array, $keys[$key2]));
   }
}

?>