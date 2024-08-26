<?php

namespace ClickBlocks\Web\UI\POM;

class WebForm extends Panel
{
   public function __construct($id)
   {
      parent::__construct($id);
      $this->attributes['target'] = null;
      $this->attributes['action'] = $_SERVER['REQUEST_URI'];
      $this->attributes['method'] = 'post';
      $this->attributes['enctype'] = 'application/x-www-form-urlencoded';
      $this->attributes['accept'] = null;
      $this->attributes['onsubmit'] = null;
      $this->attributes['onreset'] = null;
      $this->attributes['name'] = $id;
      $this->properties['tag'] = 'form';
   }

   public function isSubmitted()
   {
      return ($_SERVER['REQUEST_METHOD'] == 'POST');
   }

   public function isUpload()
   {
      return ($this->enctype == 'multipart/form-data');
   }

   public function uploaded()
   {
      $this->enctype = 'multipart/form-data';
      return $this;
   }
}

?>
