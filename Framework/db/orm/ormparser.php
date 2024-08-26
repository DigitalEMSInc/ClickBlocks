<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core;

class ORMParser
{
   private $config = null;

   public function __construct()
   {
      $this->config = Core\Register::getInstance()->config;
   }

   public function parseXML($xmlfile = null)
   {
      if (!$xmlfile) $xmlfile = Core\IO::dir('engine') . '/db.xml';
      if (!is_file($xmlfile)) throw new \Exception('file not found');
      $dom = new \DOMDocument('1.0', 'utf-8');
      $dom->load($xmlfile);
      /*if (!$dom->validate())
      {

      }*/
      $dom = simplexml_import_dom($dom);
      $aliases = $this->getAliases($dom);
      $dbs = array();
      foreach ($dom->xpath('/Config/DataBase') as $db)
      {
         $dbname = (string)$db['DB'];
         $dbalias = (string)$db['Name'];
         $dbs[$dbalias]['name'] = $dbname;
         $tables = array();
         foreach ($dom->xpath('/Config/DataBase[@Name="' . $dbalias . '"]/ModelLogical/Tables/Table') as $nodeLogicalTable)
         {
            $tableName = (string)$nodeLogicalTable['Repository'];
            $tableAlias = (string)$nodeLogicalTable['Name'];
            $nodePhysicalTables = $dom->xpath('/Config/DataBase[@Name="' . $dbalias . '"]/ModelPhysical/Tables/Table[@Name="' . $tableName . '"]');
            if (count($nodePhysicalTables) < 1)
            {
               throw new \Exception('Physical table "' . $tableName . '" is not existing in the "' . $dbalias . '".');
            }
            if (count($nodePhysicalTables) > 1)
            {
               throw new \Exception('Physical table "' . $tableName . '" encounters more than once in the "' . $dbalias . '".');
            }
            $nodePhysicalTable = $nodePhysicalTables[0];
            $tables[$tableAlias]['name'] = $tableName;
            $tables[$tableAlias]['inherit'] = (string)$nodeLogicalTable['Inherit'];
            if ($tables[$tableAlias]['inherit']) $tables[$tableAlias]['inherit'] = explode('.', $tables[$tableAlias]['inherit']);
            $tables[$tableAlias]['engine'] = (string)$nodePhysicalTable['Engine'];
            $tables[$tableAlias]['charset'] = (string)$nodePhysicalTable['Charset'];
            $tables[$tableAlias]['fields'] = $tables[$tableAlias]['logicFields'] = $tables[$tableAlias]['navigationFields'] = $fieldAliases = array();
            foreach ($nodeLogicalTable->Fields->Field as $nodeLogicalField)
            {
               $fieldName = (string)$nodeLogicalField['Link'];
               $fieldAlias = $fieldAliases[$fieldName] = (string)$nodeLogicalField['Name'];
               $nodePhysicalFields = $dom->xpath('/Config/DataBase[@Name="' . $dbalias . '"]/ModelPhysical/Tables/Table[@Name="' . $tableName . '"]/Fields/Field[@Name="' . $fieldName . '"]');
               if (count($nodePhysicalFields) < 1)
               {
                  throw new \Exception('Field "' . $fieldName . '" is not existing in physical table "' . $dbalias . '.' . $tableName . '".');
               }
               if (count($nodePhysicalFields) > 1)
               {
                  throw new \Exception('Field "' . $fieldName . '" encounters more than once in physical table "' . $dbalias . '.' . $tableName . '".');
               }
               $nodePhysicalField = $nodePhysicalFields[0];
               $tables[$tableAlias]['fields'][$fieldAlias] = array('name' => $fieldName,
                                                                   'type' => (string)$nodePhysicalField['Type'],
                                                                   'null' => (string)$nodePhysicalField['Null'],
                                                                   'unsigned' => (string)$nodePhysicalField['Unsigned'],
                                                                   'length' => (string)$nodePhysicalField['Length'],
                                                                   'precision' => (string)$nodePhysicalField['Precision'],
                                                                   'collection' => (string)$nodePhysicalField['Collection'],
                                                                   'default' => (string)$nodePhysicalField['Default'],
                                                                   'autoincrement' => (string)$nodePhysicalField['Autoincrement']);
               $this->getFieldAdditionalInfo($fieldAlias, $nodeLogicalField, $tables[$tableAlias]['fields'][$fieldAlias]);
            }
            foreach ($nodeLogicalTable->LogicProperties->Property as $nodeProperty)
            {
               $fieldName = (string)$nodeProperty['Name'];
               $tables[$tableAlias]['logicFields'][$fieldName]['Name'] = $fieldName;
               $this->getFieldAdditionalInfo($fieldName, $nodeProperty, $tables[$tableAlias]['logicFields'][$fieldName]);
            }
            foreach ($nodeLogicalTable->NavigationProperties->Property as $nodeProperty)
            {
               $fieldName = (string)$nodeProperty['Name'];
               $tables[$tableAlias]['navigationFields'][$fieldName]['Name'] = $fieldName;
               $tables[$tableAlias]['navigationFields'][$fieldName]['insertable'] = (int)$nodeProperty['Insertable'];
               $tables[$tableAlias]['navigationFields'][$fieldName]['updateable'] = (int)$nodeProperty['Updateable'];
               $tables[$tableAlias]['navigationFields'][$fieldName]['deleteable'] = (int)$nodeProperty['Deleteable'];
               $tables[$tableAlias]['navigationFields'][$fieldName]['readable'] = (int)$nodeProperty['Readable'];
               $tables[$tableAlias]['navigationFields'][$fieldName]['output'] = strtolower((string)$nodeProperty->Select['Output']);
               $tables[$tableAlias]['navigationFields'][$fieldName]['multiplicity'] = abs((int)$nodeProperty['Multiplicity']);
               $from = (string)$nodeProperty->From['Repository'];
               $from = explode('.', $from);
               if (count($from) != 2)
               {
                  throw new \Exception('"From" parameter of navigation property "' . $tableAlias . '.' . $fieldName . '" is wrong.');
               }
               $tables[$tableAlias]['navigationFields'][$fieldName]['from'] = array('db' => $from[0], 'table' => $from[1], 'fields' => array());
               foreach ($nodeProperty->From->Field as $nodeField)
               {
                  $fldName = (string)$nodeField['Name'];
                  $tables[$tableAlias]['navigationFields'][$fieldName]['from']['fields'][$fldName] = $fldName;
               }
               $to = (string)$nodeProperty->To['Repository'];
               $to = explode('.', $to);
               if (count($to) != 2)
               {
                  throw new \Exception('"To" parameter of navigation property "' . $tableAlias . '.' . $fieldName . '" is wrong.');
               }
               $tables[$tableAlias]['navigationFields'][$fieldName]['to'] = array('db' => $to[0], 'table' => $to[1], 'fields' => array());
               foreach ($nodeProperty->To->Field as $nodeField)
               {
                  $fldName = (string)$nodeField['Name'];
                  $tables[$tableAlias]['navigationFields'][$fieldName]['to']['fields'][$fldName] = $fldName;
               }
               $tables[$tableAlias]['navigationFields'][$fieldName]['init']['name'] = '_init' . $fieldName;
               $tables[$tableAlias]['navigationFields'][$fieldName]['init']['type'] = 'code';
               $tables[$tableAlias]['navigationFields'][$fieldName]['init']['code'] = '$this->navigators[\'' . $fieldName . '\'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, \'' . addslashes($fieldName) . '\');';
            }
            $tables[$tableAlias]['pk'] = array();
            $tables[$tableAlias]['aliases'] = $fieldAliases;
            foreach ($nodePhysicalTable->PrimaryKey->Ref as $nodePK)
            {
               $pkName = (string)$nodePK['Name'];
               if (!array_key_exists($pkName, $fieldAliases))
               {
                  throw new \Exception('In the DB "' . $dbalias . ' in the logical table "' . $tableAlias . ' no field associated with the field "' . $pkName . '" from the physical table "' . $tableName . '"');
               }
               if (in_array($fieldAliases[$pkName], $tables[$tableAlias]['pk']))
               {
                  throw new \Exception('In the DB "' . $dbalias . '" in the table "' . $tableName . '" field "' . $pkName . '" which is part of PK is found in it more than once.');
               }
               $tables[$tableAlias]['pk'][$fieldAliases[$pkName]] = $fieldAliases[$pkName];
            }
         }
         $dbs[$dbalias]['tables'] = $tables;
      }
      $info = array();
      $nodeMappings = $dom->xpath('/Config/Mapping');
      $nodeMapping = $nodeMappings[0];
      $info['namespace'] = (string)$nodeMapping['Namespace'];
      $info['model'] = $dbs;
      $info['classes'] = array();
      $info['aliases'] = $aliases;
      foreach ($dom->xpath('/Config/Mapping/Classes/Class') as $nodeClass)
      {
         $className = (string)$nodeClass['Name'];
         $info['classes'][$className]['service'] = (string)$nodeClass['Service'];
         $info['classes'][$className]['orchestra'] = (string)$nodeClass['Orchestra'];
         $info['classes'][$className]['table']['fields'] = array();
         $info['classes'][$className]['table']['logicFields'] = array();
         $info['classes'][$className]['table']['navigationFields'] = array();
         $this->getClassFields($info, $className, $nodeClass, $dom);
         $nodes = $dom->xpath('/Config/Mapping/Classes/Class[@Name="' . $className . '"]/Properties/Exclude/Property');
         if ($nodes)
         foreach ($nodes as $nodeProperty)
         {
            $name = explode('.', (string)$nodeProperty['Name']);
            $name = $name[2];
            unset($info['classes'][$className]['table']['fields'][$name]);
            unset($info['classes'][$className]['table']['logicFields'][$name]);
            unset($info['classes'][$className]['table']['navigationFields'][$name]);
         }
      }
      return $info;
   }

