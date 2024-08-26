<?php

namespace ClickBlocks\DB;

interface IService
{
   public function getByID($pk = null, $expire = null);
   public function updateByID($pk, array $values);
   public function deleteByID($pk);
   public function getOrchestra($protocol = Orchestra::PROTOCOL_RAW);
   public function save($tb, $expire = null);
   public function insert($tb, $expire = null);
   public function replace($tb);
   public function update($tb);
   public function delete(&$tb);
}
