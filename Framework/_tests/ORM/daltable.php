<?php

namespace ClickBlocks\ORMTest;

use ClickBlocks\Core,
    ClickBlocks\DB,
    ClickBlocks\Exceptions;

/**
 * @group ORM 
 */ 
class DALTableTest extends \PHPUnit_Framework_TestCase
{
   const TEST_DB_0 = 'db_test_0';
   
   protected static $db = null;
   
   public static function setUpBeforeClass()
   {      
      $orm = DB\ORM::getInstance();
      self::$db = $orm->getDB(self::TEST_DB_0);
      self::$db->execute(file_get_contents(Core\Register::getInstance()->config->root . '/Framework/_tests/ORM/data/sql/db_test_0.sql'));
   }
   
   public static function tearDownAfterClass()
   {
      self::$db = null;
   }
   
   protected function setUp(){}
   
   public function testGetSimplePrimaryKey()
   {      
      $tb = new DALUsers();
      $this->assertEquals('UserID', $tb->getKey(false));
   }
   
   public function testGetComplexPrimaryKey()
   {      
      $tb = new DALOrderProducts();
      $this->assertEquals(array('OrderID', 'ProductID', 'SupplierID'), $tb->getKey(false));
   }
   
   public function testGetSimplePrimaryKeyAlias()
   {      
      $tb = new DALUsers();
      $this->assertEquals('userID', $tb->getKey(true));
   }
   
   public function testGetComplexPrimaryKeyAlias()
   {      
      $tb = new DALOrderProducts();
      $this->assertEquals(array('orderID', 'productID', 'supplierID'), $tb->getKey(true));
   }
    
   /**   
    * @expectedException ClickBlocks\Exceptions\NotExistingPropertyException
    */   
   public function testSetNotExistingField()
   {
      $tb = new DALUsers();
      $tb->foo = true;
   }
   
   /**   
    * @expectedException ClickBlocks\Exceptions\NotExistingPropertyException
    */ 
   public function testGetNotExistingField()
   {
      $tb = new DALUsers();
      $foo = $tb->foo;
   }
   
   /**   
    * @expectedException ClickBlocks\Exceptions\IncorrectValueException
    */ 
   public function testSetIncorrectValues()
   {
      $tb = new DALUsers();
      $tb->UserID = new \StdClass();      
   }
   
   public function testIsSetField()
   {
      $tb = new DALUsers();
      $this->assertTrue(isset($tb->UserID));
      $this->assertTrue(isset($tb->FullName));   
      $this->assertFalse(isset($tb->foo));
   }
   
   public function testIsNumericField()
   {
      $tb = new DALDataTypes();
      $this->assertTrue($tb->isNumericField('ID'));
      $this->assertTrue($tb->isNumericField('tpTINYINT'));
      $this->assertTrue($tb->isNumericField('tpSMALLINT'));
      $this->assertTrue($tb->isNumericField('tpMEDIUMINT'));
      $this->assertTrue($tb->isNumericField('tpBIGINT'));
      $this->assertTrue($tb->isNumericField('tpBIT'));
      $this->assertTrue($tb->isNumericField('tpYEAR'));
      $this->assertTrue($tb->isNumericField('ID'));      
      $this->assertTrue($tb->isNumericField('tpFLOAT'));
      $this->assertTrue($tb->isNumericField('tpDOUBLE')); 
      $this->assertTrue($tb->isNumericField('tpDECIMAL'));
      $this->assertTrue($tb->isNumericField('tpBOOLEAN'));     
   }
   
   public function testIsTextField()
   {
      $tb = new DALDataTypes();
      $this->assertTrue($tb->isTextField('tpCHAR'));
      $this->assertTrue($tb->isTextField('tpVARCHAR'));
      $this->assertTrue($tb->isTextField('tpTINYBLOB'));
      $this->assertTrue($tb->isTextField('tpBLOB'));
      $this->assertTrue($tb->isTextField('tpMEDIUMBLOB'));
      $this->assertTrue($tb->isTextField('tpLONGBLOB'));
      $this->assertTrue($tb->isTextField('tpTINYTEXT'));
      $this->assertTrue($tb->isTextField('tpTEXT'));
      $this->assertTrue($tb->isTextField('tpMEDIUMTEXT'));
      $this->assertTrue($tb->isTextField('tpLONGTEXT'));
      $this->assertTrue($tb->isTextField('tpENUM'));
      $this->assertTrue($tb->isTextField('tpSET')); 
      $this->assertTrue($tb->isTextField('tpBINARY'));
      $this->assertTrue($tb->isTextField('tpVARBINARY'));                 
   }
   
