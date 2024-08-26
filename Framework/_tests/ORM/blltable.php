<?php                

namespace ClickBlocks\ORMTest;

use ClickBlocks\Core,
    ClickBlocks\DB,
    ClickBlocks\Exceptions;

/**
 * @group ORM 
 */ 
class BLLTableTest extends \PHPUnit_Framework_TestCase
{
   const TEST_DB_0 = 'db_test_0';
   const TEST_DB_1 = 'db_test_1';
   
   protected static $db0 = null;
   protected static $db1 = null;
   
   public static function setUpBeforeClass()
   {
      $config = Core\Register::getInstance()->config;
      $config->useCacheForDataObjects = false;
      $orm = DB\ORM::getInstance();
      
      self::$db0 = $orm->getDB(self::TEST_DB_0);
      self::$db0->execute(file_get_contents($config->root . '/Framework/_tests/ORM/data/sql/db_test_0.sql'));
            
      //self::$db1 = $orm->getDB(self::TEST_DB_1);
      //self::$db1->execute(file_get_contents($config->root . '/Framework/_tests/ORM/data/sql/db_test_1.sql'));
      
      //$orm->generateClasses();
   }
   
   public static function tearDownAfterClass()
   {
      self::$db0 = self::$db1 = null;
   }
   
   protected function setUp(){}  
       
   public function testGetDAL()
   {
      $tb = new Customers();
      $this->assertEquals('ClickBlocks\ORMTest\DALCustomers', get_class($tb->getDAL('ClickBlocks\ORMTest\Customers')));
      $this->assertEquals('ClickBlocks\ORMTest\DALCustomers', get_class($tb->getDAL()));
      $this->assertEquals('ClickBlocks\ORMTest\DALUsers', get_class($tb->getDAL('ClickBlocks\ORMTest\Users')));      
   }
   
   /**
    * @depends testGetDAL
    */       
   public function testGetField()
   {
      $tb = new Customers();
      $this->assertEquals($tb->getDAL('ClickBlocks\ORMTest\Customers')->getField('CustomerID'), $tb->getField('CustomerID'));
      $this->assertEquals($tb->getDAL('ClickBlocks\ORMTest\Users')->getField('UserID'), $tb->getField('UserID'));  
   }
   
   /**   
    * @expectedException ClickBlocks\Exceptions\NotExistingPropertyException
    */  
   public function testSetNotExistingField()
   {
      $tb = new Customers();
      $tb->foo = 1;  
   }
   
   /**   
    * @expectedException ClickBlocks\Exceptions\NotExistingPropertyException
    */ 
   public function testGetNotExistingField()
   {
      $tb = new DALUsers();
      $foo = $tb->foo;
   }
   
   public function testIsSetField()
   {
      $tb = new Customers();
      $this->assertTrue(isset($tb->UserID));
      $this->assertTrue(isset($tb->CustomerID));
      $this->assertTrue(isset($tb->FullName));
      $this->assertFalse(isset($tb->Customers));          
      $this->assertFalse(isset($tb->foo));
   }
   
   /**
    * @depends testGetField
    */       
   public function testSetExistingField()
   {
      $tb = new Customers();
      $tb->CustomerID = 1;
      $tb->TypeID = 'C';
      $tb->FirstName = 'Bruce';
      $tb->LastName = 'Willis'; 
      $field = $tb->getField('CustomerID');
      $this->assertEquals(1, $field['value']);
      $field = $tb->getField('TypeID');
      $this->assertEquals('C', $field['value']);
      $field = $tb->getField('FirstName');
      $this->assertEquals('Bruce', $field['value']);
      $field = $tb->getField('LastName');
      $this->assertEquals('Willis', $field['value']);        
   }
   
   /**   
    * @depends testSetExistingField   
    */
   public function testGetExistingField()
   {
      $tb = new Customers();
      $tb->CustomerID = 1;
      $tb->TypeID = 'C';
      $tb->FirstName = 'Bruce';
      $tb->LastName = 'Willis';  
      $this->assertEquals(1, $tb->CustomerID);
      $this->assertEquals('C', $tb->TypeID);
      $this->assertEquals('Bruce', $tb->FirstName);
      $this->assertEquals('Willis', $tb->LastName);   
   }
   
