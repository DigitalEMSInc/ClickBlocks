<?php

namespace ClickBlocks\ORMTest;

use ClickBlocks\Core,
    ClickBlocks\DB,
    ClickBlocks\Exceptions;

/**
 * @group ORM 
 */ 
class ServiceTest extends \PHPUnit_Framework_TestCase
{
   const TEST_DB_0 = 'db_test_0';
   const TEST_DB_1 = 'db_test_1';
   
   protected static $db0 = null;
   protected static $db1 = null;
   protected static $cache = null;
   protected static $config = null;
   
   public static function setUpBeforeClass()
   {
      self::$config = Core\Register::getInstance()->config;
      self::$config->useCacheForDataObjects = false;
      $orm = DB\ORM::getInstance();
      
      self::$cache = Core\Register::getInstance()->cache;
      
      self::$db0 = $orm->getDB(self::TEST_DB_0);
      self::$db0->execute(file_get_contents(self::$config->root . '/Framework/_tests/ORM/data/sql/db_test_0.sql'));
            
      //self::$db1 = $orm->getDB(self::TEST_DB_1);
      //self::$db1->execute(file_get_contents($config->root . '/Framework/_tests/ORM/data/sql/db_test_1.sql'));
      
      //$orm->generateClasses();
   }
   
   public static function tearDownAfterClass()
   {
      self::$db0 = self::$db1 = null;
   }
   
   protected function setUp(){}
   
   public function testInsert()
   {
      $tb = new Customers();
      $tb->TypeID = 'M';
      $tb->FirstName = 'Sergey';
      $tb->LastName = 'Milimko';
      $tb->Email = 'smilimko@gmail.com';
      $tb->Password = 'qqwwee';
      $tb->Country = 'USA';
      $tb->Created = 'NOW()';
      foo(new ServiceCustomers())->insert($tb);
      $userID = self::$db0->col('SELECT userID FROM _test_Users WHERE userID = 1');
      $this->assertEquals(1, $userID); 
      $customerID = self::$db0->col('SELECT customerID FROM _test_Customers WHERE customerID = 1');
      $this->assertEquals(1, $customerID);           
   }
   
   public function testGetByID()
   {
      $svc = new ServiceCustomers();
      $tb = $svc->getByID(1);
      $this->assertEquals('smilimko@gmail.com', $tb->Email);
      $this->assertEquals('USA', $tb->Country);
      $tb = $svc->getByID(array('UserID' => 1));      
      $this->assertEquals('smilimko@gmail.com', $tb->Email);
      $this->assertEquals('USA', $tb->Country);  
      $tb = $svc->getByID(array('CustomerID' => 1));      
      $this->assertEquals('smilimko@gmail.com', $tb->Email);
      $this->assertEquals('USA', $tb->Country); 
      $svc = new ServiceUsers();
      $tb = $svc->getByID(1);
      $this->assertEquals('smilimko@gmail.com', $tb->Email);
      $this->assertEquals(1, $tb->UserID);        
   }
   
   /**
    * @depends testGetByID
    */       
   public function testUpdate()
   {
      $svc = new ServiceCustomers();
      $tb = $svc->getByID(1);
      $tb->Password = '1234';
      $tb->Fax = '0-1-2';
      $svc->update($tb);
      $tb = $svc->getByID(1);
      $this->assertEquals('1234', $tb->Password);
      $this->assertEquals('0-1-2', $tb->Fax);                
   }
   
   /**
    * @depends testGetByID
    */       
   public function testReplace()
   {
      $svc = new ServiceCustomers();
      $tb = $svc->getByID(1);
      $tb->Password = '00000';
      $tb->Fax = '#-#-#';
      $svc->replace($tb);
      $tb = $svc->getByID(1);
      $this->assertEquals('00000', $tb->Password);
      $this->assertEquals('#-#-#', $tb->Fax);                
   }
   
   /**
    * @depends testGetByID
    */       
   public function testSave()
   {
      $svc = new ServiceCustomers();
      $tb = new Customers();
      $tb->TypeID = 'C';
      $tb->FirstName = 'Bruce';
      $tb->LastName = 'Willis';
      $tb->Email = 'bruce_wilis@gmail.com';
      $tb->Password = '@@@';
      $tb->Country = 'USA';
      $tb->Created = 'NOW()';
      $svc->save($tb);
      $tb = $svc->getByID(2);
      $this->assertEquals('Bruce', $tb->FirstName);
      $this->assertEquals(2, $tb->CustomerID);
      $tb->Password = '%^&';
      $tb->Phone = '123456';
      $svc->save($tb);
      $tb = $svc->getByID(2);
      $this->assertEquals('%^&', $tb->Password);
      $this->assertEquals('123456', $tb->Phone);                           
   }
   
   /**
    * @depends testInsert
    * @depends testGetByID    
    */
   public function testDelete()
   {
      $svc = new ServiceCustomers();
      $tb = new Customers();
      $tb->TypeID = 'M';
      $tb->FirstName = 'Test';
      $tb->LastName = 'Man';
      $tb->Email = 'test_man@gmail.com';
      $tb->Password = '';
      $tb->Country = 'Kazakhstan';
      $tb->Created = 'NOW()'; 
      $svc->insert($tb);
      $svc->delete($tb);
      $this->assertNull($tb);
      $tb = $svc->getByID(3);
      $this->assertEquals('', $tb->CustomerID);
   }
  
