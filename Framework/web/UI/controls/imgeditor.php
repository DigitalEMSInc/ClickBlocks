<?php

namespace ClickBlocks\Web\UI\POM;

use ClickBlocks\App\Storage;
use ClickBlocks\Core,
    ClickBlocks\Web,
    ClickBlocks\Web\UI\Helpers,
    ClickBlocks\Utils;

class ImgEditor extends Panel
{
   public function __construct($id)
   {
      parent::__construct($id);
      $this->properties['uploads'] = array();
      $this->properties['isProgress'] = false;
      $this->properties['messageID'] = null;
      $this->properties['hidden'] = false;
      $this->properties['showLoader'] = true;
      $this->attributes['class'] = 'imgeditor_main';
   }

   public function parse(array &$attributes, Core\ITemplate $tpl)
   {
      if (!$attributes['template']) $attributes['template'] = $this->reg->config->root . '/Framework/_engine/web/js/imgeditor/imgeditor.html';
      return parent::parse($attributes, $tpl);
   }

   public function init()
   {
      $this->tpl->uniqueID = $this->attributes['uniqueID'];
      $this->initUploads($this->properties['uploads']);
   }

   public function __set($param, $value)
   {
      if ($param == 'uploads' && !Web\XHTMLParser::isParsing()) $this->initUploads($value);
      else parent::__set($param, $value);
   }

   public function &__get($param)
   {
      if ($param == 'uploads') return $this->properties['uploads'];
      return parent::__get($param);
   }

   protected function initUploads(array $value)
   {
      $class = get_class($this);
      $tmp = array();
      foreach ($value as $id => $params)
      {
         $ctrl = $this->page->getByUniqueID($id);
         if (!$ctrl) $ctrl = $this->page->get($id);
         if ($ctrl === false) throw new \Exception(err_msg('ERR_CTRL_14', array($id)));
         $ctrl->isProgress = $this->properties['isProgress'];
         $ctrl->callBackProgress = $class . '@' . $this->attributes['uniqueID'] . '->uploadProgress_' . $ctrl->uniqueID;
         $ctrl->callBack = $class . '@' . $this->attributes['uniqueID'] . '->uploadImage_' . $ctrl->uniqueID;
         if ($this->properties['showLoader']) $ctrl->onchange .= 'ajax.options.onShowLoader = showLoader;ajax.options.onHideLoader = hideLoader;';
         $tmp[$ctrl->uniqueID] = $params;
      }
      $this->properties['uploads'] = $tmp;
   }

   public function CSS()
   {
      parent::CSS();
      if (Web\Ajax::isAction()) $this->ajax->css('/Framework/_engine/web/js/imgeditor/imgeditor.css', 0, true);
      else $this->css->add(new Helpers\Style('imgeditor', null, '/Framework/_engine/web/js/imgeditor/imgeditor.css'));
      return $this;
   }

   public function JS()
   {
      parent::JS();
      $this->js->addTool('imgeditor');
      if ($this->properties['visible'])
      {
         $script = 'var iem_' . $this->attributes['uniqueID'] . ' = new ImageEditorManager();iem_' . $this->attributes['uniqueID'] .'.initialize(\'' . $this->attributes['uniqueID'] . '\');';
         if (Web\Ajax::isAction()) $this->ajax->script($script, $this->updated, true);
         else $this->js->add(new Helpers\Script('imgeditor_' . $this->attributes['uniqueID'], $script), 'foot');
      }
      return $this;
   }

   public function __call($method, $params)
   {
      if (substr($method, 0, 14) == 'uploadProgress') return $this->uploadProgress(substr($method, 15), $params[0]);
      else if (substr($method, 0, 11) == 'uploadImage') return $this->uploadImage(substr($method, 12), $params[0]);
      throw new \Exception(err_msg('ERR_GENERAL_7', array($method, get_class($this))));
   }

