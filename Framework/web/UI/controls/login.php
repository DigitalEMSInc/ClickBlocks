<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core,
    ClickBlocks\MVC,
    ClickBlocks\Web,
    ClickBlocks\Web\UI\Helpers,
    ClickBlocks\Utils;

class Login extends Panel
{
   public function __construct($id)
   {
      parent::__construct($id);
      $this->properties['callBack'] = null;
      $this->properties['login'] = null;
      $this->properties['password'] = null;
      $this->properties['namespace'] = null;
      $this->properties['button'] = null;
      $this->properties['field'] = 'userID';
      $this->properties['redirect'] = null;
      $this->properties['get'] = 'redirect';
      $this->properties['groups'] = 'login';
   }

   public function load()
   {
      if (!MVC\MVC::isFirstRequest()) return;
      $this->js->addTool('controls');
      $btn = $this->get($this->properties['button']);
      $login = $this->get($this->properties['login']);
      $password = $this->get($this->properties['password']);
      $js = 'function login_' . $this->attributes['uniqueID'] . '(){' . $btn->onclick . ';}' . endl;
      $js .= 'if (document.getElementById(\'' . $login->uniqueID . '\')) document.getElementById(\'' . $login->uniqueID . '\').onkeydown = document.getElementById(\'' . $password->uniqueID . '\').onkeydown = function(e){if ((e || event).keyCode == 13) login_' . $this->attributes['uniqueID'] . '();}' . endl;
      $js .= 'controls.focus(\'' . $login->uniqueID . '\');';
      if ($btn) {
         $btn->onclick = 'login_' . $this->attributes['uniqueID'] . '();';
      }
      $this->js->add(new Helpers\Script('loginaction_' . $this->attributes['uniqueID'], $js), 'foot');
   }

   public function check(array $controls, $mode)
   {
      $login = new Core\Delegate($this->properties['callBack']);
      $userID = $login($this->get($this->properties['login'])->value, $this->get($this->properties['password'])->value);
      $_SESSION[$this->properties['namespace']][$this->properties['field']] = $userID;
      return $userID;
   }

   public function login()
   {
      unset($_SESSION[$this->properties['namespace']][$this->properties['field']]);
	  Validators::getInstance()->clean($this->properties['groups'], true);
      if (Validators::getInstance()->isValid($this->properties['groups']))
      {
         $redirect = ($this->reg->fv[$this->properties['get']]) ?: $this->properties['redirect'];
         if ($redirect) $this->ajax->redirect($redirect);
      }
   }
   
   protected function repaint()
   {
      $this->updated = $this->updated ?: 200;
      $login = $this->get($this->properties['login']);
      $password = $this->get($this->properties['password']);
      $js = 'if (document.getElementById(\'' . $login->uniqueID . '\')) document.getElementById(\'' . $login->uniqueID . '\').onkeydown = document.getElementById(\'' . $password->uniqueID . '\').onkeydown = function(e){if ((e || event).keyCode == 13) login_' . $this->attributes['uniqueID'] . '();};';
      $js .= 'controls.focus(\'' . $login->uniqueID . '\');';
      $this->ajax->script($js, 200);
      parent::repaint();
   }
}

?>