   private function getClassFields(&$info, $className, \SimpleXMLElement $nodeClass, \SimpleXMLElement $dom)
   {
      $repository = explode('.', (string)$nodeClass['Repository']);
      $info['classes'][$className]['db'] = $repository[0];
      if (!array_key_exists($repository[1], $info['model'][$repository[0]]['tables']))
      {
         throw new \Exception('Logic table "' . $repository[1] . '" is not existing in the DB "' . $repository[0] . '".');
      }
      if (!isset($info['classes'][$className]['table']['name']))
      {
         $info['classes'][$className]['table']['name'] = $info['model'][$repository[0]]['tables'][$repository[1]]['name'];
         $info['classes'][$className]['table']['alias'] = $repository[1];
      }
      $tbInfo = $info['model'][$repository[0]]['tables'][$repository[1]];
      $inherit = $tbInfo['inherit'];
      if ($inherit)
      {
         if (count($inherit) == 1)
         {
            $dbinherit = $repository[0];
            $inherit = $inherit[0];
         }
         else
         {
            $dbinherit = $inherit[0];
            $inherit = $inherit[1];
         }
         if (!array_key_exists($inherit, $info['model'][$dbinherit]['tables']))
         {
            throw new \Exception('Logic table "' . $inherit . '" is not existing in the DB "' . $dbinherit . '"');
         }
      }
      else $dbinherit = $repository[0];
      foreach ($nodeClass->Properties->Property as $nodeProperty)
      {
         $name = (string)$nodeProperty['Name'];
         $name = explode('.', $name);
         if ($name[0] != $dbinherit || $name[0] != $repository[0])
         {
            throw new \Exception('DB "' . $name[0] . '" does not exist.');
         }
         $class = (($info['namespace'] != '\\') ? $info['namespace'] : '') . '\\' . $info['aliases']['classes']['tables'][$name[0]][$name[1]];
         $fields = $info['model'][$name[0]]['tables'][$name[1]]['fields'];
         if (!isset($fields[$name[2]]))
         {
            $fields = $info['model'][$name[0]]['tables'][$name[1]]['logicFields'];
            if (!isset($fields[$name[2]]))
            {
               $fields = $info['model'][$name[0]]['tables'][$name[1]]['navigationFields'];
               if (!isset($fields[$name[2]]))
               {
                  throw new \Exception('Logic Field "' . $name[1] . '.' . $name[2] . '" does not exist in the DB "' . $name[0] . '".');
               }
               else
               {
                  $info['classes'][$className]['table']['navigationFields'][$name[2]] = $class;
                  $toTable = $fields[$name[2]]['to']['table'];
                  $toClass = $dom->xpath('/Config/Mapping/Classes/Class[@Name="' . ucfirst($toTable) . '"]');
                  $info['model'][$name[0]]['tables'][$name[1]]['navigationFields'][$name[2]]['to']['bll'] = $info['namespace'] . '\\' . (string)$toClass[0]['Name'];
                  $info['model'][$name[0]]['tables'][$name[1]]['navigationFields'][$name[2]]['to']['service'] = $info['namespace'] . '\\' . (string)$toClass[0]['Service'];
                  $info['model'][$repository[0]]['tables'][$repository[1]]['navigationFields'][$name[2]] = $info['model'][$name[0]]['tables'][$name[1]]['navigationFields'][$name[2]];
               }
            }
            else $info['classes'][$className]['table']['logicFields'][$name[2]] = $class;
         }
         else
         {
            $info['classes'][$className]['table']['fields'][$name[2]] = $class;
            $field = $info['model'][$name[0]]['tables'][$name[1]]['fields'][$name[2]]['name'];
         }
      }
      if ($tbInfo['inherit'])
      {
         $table = $info['aliases']['classes']['names'][(string)$nodeClass['Name']];
         $nodeTable = $dom->xpath('/Config/DataBase[@Name="' . $table[0] . '"]/ModelLogical/Tables/Table[@Name="' . $table[1] . '"]');
         $inherit = explode('.', (string)$nodeTable[0]['Inherit']);
         $class = $info['aliases']['classes']['tables'][$inherit[0]][$inherit[1]];
         $nodes = $dom->xpath('/Config/Mapping/Classes/Class[@Name="' . $class . '"]');
         $this->getClassFields($info, $className, $nodes[0], $dom);
      }
   }