   /**
    * @depends testSetExistingField    
    */
   public function testGetValues()
   {
      $tb = new Customers();
      $tb->CustomerID = $tb->UserID = 1;
      $tb->TypeID = 'C';
      $tb->Phone = '(701)501-47-43';
      $tb->Fax = 123;
      $tb->Country = 'USA';
      $tb->FirstName = 'Bruce';
      $tb->LastName = 'Willis';
      $tb->Email = 'bruce_willis@gmail.com';
      $tb->Password = 'nuts'; 
      $this->assertEquals(array('UserID' => 1, 
                                'TypeID' => 'C', 
                                'FirstName' => 'Bruce', 
                                'LastName' => 'Willis', 
                                'Email' => 'bruce_willis@gmail.com', 
                                'Password' => 'nuts',
                                'Created' => '',
                                'Updated' => '',
                                'Deleted' => '',
                                'LastActivity' => '',
                                'CustomerID' => 1,
                                'Phone' => '(701)501-47-43', 
                                'Fax' => 123, 
                                'Country' => 'USA',
                                'ParentCustomerID' => ''), $tb->getValues(false));  
   }
   
   /**
    * @depends testSetExistingField    
    */
   public function testGetRawValues()
   {
      $tb = new Customers();
      $tb->CustomerID = $tb->UserID = 1;
      $tb->TypeID = 'C';
      $tb->Phone = '(701)501-47-43';
      $tb->Fax = 123;
      $tb->Country = 'USA';
      $tb->FirstName = 'Bruce';
      $tb->LastName = 'Willis';
      $tb->Email = 'bruce_willis@gmail.com';
      $tb->Password = 'nuts'; 
      $this->assertEquals(array('userID' => 1, 
                                'typeID' => 'C', 
                                'firstName' => 'Bruce', 
                                'lastName' => 'Willis', 
                                'email' => 'bruce_willis@gmail.com', 
                                'password' => 'nuts',
                                'created' => '',
                                'updated' => '',
                                'deleted' => '',
                                'lastActivity' => '',
                                'customerID' => 1,
                                'phone' => '(701)501-47-43', 
                                'fax' => 123, 
                                'country' => 'USA',
                                'parentCustomerID' => ''), $tb->getValues(true));  
   }
   
   /**
    * @depends testGetValues
    */       
   public function testSetValues()
   {
      $values = array('UserID' => 1, 
                      'TypeID' => 'C', 
                      'FirstName' => 'Bruce', 
                      'LastName' => 'Willis', 
                      'Email' => 'bruce_willis@gmail.com', 
                      'Password' => 'nuts',
                      'Created' => '',
                      'Updated' => '',
                      'Deleted' => '',
                      'LastActivity' => '',
                      'CustomerID' => 1,
                      'Phone' => '(701)501-47-43', 
                      'Fax' => 123, 
                      'Country' => 'USA',
                      'ParentCustomerID' => '');
      $tb = new Customers();
      $tb->setValues($values, false);
      $this->assertEquals($values, $tb->getValues(false));
   }
   
   /**
    * @depends testGetRawValues
    */       
   public function testSetRawValues()
   {
      $values = array('userID' => 1, 
                      'typeID' => 'C', 
                      'firstName' => 'Bruce', 
                      'lastName' => 'Willis', 
                      'email' => 'bruce_willis@gmail.com', 
                      'password' => 'nuts',
                      'created' => '',
                      'updated' => '',
                      'deleted' => '',
                      'lastActivity' => '',
                      'customerID' => 1,
                      'phone' => '(701)501-47-43', 
                      'fax' => 123, 
                      'country' => 'USA',
                      'parentCustomerID' => '');
      $tb = new Customers();
      $tb->setValues($values, true);
      $this->assertEquals($values, $tb->getValues(true));
   }
   
   /**
    * @depends testGetRawValues
    */       
   public function testAssign()
   {
      $values = array('userID' => 1, 
                      'typeID' => 'C', 
                      'firstName' => 'Bruce', 
                      'lastName' => 'Willis', 
                      'email' => 'bruce_willis@gmail.com', 
                      'password' => 'nuts',
                      'created' => date('Y-m-d H:i:s'),
                      'updated' => '',
                      'deleted' => '',
                      'lastActivity' => '');
      $tb = new Users();
      $tb->assign($values);
      $this->assertEquals($values, $tb->getValues(true));
   }
   