   public function testIsDateOrTimeField()
   {
      $tb = new DALDataTypes();
      $this->assertTrue($tb->isDateOrTimeField('tpDATE'));
      $this->assertTrue($tb->isDateOrTimeField('tpTIME'));
      $this->assertTrue($tb->isDateOrTimeField('tpDATETIME'));
      $this->assertTrue($tb->isDateOrTimeField('tpTIMESTAMP'));        
   }
   
   public function testGetLength()
   {
      $tb = new DALDataTypes();
      $this->assertEquals(11, $tb->getLength('ID'));
      $this->assertEquals(4, $tb->getLength('tpTINYINT'));
      $this->assertEquals(6, $tb->getLength('tpSMALLINT'));
      $this->assertEquals(9, $tb->getLength('tpMEDIUMINT'));
      $this->assertEquals(20, $tb->getLength('tpBIGINT'));
      $this->assertEquals(9, $tb->getLength('tpFLOAT'));
      $this->assertEquals(15, $tb->getLength('tpDOUBLE'));
      $this->assertEquals(11, $tb->getLength('tpDECIMAL'));
      $this->assertEquals(1, $tb->getLength('tpBIT'));
      $this->assertEquals(1, $tb->getLength('tpBINARY'));
      $this->assertEquals(4, $tb->getLength('tpYEAR'));
      $this->assertEquals(0, $tb->getLength('tpTEXT'));
   }
   
   public function testGetPrecision()
   {
      $tb = new DALDataTypes();
      $this->assertEquals(3, $tb->getPrecision('tpFLOAT'));
      $this->assertEquals(3, $tb->getPrecision('tpDOUBLE'));
      $this->assertEquals(0, $tb->getPrecision('tpDECIMAL'));
   }
 
   public function testSetExistingField()
   {
      $tb = new DALUsers();
      $tb->UserID = 1;
      $tb->TypeID = 'C';
      $tb->FirstName = 'Sergey';
      $tb->LastName = 'Milimko'; 
      $field = $tb->getField('UserID');
      $this->assertEquals(1, $field['value']);
      $field = $tb->getField('TypeID');
      $this->assertEquals('C', $field['value']);
      $field = $tb->getField('FirstName');
      $this->assertEquals('Sergey', $field['value']);
      $field = $tb->getField('LastName');
      $this->assertEquals('Milimko', $field['value']);        
   }
   
   /**   
    * @depends testSetExistingField   
    */
   public function testGetExistingField()
   {
      $tb = new DALUsers();
      $tb->UserID = 1;
      $tb->TypeID = 'C';
      $tb->FirstName = 'Sergey';
      $tb->LastName = 'Milimko';  
      $this->assertEquals(1, $tb->UserID);
      $this->assertEquals('C', $tb->TypeID);
      $this->assertEquals('Sergey', $tb->FirstName);
      $this->assertEquals('Milimko', $tb->LastName);   
   }
   
   /**
    * @depends testSetExistingField    
    */
   public function testGetValues()
   {
      $tb = new DALCustomers();
      $tb->CustomerID = 1;
      $tb->Phone = '(701)501-47-43';
      $tb->Fax = 123;
      $tb->Country = 'USA';
      $this->assertEquals(array('CustomerID' => 1, 'Phone' => '(701)501-47-43', 'Fax' => 123, 'Country' => 'USA', 'ParentCustomerID' => ''), $tb->getValues(false));  
   }
   
   /**
    * @depends testGetValues
    * @covers DALTable::setValues        
    */
   public function testSetValues()
   {
      $values = array('CustomerID' => 1, 'Phone' => '(701)501-47-43', 'Fax' => 123, 'Country' => 'USA', 'ParentCustomerID' => '');
      $tb = new DALCustomers();
      $tb->setValues($values, false);
      $this->assertEquals($values, $tb->getValues(false));    
   }
   
   /**
    * @depends testSetExistingField    
    */
   public function testGetRawValues()
   {
      $tb = new DALCustomers();
      $tb->CustomerID = 1;
      $tb->Phone = '(701)501-47-43';
      $tb->Fax = 123;
      $tb->Country = 'USA';
      $this->assertEquals(array('customerID' => 1, 'phone' => '(701)501-47-43', 'fax' => 123, 'country' => 'USA', 'parentCustomerID' => ''), $tb->getValues(true));  
   }
   