   private function getFieldAdditionalInfo($fieldAlias, \SimpleXMLElement $nodeField, &$data)
   {
      $nodeNames = array('Setter', 'Getter', 'Set', 'Get');
      foreach ($nodeNames as $nodeName)
      {
         $nodes = $nodeField->{$nodeName};
         if (count($nodes) > 1)
         {
            throw new \Exception('Tag "' . $nodeName . '" should be only one time.');
         }
         if (count($nodes) == 0) continue;
         $node = $nodes[0];
         $nodeName = strtolower($nodeName);
         $name = '_' . $nodeName . ucfirst($fieldAlias);
         switch (strtolower((string)$node['Type']))
         {
            case 'code':
              $nodeCodes = $node->xpath('Code');
              if (count($nodeCodes) != 1)
              {
                 throw new \Exception('Tag "Code" should be only one time.');
              }
              $data[$nodeName] = array('type' => 'code', 'code' => (string)$nodeCodes[0], 'name' => $name);
              break;
            case 'query':
              $nodeSQLs = $node->xpath('SQL');
              if (count($nodeSQLs) != 1)
              {
                 throw new \Exception('Tag "SQL" should be only one time.');
              }
              $data[$nodeName] = array('type' => 'query', 'sql' => (string)$nodeSQLs[0], 'output' => strtolower((string)$nodeSQLs[0]['Output']), 'name' => $name, 'parameters' => array());
              if (count($node->Parameters->Parameter))
              foreach ($node->Parameters->Parameter as $nodeParameter)
              {
                 $params = array();
                 if ((string)$nodeParameter['Name']) $params['name'] = (string)$nodeParameter['Name'];
                 if ((string)$nodeParameter['Value']) $params['value'] = (string)$nodeParameter['Value'];
                 if ((string)$nodeParameter['Maps']) $params['maps'] = (string)$nodeParameter['Maps'];
                 if (!isset($params['value']) && !isset($params['maps']))
                 {
                    throw new \Exception('At least one "Value" or "Maps" attribute of sql parameter should be defined.');
                 }
                 $data[$nodeName]['parameters'][] = $params;
              }
              break;
            case 'function':
              $data[$nodeName]['type'] = 'function';
              $data[$nodeName]['name'] = $name;
              $data[$nodeName]['call'] = (string)$node['Call'];
              $data[$nodeName]['parameters'] = array();
              foreach ($node->Parameters->Parameter as $nodeParameter)
              {
                 $params = array();
                 if ((string)$nodeParameter['Value']) $params['value'] = (string)$nodeParameter['Value'];
                 if ((string)$nodeParameter['Maps']) $params['maps'] = (string)$nodeParameter['Maps'];
                 if (!isset($params['value']) && !isset($params['maps']))
                 {
                    throw new \Exception('Either "Value" or "Maps" attribute of sql parameter should be defined.');
                 }
                 $data[$nodeName]['parameters'][] = $params;
              }
              break;
         }
      }
   }