   public function uploadProgress($key)
   {
     /* if (!$key) return;
        $progress = new Progress($key);
        $per = number_format($progress->getPercent(), 2);
        $this->ajax->addAction('insert', $per . '%', 'percent1');
        $this->ajax->addAction('script', "$('percent2').style.width = '" . $per . "%'");
        $progress->doNext($id, $this->page->control($id)->callBackProgress);
        if (!$progress->isUploaded() && !$_SESSION['imgeditor'][$key]['isCancel']) $this->ajax->addAction('script', "if (!popupProgress) popupProgress = new Popup({windowId: 'fileload', isCentre: true})");
        if ($progress->isCanceled())
        {
          $this->ajax->addAction('alert', 'ERROR! Impossible to load file. Try later.');
          $this->ajax->addAction('reload');
        } */
   }

   public function editImage($uniqueID)
   {
      $params = $this->properties['uploads'][$uniqueID];
      $info = Utils\UploadFile::getInfo($uniqueID);
      $iem = 'iem_' . $this->attributes['uniqueID'];
      $this->get('btnUseOriginal')->onclick = $this->method('useOriginalImage', array($uniqueID, 1));
      $this->get('btnUseThisImage')->onclick = $this->method('useNewImage', array($uniqueID, 'js::' . $iem . '.edt.options.angle', 'js::' . $iem . '.edt.options.scale', 'js::' . $iem . '.edt.getCropParameters()', 1));
      $this->get('btnCancel')->onclick = 'iem_' . $this->attributes['uniqueID'] . '.hide();';
      list($width, $height, $type, $attr) = getimagesize($info['fullname']);
      $options = array('editorID' => 'image_area_' . $this->attributes['uniqueID'],
                       'zoomID' => $this->get('zoom')->uniqueID,
                       'angleID' => $this->get('angle')->uniqueID,
                       'cropWidthID' => 'crop_width_' . $this->attributes['uniqueID'],
                       'cropHeightID' => 'crop_height_' . $this->attributes['uniqueID'],
                       'cropResizable' => $params['cropResizable']);
      if ((int)$params['cropWidth']) $options['cropWidth'] = (int) $params['cropWidth'];
      if ((int)$params['cropHeight']) $options['cropHeight'] = (int) $params['cropHeight'];
      if ((int)$params['cropMaxWidth']) $options['cropMaxWidth'] = (int)$params['cropMaxWidth'];
      if ((int)$params['cropMaxHeight']) $options['cropMaxHeight'] = (int)$params['cropMaxHeight'];
      $ops = array();
      foreach ($options as $k => $v) $ops[] = $k . ': ' . json_encode($v);
      $this->ajax->script('parent.' . $iem . '.init(\'' . addslashes($info['url']) . '\', ' . (int)$width . ', ' . (int)$height . ', {' . implode(', ', $ops) . '})');
   }

