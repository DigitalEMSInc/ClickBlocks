<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\Core,
    ClickBlocks\Utils,
    ClickBlocks\MVC,
    ClickBlocks\Web,
    ClickBlocks\Web\UI\Helpers;

class Verification extends Image
{
   public function __construct($id)
   {
      parent::__construct($id);
      $this->properties['length'] = 7;
      $this->properties['chars'] = 'abcdefghijklmnopqrstvuwxyzABCDEFGHIJKLMNOPQRSTVUWXYZ123456789';
      $this->properties['value'] = null;
   }

   public function validate($type, Core\IDelegate $check)
   {
      return $check($this->attributes['value']);
   }

   public function init()
   {
      $this->properties['value'] = $this->getRandomCode();
   }

   public function render()
   {
      $this->attributes['src'] = 'data:image/gif;base64,' . $this->getVerificationImage();
      return parent::render();
   }

   public function redraw(array $parameters = null)
   {
      $this->properties['value'] = $this->getRandomCode();
      return parent::redraw($parameters);
   }

   public function getVerificationImage()
   {
      $font = Core\IO::dir('application') . '/_includes/verification/arial.ttf';
      $img = imagecreatefromjpeg(Core\IO::dir('application') . '/_includes/verification/bgcolor' . rand(1, 33) . '.jpg');
      $x = imagesx($img);
      $y = imagesy($img);
      $fontsize = 20;
      $fontsizedate = 20;
      $strx = 10;
      $dy = 18;
      $xpad = 25;
      $ypad = 25;
      $white = imagecolorallocate($img, 255, 255, 255);
      $black = imagecolorallocate($img, 0, 0, 0);
      imagettftext($img, $fontsize, 0, intval($xpad / 2 + 1), $dy + intval($ypad / 2), $black, $font, $this->properties['value']);
      imagettftext($img, $fontsizedate, 0, intval($xpad / 2), $dy + intval($ypad / 2) - 1, $black, $font, $this->properties['value']);
      ob_start();
      imagejpeg($img);
      $base64 = base64_encode(ob_get_contents());
      ob_end_clean();
      imagedestroy($img);
      return $base64;
   }

   protected function getRandomCode()
   {
      if ($this->properties['length'] < 1) $this->properties['length'] = 1;
      for ($i = 0; $i < $this->properties['length']; $i++)
      {
         $n = rand(1, strlen($this->properties['chars']));
         $code .= substr($this->properties['chars'], $n - 1, 1);
      }
      return $code;
   }
}

?>