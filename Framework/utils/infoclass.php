<?php

namespace ClickBlocks\Utils;

use ClickBlocks\Core;

class InfoClass
{
   private $info = array();
   private $lines = array();
   private $current = null;  

   public function __construct($name = null)
   {
      if (is_file($name)) $this->parse($name);
      else if ($name)
      { 
         if (!is_object($name) || !get_class($name)) $this->parseCode($name);
         else $this->extraction($name);
      }
   }

   public function parse($file)
   {
      if (!is_file($file)) throw new \RuntimeException('File "' . $file . '" is not found.');
      require_once($file);
      $this->extractAll($this->extractFileContent($file));
      return $this;
   }
   
   public function parseCode($code, $key = null)
   {
      $reg = \ClickBlocks\Core\Register::getInstance();
      $reg->loader->load($code);
      $this->extractContentFromCode($code, $key);
      $this->extractAll($code, $key);
      return $this;
   }

   public function extraction($class, $key = null)
   {
      if (is_object($class)) $class = get_class($class);
      $info = array();
      $class = new \ReflectionClass($class);
      $parent = $class->getParentClass();
      $info['file'] = $class->getFileName();
      if (is_file($info['file']))
      { 
         $key = $info['file'];
         $this->extractFileContent($info['file']);
      }
      list ($info['name'], $info['shortName'], $info['namespace']) = $this->getClassNames($class);  
      $info['comment'] = $class->getDocComment();
      $info['startLine'] = $class->getStartLine();
      $info['endLine'] = $class->getEndLine();
      $info['isAbstract'] = $class->isAbstract();
      $info['isFinal'] = $class->isFinal();
      $info['isInterface'] = $class->isInterface();
      $info['isInternal'] = $class->isInternal();
      $info['isUserDefined'] = $class->isUserDefined();
      $info['isInstantiable'] = $class->isInstantiable();
      $info['isIterateable'] = $class->isIterateable();
      $info['inNamespace'] = $class->inNamespace();
      list ($info['parentName'], $info['parentShortName'], $info['parentNamespace']) = (is_object($parent)) ? $this->getClassNames($parent) : array('', '', '');
      $info['extension'] = $class->getExtensionName();
      $info['interfaces'] = array();
      foreach ($class->getInterfaces() as $name => $interface)
      {
         if ($parent instanceof \ReflectionClass && $parent->implementsInterface($name)) continue;
         list ($info['interfaces'][$name]['name'], $info['interfaces'][$name]['shortName'], $info['interfaces'][$name]['namespace']) = $this->getClassNames($interface);              
      }
      $info['constants'] = array();
      foreach ($class->getConstants() as $name => $constant)
      {
         if ($parent instanceof \ReflectionClass && $parent->hasConstant($name)) continue;
         $info['constants'][$name] = self::getPHPType($constant);
      }
      $info['properties'] = array();
      $defValues = $class->getDefaultProperties();
      foreach ($class->getProperties() as $property)
      {
         $name = $property->getName();
         if ($parent instanceof \ReflectionClass && $parent->hasProperty($name)) continue;
         $info['properties'][$name]['isStatic'] = $property->isStatic();
         $info['properties'][$name]['isPublic'] = $property->isPublic();
         $info['properties'][$name]['isProtected'] = $property->isProtected();
         $info['properties'][$name]['isPrivate'] = $property->isPrivate();
         $info['properties'][$name]['isDefault'] = $property->isDefault();
         $info['properties'][$name]['value'] = $this->getPHPType($defValues[$name]);
         $info['properties'][$name]['comment'] = $property->getDocComment();
      }
      $info['methods'] = array();
      foreach ($class->getMethods() as $method)
      {
         $name = $method->getName();
         if ($parent instanceof \ReflectionClass && $parent->hasMethod($name) && $method->getDeclaringClass()->getName() != $info['name']) continue;        
         $info['methods'][$name]['isConstructor'] = $method->isConstructor();
         $info['methods'][$name]['isDestructor'] = $method->isDestructor();            
         $info['methods'][$name]['isStatic'] = $method->isStatic();
         $info['methods'][$name]['isPublic'] = $method->isPublic();
         $info['methods'][$name]['isProtected'] = $method->isProtected();
         $info['methods'][$name]['isPrivate'] = $method->isPrivate();
         $info['methods'][$name]['isAbstract'] = $method->isAbstract();
         $info['methods'][$name]['isFinal'] = $method->isFinal();
         $info['methods'][$name]['isInternal'] = $method->isInternal();
         $info['methods'][$name]['isUserDefined'] = $method->isUserDefined();
         $info['methods'][$name]['isClosure'] = $method->isClosure();
         $info['methods'][$name]['isDeprecated'] = $method->isDeprecated();
         $info['methods'][$name]['returnsReference'] = $method->returnsReference();
         $info['methods'][$name]['comment'] = $method->getDocComment();
         $info['methods'][$name]['startLine'] = $method->getStartLine();
         $info['methods'][$name]['endLine'] = $method->getEndLine();         
         $info['methods'][$name]['numberOfParameters'] = $method->getNumberOfParameters();
         $info['methods'][$name]['numberOfRequiredParameters'] = $method->getNumberOfRequiredParameters();
         $info['methods'][$name]['arguments'] = array();
         foreach ($method->getParameters() as $parameter)
         { 
            $class = $parameter->getClass();
            $info['methods'][$name]['arguments'][$parameter->getPosition()] = array('name' => $parameter->getName(),
                                                                                    'isDefaultValueAvailable' => $parameter->isDefaultValueAvailable(),
                                                                                    'isArray' => $parameter->isArray(),
                                                                                    'isOptional' => $parameter->isOptional(),
                                                                                    'isPassedByReference' => $parameter->isPassedByReference(),
                                                                                    'allowsNull' => $parameter->allowsNull(),
                                                                                    'class' => ($class) ? $this->getClassNames($class) : '',
                                                                                    'value' => ($parameter->isOptional()) ? $this->getPHPType($parameter->getDefaultValue()) : '');
         }
         $code = array();
         for ($n = $method->getStartLine() - 1; $n < $method->getEndLine(); $n++) $code[] = $this->lines[$key]['rows'][$n];
         $code = implode(PHP_EOL, $code);         
         $info['methods'][$name]['code'] = self::getMethodBody($code);
      }
      $this->lines[$key]['classes'][] = $info['name'];
      $this->current = $info['name']; 
      if (strlen($info['comment']))
      { 
         for ($i = $info['startLine'] - 2; $i > 0; $i--)
         { 
            if (trim($this->lines[$key]['rows'][$i]) != '') break;
         } 
         $info['startLine'] = $i - count(explode(PHP_EOL, $info['comment'])) + 2;
      }
      $this->info[$info['name']] = new Simplex($info);
   }
   
