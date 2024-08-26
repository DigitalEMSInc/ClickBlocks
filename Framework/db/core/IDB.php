<?php

namespace ClickBlocks\DB;

interface IDB
{
   public function execute($sql, array $data = array(), $type = DB::DB_EXEC, $style = \PDO::FETCH_BOTH);
   public function insert($table, array $data);
   public function replace($table, array $data, $where = null);
   public function update($table, array $data, $where = null);
   public function delete($table, $where = null);
   public function row($sql, array $data = array(), $style = \PDO::FETCH_ASSOC);
   public function rows($sql, array $data = array(), $style = \PDO::FETCH_ASSOC);
   public function col($sql, array $data = array());
   public function cols($sql, array $data = array());
   public function couples($sql, array $data = array());

}