   private function getAliases(\SimpleXMLElement $dom)
   {
      $aliases = array();
      foreach ($dom->xpath('/Config/DataBase') as $db)
      {
         $dbName = (string)$db['DB'];
         $dbAlias = (string)$db['Name'];
         $aliases['dbs']['aliases'][$dbAlias] = $dbName;
         $aliases['dbs']['names'][$dbName] = $dbAlias;
         foreach ($dom->xpath('//Config/DataBase[@Name="' . $dbAlias . '"]/ModelLogical/Tables/Table') as $tb)
         {
            $tbName = (string)$tb['Repository'];
            $tbAlias = (string)$tb['Name'];
            $aliases['tables']['aliases'][$dbAlias][$tbAlias] = $tbName;
            $aliases['tables']['names'][$dbAlias][$tbName] = $tbAlias;
            foreach ($dom->xpath('//Config/DataBase[@Name="' . $dbAlias . '"]/ModelLogical/Tables/Table[@Name="' . $tbAlias . '"]/Fields/Field') as $field)
            {
               $fieldAlias = (string)$field['Name'];
               $fieldName = (string)$field['Link'];
               $aliases['fields']['aliases'][$dbAlias][$tbAlias][$fieldAlias] = $fieldName;
               $aliases['fields']['names'][$dbAlias][$tbAlias][$fieldName] = $fieldAlias;
            }
         }
      }
      foreach ($dom->xpath('/Config/Mapping/Classes/Class') as $class)
      {
         $className = (string)$class['Name'];
         $classTable = explode('.', (string)$class['Repository']);
         $aliases['classes']['names'][$className] = $classTable;
         $aliases['classes']['tables'][$classTable[0]][$classTable[1]] = $className;
      }
      return $aliases;
   }
}

?>