   public function __get($param)
   {
      if (isset($this->info[$param]))
      { 
         $this->current = $param;
         return $this;
      }
      else return $this->info[$this->current]->{$param};
   }
   
   public function __set($param, $value)
   {
      if (!isset($this->info[$this->current]->{$param})) throw new Exceptions\NotExistingPropertyException('Property "' . $param . '" does not exist.');
      $this->info[$this->current]->{$param} = $value;              
   }
   
   public function __isset($param)
   {
      return isset($this->info[$param]);
   }
   
   public function getCodeByClassName($class)
   {
      return $this->getCodeFile($this->info[$class]->file);
   }

   public function getCodeFile($file)
   {
      if (is_file($file)) $file = Core\IO::normalizeFileName($file);
      $code = array();
      $n = $k = 0; $max = count($this->lines[$file]['rows']);  
      while ($n < $max)
      {
         if (!$class) $class = $this->info[$this->lines[$file]['classes'][$k]];
         if ($n == $class->startLine - 1)
         { 
            $code[] = $this->getCodeClass($class->name);
            $n = $class->endLine;
            $class = '';
            $k++;
         }
         else $code[] = $this->lines[$file]['rows'][$n];
         $n++; 
      }
      return implode(PHP_EOL, $code);
   }

   public function getCodeClass($class)
   {
      $obj = $this->info[$class];
      $code = $cls = $interfaces = $constants = $properties = $methods = array();

      if ($obj->comment) $code[] = $obj->comment;                     
      if ($obj->isFinal) $cls[] = 'final';
      if ($obj->isAbstract) $cls[] = 'abstract';
      if ($obj->isInterface) $cls[] = 'interface';
      else $cls[] = 'class';
      if ($obj->inNamespace) $cls[] = $obj->shortName;
      else $cls[] = $obj->name;
      if ($obj->parentName)
      { 
         if ($obj->parentNamespace == $obj->namespace) $cls[] = 'extends ' . $obj->parentShortName;
         else $cls[] = 'extends \\' . $obj->parentName; 
      }
      
      if (count($obj->interfaces)) foreach ($obj->interfaces as $interface)
      { 
         if ($interface->namespace == $obj->namespace) $interfaces[] = $interface->shortName;
         else $interfaces[] = $interface->name;
      }
      if (count($interfaces))  $cls[] = 'implements ' . implode(', ', $interfaces);
      
      $code[] = implode(' ', $cls);
      $code[] = '{';
      
      if (count($obj->constants)) foreach ($obj->constants as $constant => $value) $constants[] = '    ' . $this->getCodeConstant($class, $constant);
      if (count($constants)) $code[] = implode(PHP_EOL, $constants) . PHP_EOL;
      
      if (count($obj->properties)) foreach ($obj->properties as $property => $value) $properties[] = $this->getCodeProperty($class, $property);
      if (count($properties)) $code[] = implode(PHP_EOL, $properties) . PHP_EOL;
      
      if (count($obj->methods)) foreach ($obj->methods as $method => $value) $methods[] = $this->getCodeMethod($class, $method);      
      if (count($methods)) $code[] = implode(PHP_EOL . PHP_EOL, $methods);
    
      $code[] = '}';
      return implode(PHP_EOL, $code) . PHP_EOL;
   }
   
