<?php

namespace ClickBlocks\Utils;

use ClickBlocks\Core;

class Picture
{
   const PIC_AUTOWIDTH = 0;
   const PIC_AUTOHEIGHT = 1;
   const PIC_MANUAL = 2;
   const PIC_AUTO = 3;

   private $img = null;
   private $config = null;
   protected $src = null;
   protected $info = null;
   protected $width = null;
   protected $height = null;

   public function __construct($source = null)
   {
      $this->config = Core\Register::getInstance()->config;
      $this->setSource($source);
   }

   public function close()
   {
      if (!function_exists('NewMagickWand')) imageDestroy($this->img);
   }

   public function setSource($source)
   {
      $this->src = Core\IO::dir($source);
      $this->info = pathinfo($this->src);
      $this->img = $this->createImage();
   }

   public function getSource()
   {
      return $this->src;
   }

   public function getSize()
   {
      if (function_exists('NewMagickWand'))
      {
         MagickReadImage($this->img, $this->src);
         return array('width' => MagickGetImageWidth($this->img), 'height' => MagickGetImageHeight($this->img));
      }
      return array('width' => imagesx($this->img), 'height' => imagesy($this->img));
   }

   public function rotate($dest, $angle, $bgcolor = '#ffffff', $isTransparent=false)
   {
//      if($isTransparent) echo 'aaa';
//      else echo 'bbb';
//      return;
      $dest = Core\IO::dir($dest);
      if ($bgcolor[0] != '#') $bgcolor = '#' . $bgcolor;
      if (function_exists('NewMagickWand'))
      {
         MagickReadImage($this->img, $this->src);
         $alpha = MagickGetImagePixels($this->img, 0, 0, 1, 1, 'A', MW_CharPixel);
         if ($alpha[0] == 0 || $isTransparent) // transparent image
         {
            $pw = NewPixelWand();
            PixelSetColor($pw, 'none');
         }
         else $pw = NewPixelWand($bgcolor);
         MagickRotateImage($this->img, $pw, (float)$angle);
         return $this->saveImage($this->img, $dest);
      }
      $this->img = $this->ImageRotateRightAngle($this->img, $angle, $bgcolor);
      $this->saveImage($this->img, $dest);
   }

