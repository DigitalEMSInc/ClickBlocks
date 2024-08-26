<?php
/**
 * ClickBlocks.PHP v. 1.0
 *
 * Copyright (C) 2010  SARITASA LLC
 * http://www.saritasa.com
 *
 * This framework is free software. You can redistribute it and/or modify
 * it under the terms of either the current ClickBlocks.PHP License
 * viewable at theclickblocks.com) or the License that was distributed with
 * this file.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY, without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * You should have received a copy of the ClickBlocks.PHP License
 * along with this program.
 *
 * Responsibility of this file: template.php
 *
 * @category   Core
 * @package    Core
 * @copyright  2007-2010 SARITASA LLC <info@saritasa.com>
 * @link       http://www.saritasa.com
 * @since      File available since Release 1.0.0
 */

namespace ClickBlocks\Core;

/**
 * This class is templator using php as template language.
 *
 * Класс шаблонизатор, использующий php в качестве языка шаблона.
 *
 * @category  Core
 * @package   Core
 * @copyright 2007-2010 SARITASA KZ <info@saritasa.kz>
 * @version   Release: 1.0.0
 */
class Template implements ITemplate
{
   public static function create()
   {
      return new static();
   }

   /**
    * Templates' variables.
    *
    * Переменные шаблонов.
    *
    * @var array $vars
    * @access protected
    */
   protected $vars = array();

   /**
    * Array of templates.
    *
    * Массив шаблонов.
    *
    * @var array $templates
    * @access protedted
    */
   protected $templates = array();

   /**
    * Unique identifier of current template.
    *
    * Уникальный идентификатор текущего шаблона.
    *
    * @var string $key
    * @access protected
    */
   protected $key = null;

   /**
    * Uses for getting unique identifier of current template.
    *
    * Используется для получения уникального ключа текущего шаблона.
    *
    * @var boolean $flag
    * @access private
    */
   private $flag = false;

   /**
    * Returns array of templates
    *
    * Возвращает массив шаблонов
    *
    * @return array
    * @access public
    */
   public function getTemplates()
   {
      return $this->templates;
   }

   /**
    * Sets new templates.
    *
    * Устанавливает новые шаблоны.
    *
    * @var array $templates
    * @access public
    */
   public function setTemplates(array $templates)
   {
      $this->templates = $templates;
   }

   /**
    * Returns an array with information about a template.
    * Format of the array following: array('template' => ..., 'parent' => ..., 'sibs' => array(...), 'var' => ...)
    *
    * Возвращает массив с информацией о шаблоне.
    * Формат массива следующий: array('template' => ..., 'parent' => ..., 'sibs' => array(...), 'var' => ...)
    *
    * @param string $key - unique identifier of a template.
    * @return string
    * @access public
    */
   public function getTemplate($key)
   {
      $template = $this->templates[$key];
      $template['var'] = $this->templates[$template['parent']]['sibs'][$key];
      return $template;
   }

   /**
    * Sets new template.
    *
    * Устанавливает новый шаблон.
    *
    * @param string $key      - unique identifier of a template.
    * @param string $template
    * @param string $parent   - unique identifier if a parent template.
    * @param string $var      - name of php-variable in the parent template.
    * @return self
    * @throws \Exception
    * @access public
    */
   public function setTemplate($key, $template, $parent = null, $var = null)
   {
      if ($key == '') {
         throw new \Exception(err_msg('ERR_TEMPLATE_2'));
      }

      if ($parent !== null) {
         if (!isset($this->templates[$parent])) {
            throw new \Exception(err_msg('ERR_TEMPLATE_3', array($parent)));
         }

         $this->templates[$parent]['sibs'][$key] = $var;
      }
      
      $this->templates[$key] = array('template' => (string)$template, 'parent' => $parent);

      return $this;
   }

   /**
    * Deletes a template.
    *
    * Удаляет шаблон.
    *
    * @param string $key - unique identifier of a template.
    * @access public
    */
   public function deleteTemplate($key)
   {
      unset($this->templates[$this->templates[$key]['parent']]['sibs'][$key]);
      foreach ((array)$this->templates[$key]['sibs'] as $k => $var) $this->deleteTemplate($k);
      unset($this->templates[$key]);
      unset($this->vars[$key]);
   }

   /**
    * Returns the number of variables of current template.
    *
    * Возвращает число переменных текущего шаблона.
    *
    * @return integer
    * @access public
    */
   public function count()
   {
      return count((array)$this->vars[$this->getKey()]);
   }

   /**
    * This method implements \Iterator inteface.
    *
    * Данный метод реализует интерфейс \Iterator.
    *
    * @return object
    * @access public
    */
   public function getIterator()
   {
      return new BaseIterator((array)$this->vars[$this->getKey()]);
   }

   /**
    * Sets new value of template variable or a template.
    *
    * Устанавливает значение переменной шаблона или значение самого шаблона.
    *
    * @param string $key      - unique identifier of a template or variable name.
    * @param string $template - template value or variable value.
    * @access public
    */
   public function offsetSet($key, $template)
   {
      if ($this->flag) $this->vars[$this->getKey()][$key] = $template;
      else
      {
         if (!isset($this->templates[$key])) throw new \Exception(err_msg('ERR_TEMPLATE_1', array($key)));
         $this->templates[$key]['template'] = $template;
      }
   }

