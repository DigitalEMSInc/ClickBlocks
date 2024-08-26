var Ajax = function()
{
   this.options =
   {
      data: '',
      url: window.location.href,
      cdurl: window.location.href,
      headers: {'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/javascript, text/html, application/xml, text/xml, */*'},
      async: true,
      method: 'post',
      urlEncoded: true,
      charset: 'utf-8',
      isShowLoader: true,
      onShowLoader: null,
      onHideLoader: null,
      onComplete: null,
      onFailure: null,
      onException: null,
      onHistory: null
   };

   this.currentHash = null;
   this.historyInterval = null;
   this.running = false;
   this.process = 0;
   this.vs = new Array();

   this.initialize = function(options)
   {
      options = options || {};
      for (var option in this.options)
      {
         if (typeof(options[option]) != 'undefined') this.options[option] = options[option];
      }
   };

   this.send = function(options)
   {
      this.running = true;
      this.process++;
      this.showLoader();
      options = options || {};
      for (var option in this.options)
      {
         if (typeof(options[option]) == 'undefined') options[option] = this.options[option];
      }
      if (options.urlEncoded && options.method == 'post')
      {
         var charset = (options.charset) ? '; charset=' + options.charset : '';
         options.headers['Content-type'] = 'application/x-www-form-urlencoded' + charset;
      }
      if (options.data && options.method == 'get')
      {
         options.url = options.url + (options.url.contains('?') ? '&' : '?') + options.data;
         options.data = null;
      }
      var xhr = this.getXHR(), bind = this;
      xhr.open(options.method.toUpperCase(), options.url, options.async);
      xhr.onreadystatechange = function()
      {
         if (xhr.readyState != 4) return;
         if (xhr.status >= 200 && xhr.status < 300)
         {
            bind.process--;
            bind.exec(xhr.responseText);
            if (typeof(options.onComplete) == 'function') options.onComplete(xhr);
         }
         else if (typeof(options.onFailure) == 'function') options.onFailure(xhr);
         xhr.onreadystatechange = function(){};
         if (bind.process < 1)
         {
            bind.running = false;
            bind.process = 0;
            bind.hideLoader();
         }
      };
      for (var key in options.headers)
      {
         try {xhr.setRequestHeader(key, options.headers[key]);}
         catch (e)
         {
            if (typeof(options.onException) == 'function') options.onException(key, options.headers[key]);
         }
      }
      xhr.send(options.data);
      return xhr;
   };

   this.exec = function(text)
   {
      if (!text) return;
      if (window.execScript) window.execScript(text);
      else eval(text);
   };

   this.getXHR = function()
   {
      try {return new XMLHttpRequest();}
      catch(e) {return new ActiveXObject('MSXML2.XMLHTTP');}
   };

   this.abort = function(xhr)
   {
      if (!xhr) return;
      xhr.abort();
      xhr.onreadystatechange = function(){};
      this.process--;
      if (this.process < 1) {this.running = false; this.process = 0; this.hideLoader()}
      return this;
   };

   this.getParams = function(func, args)
   {
      var sender;
      if (typeof func == 'object')
      {
         sender = func[1];
         func = func[0];
      }
      var params = '', pms = {}, i = 1;
      if (args.length == 2 && typeof(args[1]) == 'object')
      {
         args = args[1];
         i = 0;
      }
      params += "ajaxfunc=" + encodeURIComponent(func);
      params += "&ajaxkey=" + encodeURIComponent(document.body.id);
      if (sender) params += "&ajaxsender=" + encodeURIComponent(sender);
      for (var k = 0; i < args.length; i++, k++) pms[k] = args[i];
      params += "&ajaxargs=" + encodeURIComponent(this.encodeObj(pms));
      return params
   };

   this.doit = function(func)
   {
      var args = new Array();
      for (var i = 1; i < arguments.length; i++) args[i] = arguments[i];
      return this.call(func, this.getControlValues(), args);
   };

   this.call = function(func)
   {
      return this.send({'data': this.getParams(func, arguments)});
   };

   this.cdcall = function(func)
   {
      var params = '';
      if (this.options.cdurl.charAt(this.options.cdurl.length - 1) != '?') params = '?';
      var el = document.createElement('script');
      el.src = this.options.cdurl + this.getParams(func, arguments, params);
      el.type = 'text/javascript';
      document.getElementsByTagName('HEAD')[0].appendChild(el);
      return this;
   };

   this.convertToObject = function(param)
   {
      if (typeof(param) != 'object') return param;
      var arr = {};
      for (var i in param)
      {
         if (typeof(param[i]) == 'object') arr[i] = this.convertToObject(param[i]);
         else arr[i] = param[i];
      }
      return arr;
   };

   this.encodeObj = function(param)
   {
      return JSON.stringify(this.convertToObject(param));
   };

   this.submit = function(func, target, url)
   {
      this.showLoader();
      var form = this._getFormByTarget(target);
      var old_target = form.target;
      var old_action = form.action;
      var old_method = form.method;
      var old_enctype = form.encoding;
      var args = {'0': this.getControlValues(), '1': target.substr(6)};
      url = (url) ? url : this.options.url;
      if (url.indexOf('#') != -1) url = url.substr(0, url.indexOf('#'));
      form.action = url + ((url.indexOf('?') > -1) ? '&' : '?') + "ajaxfunc=" + encodeURIComponent(func) + "&ajaxkey=" + encodeURIComponent(document.body.id) + "&ajaxsubmit=1&ajaxargs=" + encodeURIComponent(this.encodeObj(args));
      form.method = 'post';
      form.target = target;
      form.encoding = 'multipart/form-data';
      form.submit();
      form.target = old_target;
      form.action = old_action;
      form.method = old_method;
      form.encoding = old_enctype;
      return this;
   };

   this.submitProgress = function(func, target, callback, url)
   {
      var form = this._getFormByTarget(target);
      this.isShowLoader = false;
      this.submit(func, form, target, url);
      setTimeout(function(){this.progress(target.substr(6), callback);}.bind(this), 500);
      return this;
   };

   this.progress = function(id, callback)
   {
      this.doit(callback, document.getElementById('ajax_progress_key_' + id).value, id);
      return this;
   };

   this.showLoader = function()
   {
      if (this.options.isShowLoader)
      {
         if (document.body) document.body.style.cursor = 'wait';
         if (typeof(this.options.onShowLoader) == 'function') this.options.onShowLoader();
      }
      return this;
   };

   this.hideLoader = function()
   {
      if (this.options.isShowLoader)
      {
         if (document.body) document.body.style.cursor = 'default';
         if (typeof(this.options.onHideLoader) == 'function') this.options.onHideLoader();
      }
      return this;
   };

   this.getControlValues = function(flag)
   {
      var values = new Array();
      values[document.body.id] = new Array();
      var elements = this.getFormElements();
      for (var i = 0; i < elements.length; i++)
      {
         var el = elements[i];
         if (!el.attributes['runat']) continue;
         if (el.tagName == 'INPUT' && (el.type == 'submit' || el.type == 'image' || el.type == 'reset' || el.type == 'button')) continue;
         var value;
         switch (el.type)
         {
            default:
            continue;
            case 'hidden':
            case 'text':
            case 'password':
            case 'file':
            case 'textarea':
            case 'select-one':
              if (typeof(CKEDITOR) != 'undefined' && CKEDITOR.instances[el.id]) value = CKEDITOR.instances[el.id].getData();
              else if (typeof(tinyMCE) != 'undefined' && tinyMCE.get(el.id)) value = tinyMCE.get(el.id).getContent();
              else value = el.value;
              break;
            case 'radio':
            case 'checkbox':
              value = {};
              value['state'] = (el.checked) ? 1 : 0;
              value['value'] = el.value;
              break;
            case 'select-multiple':
              value = {};
              for (var j = 0; j < el.length; j++)
              {
                  if (el.options[j].selected == true) value[j] = el.options[j].value;
              }
              break;
         }
         if ((!flag || flag && typeof(this.vs[el.id]) != 'undefined') && JSON.stringify(this.vs[el.id]) !== JSON.stringify(value))
         {
            this.vs[el.id] = values[document.body.id][el.id] = value;
         }
      }
      return values;
   };

   this.getFormValues = function(el, pref)
   {
      var values = new Array();
      var elements = this.getFormElements(el);
      for (var i = 0; i < elements.length; i++)
      {
         var el = elements[i];
         if (el.tagName == 'INPUT' && (el.type == 'submit' || el.type == 'image' || el.type == 'reset' || el.type == 'button')) continue;
         var name = el.name, key, value;
         if (name.substr(name.length - 2) == '[]') key = name.substr(0, name.length - 2);
         else key = name;
         if (pref)
         {
            var k = key.indexOf(pref);
            if (k != -1) key = key.substr(0, k);
         }
         switch (el.type)
         {
            default:
              value = el.value;
              break;
            case 'textarea':
              if (typeof(CKEDITOR) != 'undefined' && CKEDITOR.instances[el.id]) value = CKEDITOR.instances[el.id].getData();
              else if (typeof(tinyMCE) != 'undefined' && tinyMCE.get(el.id)) value = tinyMCE.get(el.id).getContent();
              else value = el.value;
              break;
            case 'radio':
            case 'checkbox':
              value = new Array();
              value['state'] = (el.checked) ? 1 : 0;
              value['value'] = el.value;
              break;
            case 'select-multiple':
              value = new Array();
              for (var j = 0; j < el.length; j++)
              {
                  if (el.options[j].selected == true) value[j] = el.options[j].value;
              }
              break;
         }
         if (key != name)
         {
            if (typeof(values[key]) == 'undefined') values[key] = new Array();
            values[key][values[key].length] = value;
         }
         else values[key] = value;
      }
      if (arguments.length > 2 && typeof(arguments[2]) == 'object') for (i in arguments[2]) values[i] = arguments[2][i];
      return values;
   };

   this.cleanFormValues = function(el, group, pref)
   {
      var elements = this.getFormElements(el);
      for (var i = 0; i < elements.length; i++)
      {
         var el = elements[i];
         if (pref && el.name.substr(0, pref.length) != pref) continue;
         switch (el.type)
         {
            case 'text':
            case 'hidden':
            case 'select-one':
            case 'select-multiple':
            case 'textarea':
              if (typeof(CKEDITOR) != 'undefined' && CKEDITOR.instances[el.id]) value = CKEDITOR.instances[el.id].setData('');
              else if (typeof(tinyMCE) != 'undefined' && tinyMCE.get(el.id)) value = tinyMCE.get(el.id).setContent('');
              else el.value = '';
              break;
            case 'checkbox':
            case 'radio':
              el.checked = false;
              break;
         }
      }
      if (typeof(validators) != 'undefined') validators.clean(group);
   };

   this.getFormElements = function(el, tags)
   {
      el = document.getElementById(el) || document;
      if (!tags) tags = 'input,select,textarea,checkbox,radio';
      tags = tags.split(',');
      var elements = [];
      var ddup = (tags.length > 1);
      for (var i = 0; i < tags.length; i++)
      {
         tag = tags[i];
         var partial = el.getElementsByTagName(tag.replace(/^\s+|\s+$/g, ''));
         if (ddup) for (var k = 0, j = partial.length; k < j; k++) elements.push(partial[k]);
         else elements = partial;
      }
      return elements;
   };

   this.initViewStates = function(flag)
   {
      this.getControlValues(flag);
   };

   this.startHistory = function()
   {
      this.currentHash = window.location.hash;
      if (window.ActiveXObject)
      {
         var el = document.createElement('iframe');
         el.id = 'ajax_historyFrame';
         el.style.display = 'none';
         el.inject(document.body, 'top');
         var iframe = document.getElementById('ajax_historyFrame').contentWindow.document;
         iframe.open();
         iframe.close();
         iframe.location.hash = this.currentHash;
         if (!this.currentHash) this.currentHash = '#';
      }
      var bind = this;
      this.historyInterval = setInterval(function()
      {
         var hash;
         if (window.ActiveXObject) hash = document.getElementById('ajax_historyFrame').contentWindow.document.location.hash;
         else hash = window.location.hash;
         if (bind.currentHash != hash)
         {
            bind.currentHash = hash;
            if (window.ActiveXObject) window.location.hash = hash;
            if (typeof(bind.options.onHistory) == 'function') bind.options.onHistory(bind.currentHash.substr(1));
         }
      }, 100);
      return this;
   };

   this.addHistory = function(hash)
   {
      if (window.ActiveXObject)
      {
         var iframe = document.getElementById('ajax_historyFrame').contentWindow.document;
         iframe.open();
         iframe.close();
         iframe.location.hash = hash;
      }
      window.location.hash = hash;
      return this;
   };

   this.stopHistory = function()
   {
      clearInterval(this.historyInterval);
      this.currentHash = null;
      this.historyInterval = null;
      return this;
   };

   this._getFormByTarget = function(target)
   {
      var el = document.getElementById(target.substr(6));
      var firstParent = el.parentNode;
      var parent = firstParent;
      while (parent != document.body && parent.tagName.toLowerCase() != 'form')
      {
         parent = parent.parentNode;
      }
      if (parent == document.body)
      {
         var html = firstParent.innerHTML;
         var form = document.createElement('form');
         form.innerHTML = html;
         firstParent.innerHTML = '';
         firstParent.appendChild(form);
         parent = form;
      }
      return parent;
   }
};

