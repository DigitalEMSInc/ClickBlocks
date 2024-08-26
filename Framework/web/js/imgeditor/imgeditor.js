var ImageEditor = function()
{
   this.options =
   {
      ID: '',
      editorID: '',
      cropWidthID: '',
      cropHeightID: '',
      zoomID: '',
      angleID: '',
      canvasWidth: 1280,
      canvasHeight: 450,
      cropWidth: 50,
      cropHeight: 50,
      cropMinWidth: 10,
      cropMinHeight: 10,
      cropMaxWidth: 1280,
      cropMaxHeight: 515,
      cropResizable: false,
      scale: 100,
      angle: 0,
      opacity: 0.5
   };

   this.initialize = function(options)
   {
      options = options || {};
      for (var option in this.options)
      {
         if (typeof(options[option]) != 'undefined') this.options[option] = options[option];
      }
      var el, bind = this;
      var fn = function(e)
      {
         e = e || event;
         pos = controls.getClientPosition(e);
         bind.sx = pos.x;
         bind.sy = pos.y;
         bind.isMove = true;
         controls.stopEvent(e);
      };
      var area = controls.$(this.options.editorID);
      area.innerHTML = '';
      controls.addClass(area, 'imgeditor_area');
      controls.addEvent(area, 'selectstart', function(){return false;});

      var shadow = document.createElement('div');
      shadow.id = this.options.editorID + '_shadow';
      shadow.className = 'imgeditor_shadow';
      shadow.style.display = 'none';
      shadow.style.position = 'absolute';

      var crop = document.createElement('div');
      crop.id = this.options.editorID + '_crop';
      crop.className = 'imgeditor_crop';
      crop.style.position =  'absolute';
      crop.style.width = this.options.cropWidth + 'px';
      crop.style.height = this.options.cropHeight + 'px';
      shadow.appendChild(crop);

      el = document.createElement('div');
      el.id = this.options.editorID + '_snap_left_top';
      el.className = 'imgeditor_crop_snap';
      el.style.cursor = 'nw-resize';
      el.style.position = 'absolute';
      el.style.display = 'none';
      shadow.appendChild(el);
      el = el.cloneNode(true);
      el.id = this.options.editorID + '_snap_top';
      el.style.cursor = 's-resize';
      shadow.appendChild(el);
      el = el.cloneNode(true);
      el.id = this.options.editorID + '_snap_right_top';
      el.style.cursor = 'ne-resize';
      shadow.appendChild(el);
      el = el.cloneNode(true);
      el.id = this.options.editorID + '_snap_left';
      el.style.cursor = 'e-resize';
      shadow.appendChild(el);
      el = el.cloneNode(true);
      el.id = this.options.editorID + '_snap_right';
      el.style.cursor = 'e-resize';
      shadow.appendChild(el);
      el = el.cloneNode(true);
      el.id = this.options.editorID + '_snap_left_bottom';
      el.style.cursor = 'ne-resize';
      shadow.appendChild(el);
      el = el.cloneNode(true);
      el.id = this.options.editorID + '_snap_bottom';
      el.style.cursor = 's-resize';
      shadow.appendChild(el);
      el = el.cloneNode(true);
      el.id = this.options.editorID + '_snap_right_bottom';
      el.style.cursor = 'nw-resize';
      shadow.appendChild(el);

      el = document.createElement('div');
      el.id = this.options.editorID + '_shadow_top';
      el.className = 'imgeditor_shadow_top';
      el.style.position = 'absolute';
      el.onmousedown = fn;
      shadow.appendChild(el);

      el = document.createElement('div');
      el.id = this.options.editorID + '_shadow_left';
      el.className = 'imgeditor_shadow_left';
      el.style.position = 'absolute';
      el.onmousedown = fn;
      shadow.appendChild(el);

      el = document.createElement('div');
      el.id = this.options.editorID + '_shadow_right';
      el.className = 'imgeditor_shadow_right';
      el.style.position = 'absolute';
      el.onmousedown = fn;
      shadow.appendChild(el);

      el = document.createElement('div');
      el.id = this.options.editorID + '_shadow_bottom';
      el.className = 'imgeditor_shadow_bottom';
      el.style.position = 'absolute';
      el.onmousedown = fn;
      shadow.appendChild(el);

      area.appendChild(shadow);

      el = document.createElement('div');
      el.id = this.options.editorID + '_container';
      el.className = 'imgeditor_container';
      area.appendChild(el);

      this.paper = Raphael(this.options.editorID + '_container', this.options.canvasWidth, this.options.canvasHeight);

      controls.makeDraggable(crop, this.options.editorID, null, function(){bind.redrawShadow();});
      controls.addEvent(this.options.editorID + '_container', 'mousedown', fn);

      controls.addEvent(document.body, 'mousemove', function(e)
      {
         e = e || event;
         pos = controls.getClientPosition(e);
         if (bind.isMove)
         {
            bind.transform(bind.options.angle, bind.options.scale, pos.x - bind.sx, pos.y - bind.sy);
            bind.sx = pos.x;
            bind.sy = pos.y;
            return;
         }
      });
      controls.addEvent(document.body, 'mouseup', function(){bind.isMove = false;});

      var resize = function(snap)
      {
         bind.cropTop = parseInt(crop.style.top);
         bind.cropLeft = parseInt(crop.style.left);
         var size = controls.getSize(crop);
         bind.cropBottom = bind.cropTop + size.y;
         bind.cropRight = bind.cropLeft + size.x;
      };

      var drag = function(snap)
      {
         var y = parseInt(snap.style.top), x = parseInt(snap.style.left);
         switch (snap.id.substr(bind.options.editorID.length + 6))
         {
            case 'top':
              if (bind.cropBottom - y > bind.options.cropMaxHeight) y = bind.cropBottom - bind.options.cropMaxHeight;
              if (bind.cropBottom - y < bind.options.cropMinHeight) y = bind.cropBottom - bind.options.cropMinHeight;
              crop.style.top = (y - 2) + 'px';
              crop.style.height = (bind.cropBottom - y) + 'px';
              break;
            case 'bottom':
              if (y - bind.cropTop > bind.options.cropMaxHeight) y = bind.cropTop + bind.options.cropMaxHeight;
              if (y - bind.cropTop < bind.options.cropMinHeight) y = bind.cropTop + bind.options.cropMinHeight;
              crop.style.height = (y - bind.cropTop) + 'px';
              break;
            case 'left':
              if (bind.cropRight - x > bind.options.cropMaxWidth) x = bind.cropRight - bind.options.cropMaxWidth;
              if (bind.cropRight - x < bind.options.cropMinWidth) x = bind.cropRight - bind.options.cropMinWidth;
              crop.style.left = (x - 2) + 'px';
              crop.style.width = (bind.cropRight - x) + 'px';
              break;
            case 'right':
              if (x - bind.cropLeft > bind.options.cropMaxWidth) x = bind.cropLeft + bind.options.cropMaxWidth;
              if (x - bind.cropLeft < bind.options.cropMinWidth) x = bind.cropLeft + bind.options.cropMinWidth;
              crop.style.width = (x - bind.cropLeft) + 'px';
              break;
            case 'left_top':
              if (bind.cropBottom - y > bind.options.cropMaxHeight) y = bind.cropBottom - bind.options.cropMaxHeight;
              if (bind.cropBottom - y < bind.options.cropMinHeight) y = bind.cropBottom - bind.options.cropMinHeight;
              if (bind.cropRight - x > bind.options.cropMaxWidth) x = bind.cropRight - bind.options.cropMaxWidth;
              if (bind.cropRight - x < bind.options.cropMinWidth) x = bind.cropRight - bind.options.cropMinWidth;
              crop.style.top = (y - 2) + 'px';
              crop.style.height = (bind.cropBottom - y) + 'px';
              crop.style.left = (x - 2) + 'px';
              crop.style.width = (bind.cropRight - x) + 'px';
              break;
            case 'right_top':
              if (bind.cropBottom - y > bind.options.cropMaxHeight) y = bind.cropBottom - bind.options.cropMaxHeight;
              if (bind.cropBottom - y < bind.options.cropMinHeight) y = bind.cropBottom - bind.options.cropMinHeight;
              if (x - bind.cropLeft > bind.options.cropMaxWidth) x = bind.cropLeft + bind.options.cropMaxWidth;
              if (x - bind.cropLeft < bind.options.cropMinWidth) x = bind.cropLeft + bind.options.cropMinWidth;
              crop.style.top = (y - 2) + 'px';
              crop.style.height = (bind.cropBottom - y) + 'px';
              crop.style.width = (x - bind.cropLeft) + 'px';
              break;
            case 'left_bottom':
              if (y - bind.cropTop > bind.options.cropMaxHeight) y = bind.cropTop + bind.options.cropMaxHeight;
              if (y - bind.cropTop < bind.options.cropMinHeight) y = bind.cropTop + bind.options.cropMinHeight;
              if (bind.cropRight - x > bind.options.cropMaxWidth) x = bind.cropRight - bind.options.cropMaxWidth;
              if (bind.cropRight - x < bind.options.cropMinWidth) x = bind.cropRight - bind.options.cropMinWidth;
              crop.style.height = (y - bind.cropTop) + 'px';
              crop.style.left = (x - 2) + 'px';
              crop.style.width = (bind.cropRight - x) + 'px';
              break;
            case 'right_bottom':
              if (y - bind.cropTop > bind.options.cropMaxHeight) y = bind.cropTop + bind.options.cropMaxHeight;
              if (y - bind.cropTop < bind.options.cropMinHeight) y = bind.cropTop + bind.options.cropMinHeight;
              if (x - bind.cropLeft > bind.options.cropMaxWidth) x = bind.cropLeft + bind.options.cropMaxWidth;
              if (x - bind.cropLeft < bind.options.cropMinWidth) x = bind.cropLeft + bind.options.cropMinWidth;
              crop.style.height = (y - bind.cropTop) + 'px';
              crop.style.width = (x - bind.cropLeft) + 'px';
              break;
         }
         if (controls.$(bind.options.cropWidthID)) controls.$(bind.options.cropWidthID).innerHTML = parseInt(crop.style.width);
         if (controls.$(bind.options.cropHeightID)) controls.$(bind.options.cropHeightID).innerHTML = parseInt(crop.style.height);
         bind.redrawShadow();
      };
      var size = controls.getCoordinates(area);
      var limit = {'x': [size.left + 2, size.right - 2], 'y': [size.top + 2, size.bottom - 2]};
      var limitX = {'x': limit.x}, limitY = {'y': limit.y};
      controls.makeDraggable(this.options.editorID + '_snap_top', null, resize, drag, null, limitY);
      controls.makeDraggable(this.options.editorID + '_snap_bottom', null, resize, drag, null, limitY);
      controls.makeDraggable(this.options.editorID + '_snap_left', null, resize, drag, null, limitX);
      controls.makeDraggable(this.options.editorID + '_snap_right', null, resize, drag, null, limitX);
      controls.makeDraggable(this.options.editorID + '_snap_left_top', null, resize, drag, null, limit);
      controls.makeDraggable(this.options.editorID + '_snap_right_top', null, resize, drag, null, limit);
      controls.makeDraggable(this.options.editorID + '_snap_left_bottom', null, resize, drag, null, limit);
      controls.makeDraggable(this.options.editorID + '_snap_right_bottom', null, resize, drag, null, limit);
      var transformImage = function(e)
      {
         var code = (e || event).keyCode;
         if (code == undefined || code == 0 || code == 13)
         {
            var angle = parseFloat(controls.$(bind.options.angleID).value), scale = parseFloat(controls.$(bind.options.zoomID).value);
            if (isNaN(angle)) angle = 0;
            if (isNaN(scale)) scale = 100;
            if (scale < 0) scale = -scale;
            if (angle < 0) angle = 0;
            if (angle > 360) angle = 360;
            if (scale < 1) scale = 1;
            if (scale > 200) scale = 200;
            bind.transform(angle, scale, 0, 0);
            controls.$(bind.options.angleID).value = angle;
            controls.$(bind.options.zoomID).value = scale;
            if (typeof(iem) != 'undefined')
            {
               iem._setSliderValue('slider_rotate_' + bind.options.ID, angle, 0, 360);
               iem._setSliderValue('slider_zoom_' + bind.options.ID, scale, 1, 200);
            }
         }
      };
      if (controls.$(this.options.zoomID))
      {
         controls.addEvent(this.options.zoomID, 'keydown', transformImage);
         controls.addEvent(this.options.zoomID, 'blur', transformImage);
      }
      if (controls.$(this.options.angleID))
      {
         controls.addEvent(this.options.angleID, 'keydown', transformImage);
         controls.addEvent(this.options.angleID, 'blur', transformImage);
      }
   };

   this.redrawShadow = function()
   {
      if (controls.$(this.options.editorID + '_shadow').style.display == 'none') return;
      var crop = controls.$(this.options.editorID + '_crop');
      var cropSize = controls.getSize(crop);
      var area = controls.$(this.options.editorID);
      var size = controls.getSize(area);
      var top = controls.$(this.options.editorID + '_shadow_top');
      var left = controls.$(this.options.editorID + '_shadow_left');
      var right = controls.$(this.options.editorID + '_shadow_right');
      var bottom = controls.$(this.options.editorID + '_shadow_bottom');
      var bx = parseInt(area.style.borderLeftWidth) + parseInt(area.style.borderRightWidth);
      var by = parseInt(area.style.borderTopWidth) + parseInt(area.style.borderBottomWidth);
      if (!bx) bx = 0;
      if (!by) by = 0;
      var cx = parseInt(crop.style.left), cy = parseInt(crop.style.top);
      top.style.top = '0px';
      top.style.left = '0px';
      top.style.width = (size.x - bx - 1) + 'px';
      top.style.height = ((cy < 0) ? 0 : cy) + 'px';
      controls.setOpacity(top, this.options.opacity);
      left.style.top = cy + 'px';
      left.style.left = '0px';
      left.style.width = ((cx < 0) ? 0 : cx) + 'px';
      left.style.height = cropSize.y + 'px';
      controls.setOpacity(left, this.options.opacity);
      var w = size.x - cx - cropSize.x - bx;
      right.style.top = cy + 'px';
      right.style.left = (cx + cropSize.x) + 'px';
      right.style.width = ((w < 0) ? 0 : w - 1) + 'px';
      right.style.height = cropSize.y + 'px';
      controls.setOpacity(right, this.options.opacity);
      var h = size.y - cy - cropSize.y - by;
      bottom.style.top = (cy + cropSize.y) + 'px';
      bottom.style.left = '0px';
      bottom.style.width = (size.x - bx - 1) + 'px';
      bottom.style.height = ((h < 0) ? 0 : h - 1) + 'px';
      controls.setOpacity(bottom, this.options.opacity);
      if (this.options.cropResizable)
      {
         var el = controls.$(this.options.editorID + '_snap_left_top');
         el.style.top = (cy - 2) + 'px';
         el.style.left = (cx - 2) + 'px';
         el.style.display = '';
         el = controls.$(this.options.editorID + '_snap_top');
         el.style.top = (cy - 2) + 'px';
         el.style.left = (cx + cropSize.x / 2 - 3) + 'px';
         el.style.display = '';
         el = controls.$(this.options.editorID + '_snap_right_top');
         el.style.top = (cy - 2) + 'px';
         el.style.left = (cx + cropSize.x - 4) + 'px';
         el.style.display = '';
         el = controls.$(this.options.editorID + '_snap_left');
         el.style.top = (cy + cropSize.y / 2 - 3) + 'px';
         el.style.left = (cx - 2) + 'px';
         el.style.display = '';
         el = controls.$(this.options.editorID + '_snap_right');
         el.style.top = (cy + cropSize.y / 2 - 3) + 'px';
         el.style.left = (cx + cropSize.x - 4) + 'px';
         el.style.display = '';
         el = controls.$(this.options.editorID + '_snap_left_bottom');
         el.style.top = (cy + cropSize.y - 4) + 'px';
         el.style.left = (cx - 2) + 'px';
         el.style.display = '';
         el = controls.$(this.options.editorID + '_snap_bottom');
         el.style.top = (cy + cropSize.y - 4) + 'px';
         el.style.left = (cx + cropSize.x / 2 - 3) + 'px';
         el.style.display = '';
         el = controls.$(this.options.editorID + '_snap_right_bottom');
         el.style.top = (cy + cropSize.y - 4) + 'px';
         el.style.left = (cx + cropSize.x - 4) + 'px';
         el.style.display = '';
      }
   };

   this.transform = function(angle, scale, dx, dy)
   {
      if (!this.image) return;
      this.options.angle = angle;
      this.options.scale = scale;
      scale = scale / 100;
      var cx = this.image.attrs.x + this.image.attrs.width / 2, cy = this.image.attrs.y + this.image.attrs.height / 2;
      this.image = this.image.rotate(angle, true).scale(scale, scale);
      this.image.translate(dx + cx - this.image.attrs.x - this.image.attrs.width / 2, dy + cy - this.image.attrs.y - this.image.attrs.height / 2);
   };

   this.read = function(src, width, height)
   {
      if (this.image) this.image.remove();
      var dx = dy = Math.sqrt(width * width + height * height) * this.options.scale / 100;
      var size = controls.getSize(this.options.editorID);
      if (dx < size.x) dx = size.x;
      if (dy < size.y) dy = size.y;
      this.setCanvasSize(dx, dy);
      if (src) this.image = this.paper.image(src, 0, 0, width, height);
      this.centre();
   };

   this.centre = function()
   {
      var size = controls.getSize(this.options.editorID);
      this.setCoordinates(0, 0);
      this.transform(this.options.angle, this.options.scale, (size.x - this.image.attrs.width) / 2, (size.y - this.image.attrs.height) / 2);
   };

   this.cropShow = function(isCentre)
   {
      var crop = controls.$(this.options.editorID + '_crop');
      if (controls.$(this.options.cropWidthID)) controls.$(this.options.cropWidthID).innerHTML = parseInt(crop.style.width);
      if (controls.$(this.options.cropHeightID)) controls.$(this.options.cropHeightID).innerHTML = parseInt(crop.style.height);
      controls.$(this.options.editorID + '_shadow').style.display = '';
      if (isCentre) this.cropCentre();
      else this.cropMove(0, 0);
   };

   this.cropHide = function()
   {
      controls.$(this.options.editorID + '_shadow').style.display = 'none';
   };

   this.cropMoveTo = function(x, y)
   {
      var el = controls.$(this.options.editorID + '_crop');
      el.style.left = x + 'px';
      el.style.top = y + 'px';
      this.redrawShadow();
   };

   this.cropResize = function(width, height)
   {
      var el = controls.$(this.options.editorID + '_crop');
      el.style.width = width + 'px';
      el.style.height = height + 'px';
      this.redrawShadow();
   };

   this.cropCentre = function()
   {
      var size = controls.getSize(this.options.editorID);
      var cropsize = controls.getSize(this.options.editorID + '_crop');
      this.cropMoveTo((size.x - cropsize.x) / 2, (size.y - cropsize.y) / 2);
   };

   this.getCropParameters = function()
   {
      var params = new Array();
      var crop = controls.$(this.options.editorID + '_crop');
      var size = controls.getSize(crop);
      params['width'] = size.x - 2;
      params['height'] = size.y - 2;
      params['x'] = parseInt(crop.style.left) - this.getBox().x;
      params['y'] = parseInt(crop.style.top) - this.getBox().y;
      return params;
   };

   this.getBox = function()
   {
      var cx = this.image.attrs.width / 2, cy = this.image.attrs.height / 2;
      var box = {}, x1, x2, y1, y2, angle = this.options.angle;
      if (angle == 180 || angle == 360) angle = 0;
      if (angle <= 90)
      {
         x1 = -cx, y1 = cy;
         x2 = -cx, y2 = -cy;
      }
      else if (angle < 180)
      {
         x1 = cx, y1 = cy;
         x2 = -cx, y2 = cy;
      }
      else if (angle <= 270)
      {
         x1 = cx, y1 = -cy;
         x2 = cx, y2 = cy;
      }
      else if (angle < 360)
      {
         x1 = -cx, y1 = -cy;
         x2 = cx, y2 = -cy;
      }
      var rad = angle / 180 * Math.PI;
      x1 = x1 * Math.cos(rad) - y1 * Math.sin(rad);
      y1 = x2 * Math.sin(rad) + y2 * Math.cos(rad);
      box.x = x1 + cx + this.image.attrs.x;
      box.y = y1 + cy + this.image.attrs.y;
      box.width = 2 * (cx + this.image.attrs.x) - x1;
      box.height = 2 * (cy + this.image.attrs.y) - y1;
      return box;
   };

   this.setCoordinates = function(x, y)
   {
      this.image.attr('x', x);
      this.image.attr('y', y);
   };

   this.getCoordinates = function()
   {
      return {'x': this.image.attrs.x, 'y': this.image.attrs.y};
   };

   this.setCanvasSize = function(width, height)
   {
      this.paper.setSize(width, height);
   };

   this.getCanvasSize = function()
   {
      return {'width': this.paper.canvas.width.baseVal.value, 'height': this.paper.canvas.height.baseVal.value};
   };
};

