<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;
use Doctrine\DBAL\Driver\PDOConnection;

/**
 * @property SQLBuilder $sql
 * @property $pdo
 */
class DB implements IDB
{
   const DB_EXEC = 0;
   const DB_COLUMN = 1;
   const DB_COLUMNS = 2;
   const DB_ROW = 3;
   const DB_ROWS = 4;
   const DB_COUPLE = 5;

   private $pdo = null;
   private $sql = null;
   private $dsn = array();
   private $error = array();

   protected $reg = null;
   protected $cachedSQL = array();
   protected static $statistic = array();

   public $expire = 900;
   public $cached = false;
   public $catchException = false;
   public $affectedRows = null;

   public function __construct()
   {
      $this->reg = Core\Register::getInstance();
   }

   public function __get($param)
   {
      if ($param == 'sql') return $this->sql;
      if ($param == 'pdo') return $this->pdo;
      throw new \Exception(err_msg('ERR_GENERAL_3', array($param, get_class($this))));
   }

   public function connect($dsn, $username, $password, $options = null)
   {
      $this->parseDSN($dsn);
   
      /**
       * I used Laravel PDO instead of Clickblocks own PDO,
       * to have one common DB connection instead of couple of connections.
       *
       * @var PDOConnection $pdo
       */
      $pdo = \DB::connection()->getPdo();
      $this->pdo = $pdo;
      $this->sql = new SQLBuilder($this->dsn);
   }

   public function disconnect()
   {
      $this->pdo = $this->sql = null;
      $this->dsn = array();
   }

   public function getDataBaseName()
   {
      return $this->dsn['dbname'];
   }

   public function getHost()
   {
      return $this->dsn['host'];
   }

   public function getPort()
   {
      return $this->dsn['port'];
   }

   public function getEngine()
   {
      return $this->dsn['engine'];
   }

   public function getDSN()
   {
      return $this->dsn;
   }

   public function getLastError()
   {
      return $this->error;
   }

   public static function getStatistic()
   {
      return self::$statistic;
   }

   public function wrap($str)
   {
      return $this->sql->wrap($str);
   }

   public function quote($str)
   {
      return $this->pdo->quote($str);
   }