   /**
    * @depends testAssign
    */  
   public function testAssignMapping()
   {
      $values = array('userID' => 2, 
                      'typeID' => 'C', 
                      'firstName' => 'Bruce', 
                      'lastName' => 'Willis', 
                      'email' => 'bruce_willis@gmail.com', 
                      'password' => 'nuts',
                      'created' => date('Y-m-d H:i:s'),
                      'updated' => '',
                      'deleted' => '',
                      'lastActivity' => '',
                      'customerID' => 2,
                      'phone' => '+1(701)501-47-43', 
                      'fax' => null, 
                      'country' => 'USA',
                      'parentCustomerID' => '');
      $tb = new Customers();
      $tb->assign($values);
      $this->assertEquals($values, $tb->getValues(true));
   } 
   
   /**   
    * @depends testSetExistingField   
    */
   public function testInsertWithSimpleKey()
   {
      $tb = new Users();
      $tb->TypeID = 'C';
      $tb->FirstName = 'Bruce';
      $tb->LastName = 'Willis';
      $tb->Email = 'bruce_willis@gmail.com';
      $tb->Password = 'nuts'; 
      $tb->Created = 'NOW()';
      foo(new ServiceUsers())->insert($tb);
      $this->assertEquals(1, self::$db0->affectedRows); 
   }
   
   /**
    * @depends testAssign
    */       
   public function testUpdate()
   {
      $svc = new ServiceUsers();
      $values = array('userID' => 1, 
                      'typeID' => 'C', 
                      'firstName' => 'Bruce', 
                      'lastName' => 'Willis', 
                      'email' => 'bruce_willis@gmail.com', 
                      'password' => 'nuts',
                      'created' => date('Y-m-d H:i:s'),
                      'updated' => '',
                      'deleted' => '',
                      'lastActivity' => '');
      $tb = new Users();
      $tb->assign($values);
      $tb->Password = 1; 
      self::$db0->affectedRows = 0;
      $svc->update($tb);
      $this->assertEquals(1, self::$db0->affectedRows);
      self::$db0->affectedRows = 0;
      $svc->update($tb);
      $this->assertEquals(0, self::$db0->affectedRows);
   }  
   
   /**
    * @depends testAssign
    */  
   public function testReplace()
   {
      $svc = new ServiceUsers();
      $values = array('userID' => 1, 
                      'typeID' => 'C', 
                      'firstName' => 'Bruce', 
                      'lastName' => 'Willis', 
                      'email' => 'bruce_willis@gmail.com', 
                      'password' => 'nuts',
                      'created' => date('Y-m-d H:i:s'),
                      'updated' => '',
                      'deleted' => '',
                      'lastActivity' => '');
      $tb = new Users();
      $tb->assign($values);
      $tb->Password = 'qwerty';
      $svc->replace($tb);
      $this->assertEquals(2, self::$db0->affectedRows);  
   }
   
   /**
    * @depends testAssign
    */ 
   public function testDelete()
   {
      $svc = new ServiceUsers();
      $values = array('userID' => 1, 
                      'typeID' => 'C', 
                      'firstName' => 'Bruce', 
                      'lastName' => 'Willis', 
                      'email' => 'bruce_willis@gmail.com', 
                      'password' => 'nuts',
                      'created' => date('Y-m-d H:i:s'),
                      'updated' => '',
                      'deleted' => '',
                      'lastActivity' => '');
      $tb = new Users();
      $tb->assign($values);
      $svc->delete($tb);
      $this->assertEquals(1, self::$db0->affectedRows);        
   }
   
   /**
    * @depends testInsertWithSimpleKey
    * @depends testGetExistingField    
    */
   public function testInsertMappingWithSimpleKey()
   {
      $tb = new Customers();
      $tb->TypeID = 'C';
      $tb->FirstName = 'Bruce';
      $tb->LastName = 'Willis';
      $tb->Email = 'bruce_willis@gmail.com';
      $tb->Password = 'nuts'; 
      $tb->Created = 'NOW()';
      $tb->Phone = '+1(701)501-47-43';
      $tb->Country = 'USA';
      $svc = new ServiceCustomers();
      $svc->insert($tb);
      $this->assertEquals(2, $tb->CustomerID);
      $this->assertEquals(2, $tb->UserID);   
   }
   
