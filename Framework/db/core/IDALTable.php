<?php

namespace ClickBlocks\DB;

interface IDALTable
{
   public function __set($field, $value);
   public function __get($field);
   public function save();
   public function insert();
   public function replace();
   public function update();
   public function delete();
}