var ImageEditorManager = function()
{
   this.initialize = function(id)
   {
      this.id = id;
   };

   this.show = function()
   {
      if (controls.$('imgEditorFade')) controls.fade('imgEditorFade', true, 0.5);
      else if (!controls.$('fade_' + this.id))
      {
         var fade = document.createElement('div');
         fade.id = 'fade_' + this.id;
         fade.className = 'shadow';
         fade.style.display = 'none';
         fade.style.zIndex = controls.getStyle(this.id, 'zIndex') - 1;
         document.body.appendChild(fade);
         controls.fade('fade_' + this.id, true, 0.5);
      }
      controls.display(this.id, '');
      controls.centre(this.id);
   };

   this.hide = function()
   {
      if (controls.$('imgEditorFade')) controls.fade('imgEditorFade', false);
      else if (controls.$('fade_' + this.id)) controls.fade('fade_' + this.id, false);
      controls.display(this.id, 'none');
   };

   this.init = function(url, width, height, options)
   {
      this.show();
      this.setup(url, width, height, options);
      controls.centre(this.id);
   };

   this.setup = function(url, width, height, options)
   {
      if (typeof(pm) != 'undefined' && options['cropMaxWidth']) options['cropMaxWidth'] = pm.getZoneWidth();
      var bind = this;
      this.edt = new ImageEditor();
      this.edt.initialize(options);
      this.edt.options.ID = this.id;
      this.edt.read(url, width, height);
      if (!options.hideCrop) this.edt.cropShow(true);
      else this.edt.cropHide();
      controls.$('width_' + this.id).innerHTML = width;
      controls.$('height_' + this.id).innerHTML = height;
      var el = controls.$(options.zoomID), coor;
      el.value = 100;
      el = controls.$(options.angleID);
      el.value = 0;
      this._setSliderValue('slider_rotate_' + this.id, 0, 0, 360);
      this._setSliderValue('slider_zoom_' + this.id, 100, 1, 200);
      var rotate = function(target)
      {
         var pos = controls.getPosition(target), coor = controls.getCoordinates(target.parentNode), angle = Math.round(360 * (pos.x - coor.left) / (coor.width - 10));
         controls.$(options.angleID).value = angle;
         bind.edt.transform(angle, controls.$(options.zoomID).value, 0, 0);
      };
      var zoom = function(target)
      {
         var pos = controls.getPosition(target), coor = controls.getCoordinates(target.parentNode), scale = Math.round(199 * (pos.x - coor.left) / (coor.width - 10) + 1);
         controls.$(options.zoomID).value = scale;
         bind.edt.transform(controls.$(options.angleID).value, scale, 0, 0);
      };
      coor = controls.getCoordinates('slider_rotate_' + this.id);
      controls.makeDraggable(controls.$('slider_rotate_' + this.id).firstChild, null, null, rotate, null, {x: [coor.left, coor.right - 10], y: [coor.top - 11, coor.top - 11]});
      coor = controls.getCoordinates('slider_zoom_' + this.id);
      controls.makeDraggable(controls.$('slider_zoom_' + this.id).firstChild, null, null, zoom, null, {x: [coor.left, coor.right - 10], y: [coor.top - 11, coor.top - 11]});
   };

   this._setSliderValue = function(el, value, minValue, maxValue)
   {
      if (value < minValue) value = minValue;
      if (value > maxValue) value = maxValue;
      var coor = controls.getCoordinates(el), mix = Math.round(value * (coor.width - 10) / (maxValue - minValue) + coor.left);
      el = controls.$(el).firstChild;
      controls.setPosition(el, {x: mix, y: coor.top - 11});
   }
};

var iem = new ImageEditorManager();