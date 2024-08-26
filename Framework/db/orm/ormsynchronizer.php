<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\IO,
    ClickBlocks\Exceptions;

class ORMSynchronizer
{
   const SYNC_MODE_IN = 1;
   const SYNC_MODE_OUT = 2;
   const SYNC_MODE_ALL = 3;

   private $config = null;
   private $orm = null;

   public function __construct()
   {
      $this->config = Core\Register::getInstance()->config;
      $this->orm = ORM::getInstance();
   }
   
   public function synchronize($mode = self::SYNC_MODE_ALL, $xmlfile = null)
   {
      if (!$xmlfile) $xmlfile = $this->config->dir('engine') . '/db.xml';
      if (!is_file($xmlfile)) throw new \Exception(err_msg('ERR_ORM_SYNC_1', array($xmlfile)));
      
      Core\Register::getInstance()->cache->delete(ORM::CACHE_DB_INFO_KEY);
      
      $parser = new ORMParser();
      
      $info = array();
      $info['old'] = $parser->parseXML($xmlfile);
      
      $xmlfile = $this->config->dir('temp') . '/db.xml';
      $this->orm->generateXML($info['old']['namespace'], $xmlfile);
      $info['new'] = $parser->parseXML($xmlfile);
      
      $info['res'] = array();
      $info['res']['namespace'] = $info['old']['namespace'];
      $info['res']['model'] = array();
      $info['res']['classes'] = array();
      $info['res']['aliases'] = array();
      
      $dbs = array_keys(array_intersect_key($info['old']['model'], $info['new']['model']));
      foreach ($dbs as $dbAlias)
      {
         $dbOld = $info['old']['model'][$dbAlias];
         $dbNew = $info['new']['model'][$dbAlias];
         if ($dbOld['name'] != $dbNew['name']) throw new \Exception('error');
         $info['res']['model'][$dbAlias]['name'] = $dbOld['name'];
         $info['res']['model'][$dbAlias]['tables'] = array(); 
         $info['res']['aliases']['dbs']['aliases'][$dbAlias] = $dbOld['name'];
         $info['res']['aliases']['dbs']['names'][$dbOld['name']] = $dbAlias; 
         $tables = array_keys(array_intersect_key($dbOld['tables'], $dbNew['tables']));
         foreach ($tables as $table)
         {
            $tbOld = $dbOld['tables'][$table];
            $tbNew = $dbNew['tables'][$table];  
            if ($tbOld['name'] != $tbNew['name']) throw new \Exception('error');
            $class = $info['old']['aliases']['classes']['tables'][$dbAlias][$table];
            $info['res']['model'][$dbAlias]['tables'][$table] = $tbOld; 
            $info['res']['classes'][$table] = $info['old']['classes'][$class];
            $info['res']['aliases']['tables']['aliases'][$dbAlias][$table] = $tbOld['name'];
            $info['res']['aliases']['tables']['names'][$dbAlias][$tbOld['name']] = $table;
            $info['res']['aliases']['classes']['names'][$class] = $info['old']['aliases']['classes']['names'][$class];
            $info['res']['aliases']['classes']['tables'][$dbAlias][$table] = $class;
            $fields = array_keys(array_intersect_key($tbOld['fields'], $tbNew['fields']));
            foreach ($fields as $field)
            {
               $fldOld = $tbOld['fields'][$field];
               $fldNew = $tbNew['fields'][$field];
               if ($fldOld != $fldNew)               
               { 
                  if ($mode == self::SYNC_MODE_ALL) throw new \LogicException(err_msg('ERR_ORM_SYNC_2', array($dbAlias . '.' . $table . '.' . $field)));
                  if ($mode == self::SYNC_MODE_IN)
                  {
                     $this->orm->getDB($dbAlias)->changeField($tbOld['name'], $fldOld['name'], $fldOld);    
                  }
                  else if ($mode == self::SYNC_MODE_OUT)
                  {
                     $info['res']['model'][$dbAlias]['tables'][$table]['fields'][$field] = $fldNew;
                  }
               } 
            }
            if ($mode & self::SYNC_MODE_IN)
            {
               $fields = array_keys(array_diff_key($tbOld['fields'], $tbNew['fields']));
               foreach ($fields as $field)
               {
                  if (isset($tbNew['fields'][$tbOld['fields'][$field]['name']])) continue;
                  $fld = $tbOld['fields'][$field];
                  //$this->orm->getDB($dbAlias)->addField($tbOld['name'], $fld);
                  $info['res']['model'][$dbAlias]['tables'][$table]['fields'][$field] = $fld; 
                  $info['res']['classes'][$class]['table']['fields'][$field] = $info['old']['classes'][$class]['table']['fields'][$field];   
               }  
            }
            if ($mode & self::SYNC_MODE_OUT)
            {
               $fields = array_keys(array_diff_key($tbNew['fields'], $tbOld['fields']));
               foreach ($fields as $field)
               {
                  $fieldName = $tbNew['fields'][$field]['name'];
                  if (isset($info['old']['aliases']['fields']['names'][$dbAlias][$table][$fieldName])) continue;
                  $info['res']['model'][$dbAlias]['tables'][$table]['fields'][$field] = $tbNew['fields'][$field];
                  $info['res']['model'][$dbAlias]['tables'][$table]['aliases'][$fieldName] = $tbNew['aliases'][$fieldName];
                  $info['res']['classes'][$class]['table']['fields'][$field] = $info['new']['classes'][$class]['table']['fields'][$field];
               }
            }
         }         
         if ($mode & self::SYNC_MODE_IN)
         {
            $tables = array_keys(array_diff_key($dbOld['tables'], $dbNew['tables']));
            foreach ($tables as $table)
            {
               if (isset($dbNew['tables'][$dbOld['tables'][$table]['name']])) continue;
               $tb = $dbOld['tables'][$table];
               //$this->orm->getDB($dbAlias)->createTable($tb['name'], $tb['fields'], $tb['pk'], $tb['engine'], $tb['charset']);
               $class = $info['old']['aliases']['classes']['tables'][$dbAlias][$table];
               $info['res']['model'][$dbAlias]['tables'][$table] = $tb;
               $info['res']['classes'][$table] = $info['old']['classes'][$class];               
               $info['res']['aliases']['tables']['aliases'][$dbAlias][$table] = $tb['name'];
               $info['res']['aliases']['tables']['names'][$dbAlias][$tb['name']] = $table;
               $info['res']['aliases']['classes']['names'][$class] = $info['old']['aliases']['classes']['names'][$class];
               $info['res']['aliases']['classes']['tables'][$dbAlias][$table] = $class;
            }
         }
         if ($mode & self::SYNC_MODE_OUT)
         {
            $tables = array_keys(array_diff_key($dbNew['tables'], $dbOld['tables']));
            foreach ($tables as $table)
            {
               if (isset($info['old']['aliases']['tables']['names'][$dbAlias][$table][$dbNew['tables'][$table]['name']])) continue;
               $tb = $dbNew['tables'][$table];               
               $info['res']['model'][$dbAlias]['tables'][$table] = $tb;
               $class = $info['new']['aliases']['classes']['tables'][$dbAlias][$table];
               $info['res']['classes'][$table] = $info['new']['classes'][$class];
               $info['res']['aliases']['tables']['aliases'][$dbAlias][$table] = $tb['name'];
               $info['res']['aliases']['tables']['names'][$dbAlias][$tb['name']] = $table;
               $info['res']['aliases']['classes']['names'][$class] = $info['new']['aliases']['classes']['names'][$class];
               $info['res']['aliases']['classes']['tables'][$dbAlias][$table] = $class;
            }
         }  
         ksort($info['res']['model'][$dbAlias]['tables']);                              
      }
      if ($mode & self::SYNC_MODE_IN)
      {
         $dbs = array_keys(array_intersect_key($info['old']['model'], $info['new']['model']));
      }
      if ($mode & self::SYNC_MODE_OUT)
      {
         $dbs = array_keys(array_intersect_key($info['new']['model'], $info['old']['model']));
      }
      ksort($info['res']['classes']);
      
      echo '<pre>' . print_r($info['res'], true) . '</pre>';
      //echo '<pre>' . print_r($info['old'], true) . '</pre>';
      //echo '<pre>' . print_r($info['new'], true) . '</pre>';  
      $info = $info['res'];
      
      $dom = new \DOMDocument('1.0', 'utf-8');
      $dom->formatOutput = true;      
      $root = $dom->createElement('Config');      
      $nodeMapping = $dom->createElement('Mapping');
      $nodeMapping->setAttribute('Namespace', $info['namespace']); 
      $nodeClasses = $dom->createElement('Classes');
      foreach ($info['model'] as $alias => $db)
      {
         $dbName = $db['name'];
         $scheme = $this->orm->getDB($alias)->getEngine();
         $nodeDB = $dom->createElement('DataBase');
         $nodeDB->setAttribute('DB', $dbName);
         $nodeDB->setAttribute('Name', $alias);
         $nodeDB->setAttribute('Driver', $scheme); 
         $nodeModelPhysical = $dom->createElement('ModelPhysical');
         $nodeModelLogical = $dom->createElement('ModelLogical');                  
         $nodeTables = $dom->createElement('Tables');
         $nodeLogicTables = $dom->createElement('Tables');   
         foreach ($db['tables'] as $table => $data)
         {
            $nodeLogicTable = $dom->createElement('Table');
            $nodeLogicTable->setAttribute('Name', $table);
            $nodeLogicTable->setAttribute('Repository', $data['name']);         
            $nodeFields = $dom->createElement('Fields');
            $nodePhysicalFields = $dom->createElement('Fields'); 
            $nodeLogicProperties = $dom->createElement('LogicProperties');
            $nodeNavigations = $dom->createElement('NavigationProperties');
            $nodeProperties = $dom->createElement('Properties');
            // foreing keys are here            
            $nodeTable = $dom->createElement('Table');
            $nodeTable->setAttribute('Name', $data['name']);
            $nodeTable->setAttribute('Engine', $data['engine']);
            $nodeTable->setAttribute('Charset', $data['charset']);         
            $nodePK = $dom->createElement('PrimaryKey');
            foreach ($data['pk'] as $key)
            {
               $nodeRef = $dom->createElement('Ref');
               $nodeRef->setAttribute('Name', $key);
               $nodePK->appendChild($nodeRef); 
            }
            $nodeTable->appendChild($nodePK);         
            $nodeClass = $dom->createElement('Class');
            $className = $info['aliases']['classes']['tables'][$alias][$table];
            $nodeClass->setAttribute('Name', $className);
            $nodeClass->setAttribute('Repository', $this->getRepositoryName($alias, $table));
            $nodeClass->setAttribute('Service', $info['classes'][$className]['service']);
            $nodeClass->setAttribute('Orchestra', $info['classes'][$className]['orchestra']);  
            foreach ($data['fields'] as $fieldAlias => $field)
            {
               $nodeField = $dom->createElement('Field');
               $nodeField->setAttribute('Name', $field['name']);
               $nodeField->setAttribute('Type', $field['type']);
               if ((int)$field['null'] > 0) $nodeField->setAttribute('Null', $field['null']);
               if ((int)$field['autoincrement'] > 0) $nodeField->setAttribute('Autoincrement', 1);
               if ((int)$field['unsigned'] > 0) $nodeField->setAttribute('Unsigned', 1);
               if ((int)$field['length'] > 0) $nodeField->setAttribute('Length', $field['length']);
               if ((int)$field['precision'] > 0) $nodeField->setAttribute('Precision', $field['precision']);
               if (strlen($field['collection'])) $nodeField->setAttribute('Collection', htmlspecialchars($field['collection']));
               if (strlen($field['default'])) $nodeField->setAttribute('Default', htmlspecialchars($field['default']));
               $nodePhysicalFields->appendChild($nodeField);                            
               $nodeField = $dom->createElement('Field');
               $nodeField->setAttribute('Name', $fieldAlias);
               $nodeField->setAttribute('Link', $field['name']);
               $nodeFields->appendChild($nodeField);            
               $nodeProperty = $dom->createElement('Property');
               $nodeProperty->setAttribute('Name', $this->getPropertyName($alias, $table, $fieldAlias));               
               $nodeProperties->appendChild($nodeProperty);            
            }            
            $nodeTable->appendChild($nodePhysicalFields);             
            $nodeLogicTable->appendChild($nodeFields);
            $nodeLogicTable->appendChild($nodeNavigations);
            $nodeLogicTable->appendChild($nodeLogicProperties);         
            $nodeClass->appendChild($nodeProperties);         
            $nodeTables->appendChild($nodeTable);
            $nodeLogicTables->appendChild($nodeLogicTable);
            $nodeClasses->appendChild($nodeClass);   
         }
         $nodeModelPhysical->appendChild($nodeTables);      
         $nodeModelLogical->appendChild($nodeLogicTables);
         $nodeDB->appendChild($nodeModelPhysical);
         $nodeDB->appendChild($nodeModelLogical);
         $root->appendChild($nodeDB);
      }
      $nodeMapping->appendChild($nodeClasses);
      $root->appendChild($nodeMapping);   
      $dom->appendChild($root);      
      file_put_contents($this->config->root . '/res.xml', $dom->saveXML());
      exit;
   }
   
   private function getRepositoryName($alias, $table)
   {
      $db = ORM::getInstance()->getDB($alias);
      if (strpos($alias, '.') !== false) $alias = $db->wrap($alias);
      if (strpos($table, '.') !== false) $table = $db->wrap($table);
      return $alias . '.' . $table;
   }
   
   private function getPropertyName($alias, $table, $field)
   {
      $db = ORM::getInstance()->getDB($alias);      
      if (strpos($alias, '.') !== false) $alias = $db->wrap($alias);
      if (strpos($table, '.') !== false) $table = $db->wrap($table);
      if (strpos($field, '.') !== false) $field = $db->wrap($field);
      return $alias . '.' . $table . '.' . $field;
   }
}

?>