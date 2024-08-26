var Controls = function()
{
   this.$ = function(el)
   {
      return (typeof(el) == 'string') ? document.getElementById(el) : el;
   };

   this.getStyle = function(el, property)
   {
      el = this.$(el);
      if (el.currentStyle) style = el.currentStyle[property.replace(/-\D/g, function(match){return match.charAt(1).toUpperCase();})];
      if (document.defaultView && document.defaultView.getComputedStyle)
      {
         if (property.match(/[A-Z]/)) property = property.replace(/([A-Z])/g, '-$1').toLowerCase();
         style = document.defaultView.getComputedStyle(el, '').getPropertyValue(property);
      }
      if (!style) style = '';
      if (style == 'auto') style = '0px';
      return style;
   };
   
   this.hasClass = function(el, className)
   {
      el = this.$(el);
	  return (' ' + el.className + ' ').indexOf(' ' + className + ' ') != -1;
   };

   this.addClass = function(el, className)
   {
      el = this.$(el);
      if (!this.hasClass(el, className)) el.className = (el.className + ' ' + className).replace(/\s+/g, ' ').replace(/^\s+|\s+$/g, '');
   };
   
   this.removeClass = function(el, className)
   {
      el = this.$(el);
      el.className = el.className.replace(new RegExp('(^|\\s)' + className + '(?:\\s|$)'), '$1');
   };
   
   this.toggleClass = function(el, className)
   {
      if (this.hasClass(el, className)) this.removeClass(el, className);
	  else this.addClass(el, className);
   };

   this.getCompatElement = function()
   {
      return ((!document.compatMode || document.compatMode == 'CSS1Compat')) ? document.documentElement : document.body;
   };

   this.isBody = function(el)
   {
      return (/^(?:body|html)$/i).test(el.tagName);
   };

   this.getClientPosition = function(event)
   {
      return {x: (event.pageX) ? event.pageX - window.pageXOffset : event.clientX, y: (event.pageY) ? event.pageY - window.pageYOffset : event.clientY};
   };

   this.getPagePosition = function(event)
   {
      return {x: event.pageX || event.clientX + document.scrollLeft, y: event.pageY || event.clientY + document.scrollTop};
   };

   this.getEventTarget = function(event)
   {
      var target = event.target || event.srcElement;
      while (target && target.nodeType == 3) target = target.parentNode;
      return target;
   };

   this.addEvent = function(el, type, fn)
   {
      el = this.$(el);
      if (el.addEventListener) el.addEventListener(type.toLowerCase(), fn, false);
      else el.attachEvent('on' + type.toLowerCase(), fn);
   };

   this.removeEvent = function(el, type, fn)
   {
      el = this.$(el);
      if (el.removeEventListener) el.removeEventListener(type.toLowerCase(), fn, false);
      else el.detachEvent('on' + type.toLowerCase(), fn);
   };

   this.stopEvent = function(event)
   {
      if (event.stopPropagation) event.stopPropagation();
      else event.cancelBubble = true;
      if (event.preventDefault) event.preventDefault();
      else event.returnValue = false;
   };

   this.getWindowSize = function()
   {
      if (window.opera || !navigator.taintEnabled) return {x: window.innerWidth, y: window.innerHeight};
      var doc = this.getCompatElement();
      return {x: doc.clientWidth, y: doc.clientHeight};
   };

   this.getWindowScroll = function()
   {
      var doc = this.getCompatElement();
      return {x: window.pageXOffset || doc.scrollLeft, y: window.pageYOffset || doc.scrollTop};
   };

   this.getWindowScrollSize = function()
   {
      var doc = this.getCompatElement(), min = this.getWindowSize();
      return {x: Math.max(doc.scrollWidth, min.x), y: Math.max(doc.scrollHeight, min.y)};
   };

   this.getWindowCoordinates = function()
   {
      var size = this.getWindowSize();
      return {top: 0, left: 0, bottom: size.y, right: size.x, height: size.y, width: size.x};
   };

   this.getSize = function(el)
   {
      el = this.$(el);
      return (this.isBody(el)) ? this.getWindowSize() : {x: el.offsetWidth, y: el.offsetHeight};
   };

   this.getScrollSize = function(el)
   {
      el = this.$(el);
      return (this.isBody(el)) ? this.getWindowScrollSize() : {x: el.scrollWidth, y: el.scrollHeight};
   };

   this.getScroll = function(el)
   {
      el = this.$(el);
      return (this.isBody(el)) ? this.getWindowScroll() : {x: el.scrollLeft, y: el.scrollTop};
   };

   this.getScrolls = function(el)
   {
      el = this.$(el);
      var position = {x: 0, y: 0};
      while (el && !this.isBody(el))
      {
         position.x += el.scrollLeft;
         position.y += el.scrollTop;
         el = el.parentNode;
      }
      return position;
   };

   this.getOffsets = function(el)
   {
      el = this.$(el);
      if (el.getBoundingClientRect)
      {
         var bound = el.getBoundingClientRect(), html = this.$(document.documentElement), htmlScroll = this.getScroll(html), elemScrolls = this.getScrolls(el), elemScroll = this.getScroll(el), isFixed = (this.getStyle(el, 'position') == 'fixed');
         return {x: parseInt(bound.left) + elemScrolls.x - elemScroll.x + ((isFixed) ? 0 : htmlScroll.x) - html.clientLeft, y: parseInt(bound.top)  + elemScrolls.y - elemScroll.y + ((isFixed) ? 0 : htmlScroll.y) - html.clientTop};
      }
      var sel = el, position = {x: 0, y: 0};
      if (this.isBody(el)) return position;
      while (el && !this.isBody(el))
      {
         position.x += el.offsetLeft;
         position.y += el.offsetTop;
         if (document.getBoxObjectFor || window.mozInnerScreenX != null)
         {
            if (this.getStyle(el, '-moz-box-sizing') != 'border-box')
            {
               position.x += parseInt(this.getStyle(el, 'border-left-width'));
               position.y += parseInt(this.getStyle(el, 'border-top-width'));
            }
            var parent = el.parentNode;
            if (parent && this.getStyle(parent, 'overflow') != 'visible')
            {
               position.x += parseInt(this.getStyle(parent, 'border-left-width'));
               position.y += parseInt(this.getStyle(parent, 'border-top-width'));
            }
         }
         else if (el != sel && !navigator.taintEnabled)
         {
            position.x += parseInt(this.getStyle(el, 'border-left-width'));
            position.y += parseInt(this.getStyle(el, 'border-top-width'));
         }
         el = el.offsetParent;
      }
      if ((document.getBoxObjectFor || window.mozInnerScreenX != null) && this.getStyle(sel, '-moz-box-sizing') != 'border-box')
      {
         position.x += parseInt(this.getStyle(sel, 'border-left-width'));
         position.y += parseInt(this.getStyle(sel, 'border-top-width'));
      }
      return position;
   };

   this.getPosition = function(el, relative)
   {
      el = this.$(el);
      if (this.isBody(el)) return {x: 0, y: 0};
      var offset = this.getOffsets(el), scroll = this.getScrolls(el);
      var position = {x: offset.x - scroll.x, y: offset.y - scroll.y};
      var relativePosition = (relative && (relative = this.$(relative))) ? this.getPosition(relative) : {x: 0, y: 0};
      return {x: position.x - relativePosition.x, y: position.y - relativePosition.y};
   };

   this.getCoordinates = function(el, relative)
   {
      el = this.$(el);
      if (this.isBody(el)) return this.getWindowCoordinates();
      var position = this.getPosition(el, relative), size = this.getSize(el);
      var obj = {left: position.x, top: position.y, width: size.x, height: size.y};
      obj.right = obj.left + obj.width;
      obj.bottom = obj.top + obj.height;
      return obj;
   };

   this.setPosition = function(el, pos)
   {
      el = this.$(el);
      var position = {left: pos.x - parseInt(this.getStyle(el, 'margin-left')), top: pos.y - parseInt(this.getStyle(el, 'margin-top'))}
      var parent = el.parentNode;
      if (this.getStyle(el, 'position') != 'fixed')
      {
         while (parent && !this.isBody(parent))
         {
            pos = this.getStyle(parent, 'position');
            if (pos == 'absolute' || pos == 'relative')
            {
               var pos = this.getPosition(parent);
               position.left -= pos.x;
               position.top -= pos.y;
               break;
            }
            parent = parent.parentNode;
         }
      }
      else
      {
         var scroll = this.getWindowScroll();
         position.left -= scroll.x;
         position.top -= scroll.y;
      }
      el.style.left = position.left + 'px';
      el.style.top = position.top + 'px';
   };

   this.centre = function(el, overflow)
   {
      el = this.$(el);
      var size = this.getSize(el), winSize = this.getWindowSize();
      var scroll = this.getWindowScroll();
      var xx = (winSize.x - size.x) / 2 + scroll.x, yy = (winSize.y - size.y) / 2 + scroll.y;
      if (!overflow)
      {
         if (xx < 0) xx = 0;
         if (yy < 0) yy = 0;
      }
      this.setPosition(el, {x: xx, y: yy});
   };

   this.scrollTo = function(el)
   {
      var pos = this.getPosition(el);
      window.scrollTo(pos.x, pos.y);
   };

   this.focus = function(el, x, y)
   {
      el = this.$(el);
      if (!el) return;
      var parent = el.parentNode, flag = false;
      if (this.getStyle(el, 'position') != 'fixed')
      {
         while (parent && !this.isBody(parent))
         {
            if (this.getStyle(parent, 'position') == 'fixed')
            {
               flag = true;
               break;
            }
            parent = parent.parentNode;
         }
      }
      else flag = true;
      el = this.$(el);
      if (!flag)
      {
         x = x || 0;
         y = y || 0;
         var pos = this.getPosition(el), winSize = this.getWindowSize(), scroll = this.getWindowScroll();
         if (pos.x > winSize.x + scroll.x || pos.x < scroll.x || pos.y > winSize.y + scroll.y || pos.y < scroll.y) window.scrollTo(pos.x + parseInt(x), pos.y + parseInt(y));
      }
      try {el.focus();} catch (err){}
   };

   this.setOpacity = function(el, opacity)
   {
      el = this.$(el);
      if (opacity == 0 && el.style.visibility != 'hidden') el.style.visibility = 'hidden';
      else if (el.style.visibility != 'visible') el.style.visibility = 'visible';
      if (!el.currentStyle || !el.currentStyle.hasLayout) el.style.zoom = 1;
      if (window.ActiveXObject) el.style.filter = (opacity == 1) ? '' : 'progid:DXImageTransform.Microsoft.Alpha(opacity=' + opacity * 100 + ')';
      el.style.opacity = opacity;
   };

   this.insert = function(el, content)
   {
      this.$(el).innerHTML = content;
   };

   this.display = function(el, display)
   {
      el = this.$(el);
      if (display != undefined) el.style.display = display;
      else (el.style.display == 'none') ? el.style.display = '' : el.style.display = 'none';
   };

   this.fade = function(el, show, opacity)
   {
      if (!show) this.display(el, 'none');
      else
      {
         el = this.$(el);
         if (opacity == undefined) opacity = 0.5;
         var size = this.getWindowSize();
         el.style.position = 'fixed';
         el.style.top = '0px';
         el.style.left = '0px';
         el.style.width = size.x + 'px';
         el.style.height = size.y + 'px';
         this.setOpacity(el, opacity);
         this.display(el, '');
      }
   };

   this.makeDraggable = function(el, container, fnStart, fnDrag, fnStop, limit)
   {
      var sx, sy, target; bind = this;
      el = this.$(el);
      if (container) container = this.$(container);
      var fnMouseMove = function(e)
      {
         var pos = bind.getClientPosition(e || event);
         var x = pos.x - sx, y = pos.y - sy;
         if (container)
         {
            var maxX, maxY, minX, minY, sizeContainer = bind.getSize(container), size = bind.getSize(el), pos = bind.getPosition(container);
            minX = pos.x;
            minY = pos.y;
            maxX = minX + sizeContainer.x - size.x;
            maxY = minY + sizeContainer.y - size.y;
            if (x < minX) x = minX;
            if (x > maxX) x = maxX;
            if (y < minY) y = minY;
            if (y > maxY) y = maxY;
         }
         if (limit)
         {
            if (limit.x)
            {
               if (x < limit.x[0]) x = limit.x[0];
               if (x > limit.x[1]) x = limit.x[1];
            }
            if (limit.y)
            {
               if (y < limit.y[0]) y = limit.y[0];
               if (y > limit.y[1]) y = limit.y[1];
            }
         }
         bind.setPosition(el, {x: x, y: y});
         if (fnDrag) fnDrag(target);
         return false;
      };
      var fnMouseUp = function()
      {
         document.onmousemove = null;
         document.onmouseup = null;
         document.ondragstart = null;
         document.body.onselectstart = null;
         if (fnStop) fnStop(target);
      };
      var fnMouseDown = function(e)
      {
         e = e || event;
         var pos = bind.getClientPosition(e), cpos = bind.getPosition(el);
         sx = pos.x - cpos.x;
         sy = pos.y - cpos.y;
         target = bind.getEventTarget(e);
         if (fnStart) fnStart(target);
         document.onmousemove = fnMouseMove;
         document.onmouseup = fnMouseUp;
         document.ondragstart = function(){return false;};
         document.body.onselectstart = function(){return false;};
         return false;
      };
      this.addEvent(el, 'mousedown', fnMouseDown);
   };
};

var controls = new Controls();