   public function execute($sql, array $data = array(), $type = self::DB_EXEC, $style = \PDO::FETCH_BOTH)
   {
      if ($this->cached && $this->reg->cache instanceof Cache\ICache && $type != self::DB_EXEC)
      {
         $key = strtr($sql, $data);
         $flag = $this->parseCachedSQL($sql);
         if ($flag && $this->reg->cache->isExists($key)) return $this->reg->cache->get($key);
      }
      $logger = Core\Logger::getInstance();
      $logger->pStart('db_sql_log');
   
      $oldAttributes = [
          \PDO::ATTR_CASE => $this->pdo->getAttribute(\PDO::ATTR_CASE),
          \PDO::ATTR_ERRMODE => $this->pdo->getAttribute(\PDO::ATTR_ERRMODE),
          \PDO::ATTR_ORACLE_NULLS => $this->pdo->getAttribute(\PDO::ATTR_ORACLE_NULLS),
          \PDO::ATTR_EMULATE_PREPARES => $this->pdo->getAttribute(\PDO::ATTR_EMULATE_PREPARES),
          \PDO::ATTR_STATEMENT_CLASS => $this->pdo->getAttribute(\PDO::ATTR_STATEMENT_CLASS),
      ];
      $setAttributes = [
          \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
          \PDO::ATTR_ERRMODE => \PDO::ERRMODE_SILENT,
          \PDO::ATTR_ORACLE_NULLS => \PDO::NULL_NATURAL,
          \PDO::ATTR_EMULATE_PREPARES => true,
          \PDO::ATTR_STATEMENT_CLASS => [\PDOStatement::class],
      ];
      foreach ($setAttributes as $attribute => $value) {
         if ($value !== $oldAttributes[$attribute]) {
            $this->pdo->setAttribute($attribute, $value);
         }
      }
      $st = $this->pdo->prepare($sql);
      if (!$st->execute($data))
      {
         $this->error = $st->errorInfo();
         if ($this->catchException) throw new \Exception($this->error[2]);
         else
         {
            self::$statistic[$this->dsn['dsn']][] = array('sql' => $sql, 'data' => $data, 'type' => $type, 'style' => $style, 'time' => $time, 'datetime' => date('Y-m-d H:i:s'));
            Core\Debugger::exceptionHandler(new \Exception($this->error[2]), Core\Logger::LOG_CATEGORY_SQL_EXCEPTION);
            exit;
         }
      }
      foreach ($oldAttributes as $attribute => $value) {
         if ($value !== $setAttributes[$attribute]) {
            $this->pdo->setAttribute($attribute, $value);
         }
      }
      $this->affectedRows = $st->rowCount();
      $time = $logger->pStop('db_sql_log');
      try {throw new \Exception();}
      catch (\Exception $e) {$stack = $e->getTraceAsString();}
      self::$statistic[$this->dsn['dsn']][] = array('sql' => $sql, 'data' => $data, 'type' => $type, 'style' => $style, 'time' => $time, 'datetime' => date('Y-m-d H:i:s'), 'affectedRows' => $this->affectedRows, 'stack' => $stack);
      switch ($type)
      {
         case self::DB_EXEC:
           $res = $this->affectedRows;
           break;
         case self::DB_COLUMN:
           $res = $st->fetchColumn();
           if (is_array($res)) $res = end($res);
           break;
         case self::DB_COLUMNS:
           $res = array(); while ($row = $st->fetch($style)) $res[] = array_shift($row);
           break;
         case self::DB_ROW:
           $res = $st->fetch($style);
           if ($res === false) $res = array();
           break;
         case self::DB_ROWS:
           $res = $st->fetchAll($style);
           break;
         case self::DB_COUPLE:
           $rows = $st->fetchAll(\PDO::FETCH_NUM); $res = array();
           if (is_array($rows[0])) foreach ($rows as $v) $res[$v[0]] = $v[1];
           break;
      }
      if ($this->cached && $this->reg->cache instanceof Cache\ICache && $type != self::DB_EXEC && $flag) $this->reg->cache->set($key, $res, $this->expire);
      return $res;
   }

   public function insert($table, array $data)
   {
      $this->execute($this->sql->insert($table, $data), $data, self::DB_EXEC);
      if ($this->getEngine() == 'mssql') return $this->col('SELECT @@IDENTITY');
      return $this->pdo->lastInsertId();
   }

   public function update($table, array $data, $where = null)
   {
      return $this->execute($this->sql->update($table, $data, $where), is_string($where) ? $data : array_merge($data, (array)$where), self::DB_EXEC);
   }

   public function replace($table, array $data, $where = null)
   {
      return $this->execute($this->sql->replace($table, $data, $where), is_string($where) ? $data : array_merge($data, (array)$where), self::DB_EXEC);
   }

   public function delete($table, $where = null)
   {
      return $this->execute($this->sql->delete($table, $where), (array)$where, self::DB_EXEC);
   }

   public function col($sql, array $data = array())
   {
      return $this->execute($sql, $data, self::DB_COLUMN);
   }

   public function cols($sql, array $data = array())
   {
      return $this->execute($sql, $data, self::DB_COLUMNS);
   }

   public function row($sql, array $data = array(), $style = \PDO::FETCH_ASSOC)
   {
      return $this->execute($sql, $data, self::DB_ROW, $style);
   }

   public function rows($sql, array $data = array(), $style = \PDO::FETCH_ASSOC)
   {
      return $this->execute($sql, $data, self::DB_ROWS, $style);
   }

   public function couples($sql, array $data = array())
   {
      return $this->execute($sql, $data, self::DB_COUPLE);
   }