   public function uploadImage($uniqueID)
   {
      if (!count($_FILES))
      {
          $msg = $this->page->get($this->properties['messageID']);
          if ($msg)
          {
             $msg->tpl->title = 'Error';
             $msg->tpl->text = '* Unknown error';
             $msg->get('btnNo')->visible = false;
             $msg->get('btnYes')->visible = false;
             $msg->show();
          }
          else $this->ajax->message('* Unknown error', $this->properties['messageID'], 5000);
          $this->page->getByUniqueID($uniqueID)->update();
          return;
      }
      $params = $this->properties['uploads'][$uniqueID];
      Utils\UploadFile::clean($uniqueID);
      $file = new Utils\UploadFile($uniqueID);
      $file->mode = Utils\UploadFile::UPLOAD_MODE_TEMP;
      $file->maxsize = (isset($params['maxSize'])) ? : MAX_PICTURE_SIZE;
      $file->unique = true;
      $file->extensions = (isset($params['extensions'])) ? : array('jpg', 'jpeg', 'jpe', 'png', 'gif');
      if (is_array($params['thumbnails'])) foreach ($params['thumbnails'] as $data) $file->addThumbnail($data);
      if (($info = $file->upload($this->page->fv[$uniqueID])) !== false)
      {
         list($width, $height, $type, $attr) = getimagesize($info['fullname']);
         $this->page->getByUniqueID($uniqueID)->update();
         if ($this->properties['hidden'])
         {
            $this->useNewImage($uniqueID, 0, 100, array('x' => ($width - $params['cropWidth']) / 2, 'y' => ($height - $params['cropHeight']) / 2, 'width' => $params['cropWidth'], 'height' => $params['cropHeight']));
            return;
         }
         $iem = 'iem_' . $this->attributes['uniqueID'];
         $this->get('btnUseOriginal')->onclick = $this->method('useOriginalImage', array($uniqueID));
         $this->get('btnUseThisImage')->onclick = $this->method('useNewImage', array($uniqueID, 'js::' . $iem . '.edt.options.angle', 'js::' . $iem . '.edt.options.scale', 'js::' . $iem . '.edt.getCropParameters()'));
         $this->get('btnCancel')->onclick = 'iem_' . $this->attributes['uniqueID'] . '.hide();' . $this->method('cancel', array($uniqueID));
         $options = array('editorID' => 'image_area_' . $this->attributes['uniqueID'],
                          'zoomID' => $this->get('zoom')->uniqueID,
                          'angleID' => $this->get('angle')->uniqueID,
                          'cropWidthID' => 'crop_width_' . $this->attributes['uniqueID'],
                          'cropHeightID' => 'crop_height_' . $this->attributes['uniqueID'],
                          'cropResizable' => $params['cropResizable']);
         if ((int)$params['cropWidth']) $options['cropWidth'] = (int) $params['cropWidth'];
         if ((int)$params['cropHeight']) $options['cropHeight'] = (int) $params['cropHeight'];
         if ((int)$params['cropMaxWidth']) $options['cropMaxWidth'] = (int)$params['cropMaxWidth'];
         if ((int)$params['cropMaxHeight']) $options['cropMaxHeight'] = (int)$params['cropMaxHeight'];
         $ops = array();
         foreach ($options as $k => $v) $ops[] = $k . ': ' . json_encode($v);
         Storage::getInstance('temp')->moveFileToStorage($info['fullname'], $info['name']);
         if ($this->properties['showLoader']) $this->ajax->script('parent.ajax.options.onHideLoader();parent.ajax.options.onShowLoader = null;parent.ajax.options.onHideLoader = null;');
         $this->ajax->script('parent.' . $iem . '.init(\'' . addslashes(Storage::getInstance('temp')->getUrl($info['name'])) . '\', ' . (int)$width . ', ' . (int)$height . ', {' . implode(', ', $ops) . '})');
      }
      else
      {
         switch ($file->error)
         {
            case UPLOAD_ERR_INI_SIZE:
              $message = 'The uploaded file exceeds the upload_max_filesize (' . ini_get('upload_max_filesize') . ') directive in php.ini.';
              break;
            case UPLOAD_ERR_FORM_SIZE:
              $message = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
              break;
            case UPLOAD_ERR_PARTIAL:
              $message = 'The uploaded file was only partially uploaded.';
              break;
            case UPLOAD_ERR_NO_FILE:
              $message = 'No file was uploaded.';
              break;
            case UPLOAD_ERR_NO_TMP_DIR:
              $message = 'Missing a temporary folder.';
              break;
            case UPLOAD_ERR_CANT_WRITE:
              $message = 'Failed to write file to disk.';
              break;
            case UPLOAD_ERR_EXTENSION:
              $message = 'File upload stopped by extension.';
              break;
            case 10:
              $message = 'We only accept image files such as ' . implode(', ', $file->extensions) . '.';
              break;
            case 11:
              $message = 'The file size should not exceed ' . number_format(MAX_PICTURE_SIZE / (1024 * 1024), 2) . 'M';
              break;
            default:
              $message = 'File uploading is failed (error code is ' . $file->error . ').';
              break;
          }
          $msg = $this->page->get($this->properties['messageID']);
          if ($msg)
          {
             $msg->tpl->title = 'Error';
             $msg->tpl->text = $message;
             $msg->get('btnNo')->visible = false;
             $msg->get('btnYes')->visible = false;
             $msg->show();
          }
          else $this->ajax->message($message, $this->properties['messageID'], 5000);
          $this->page->getByUniqueID($uniqueID)->update();
      }
   }