   /**
    * @depends testInsertMappingWithSimpleKey
    * @covers BLLTable::update    
    */       
   public function testUpdateMappingWithSimpleKey()
   {
      $svc = new ServiceCustomers();
      $tb = new Customers();
      $tb->TypeID = 'M';
      $tb->FirstName = 'Sergey';
      $tb->LastName = 'Milimko';
      $tb->Email = 'smilimko@gmail.com';
      $tb->Password = 'root'; 
      $tb->Created = 'NOW()';
      $tb->Phone = '+7(701)501-47-43';
      $tb->Country = 'Kazakhstan';
      $tb->ParentCustomerID = 2;
      $svc->insert($tb);
      self::$db0->affectedRows = 0;
      $svc->update($tb);
      $this->assertEquals(0, self::$db0->affectedRows);
      $tb->Fax = '50-60'; 
      self::$db0->affectedRows = 0;
      $svc->update($tb);        
      $this->assertEquals(1, self::$db0->affectedRows);
   }
   
   /**
    * @depends testInsertMappingWithSimpleKey
    * @covers BLLTable::replace    
    */ 
   public function testReplaceMappingWithSimplyKey()
   {
      $svc = new ServiceCustomers();
      $tb = new Customers();
      $tb->TypeID = 'M';
      $tb->FirstName = 'Vasya';
      $tb->LastName = 'Pupkin';
      $tb->Email = 'pupkin@gmail.com';
      $tb->Password = 'pupok'; 
      $tb->Created = 'NOW()';
      $tb->Phone = '+7(701)111-22-33';
      $tb->ParentCustomerID = 'NULL';
      $tb->Country = 'Kazakhstan';
      $tb->ParentCustomerID = 2;
      $svc->insert($tb);
      self::$db0->affectedRows = 0;
      $svc->update($tb); 
      $this->assertEquals(0, self::$db0->affectedRows);
      $tb->Fax = '00-00'; 
      self::$db0->affectedRows = 0;
      $svc->replace($tb);         
      $this->assertEquals(2, self::$db0->affectedRows);  
   }
   
   /**
    * @depends testAssign
    */       
   public function testDeleteMappingWithSimplyKey()
   {
      $values = self::$db0->row('SELECT u.*, c.* FROM _test_Users AS u INNER JOIN _test_Customers AS c ON c.customerID = u.userID WHERE u.userID = 4');
      $tb = new Customers();
      $tb->assign($values);
      $svc = new ServiceCustomers();
      self::$db0->affectedRows = 0;
      $svc->delete($tb);
      $this->assertEquals(1, self::$db0->affectedRows);   
   }
   
   /**
    * @depends testAssign
    */       
   public function testGetNavigationPropertyForNonMultiplicity()
   {
      $params = self::$db0->row('SELECT * FROM _test_Users WHERE userID = 2');  
      $values = self::$db0->row('SELECT u.*, c.* FROM _test_Users AS u INNER JOIN _test_Customers AS c ON c.customerID = u.userID WHERE u.userID = 2');    
      $tb = new Users();
      $tb->assign($params);
      $params = $tb->Customers[0]->getValues(true);
      $this->assertEquals($values, $params);
   }
   
   /**
    * @depends testInsertMappingWithSimpleKey
    * @depends testGetNavigationPropertyForNonMultiplicity    
    */       
   public function testSetNavigationPropertyForNonMultiplicity()
   {
      $manager = new Managers();
      $manager->ContractSigned = 'NOW()';
      $manager->Salary = 1000;
      $tb = new Customers();
      $tb->TypeID = 'M';
      $tb->FirstName = 'Vasya';
      $tb->LastName = 'Pupkin';
      $tb->Email = 'pupkin@gmail.com';
      $tb->Password = 'pupok'; 
      $tb->Created = 'NOW()';
      $tb->Phone = '+7(701)111-22-33';
      $tb->Country = 'Kazakhstan';
      $tb->Managers = $manager;
      $this->assertEquals($tb->Managers->getValues(false), $manager->getValues(false));         
   }
   