   public function getFields($table)
   {
      $rows = $this->rows($this->sql->getSQL('ShowFields', $table));
      switch ($this->getEngine())
      {
         case 'mssql':
           return $rows;
         case 'mysql':
           $fields = array();
           foreach ($rows as $row)
           {
              preg_match('/.+\(([\d\w,\']+)\)/U', $row['Type'], $arr);
              $field = $row['Field'];
              $fields[$field]['Field'] = $row['Field'];
              $fields[$field]['PK'] = intval(($row['Key'] == 'PRI'));
              $fields[$field]['isNullable'] = (int)($row['Null'] != 'NO');
              $fields[$field]['isAutoIncrement'] = ($row['Extra'] == 'auto_increment') ? 1 : null;
              $fields[$field]['Type'] = $type = preg_replace('/\([\d\w,\']+\)/U', '', $row['Type']);
              $fields[$field]['DefaultValue'] = ($type == 'bit') ? substr($row['Default'], 2, 1) : $row['Default'];
              $fields[$field]['MaxLength'] = 0;
              $fields[$field]['Precision'] = 0;
              if (substr($type, -8) == 'unsigned')
              {
                 $fields[$field]['Type'] = trim(substr($type, 0, -8));
                 $fields[$field]['isUnsigned'] = 1;
              }
              else $fields[$field]['isUnsigned'] = 0;
              $arr = explode(',', $arr[1]);
              if ($type == 'enum' || $type == 'set') $fields[$field]['Set'] = $arr;
              else
              {
                 $fields[$field]['Set'] = null;
                 if (count($arr) == 1) $fields[$field]['MaxLength'] = $arr[0];
                 else
                 {
                    $fields[$field]['MaxLength'] = $arr[0];
                    $fields[$field]['Precision'] = $arr[1];
                 }
              }
           }
           return $fields;
      }
   }

   public function getTables()
   {
      return $this->cols($this->sql->getSQL('ShowTables'));
   }

   public function getCreateOperator($table)
   {
      $row = $this->row($this->sql->getSQL('ShowCreateOperator', $table), array(), \PDO::FETCH_NUM);
      return $row[1];
   }

   public function getDataBases()
   {
      return $this->cols($this->sql->getSQL('ShowDataBases'));
   }

   public function createTable($name, array $fields, array $pk = null, $engine = null, $charset = 'utf8')
   {
      return $this->execute($this->sql->getSQL('CreateTable', array($name, $fields, $pk, $engine, $charset)));
   }

   public function addField($table, array $params)
   {
      return $this->execute($this->sql->getSQL('AddField', array($table, $params)));
   }

   public function deleteField($table, $field)
   {
      return $this->execute($this->sql->getSQL('DeleteField', array($table, $field)));
   }

   public function changeField($table, $field, array $params)
   {
      return $this->execute($this->sql->getSQL('ChangeField', array($table, $field, $params)));
   }

   public function addSQL($sql, $isCached = true)
   {
      $this->cachedSQL[$sql] = $isCached;
   }

   public function deleteSQL($sql)
   {
      unset($this->cachedSQL[$sql]);
   }
   public function lastInsertId()
   {
      return $this->pdo->lastInsertId();
   }

   protected function parseCachedSQL(&$sql)
   {
      foreach ($this->cachedSQL as $s => $isCached)
      {
         $flag = preg_match('@' . $s . '@isU', $sql);
         if ($isCached) if ($flag) {$sql = $s; return true;}
         else if ($flag) return false;
      }
      return true;
   }

   private function parseDSN($dsn)
   {
      $this->dsn = array('dsn' => $dsn);
      $dsn = explode(':', $dsn);
      $this->dsn['engine'] = ($dsn[0] == 'dblib') ? 'mssql' : $dsn[0];
      $dsn = explode(';', $dsn[1]);
      foreach ($dsn as $v)
      {
         $v = explode('=', $v);
         $this->dsn[strtolower(trim($v[0]))] = trim($v[1]);
      }
   }
   
}

?>