   public function crop($dest, $left, $top, $width, $height, $bgcolor = '#ffffff', $isSmartCrop = false, $isTransparent=false)
   {
      $dest = Core\IO::dir($dest);
      if ($bgcolor[0] != '#') $bgcolor = '#' . $bgcolor;
      if (function_exists('NewMagickWand'))
      {
         MagickReadImage($this->img, $this->src);
         if($isTransparent){
             $bgcolor = NewPixelWand();
             PixelSetColor($bgcolor, 'none');
         }
         MagickCropImage($this->img, $width, $height, $left, $top);
         $magickVersion = MagickGetVersion();
         if ($magickVersion[1] >= 1589)
         {
            // magick wrote: We added MagickResetImagePage() to replicate the functionality of the -repage option.
            // It will be available in ImageMagick 6.3.3 Beta and MagickWandForPHP 1.0.2 Beta tommorrow.
            MagickSetImageBackgroundColor($this->img, $bgcolor);
            MagickResetImagePage($this->img, $width . 'x' . $height . '-' . $left . '-' . $top);
         }
         if ($isSmartCrop)
         {
            $mw = NewMagickWand();
            MagickNewImage($mw, $width, $height, $bgcolor);
            MagickSetImageFormat($mw, 'png');
            MagickCompositeImage($mw, $this->img, MW_SrcOverCompositeOp , ($left < 0) ? -$left : 0, ($top < 0) ? -$top : 0);
            MagickWriteImage($mw, $dest);
         }
         else $this->saveImage($this->img, $dest);
      }
      else
      {
         $size = $this->getSize();
         $srcDimensions = array('top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'width' => 0, 'height' => 0);
         $destDimensions = array('top' => 0, 'left' => 0, 'width' => $width, 'height' => $height);
         $keys = array('top' => 'height', 'right' => 'width', 'bottom' => 'height', 'left' => 'width');
         $srcDimensions['top'] = $top;
         $srcDimensions['left'] = $left;
         $srcDimensions['right'] = $left + $width;
         $srcDimensions['bottom'] = $top + $height;
         foreach ($srcDimensions as $key => $value)
         {
            if (array_key_exists($key, $keys))
            {
               if ($srcDimensions[$key] < 0) $srcDimensions[$key] = 0;
               if ($srcDimensions[$key] > $size[$keys[$key]]) $srcDimensions[$key] = $size[$keys[$key]];
            }
         }
         $srcDimensions['width'] = $srcDimensions['right'] - $srcDimensions['left'];
         $srcDimensions['height'] = $srcDimensions['bottom'] - $srcDimensions['top'];
         $img = imageCreateTrueColor($destDimensions['width'], $destDimensions['height']);
         $bg = imagecolorallocate($img, 255, 255, 255);
         imagefill($img, 0, 0, $bg);
         imageCopy($img, $this->img, $destDimensions['left'], $destDimensions['top'], $srcDimensions['left'], $srcDimensions['top'], $srcDimensions['width'], $srcDimensions['height']);
         $this->saveImage($img, $dest);
      }
   }

   public function resize($dest, $width, $height, $mode = self::PIC_MANUAL, $maxwidth = null, $maxheight = null)
   {
      $dest = Core\IO::dir($dest);
      if (function_exists('NewMagickWand'))
      {
         MagickReadImage($this->img, $this->src);
         $size = $this->getSize();
         $this->setSize($width, $height, $mode, $size['width'], $size['height'], $maxwidth, $maxheight);
         MagickScaleImage($this->img, $this->width, $this->height);
         return $this->saveImage($this->img, $dest);
      }
      $size = $this->getSize();
      $this->width = $size['width']; $ow = $this->width;
      $this->height = $size['height']; $oh = $this->height;
      $this->setSize($width, $height, $mode, $this->width, $this->height, $maxwidth, $maxheight);
      $img = ImageCreateTrueColor($this->width, $this->height);
      $bg = imagecolorallocate($img, 255, 255, 255);
      imagefill($img, 0, 0, $bg);
      imagecopyresized($img, $this->img, 0, 0, 0, 0, $this->width, $this->height, $ow, $oh);
      $this->saveImage($img, $dest);
   }

   // $imgSrc - GD image handle of source image
   // $angle - angle of rotation. Needs to be positive integer
   // angle shall be 0,90,180,270, but if you give other it
   // will be rouned to nearest right angle (i.e. 52->90 degs, 96->90 degs)
   // returns GD image handle of rotated image.
   protected function ImageRotateRightAngle($imgSrc, $angle, $bgcolor = '#ffffff')
   {
      $angle = min(((int)(($angle + 45) / 90) * 90), 270);
      if ($angle == 0) return $imgSrc;
      $srcX = imagesx($imgSrc);
      $srcY = imagesy($imgSrc);
      switch ($angle)
      {
         case 90:
           $imgDest = imagecreatetruecolor($srcY, $srcX);
           for ($x = 0; $x < $srcX; $x++)
           {
              for ($y = 0; $y < $srcY; $y++)
              {
                 imagecopy($imgDest, $imgSrc, $srcY - $y - 1, $x, $x, $y, 1, 1);
              }
           }
           break;
         case 270:
           $imgDest = imagecreatetruecolor($srcY, $srcX);
           for ($x = 0; $x < $srcX; $x++)
           {
              for ($y = 0; $y < $srcY; $y++)
              {
                 imagecopy($imgDest, $imgSrc, $y, $srcX - $x - 1, $x, $y, 1, 1);
              }
           }
           break;
         default:
           return $imgSrc;
      }
      return $imgDest;
   }

   protected function setSize($width, $height, $mode, $w, $h, $maxwidth, $maxheight)
   {
      switch ($mode)
      {
         case self::PIC_AUTOWIDTH:
           $nh = $height;
           if ($maxheight > 0 && $nh > $maxheight)
           {
              $nh = $maxheight;
              $height = $maxheight;
           }
           $nw = $height / $h * $w;
           if ($maxwidth > 0 && $nw > $maxwidth) $nw = $maxwidth;
           break;
         case self::PIC_AUTOHEIGHT:
           $nw = $width;
           if ($maxwidth > 0 && $nw > $maxwidth)
           {
              $nw = $maxwidth;
              $width = $maxwidth;
           }
           $nh = $width / $w * $h;
           if ($maxheight > 0 && $nh > $maxheight) $nh = $maxheight;
           break;
         case self::PIC_MANUAL;
           $nw = $width;
           $nh = $height;
           if ($maxwidth > 0 && $nw > $maxwidth) $nw = $maxwidth;
           if ($maxheight > 0 && $nh > $maxheight) $nh = $maxheight;
           break;
         case self::PIC_AUTO:
         default:
           $nw = $w;
           $nh = $h;
           if ($maxwidth > 0 && $nw > $maxwidth)
           {
              $nw = $maxwidth;
              $nh = $nw / $w * $h;
           }
           if ($maxheight > 0 && $nh > $maxheight)
           {
              $nh = $maxheight;
              $nw = $nh / $h * $w;
           }
           break;
      }
      $this->height = $nh; $this->width = $nw;
   }

   private function saveImage($img, $dest)
   {
      if (function_exists('NewMagickWand')) return MagickWriteImage($img, $dest);
      switch (strtolower($this->info['extension']))
      {
         case 'png':
           imagePNG($img, $dest);
           break;
         case 'gif':
           imageGIF($img, $dest);
           break;
         case 'jpeg':
         case 'jpg':
           imageJPEG($img, $dest);
           break;
      }
      return imageDestroy($img);
   }

   public function createImage()
   {
      if (function_exists('NewMagickWand')) return NewMagickWand();
      try
      {
         switch (strtolower($this->info['extension']))
         {
            case 'png':
              return imagecreatefrompng($this->src);
              break;
            case 'gif':
              return imagecreatefromgif($this->src);
              break;
            case 'jpeg':
            case 'jpg':
              return imagecreatefromjpeg($this->src);
              break;
            default: // if extension empty
              return $this->createImageTry();
         }
      }
      catch(Exception $e)
      {
         return $this->createImageTry();
      }
   }

   private function createImageTry()
   {
      try
      {
         return imagecreatefromgif($this->src);
      }
      catch(\Exception $e)
      {
         try
         {
            return imagecreatefromjpeg($this->src);
         }
         catch(\Exception $e)
         {
            try
            {
               return imagecreatefrompng($this->src);
            }
            catch(\Exception $e)
            {
               return false;
            }
         }
      }
   }
}

?>