   /**
    * @depends testSetNavigationPropertyForNonMultiplicity
    * @depends testInsertMappingWithSimpleKey  
    */       
   public function testInsertNavigationPropertyForNonMultiplicity()
   {
      $manager = new Managers();
      $manager->ContractSigned = 'NOW()';
      $manager->Salary = 1000;
      $tb = new Customers();
      $tb->TypeID = 'M';
      $tb->FirstName = 'Vasya';
      $tb->LastName = 'Pupkin';
      $tb->Email = 'pupkin@gmail.com';
      $tb->Password = 'pupok'; 
      $tb->Created = 'NOW()';
      $tb->Phone = '+7(701)111-22-33';
      $tb->Country = 'Kazakhstan';
      $tb->Managers = $manager;
      $tb->ParentCustomerID = 2;
      $svc = new ServiceCustomers();
      $svc->save($tb);
      $this->assertEquals(5, $tb->UserID);
      $this->assertEquals($tb->UserID, $tb->Managers->ManagerID);
   }
   
   /**
    * @depends testAssign
    */       
   public function testUpdateNavigationPropertyForNonMultiplicity()
   {
      $values = self::$db0->row('SELECT u.*, c.* FROM _test_Users AS u INNER JOIN _test_Customers AS c ON c.customerID = u.userID WHERE u.userID = 5');
      $tb = new Customers();
      $tb->assign($values);
      $tb->Managers->Salary = 1111;
      self::$db0->affectedRows = 0;
      $svc = new ServiceCustomers();
      $svc->save($tb);
      $this->assertEquals(1, self::$db0->affectedRows);
      $values = self::$db0->row('SELECT * FROM _test_Managers WHERE managerID = 5');
      $this->assertEquals(1111, $values['salary']);  
   }
   
   /**
    * @depends testAssign
    */       
   public function testReadNavigationPropertyForNonMultiplicity()
   {
      $values = self::$db0->row('SELECT u.*, c.* FROM _test_Users AS u INNER JOIN _test_Customers AS c ON c.customerID = u.userID WHERE u.userID = 5');
      $tb = new Customers();
      $tb->assign($values);
      $values = self::$db0->row('SELECT * FROM _test_Managers WHERE managerID = 5');      
      $this->assertEquals($values, $tb->Managers->getValues(true));      
   }
   
   /**
    * @depends testAssign
    */
   public function testSerializationUnserialization()
   {
      $values = self::$db0->row('SELECT u.*, c.* FROM _test_Users AS u INNER JOIN _test_Customers AS c ON c.customerID = u.userID WHERE u.userID = 5');
      $tb = new Customers();
      $tb->assign($values);
      $new = serialize($tb);
      $new = unserialize($new);
      $this->assertEquals($tb->getValues(), $new->getValues());
      $this->assertEquals($tb->Managers->getValues(), $new->Managers->getValues());
      $this->assertEquals($tb->Parent->getValues(), $new->Parent->getValues());     
      $this->assertEquals($tb->Parent->Children[0]->getValues(), $new->Parent->Children[0]->getValues());
      $this->assertEquals($tb->Parent->Children[1]->getValues(), $new->Parent->Children[1]->getValues());     
   }    
   
   /**
    * @depends testAssign
    */       
   public function testDeleteNavigationPropertyForNonMultiplicity()
   {
      $values = self::$db0->row('SELECT u.*, c.* FROM _test_Users AS u INNER JOIN _test_Customers AS c ON c.customerID = u.userID WHERE u.userID = 5');
      $tb = new Customers();
      $tb->assign($values);
      unset($tb->Managers[0]);
      $svc = new ServiceCustomers();
      $svc->save($tb);
      $values = self::$db0->row('SELECT * FROM _test_Managers WHERE managerID = 5');
      $this->assertEquals(array(), $values); 
   }
   
   /**
    * @depends testAssign
    */
   public function testReadNavigationPropertyForMultiplicity()
   {
      $values = self::$db0->row('SELECT * FROM _test_Categories WHERE categoryID = 8');
      $tb = new Categories();
      $tb->assign($values);
      $this->assertEquals('Cars', $tb->Parent->Name);
      $this->assertEquals(4, count($tb->Parent->Categories));
   }
   