   public function getCodeConstant($class, $constant)
   {
      if (!isset($this->info[$class]->constants->{$constant})) return '';
      return 'const ' . $constant . ' = ' . $this->info[$class]->constants->{$constant} . ';';
   }
   
   public function getCodeProperty($class, $property)
   {
      if (!isset($this->info[$class]->properties->{$property})) return '';
      $code = array();
      $obj = $this->info[$class]->properties->{$property};
      if ($obj->isPublic) $code[] = 'public';
      if ($obj->isProtected) $code[] = 'protected';
      if ($obj->isPrivate) $code[] = 'private';
      if ($obj->isStatic) $code[] = 'static';
      $code[] = '$' . $property;
      if ($obj->isDefault) $code[] = '= ' . $obj->value;
      return (($obj->comment) ? '    ' . $obj->comment . PHP_EOL : '') .  '    ' . implode(' ', $code) . ';';
   }

   public function getCodeMethod($class, $method)
   {
      if (!isset($this->info[$class]->methods->{$method})) return '';
      $code = $parameters = array();
      $obj = $this->info[$class]->methods->{$method};
      foreach ($obj->arguments as $parameter)
      {
         $param = '';
         if (!$parameter->allowsNull && isset($parameter->class->name))
         {
            if ($this->info[$class]->namespace == $parameter->class->namespace) $param .= $parameter->class->shortName . ' ';
            else $param .= $parameter->class->name . ' ';
         }
         if ($parameter->isArray) $param .= 'array ';
         $param .= (($parameter->isPassedByReference) ? '&' : '') . '$' . $parameter->name;
         if ($parameter->isDefaultValueAvailable) $param .= ' = ' . $parameter->value;
         $parameters[] = $param;  
      }
      if ($obj->isFinal) $code[] = 'final';
      if ($obj->isAbstract) $code[] = 'abstract';
      if ($obj->isPublic) $code[] = 'public';
      if ($obj->isProtected) $code[] = 'protected';
      if ($obj->isPrivate) $code[] = 'private';
      if ($obj->isStatic) $code[] = 'static';
      $code[] = 'function ' . $method . '(' . implode(', ', $parameters) . ')';
      $code = '    ' . implode(' ', $code);
      if ($obj->isAbstract) $code .= ';';
      else $code .= PHP_EOL . '    {' . $obj->code . '}';
      return (($obj->comment) ? '    ' . $obj->comment . PHP_EOL : '') . $code;
   }

   public function getInfo()
   {
      return $this->info;
   }
   
   private function extractAll($content, $key = null)
   {
      foreach (PHPParser::getFullClassNames($content) as $class) $this->extraction($class, $key);
   }
   
   private function getClassNames(\ReflectionClass $class)
   {
      $name = $class->getShortName();
      $namespace = $class->getNamespaceName();
      if (!$namespace) $namespace = '\\';      
      return array($namespace . (($namespace != '\\') ? '\\' : '') . $name, $name, $namespace);  
   }
   
   private function extractContentFromCode($code, $key)
   {
      if (!isset($this->lines[$key]))
      {        
         $this->lines[$key]['rows'] = explode("\n", str_replace("\r", '', $code));         
      }
   }
   
   private function extractFileContent($file)
   {                                       
      $file = Core\IO::normalizeFileName($file); 
      if (!isset($this->lines[$file]))
      { 
         $content = file_get_contents($file);       
         $this->lines[$file]['rows'] = explode("\n", str_replace("\r", '', $content));
         return $content;         
      }      
   }
   
   private static function getPHPType($value)
   {
      if (is_string($value)) return "'" . $value . "'";
      if (is_bool($value)) return ($value) ? 'true' : 'false';
      if (is_null($value)) return 'null';
      if (is_array($value))
      {
         $tmp = array();
         foreach ($value as $k => $v) $tmp[] = self::getPHPType($k) . ' => ' . self::getPHPType($v);
         return 'array(' . implode(', ', $tmp) . ')';
      }
      return $value;
   }
   
   private static function getMethodBody($code)
   {
      $tokens = PHPParser::getTokens($code);
      $max = count($tokens) - 3;
      for ($i = 1; $i < $max; $i++)
      {
         if ($tokens[$i] == '{')
         {
            $min = $i + 1;
            break;
         }  
      }
      $code = '';
      for ($i = $min; $i < $max; $i++)
      {
         $value = $tokens[$i];
         $code .= is_array($value) ? $value[1] : $value;
      }
      return $code;
   }
}

?>
