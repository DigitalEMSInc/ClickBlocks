<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core;
use ClickBlocks\Utils;

class ORMGenerator
{
    private $config = null;
    private $info = null;

    public function __construct()
    {
        $this->config = Core\Register::getInstance()->config;
    }

    public function generateXML($namespace = 'ClickBlocks\\DB', $xmlfile = null)
    {
        if (!$xmlfile) {
            $xmlfile = Core\IO::dir('engine') . '/db.xml';
        }
        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;
        $root = $dom->createElement('Config');
        $nodeMapping = $dom->createElement('Mapping');
        $nodeMapping->setAttribute('Namespace', $namespace);
        $nodeClasses = $dom->createElement('Classes');
        $info = $this->info();
        foreach (ORM::getInstance()->getDBList() as $db) {
            $dbName = $db->getDataBaseName();
            $alias = $info[$dbName]['alias'];
            $scheme = $db->getEngine();
            $nodeDB = $dom->createElement('DataBase');
            $nodeDB->setAttribute('DB', $dbName);
            $nodeDB->setAttribute('Name', $alias);
            $nodeDB->setAttribute('Driver', $scheme);
            $nodeModelPhysical = $dom->createElement('ModelPhysical');
            $nodeModelLogical = $dom->createElement('ModelLogical');
            $nodeTables = $dom->createElement('Tables');
            $nodeLogicTables = $dom->createElement('Tables');
            $ignoreTables = ['IncidentsTempAlvin', 'AppFirstRunMailSendings'];
            foreach ($info[$dbName]['tables'] as $table => $data) {
                if (in_array($table, $ignoreTables)) {
                    continue;
                }
                $pc = $cc = 0;
                $nodeLogicTable = $dom->createElement('Table');
                $nodeLogicTable->setAttribute('Name', $table);
                $nodeLogicTable->setAttribute('Repository', $table);
                $nodeFields = $dom->createElement('Fields');
                $nodePhysicalFields = $dom->createElement('Fields');
                $nodeLogicProperties = $dom->createElement('LogicProperties');
                $nodeNavigations = $dom->createElement('NavigationProperties');
                $nodeProperties = $dom->createElement('Properties');
                foreach (array('dependences', 'foreignkeys') as $mode)
                    foreach ((array)$info[$dbName][$mode][$table] as $dd) {
                        $nodeName = lcfirst($dd['toTable']);
                        if ($alias == $info[$dd['toDB']]['alias'] && $table == $dd['toTable']) {
                            if ((int)$dd['multiplicity'] == 0) {
                                $nodeName = 'parent' . ucfirst($nodeName) . ++$pc;
                            } else {
                                $nodeName = 'children' . ucfirst($nodeName) . ++$cc;
                            }
                        }
                        $nodeProperty = $dom->createElement('Property');
                        $nodeProperty->setAttribute('Name', $nodeName);
                        $nodeProperty->setAttribute('Multiplicity', (int)$dd['multiplicity']);
                        if ($dd['multiplicity']) {
                            $nodeProperty->setAttribute('Insertable', 1);
                            $nodeProperty->setAttribute('Updateable', 1);
                            $nodeProperty->setAttribute('Deleteable', 1);
                        } else {
                            $nodeProperty->setAttribute('Insertable', 0);
                            $nodeProperty->setAttribute('Updateable', 0);
                            $nodeProperty->setAttribute('Deleteable', 0);
                        }
                        $nodeProperty->setAttribute('Readable', 1);
                        $nodeFrom = $dom->createElement('From');
                        $nodeFrom->setAttribute('Repository', $this->getRepositoryName($alias, $table));
                        foreach ($dd['fromFields'] as $field) {
                            $nodeField = $dom->createElement('Field');
                            $nodeField->setAttribute('Name', $field);
                            $nodeFrom->appendChild($nodeField);
                        }
                        $nodeProperty->appendChild($nodeFrom);
                        $nodeTo = $dom->createElement('To');
                        $nodeTo->setAttribute('Repository', $this->getRepositoryName($info[$dd['toDB']]['alias'], $dd['toTable']));
                        foreach ($dd['toFields'] as $field) {
                            $nodeField = $dom->createElement('Field');
                            $nodeField->setAttribute('Name', $field);
                            $nodeTo->appendChild($nodeField);
                        }
                        $nodeProperty->appendChild($nodeTo);
                        $nodeSelect = $dom->createElement('Select');
                        $nodeSelect->setAttribute('Output', 'object');
                        $nodeProperty->appendChild($nodeSelect);
                        $nodeNavigations->appendChild($nodeProperty);
                        $nodeProperty = $dom->createElement('Property');
                        $nodeProperty->setAttribute('Name', $this->getPropertyName($alias, $table, $nodeName));
                        $nodeProperty->setAttribute('Navigation', 1);
                        $nodeProperties->appendChild($nodeProperty);
                    }
                $nodeTable = $dom->createElement('Table');
                $nodeTable->setAttribute('Name', $table);
                $nodeTable->setAttribute('Engine', $data['engine']);
                $nodeTable->setAttribute('Charset', $data['charset']);
                $nodePK = $dom->createElement('PrimaryKey');
                foreach ((array)$data['key'] as $key) {
                    $nodeRef = $dom->createElement('Ref');
                    $nodeRef->setAttribute('Name', $key);
                    $nodePK->appendChild($nodeRef);
                }
                $nodeTable->appendChild($nodePK);
                $nodeClass = $dom->createElement('Class');
                $className = self::getClassName($table);
                $nodeClass->setAttribute('Name', $className);
                $nodeClass->setAttribute('Repository', $this->getRepositoryName($alias, $table));
                $nodeClass->setAttribute('Service', 'Service' . $className);
                $nodeClass->setAttribute('Orchestra', 'Orchestra' . $className);
                foreach ($data['fields'] as $field) {
                    $nodeField = $dom->createElement('Field');
                    $nodeField->setAttribute('Name', $field['Field']);
                    $nodeField->setAttribute('Type', $field['Type']);
                    if ((int)$field['isNullable'] > 0) {
                        $nodeField->setAttribute('Null', $field['isNullable']);
                    }
                    if ((int)$field['isAutoIncrement'] > 0) {
                        $nodeField->setAttribute('Autoincrement', 1);
                    }
                    if ((int)$field['isUnsigned'] > 0) {
                        $nodeField->setAttribute('Unsigned', 1);
                    }
                    if ((int)$field['MaxLength'] > 0) {
                        $nodeField->setAttribute('Length', $field['MaxLength']);
                    }
                    if ((int)$field['Precision'] > 0) {
                        $nodeField->setAttribute('Precision', $field['Precision']);
                    }
                    if (is_array($field['Set'])) {
                        $nodeField->setAttribute('Collection', htmlspecialchars(implode(',', $field['Set'])));
                    }
                    if (strlen($field['DefaultValue'])) {
                        $nodeField->setAttribute('Default', htmlspecialchars($field['DefaultValue']));
                    }
                    $nodePhysicalFields->appendChild($nodeField);
                    $nodeField = $dom->createElement('Field');
                    $nodeField->setAttribute('Name', $field['Field']);
                    $nodeField->setAttribute('Link', $field['Field']);
                    $nodeFields->appendChild($nodeField);
                    $nodeProperty = $dom->createElement('Property');
                    $nodeProperty->setAttribute('Name', $this->getPropertyName($alias, $table, $field['Field']));
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
        Core\IO::createDirectories(Core\IO::dir('engine'));
        file_put_contents($xmlfile, $dom->saveXML());
    }

    public function generateFiles()
    {
        $getPHPType = function ($dbType) {
            $TypeMap = array(
                'bigint' => 'int',
                'smallint' => 'int',
                'tinyint' => 'int',
                'char' => 'string',
                'varchar' => 'string',
                'text' => 'string',
                'date' => 'string',
                'datetime' => 'string',
                'timestamp' => 'string',
                'decimal' => 'float',
                'time' => 'string',
            );
            return $TypeMap[$dbType] ?: $dbType;
        };

        $this->info = ORM::getInstance()->getORMInfo();
        $tplDAL = new Core\Template();
        $tplDAL->setTemplate('dal', $this->config->root . '/Framework/_templates/orm/daltable.tpl');
        $tplBLL = new Core\Template();
        $tplBLL->setTemplate('bll', $this->config->root . '/Framework/_templates/orm/blltable.tpl');
        $tplSVC = new Core\Template();
        $tplSVC->setTemplate('svc', $this->config->root . '/Framework/_templates/orm/service.tpl');
        $tplORC = new Core\Template();
        $tplORC->setTemplate('orc', $this->config->root . '/Framework/_templates/orm/orchestra.tpl');
        $tplDAL->namespace = $tplBLL->namespace = $tplSVC->namespace = $tplORC->namespace = $this->info['namespace'];
        foreach ($this->info['model'] as $dbalias => $dbs) {
            foreach ($dbs['tables'] as $table => $data) {
                $methods = $properties = array();
                foreach ($data['fields'] as $fieldAlias => $info) {
                    $properties[$fieldAlias] = ' * @property ' . $getPHPType($info['type']) . ' $' . $fieldAlias;
                    foreach (array('setter', 'getter', 'get', 'set') as $methodName) {
                        if (!isset($info[$methodName])) {
                            continue;
                        }
                        $method = '    protected function _' . $methodName . ucfirst($fieldAlias) . '($value)' . endl . '    {' . endl . '    ';
                        $method .= $this->getMethodCode($info[$methodName]);
                        $method .= endl . '    }' . endl;
                        $methods[$methodName . ucfirst($fieldAlias)] = $method;
                    }
                }
                foreach ($data['logicFields'] as $fieldAlias => $info) {
                    $properties[$fieldAlias] = ' * @property custom $' . $fieldAlias;
                    foreach (array('setter', 'getter', 'get', 'set') as $methodName) {
                        if (!isset($info[$methodName])) {
                            continue;
                        }
                        $method = '    protected function _' . $methodName . ucfirst($fieldAlias) . '(' . (($methodName == 'get') ? '' : '$value') . ')' . endl . '    {' . endl . '    ';
                        $method .= $this->getMethodCode($info[$methodName]);
                        $method .= endl . '   }' . endl;
                        $methods[$methodName . ucfirst($fieldAlias)] = $method;
                    }
                }
                foreach ($data['navigationFields'] as $fieldAlias => $info) {
                    $navProperties[$class][$fieldAlias] = ' * @property NavigationProperty $' . $fieldAlias;

                }
                $tplDAL->class = 'DAL' . self::getClassName($table);
                $tplDAL->dbAlias = $dbalias;
                $tplDAL->logicTableName = $table;
                $tplDAL->methods = (count($methods)) ? PHP_EOL . PHP_EOL . implode(PHP_EOL, $methods) : PHP_EOL;
                $tplDAL->properties = implode(PHP_EOL, $properties) . PHP_EOL;
                $file = self::getFileName(Core\IO::dir('dal') . '/' . $dbalias, $table);
                if (!is_file($file) || !$this->rewriteClass($file, $tplDAL)) {
                    self::saveClassToFile($file, $tplDAL->render());
                }
            }
        }
        foreach ($this->info['classes'] as $class => $data) {
            $dbalias = $data['db'];
            $table = $data['table']['alias'];
            $parent = '\ClickBlocks\DB\BLLTable';
            $properties = $methods = array();
            foreach ($data['table']['fields'] as $field => $className) {
                $k = strrpos($className, '\\');
                if ($k !== false) {
                    $className = substr($className, $k + 1);
                }
                $db = $this->info['classes'][$className]['db'];
                $tb = $this->info['classes'][$className]['table']['alias'];
                $properties[$field] = ' * @property ' .
                    $getPHPType($this->info['model'][$db]['tables'][$tb]['fields'][$field]['type']) .
                    ' $' . $field;
            }
            foreach ($data['table']['logicFields'] as $field => $className) {
                $properties[$field] = ' * @property custom $' . $field;
            }
            foreach ($data['table']['navigationFields'] as $field => $className) {
                $info = $this->info['model'][$dbalias]['tables'][$table]['navigationFields'][$field];
                $properties[$field] = ' * @property NavigationProperty $' . $field;
                foreach (array('init') as $methodName) {
                    if (!isset($info[$methodName])) {
                        continue;
                    }
                    $method = '    protected function ' . $info[$methodName]['name'] . '()' . endl . '    {' . endl . '        ';
                    $method .= $this->getMethodCode($info[$methodName]);
                    $method .= endl . '    }' . endl;
                    $methods[$info[$methodName]['name']] = $method;
                }
            }
            $inherit = $this->info['model'][$dbalias]['tables'][$table]['inherit'];
            if ($inherit) {
                $parent = '\ClickBlocks\DB\BLLTable';
                foreach ($this->info['classes'] as $cls => $info) {
                    if ($cls != $class && $inherit[1] == $info['table']['alias']) {
                        $parent = $cls;
                        break;
                    }
                }
                if ($parent == '\ClickBlocks\DB\BLLTable') {

                }
            }
            $tplBLL->class = $class;
            $tplBLL->dalclass = 'DAL' . self::getClassName($table);
            $tplBLL->parent = $parent;
            $tplBLL->methods = (count($methods)) ? PHP_EOL . PHP_EOL . implode(PHP_EOL, $methods) : PHP_EOL;
            $tplBLL->properties = implode(PHP_EOL, $properties) . PHP_EOL;
            $file = self::getFileName(Core\IO::dir('bll'), $dbalias . '.' . $table);
            if (!is_file($file) || !$this->rewriteClass($file, $tplBLL)) {
                self::saveClassToFile($file, $tplBLL->render());
            }
            $tplSVC->class = $data['service'];
            $tplSVC->objectName = (($this->info['namespace'] != '\\') ? '\\' . $this->info['namespace'] : '') . '\\' . $class;
            $file = self::getFileName(Core\IO::dir('services'), $class);
            if (!is_file($file) || !$this->rewriteClass($file, $tplSVC)) {
                self::saveClassToFile($file, $tplSVC->render());
            }
            $tplORC->class = $data['orchestra'];
            $tplORC->className = (($this->info['namespace'] != '\\') ? '\\' . $this->info['namespace'] : '') . '\\' . $class;
            $file = self::getFileName(Core\IO::dir('orchestras'), $class);
            if (!is_file($file) || !$this->rewriteClass($file, $tplORC)) {
                self::saveClassToFile($file, $tplORC->render());
            }
        }
    }

    protected function info()
    {
        $info = array();
        foreach (ORM::getInstance()->getDBList() as $alias => $db) {
            $dbName = $db->getDataBaseName();
            $info[$dbName]['alias'] = $alias;
            $tables = $db->getTables();
            foreach ($tables as $table) {
                $ddl = $db->getCreateOperator($table);
                $info[$dbName]['tables'][$table]['fields'] = $db->getFields($table);
                preg_match('/ +ENGINE=([^ \s]+)/i', $ddl, $engine);
                $info[$dbName]['tables'][$table]['engine'] = $engine[1];
                preg_match('/ +DEFAULT CHARSET=([^ \s]+)/i', $ddl, $charset);
                $info[$dbName]['tables'][$table]['charset'] = $charset[1];
                foreach ($info[$dbName]['tables'][$table]['fields'] as $field => $v) if ($v['PK']) {
                    $info[$dbName]['tables'][$table]['key'][$field] = $field;
                }
                if (preg_match_all('/FOREIGN KEY \(`([^)]+)`\) REFERENCES `([^`]+(`.`)?[^`]+)` \(`([^)]+)`\)/is', $ddl, $key)) {
                    foreach ($key[0] as $n => $ddl) {
                        $fromFields = explode('`, `', $key[1][$n]);
                        $toFields = explode('`, `', $key[4][$n]);
                        $toTable = explode('`.`', $key[2][$n]);
                        if (count($toTable) == 1) {
                            $toDB = $dbName;
                            $toTable = $toTable[0];
                        } else {
                            $toDB = $toTable[0];
                            $toTable = $toTable[1];
                        }
                        foreach ($tables as $tb) {
                            if (strcasecmp($tb, $toTable) == 0) {
                                $toTable = $tb;
                                break;
                            }
                        }
                        $info[$toDB]['foreignkeys'][$toTable][] = array(
                            'toDB' => $dbName,
                            'toTable' => $table,
                            'toFields' => $fromFields,
                            'fromFields' => $toFields
                        );
                        $info[$dbName]['dependences'][$table][] = array(
                            'toDB' => $toDB,
                            'toTable' => $toTable,
                            'toFields' => $toFields,
                            'fromFields' => $fromFields
                        );
                    }
                }
            }
        }
        foreach (ORM::getInstance()->getDBList() as $db) {
            $dbName = $db->getDataBaseName();
            $tables = array_keys($info[$dbName]['tables']);
            foreach (array('foreignkeys', 'dependences') as $mode) {
                foreach ($tables as $table) {
                    foreach ((array)$info[$dbName][$mode][$table] as $n => $data) {
                        $pk = $info[$data['toDB']]['tables'][$data['toTable']]['key'];
                        $multiplicity = false;
                        foreach ($data['toFields'] as $field) {
                            if (!isset($pk[$field])) {
                                $multiplicity = true;
                                break;
                            }
                        }
                        $info[$dbName][$mode][$table][$n]['multiplicity'] = $multiplicity;
                    }
                }
            }
        }
        return $info;
    }

    private function getPropertyName($alias, $table, $field)
    {
        $db = ORM::getInstance()->getDB($alias);
        if (strpos($alias, '.') !== false) {
            $alias = $db->wrap($alias);
        }
        if (strpos($table, '.') !== false) {
            $table = $db->wrap($table);
        }
        if (strpos($field, '.') !== false) {
            $field = $db->wrap($field);
        }
        return $alias . '.' . $table . '.' . $field;
    }

    private function getRepositoryName($alias, $table)
    {
        $db = ORM::getInstance()->getDB($alias);
        if (strpos($alias, '.') !== false) {
            $alias = $db->wrap($alias);
        }
        if (strpos($table, '.') !== false) {
            $table = $db->wrap($table);
        }
        return $alias . '.' . $table;
    }

    private function getMethodCode($data)
    {
        $method = '';
        switch ($data['type']) {
            case 'code':
                $method .= $data['code'];
                break;
            case 'query':
                $params = array();
                foreach ($data['parameters'] as $param) {
                    if (isset($param['maps'])) {
                        if (isset($param['name'])) {
                            $params[] = "'" . $param['name'] . '\' => $this->' . $param['maps'];
                        } else {
                            $params[] = '$this->' . $param['maps'];
                        }
                    } else {
                        if (isset($param['value'])) {
                            if (isset($param['name'])) {
                                $params[] = "'" . $param['name'] . '\' => \'' . self::getParameter($param['value']) . "'";
                            } else {
                                $params[] = "'" . self::getParameter($param['value']) . "'";
                            }
                        }
                    }
                }
                if (count($params)) {
                    $method .= 'return $this->db->' . strtolower($data['output']) . '(\'' . addslashes($data['sql']) . '\', array(' . implode(', ', $params) . '));';
                } else {
                    $method .= 'return $this->db->' . strtolower($data['output']) . '(\'' . addslashes($data['sql']) . '\');';
                }
                break;
            case 'function':
                $params = array();
                foreach ($data['parameters'] as $param) {
                    if (isset($param['maps'])) {
                        $params[] = '$this->' . $param['maps'];
                    } else {
                        if (isset($param['value'])) {
                            $params[] = self::getParameter($param['value']);
                        }
                    }
                }
                $call = explode('::', $data['call']);
                if (count($call) == 2) {
                    if (!strlen($call[0])) {
                        $method = 'call_user_func_array(get_class($this->parent) . \'' . $data['call'] . '\', array(' . implode(', ', $params) . '));';
                        break;
                    }
                } else {
                    $call = explode('->', $data['call']);
                    if (count($call) == 2) {
                        if (!strlen($call[0])) {
                            $call = '$this->parent' . $data['call'];
                        }
                    } else {
                        $call = $data['call'];
                    }
                }
                $method = 'return ' . $call . '(' . implode(', ', $params) . ');';
                break;
        }
        return $method;
    }

    private static function getParameter($value)
    {
        if (strtolower($value) == 'true' || strtolower($value) == 'false') {
            return $value;
        }
        if (is_numeric($value)) {
            return $value;
        }
        return addslashes($value);
    }

    private static function getClassName($table)
    {
        $tmp = explode('_', ($table));
        foreach ($tmp as $k => $v) if (trim($v)) {
            $tmp[$k] = ucfirst(trim($v));
        } else {
            unset($tmp[$k]);
        }
        return implode('', $tmp);
    }

    private static function getFileName($dir, $table, $fileName = null)
    {
        $file = $dir . '/';
        if (stripos($table, 'lookup') !== false) {
            $file .= 'lookup/';
        }
        $file .= (!$fileName) ? str_replace('_', '', strtolower($table)) . '.php' : $fileName;
        return $file;
    }

    private static function saveClassToFile($file, $code)
    {
        Core\IO::createDirectories(dirname($file));
        file_put_contents($file, '<?php' . PHP_EOL . PHP_EOL . $code . PHP_EOL . PHP_EOL . '?>');
    }

    private function rewriteClass($file, Core\Template $tpl)
    {
        $fullClassName = (($tpl->namespace != '\\') ? $tpl->namespace : '') . '\\' . $tpl->class;
        $info = new Utils\InfoClass($file);
        if (!isset($info->{$fullClassName})) {
            return false;
        }
        $tpl->class = 'T' . $tpl->class;
        $tplClass = (($tpl->namespace != '\\') ? $tpl->namespace : '') . '\\' . $tpl->class;
        $tplInfo = new Utils\InfoClass();
        $tplInfo->parseCode('<?php ' . PHP_EOL . $tpl->render() . PHP_EOL . '?>', 'template');
        foreach ($tplInfo->{$tplClass}->methods as $method => $methodInfo) {
            $needCode = trim($methodInfo->code);
            if ($method != '__construct') {
                $info->{$fullClassName}->methods[$method] = $methodInfo;
            } else {
                $code = $info->{$fullClassName}->methods[$method]->code;
                if (!Utils\PHPParser::inCode($needCode, $code)) {
                    $info->{$fullClassName}->methods[$method]->code = PHP_EOL . '      ' . $needCode . $code;
                }
            }
        }
        foreach ($info->{$fullClassName}->methods as $method => $methodInfo) {
            if ($method[0] == '_' && $method[1] !== '_' && $tplInfo->{$tplClass}->methods[$method] === null) {
                unset($info->{$fullClassName}->methods[$method]);
            }
        }
        $info->{$fullClassName}->comment = $tplInfo->{$tplClass}->comment;
        $info->{$fullClassName}->parentName = $tplInfo->{$tplClass}->parentName;
        $info->{$fullClassName}->parentShortName = $tplInfo->{$tplClass}->parentShortName;
        $info->{$fullClassName}->parentNamespace = $tplInfo->{$tplClass}->parentNamespace;
        Core\IO::createDirectories(dirname($file));
        file_put_contents($file, $info->getCodeFile($file));
        return true;
    }
}

?>