   /**
    * @depends testAssign
    */
   public function testReadMappingNavigationPropertyForNonMultiplicity()
   {
      $values = self::$db0->row('SELECT u.*, c.* FROM _test_Users AS u INNER JOIN _test_Customers AS c ON c.customerID = u.userID WHERE u.userID = 5');
      $tb = new Customers();
      $tb->assign($values);
      $this->assertEquals('bruce_willis@gmail.com', $tb->Parent->Email);  
   }
   
   /**
    * @depends testAssign
    */
   public function testReadMappingNavigationPropertyForMultiplicity()
   {
      $values = self::$db0->row('SELECT u.*, c.* FROM _test_Users AS u INNER JOIN _test_Customers AS c ON c.customerID = u.userID WHERE u.userID = 2');
      $tb = new Customers();
      $tb->assign($values);
      $this->assertEquals(2, count($tb->Children));
   }
   
   /**
    * @depends testAssign
    * @expectedException \LogicException    
    */       
   public function testAttempToReadNavigationProperty()
   {
      $values = self::$db0->row('SELECT * FROM _test_Users WHERE userID = 2');
      $tb = new Users();
      $tb->assign($values);
      $foo = $tb->LookupUserTypes->TypeID;    
   }  
   
   /**
    * @depends testAssign
    * @expectedException \LogicException    
    */       
   public function testAttempToInsertNavigationProperty()
   {
      $values = self::$db0->row('SELECT * FROM _test_Users WHERE userID = 2');
      $tb = new Users();
      $tb->assign($values);
      $type = new LookupUserTypes();
      $type->TypeID = 'A';
      $type->TypeName = 'Admin';
      $tb->LookupUserTypes = $type;     
   }
   
   /**
    * @depends testAssign
    * @expectedException \LogicException    
    */       
   public function testAttempToDeleteNavigationProperty()
   {
      $values = self::$db0->row('SELECT * FROM _test_Users WHERE userID = 2');
      $tb = new Users();
      $tb->assign($values);
      unset($tb->LookupUserTypes[0]);     
   }
   
   /**
    * @depends testAssign
    * @expectedException \LogicException    
    */       
   public function testAttempToUpdateNavigationProperty()
   {
      $values = self::$db0->row('SELECT * FROM _test_Users WHERE userID = 2');
      $tb = new Users();
      $tb->assign($values);
      $tb->Customers->Country = 'Canada';     
   }
   
   /**
    * @depends testAssign
    */       
   public function testReadNavigationPropertyInRawFormat()
   {
      $values = self::$db0->row('SELECT * FROM _test_LookupUserTypes WHERE typeID = \'M\'');
      $tb = new LookupUserTypes();
      $tb->assign($values);
      $this->assertEquals('Sergey', $tb->Users[0]['firstName']);
      $this->assertEquals('Vasya', $tb->Users[1]['firstName']);
      $this->assertEquals(2, count($tb->Users()));
      $values = self::$db0->row('SELECT * FROM _test_LookupUserTypes WHERE typeID = \'C\'');
      $tb = new LookupUserTypes();
      $tb->assign($values);
      $this->assertEquals('Bruce', $tb->Users->firstName);              
   }
   
   /**
    * @depends testAssign
    */
   public function testSetNavigationPropertyInRawFormat()
   {
      $values = self::$db0->row('SELECT * FROM _test_LookupUserTypes WHERE typeID = \'M\'');
      $tb = new LookupUserTypes();
      $tb->assign($values);
      $values = array('userID' => 1, 
                      'typeID' => 'C', 
                      'firstName' => 'Test', 
                      'lastName' => 'Man', 
                      'email' => 'bruce_willis@gmail.com', 
                      'password' => 'nuts',
                      'created' => date('Y-m-d H:i:s'),
                      'updated' => '',
                      'deleted' => '',
                      'lastActivity' => '');
      $tb->Users = $values;
      $tb->Users = $values;
      $tb->Users[] = $values;
      $this->assertEquals(5, count($tb->Users()));
      $this->assertEquals(5, count($tb->Users));        
   }                           
}

?>