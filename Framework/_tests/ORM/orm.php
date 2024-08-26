<?php

use ClickBlocks\Core,
    ClickBlocks\DB,
    ClickBlocks\Exceptions;

/**
 * @group ORM 
 */ 
class ORMTest extends PHPUnit_Framework_TestCase
{
   const DB_ALIAS = 'db0';
   
   protected static $orm = null;   
   protected static $db = null;
   
   public static function setUpBeforeClass()
   {
      $path = Core\Register::getInstance()->config->root . '/../artifacts/ORM ER DB/';
      self::$orm = DB\ORM::getInstance();
      self::$db = self::$orm->restoreDB(self::DB_ALIAS, $path . 'create.sql', $path . 'drop.sql')->getDB(self::DB_ALIAS);
   }
   
   public static function tearDownAfterClass()
   {
      self::$orm = self::$db = null;
   }
   
   public function testGenerateXML()
   {
      self::$orm->generateXML('ClickBlocks\\ORM');
      $this->assertXmlFileEqualsXmlFile(Core\Register::getInstance()->config->dir('engine') . '/db.xml', Core\Register::getInstance()->config->root . '/Framework/_tests/ORM/data/db_standard.xml');
   }
   
   public function test
}

?>