   /**
    * @depends testGetRawValues
    * @covers DALTable::setValues        
    */
   public function testSetRawValues()
   {
      $values = array('customerID' => 1, 'phone' => '(701)501-47-43', 'fax' => 123, 'country' => 'USA', 'parentCustomerID' => '');
      $tb = new DALCustomers();
      $tb->setValues($values, true);
      $this->assertEquals($values, $tb->getValues(true));    
   }
   
   /**
    * @depends testSetExistingField    
    */
   public function testGetSimpleKeyValue()
   {
      $tb = new DALUsers();
      $tb->UserID = 1;
      $this->assertEquals(1, $tb->getKeyValue(false));  
   }
   
   /**
    * @depends testSetExistingField    
    */
   public function testGetComplexKeyValue()
   {
      $tb = new DALOrderProducts();
      $tb->OrderID = 1;
      $tb->ProductID = 2;
      $tb->SupplierID = 3;
      $this->assertEquals(array('OrderID' => 1, 'ProductID' => 2, 'SupplierID' => 3), $tb->getKeyValue(false));
   }
   
   /**
    * @depends testSetExistingField    
    */
   public function testGetSimpleKeyRawValue()
   {
      $tb = new DALUsers();
      $tb->UserID = 1;
      $this->assertEquals(1, $tb->getKeyValue(true));  
   }
   
   /**
    * @depends testSetExistingField    
    */
   public function testGetComplexKeyRawValue()
   {
      $tb = new DALOrderProducts();
      $tb->OrderID = 1;
      $tb->ProductID = 2;
      $tb->SupplierID = 3;
      $this->assertEquals(array('orderID' => 1, 'productID' => 2, 'supplierID' => 3), $tb->getKeyValue(true));
   }
   
   /**
    * @depends testGetSimpleKeyValue
    */       
   public function testSetSimpleKeyValue()
   {
      $tb = new DALUsers();
      $tb->setKeyValue(5);
      $this->assertEquals(5, $tb->getKeyValue(false));  
   }
   
   /**
    * @depends testGetComplexKeyValue
    */
   public function testSetComplexKeyValue()
   {
      $tb = new DALOrderProducts();
      $tb->setKeyValue(array(1, 2, 3));
      $this->assertEquals(array('OrderID' => 1, 'ProductID' => 2, 'SupplierID' => 3), $tb->getKeyValue(false));
      $tb->setKeyValue(array('OrderID' => 2, 'ProductID' => 3, 'SupplierID' => 1));
      $this->assertEquals(array('OrderID' => 2, 'ProductID' => 3, 'SupplierID' => 1), $tb->getKeyValue(false));              
   }
   
   /**
    * @dataProvider providerIsKeyFilled
    * @depends testSetValues        
    */
   public function testIsKeyFilledFalse($values)
   {
      $tb = new DALOrderProducts();
      $tb->setValues((array)$values, false);
      $this->assertFalse($tb->isKeyFilled()); 
   }
   
   public function providerIsKeyFilled()
   {
      return array(array(array('OrderID' => 1, 'ProductID' => 2)),
                   array(array('OrderID' => 1, 'SupplierID' => 3)),
                   array(array('ProductID' => 2, 'SupplierID' => 3)),
                   array(array('SupplierID' => 3)),
                   array(array('OrderID' => 1)),
                   array(array('ProductID' => 2)),
                   array(array()));
   }
   
   /**
    * @depends testSetValues             
    */
   public function testIsKeyFilledTrue()
   {
      $tb = new DALOrderProducts();
      $tb->setValues(array('OrderID' => 1, 'ProductID' => 2, 'SupplierID' => 3), false);
      $this->assertTrue($tb->isKeyFilled()); 
   }
   
   /**
    * @expectedException \LogicException
    * @depends testSetExistingField   
    */       
   public function testAttemptToInsertWithFilledAutoincrementSimpleKey()
   {
      $tb = new DALUsers();
      $tb->UserID = 1;
      $tb->insert();
   }
   
   /**
    * @depends testAttemptToInsertWithFilledAutoincrementSimpleKey  
    */       
   public function testInsertWithAutoincrementSimpleKey()
   {
      $tb = new DALUsers();
      $tb->TypeID = 'M';
      $tb->FirstName = 'Sergey';
      $tb->LastName = 'Milimko';
      $tb->Email = 'smilimko@gmail.com';
      $tb->Password = 'qwerty';
      $tb->Created = 'NOW()';
      $this->assertEquals(1, $tb->insert()); 
   }
   
