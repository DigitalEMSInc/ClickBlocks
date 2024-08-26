<?php

namespace ClickBlocks\Web\UI\POM;

class ValidatorEmail extends ValidatorRegularExpression
{
   const EMAIL_REG_EXP = '/^[0-9a-z_\.\-]+@[0-9a-z_^\.\-]+\.[a-z]{2,6}$/i';

   public function __construct($id, $message = null)
   {
      parent::__construct($id, $message);
      $this->type = 'email';
      $this->properties['expression'] = self::EMAIL_REG_EXP;
   }
}