Ajax.action = function()
{
   switch (arguments[0])
   {
      case 'alert':
        alert(arguments[1]);
        break;
      case 'redirect':
        window.location.assign(arguments[1]);
        break;
      case 'reload':
        window.location.reload(true);
        break;
      case 'display':
        var el = document.getElementById(arguments[1]);
        if (arguments[2] != undefined) el.style.display = arguments[2];
        else (el.style.display == 'none') ? el.style.display = '' : el.style.display = 'none';
        break;
      case 'check':
        var el = document.getElementById(arguments[1]);
        if (el.type == 'checkbox') el.checked = arguments[2];
        else
        {
           var elements = ajax.getFormElements(arguments[1], 'input');
           for (var i = 0; i < elements.length; i++)
           {
              el = elements[i];
              if (el.type == 'checkbox' && (!arguments[3] || el.name.substr(0, arguments[3].length) == arguments[3])) el.checked = arguments[2];
           }
        }
        break;
      case 'insert':
        if (document.getElementById(arguments[2])) document.getElementById(arguments[2]).innerHTML = arguments[1];
        break;
      case 'replace':
        var old = document.getElementById(arguments[2]);
        if (!old) break;
        var el = document.createElement('span');
        el.innerHTML = arguments[1];
        if (el.firstChild && el.firstChild.nodeName && el.firstChild.nodeType == 1) el = el.firstChild;
        else el = document.createTextNode(el.innerHTML);
        old.parentNode.replaceChild(el, old);
        break;
      case 'inject':
        var element = document.getElementById(arguments[2]);
        if (!element) break;
        var el = document.createElement('span');
        el.innerHTML = arguments[1];
        if (el.firstChild && el.firstChild.nodeName && el.firstChild.nodeType == 1) el = el.firstChild;
        else el = document.createTextNode(el.innerHTML);
        switch (arguments[3])
        {
           case 'top':
             var first = element.firstChild;
             (first) ? element.insertBefore(el, first) : element.appendChild(el);
             break;
           case 'bottom':
             element.appendChild(el);
             break;
           case 'before':
             if (element.parentNode) element.parentNode.insertBefore(el, element);
             break;
           case 'after':
             if (!element.parentNode) break;
             var next = element.nextSibling;
             (next) ? element.parentNode.insertBefore(el, next) : element.parentNode.appendChild(el);
             break;
        }
        break;
      case 'remove':
        var element = document.getElementById(arguments[1]);
        if (element && element.parentNode) element.parentNode.removeChild(element);
        break;
      case 'message':
        var el = document.getElementById(arguments[2]);
        Ajax.action('insert', arguments[1], arguments[2])
        setTimeout(function(){if (el) el.innerHTML = '';}, arguments[3]);
        break;
      case 'tool':
        var el = document.createElement('script'), head = document.getElementsByTagName('HEAD')[0];
        el.src = arguments[1];
        el.type = 'text/javascript';
        var scripts = head.getElementsByTagName('SCRIPT');
        for (var i in scripts) if (scripts[i].src == el.src) return;
        head.appendChild(el);
        break;
      case 'css':
        var el = document.createElement('link'), head = document.getElementsByTagName('HEAD')[0];
        el.src = arguments[1];
        el.rel = 'stylesheet';
        el.type = 'text/css';
        var links = head.getElementsByTagName('LINK');
        for (var i in links) if (links[i].src == el.src) return;
        head.appendChild(el);
   }
};

var ajax = new Ajax();