   /**   
    * @expectedException \LogicException
    * @depends testSetExistingField  
    */
   public function testAttemptToInsertWithNotFilledSimpleKey()
   {
      $tb = new DALCustomers();
      $this->assertEquals(1, $tb->insert());    
   }  
    
   /**
    * @depends testAttemptToInsertWithNotFilledSimpleKey
    */            
   public function testInsertWithNonAutoincrementSimpleKey()
   {
      $tb = new DALCustomers();
      $tb->CustomerID = 1;
      $tb->Phone = '+7(701)501-47-43';
      $tb->Country = 'Kazakhstan';
      $this->assertEquals(1, $tb->insert()); 
   }
   
   /**   
    * @expectedException \LogicException
    * @depends testSetExistingField  
    */
   public function testAttemptToInsertWithNotFilledComlexKey()
   {
      $tb = new DALStock();
      $tb->ProductID = 1;
      $tb->insert();    
   }
   
   /**
    * @depends testAttemptToInsertWithNotFilledComlexKey
    * @depends testInsertWithNonAutoincrementSimpleKey 
    * @depends testInsertWithAutoincrementSimpleKey        
    */       
   public function testInsertWithComplexKey()
   {
      $man = new DALManagers();
      $man->ManagerID = 1;
      $man->ContractSigned = 'NOW()';
      $man->Salary = 1000;
      $man->insert();
      $prod = new DALProducts();
      $prod->Name = 'BatCar';
      $prod->Description = 'The Batman\'s Car';
      $prod->CategoryID = 2;
      $prod->ManagerID = $man->ManagerID;
      $prod->insert();
      $stock = new DALStock();
      $stock->ProductID = $prod->ProductID;
      $stock->SupplierID = 2;
      $stock->Quantity = 10;
      $stock->Price = 999.99;       
      $this->assertEquals(1, $stock->insert());         
   }
   
   /**
    * @expectedException \LogicException   
    * @depends testInsertWithAutoincrementSimpleKey
    */         
   public function testAttemptToRewriteAutoincrementField()
   {
      $tb = new DALUsers();
      $tb->TypeID = 'C';
      $tb->FirstName = 'Bruce';
      $tb->LastName = 'Wayne';
      $tb->Email = 'bruce_wayne@gmail.com';
      $tb->Password = 'pass';
      $tb->Created = 'NOW()';
      $tb->insert();
      $tb->UserID = 1;  
   }
   
   /**
    * @depends testSetExistingField   
    * @depends testIsKeyFilledTrue     
    */
   public function testUpdateWithSimpleKey()
   {
      $tb = new DALUsers();
      $tb->TypeID = 'C';
      $tb->FirstName = 'John';
      $tb->LastName = 'Wayne';
      $tb->Email = 'john_wayne@gmail.com';
      $tb->Password = 'password';
      $tb->Created = 'NOW()';
      $tb->insert();
      $tb->Password = 'secret';
      $this->assertEquals(1, $tb->update());
      $this->assertEquals(0, $tb->update());  
   }
   
   /**
    * @depends testUpdateWithSimpleKey
    */       
   public function testUpdateWithComplexKey()
   {
      $tb = new DALStock();
      $tb->ProductID = 1;
      $tb->SupplierID = 1;
      $tb->Quantity = 5;
      $tb->Price = 0.99;
      $tb->insert();
      $tb->Price = 1.99;
      $this->assertEquals(1, $tb->update());
      $this->assertEquals(0, $tb->update());
   }
   
   /**
    * @depends testSetExistingField   
    * @depends testIsKeyFilledTrue               
    */
   public function testReplaceWithSimpleKey()
   {
      $tb = new DALUsers();
      $tb->UserID = 3;
      $tb->TypeID = 'C';
      $tb->FirstName = 'John';
      $tb->LastName = 'Wayne';
      $tb->Email = 'john_wayne@yahoo.com';
      $tb->Password = 'secret';
      $tb->Created = 'NOW()';
      $this->assertEquals(2, $tb->replace());  
   }
   
   /**
    * @depends testSetExistingField   
    * @depends testIsKeyFilledTrue                
    */
   public function testReplaceWithComplexKey()
   {
      $tb = new DALStock();
      $tb->ProductID = 1;
      $tb->SupplierID = 1;
      $tb->Quantity = 5;
      $tb->Price = 1.11;
      $this->assertEquals(2, $tb->replace());  
   }
   