   public function testCleanCache()
   {
      $key = self::$config->siteUniqueID . '_orm_bll_objects';
      DB\Service::cleanCache();
      $this->assertNull(self::$cache->get($key));
      self::$cache->set($key, 'test', 10);
      DB\Service::cleanCache();      
      $this->assertNull(self::$cache->get($key));
      self::$cache->set($key, array(), 10);
      DB\Service::cleanCache();      
      $this->assertNull(self::$cache->get($key));
      self::$cache->set($key, array(array('k' => 'v')), 10);
      DB\Service::cleanCache();      
      $this->assertNull(self::$cache->get($key));
   }
   
   /**
    * @depends testInsert
    */
   public function testInsertCache()
   {
      $svc = new ServiceCustomers();
      $tb = new Customers();
      $tb->TypeID = 'C';
      $tb->FirstName = 'Mark';
      $tb->LastName = 'Twain';
      $tb->Email = 'mark_twain@gmail.com';
      $tb->Password = '&%^&%';
      $tb->Country = 'German';
      $tb->Created = 'NOW()';
      $tb->ParentCustomerID = 1;
      $svc->cache->insert($tb);
      $objs = self::$cache->get(self::$config->siteUniqueID . '_orm_bll_objects');
      $this->assertEquals(4, end($objs[get_class($svc)]));
   }
   
   /**
    * @depends testDelete   
    * @depends testGetByID
    */
   public function testGetByIDCache()
   {
      self::$db0->delete('_test_Customers', array('customerID' => 4));
      self::$db0->delete('_test_Users', array('userID' => 4));      
      $svc = new ServiceCustomers();      
      $tb = $svc->cache->getByID(4);
      $this->assertEquals('Sergey', $tb->Parent->FirstName);
      $this->assertEquals(1, $tb->Parent->CustomerID);      
   }
   
   /**
    * @depends testUpdate
    * @depends testGetByIDCache    
    */
   public function testUpdateCache()
   {
      $svc = new ServiceCustomers();      
      $tb = $svc->cache->getByID(4);
      $tb->Fax = '0-1-2-3';
      $tb->Phone = '(111)456-30-54';
      $svc->cache->update($tb);
      $tb = $svc->cache->getByID(4);      
      $this->assertEquals('0-1-2-3', $tb->Fax);
      $this->assertEquals('(111)456-30-54', $tb->Phone);  
   }
   
   /**
    * @depends testReplace
    * @depends testGetByIDCache    
    */
   public function testReplaceCache()
   {
      $svc = new ServiceCustomers();      
      $tb = $svc->cache->getByID(1);
      $tb->Fax = '3-2-1-0';
      $tb->Phone = 'no phone';
      $svc->cache->replace($tb);
      $tb = $svc->cache->getByID(1);      
      $this->assertEquals('3-2-1-0', $tb->Fax);
      $this->assertEquals('no phone', $tb->Phone);  
   }
   
   /**
    * @depends testDelete
    * @depends testGetByIDCache         
    */                   
   public function testDeleteCache()
   {
      $svc = new ServiceCustomers();
      $tb = $svc->cache->getByID(4);
      $svc->cache->delete($tb);
      $this->assertNull($tb);
      $objs = self::$cache->get(self::$config->siteUniqueID . '_orm_bll_objects');
      $this->assertEquals(1, count($objs[get_class($svc)]));
      $this->assertEquals(1, end($objs[get_class($svc)]));
   }
   
   /**
    * @depends testUpdate
    * @depends testUpdateCache
    * @depends testGetByID        
    */       
   public function testUpdateByID()
   {
      $svc = new ServiceCustomers();
      $svc->updateByID(array('UserID' => 1), array('Password' => '$$$', 'Phone' => '8888'));
      $tb = $svc->getByID(1);
      $this->assertEquals('$$$', $tb->Password);
      $this->assertEquals('8888', $tb->Phone);
      $svc = new ServiceUsers();
      $svc->updateByID(array('UserID' => 2), array('Password' => 'tesserakt'));
      $tb = $svc->getByID(2);
      $this->assertEquals('tesserakt', $tb->Password);        
   }
   
   /**
    * @depends testUpdateByID
    * @depends testGetByIDCache        
    */       
   public function testUpdateByIDCache()
   {      
      $svc = new ServiceCustomers();
      $svc->cache->updateByID(1, array('Password' => '$$$', 'Phone' => '8888'));
      $tb = $svc->cache->getByID(1);
      $this->assertEquals('$$$', $tb->Password);
      $this->assertEquals('8888', $tb->Phone);        
   }
   
   /**
    * @depends testGetByID
    * @depends testGetByIDCache    
    */       
   public function testDeleteByID()
   {
      $svc = new ServiceCustomers();
      $svc->deleteByID(1); 
      $tb = $svc->getByID(1);
      $this->assertEquals('', $tb->CustomerID);
      $svc->cache->deleteByID(1);      
      $tb = $svc->cache->getByID(1);
      $this->assertEquals('', $tb->CustomerID);           
   }                  
}

?>