   /**
    * Checks whether or not a template with some unique identifier or a template variable exist.
    *
    * Проверяет существует или нет шаблон с некоторым уникальным идентификатором или переменная шаблона.
    *
    * @param string $key - unique identifier of a template or variable name.
    * @return boolean
    * @access public
    */
   public function offsetExists($key)
   {
      if ($this->flag) return isset($this->vars[$this->getKey()][$key]);
      return isset($this->templates[$key]);
   }

   /**
    * Deletes some template or variable.
    *
    * Удаляет некоторый шаблон или переменную.
    *
    * @param string $key - unique identifier of a template or variable name.
    * @access public
    */
   public function offsetUnset($key)
   {
      if ($this->flag) unset($this->vars[$this->getKey()][$key]);
      else $this->deleteTemplate($key);
   }

   /**
    * Gets template value or variable value.
    *
    * Получает значение шаблона или значение переменной.
    *
    * @param string $key - unique identifier of a template or variable name.
    * @return mixed
    * @access public
    */
   public function offsetGet($key)
   {
      if ($this->flag) return $this->vars[$this->getKey()][$key];
      if (!isset($this->templates[$key])) throw new \Exception(err_msg('ERR_TEMPLATE_1', array($key)));
      $this->key = $key;
      $this->flag = true;
      return $this;
   }

   /**
    * Adds new variable in the current template.
    *
    * Добавляет переменную в текущий шаблон.
    *
    * @param string $name - variable name.
    * @param mixed $value - variable value.
    * @access public
    */
   public function __set($name, $value)
   {
      $this->vars[$this->getKey()][$name] = $value;
   }

   /**
    * Returns variable value of the current template.
    *
    * Получает значение переменной текущего шаблона.
    *
    * @param string $name - variable name.
    * @return mixed
    * @access public
    */
   public function &__get($name)
   {
      return $this->vars[$this->getKey()][$name];
   }

   /**
    * Checks whether or not a template variable exist.
    *
    * Проверяет существует или нет переменная шаблона.
    *
    * @param string $name - variable name.
    * @return boolean
    * @access public
    */
   public function __isset($name)
   {
      return isset($this->vars[$this->getKey()][$name]);
   }

   /**
    * Deletes a template variable.
    *
    * Удаляет переменную шаблона.
    *
    * @param string $name
    * @access public
    */
   public function __unset($name)
   {
      unset($this->vars[$this->getKey()][$name]);
   }

   /**
    * Adds array of template variables.
    *
    * Добавляет массив переменных шаблона.
    *
    * @param array $vars
    * @access public
    */
   public function add(array $vars)
   {
      $key = $this->getKey();
      $this->vars[$key] = array_merge((array)$this->vars[$key], $vars);
   }

   /**
    * Removes current template variables.
    *
    * Удаляет переменные текущего шаблона.
    *
    * @access public
    */
   public function clean()
   {
      $this->vars[$this->getKey()] = array();
   }

   /**
    * Returns a rendered template.
    *
    * Возвращает отрендеренный шаблон.
    *
    * @param string $key - unique identifier of a template.
    * @return string
    * @access public
    */
   public function render($key = null)
   {
      if ($key === null)
      {
         if ($this->flag) $key = $this->getKey();
         else
         {
            reset($this->templates);
            $key = key($this->templates);
         }
      }
      $template = $this->templates[$key];
      ${']|['} = $template['template'];
      ${'}|{'} = (array)$this->vars[$key];
      foreach ((array)$template['sibs'] as $k => $var)
      {
         if (isset($this->vars[''][$var]) || isset($this->vars[$key][$var])) continue;
         $$var = $this->render($k);
      }
      extract((array)$this->vars['']);
      extract(${'}|{'});
      ob_start();
      if (strlen(${']|['})<=4096 && is_file(${']|['})) require(${']|['});
      else
      {
         Debugger::setEvalCode(${']|['});
         if (eval(' ?>' . ${']|['} . '<?php ') === false) Debugger::setEvalCode(${']|['}, true);
      }
      $content = ob_get_contents();
      ob_end_clean();
      return $content;
   }

   /**
    * Push a rendered template to a browser.
    *
    * Выдаёт отрендеренный шаблон в браузер.
    *
    * @param string $key - unique identifier of a template.
    * @access public
    */
   public function show($key = null)
   {
      echo $this->render($key);
   }

   /**
    * Converts the instance of this class to a string.
    *
    * Конвертирует объект этого класса в строку.
    *
    * @return string
    * @access public
    */
   public function __toString()
   {
      try
      {
         return ($this->flag) ? $this->templates[$this->getKey()]['template'] : $this->render();
      }
      catch (\Exception $ex)
      {
         Debugger::exceptionHandler($ex);
      }
   }

   /**
    * Returns unique identifier of the current template.
    *
    * Возвращает уникальный идентификатор текущего шаблона.
    *
    * @return string
    * @access protected
    */
   protected function getKey()
   {
      $key = ($this->flag) ? $this->key : '';
      $this->flag = false;
      return $key;
   }

   /**
    * Adds new variables in the current template.
    *
    * Добавляет переменные в текущий шаблон.
    *
    * @param array $vars An array where keys are names of variables, values are values of variables
    * @return self
    * @access public
    */
   public function setVars(array $vars)
   {
      foreach($vars as $name => $value) {
         $this->__set($name, $value);
      }

      return $this;
   }
}

?>