   /**
    * @depends testIsKeyFilledTrue    
    * @depends testInsertWithAutoincrementSimpleKey
    * @depends testInsertWithNonAutoincrementSimpleKey  
    * @depends testInsertWithComplexKey
    * @depends testUpdateWithSimpleKey
    * @depends testUpdateWithComplexKey                      
    */
   public function testSaveRecord()
   {
      $tb = new DALStock();
      $tb->ProductID = 1;
      $tb->SupplierID = 3;
      $tb->Quantity = 2;
      $tb->Price = 0.05;
      $this->assertEquals(1, $tb->save());
      $this->assertEquals(0, $tb->save());
      $tb->Price = 10.05;
      $this->assertEquals(1, $tb->save());
      $this->assertEquals(0, $tb->save());            
   }
   
   /**
    * @depends testSetExistingField     
    * @depends testIsKeyFilledTrue               
    */
   public function testDeleteWithSimpleKey()
   {
      $tb = new DALUsers();
      $tb->UserID = 3;
      $this->assertEquals(1, $tb->delete());  
   }
   
   /**
    * @depends testSetExistingField    
    * @depends testIsKeyFilledTrue              
    */
   public function testDeleteRecordWithComplexKey()
   {
      $tb = new DALStock();
      $tb->ProductID = 1;
      $tb->SupplierID = 3;
      $this->assertEquals(1, $tb->delete());  
   }
                       
   public function testGetAll()
   {
      $tb = new DALStock();
      $this->assertEquals(array(array('productID' => 1, 'supplierID' => 1, 'quantity' => 5, 'price' => 1.11), 
                                array('productID' => 1, 'supplierID' => 2, 'quantity' => 10, 'price' => 999.99)), $tb->getAll());
      $this->assertEquals(array(array('productID' => 1, 'supplierID' => 2, 'quantity' => 10, 'price' => 999.99), 
                                array('productID' => 1, 'supplierID' => 1, 'quantity' => 5, 'price' => 1.11)), $tb->getAll(null, 'productID DESC'));
      $this->assertEquals(array(array('productID' => 1, 'supplierID' => 2, 'quantity' => 10, 'price' => 999.99)), $tb->getAll(null, null, '1, 1'));
      $this->assertEquals(array(array('productID' => 1, 'supplierID' => 1, 'quantity' => 5, 'price' => 1.11)), $tb->getAll('quantity = 5', 'price ASC', '1'));
   }
   
   /**
    * @depends testGetAll
    * @covers DALTable::deleteAll    
    */       
   public function testDeleteAll()
   {
      $tb = new DALStock();
      $tb->deleteAll();
      $this->assertEquals(array(), $tb->getAll());    
   }
      
   /**
    * @depends testGetExistingField      
    */
   public function testGetLogicProperty()
   {
      $tb = new DALUsers();
      $tb->FirstName = 'Sergey';
      $tb->LastName = 'Milimko';
      $this->assertEquals('Sergey Milimko', $tb->FullName);    
   }
   
   /**
    * @depends testGetExistingField      
    */
   public function testSetLogicProperty()
   {
      $tb = new DALUsers();
      $tb->FullName = 'Sergey Milimko';
      $this->assertEquals('Sergey', $tb->FirstName);
      $this->assertEquals('Milimko', $tb->LastName);             
   }
   
   /**
    * @depends testGetExistingField      
    */
   public function testSetterProperty()
   {
      $tb = new DALUsers();
      $tb->LastActivity = '02/10/2010 15:45:33';
      $field = $tb->getField('LastActivity');
      $this->assertEquals('2010-02-10 15:45:33', $field['value']);
   }
   
   /**
    * @depends testSetterProperty      
    */
   public function testGetterProperty()
   {
      $tb = new DALUsers();
      $tb->LastActivity = '02/10/2010 15:45:33';
      $this->assertEquals('02/10/2010 15:45:33', $tb->LastActivity);  
   }
   
   /**
    * @depends testSetExistingField            
    */
   public function testSerializationUnserialization()
   {
      $tb = new DALUsers();
      $tb->TypeID = 'M';
      $tb->FirstName = 'Sergey';
      $tb->LastName = 'Milimko';
      $tb->Email = 'smilimko@gmail.com';
      $tb->Password = 'qwerty';
      $tb->Created = 'NOW()';
      $new = serialize($tb);
      $new = unserialize($new); 
      $this->assertEquals($tb->getValues(), $new->getValues());  
   }
}

?>