   public function useOriginalImage($uniqueID, $isEdit = false)
   {
      $params = $this->properties['uploads'][$uniqueID];
      $this->ajax->script('iem_' . $this->attributes['uniqueID'] . '.hide()');
      if (!$isEdit)
      {
         if ($params['callBack'])
         {
            $method = new Core\Delegate($params['callBack']);
            $method($uniqueID);
         }
      }
      else
      {
         if ($params['callBackEdit'])
         {
            $method = new Core\Delegate($params['callBackEdit']);
            $method($uniqueID);
         }
      }
   }

   public function useNewImage($uniqueID, $angle, $scale, $cropInfo, $isEdit = false)
   {
      $params = $this->properties['uploads'][$uniqueID];
      $info = Utils\UploadFile::getInfo($uniqueID);
      if (!Storage::getInstance('temp')->isExists($info['name'])) return;
      Storage::getInstance('temp')->copyToFile($info['name'], $info['fullname']);
      list($width, $height, $type, $attr) = getimagesize($info['fullname']);
      $scale = $scale / 100;
      $this->ajax->script('iem_' . $this->attributes['uniqueID'] . '.hide()');
      $this->scale($info['fullname'], $info['fullname'], $width * $scale, $height * $scale);
      $this->rotate($info['fullname'], $info['fullname'], $angle, $this->get('backgroundColor')->value, $this->get('isTransparent')->checked);
      if (!$params['hideCrop']) $this->crop($info['fullname'], $info['fullname'], $cropInfo['x'], $cropInfo['y'], $cropInfo['width'], $cropInfo['height'], $this->get('backgroundColor')->value, $this->get('isSmartCrop')->checked, $this->get('isTransparent')->checked);
      if (is_array($info['thumbnails']))
      {
         $nails = $params['thumbnails'];
         foreach ($info['thumbnails'] as $n => $data)
         {
            $pic = new Utils\Picture($info['fullname']);
            $pic->resize($data['fullname'], intval($nails[$n]['width']), intval($nails[$n]['height']), $nails[$n]['mode'], intval($nails[$n]['maxWidth']), intval($nails[$n]['maxHeight']));
         }
      }
      if (!$isEdit)
      {
         if ($params['callBack'])
         {
            $method = new Core\Delegate($params['callBack']);
            $method($uniqueID);
         }
      }
      else
      {
         if ($params['callBackEdit'])
         {
            $method = new Core\Delegate($params['callBackEdit']);
            $method($uniqueID);
         }
      }
   }

   public function cancel($uniqueID)
   {
      Utils\UploadFile::delete($uniqueID);
      $params = $this->properties['uploads'][$uniqueID];
      if ($params['callBackCancel'])
      {
         $method = new Core\Delegate($params['callBackCancel']);
         $method($uniqueID);
      }
   }

   protected function repaint()
   {
      parent::repaint();
      $this->JS();
      $this->CSS();
      foreach ($this as $ctrl) $ctrl->repaint();
   }

   private function scale($src, $dest, $width, $height)
   {
      foo(new Utils\Picture($src))->resize($dest, $width, $height, Utils\Picture::PIC_MANUAL);
   }

   private function rotate($src, $dest, $angle, $bgcolor, $isTransparent)
   {
      foo(new Utils\Picture($src))->rotate($dest, $angle, $bgcolor, $isTransparent);
   }

   private function crop($src, $dest, $left, $top, $width, $height, $bgcolor, $isSmartCrop, $isTransparent)
   {
      foo(new Utils\Picture($src))->crop($dest, $left, $top, $width, $height, $bgcolor, $isSmartCrop, $isTransparent);
   }
}

?>