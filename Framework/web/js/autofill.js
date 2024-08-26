var AutoFill = function()
{
   this.initialize = function(id)
   {
      var tt, ajax = new Ajax();
      ajax.options.isShowLoader = false;
      this._addEvent(id, 'keyup', function(e)
      {
         e = e || event;
         if (e.keyCode == 13 || e.keyCode == 38 || e.keyCode == 40) return false;
         if (tt) clearTimeout(tt);
         tt = setTimeout(function(){ajax.doit('ClickBlocks\\WebForms\\AutoFill@' + id + '->search', controls.$(id).value, 1)}, 300);
      })._addEvent(document.body, 'click', function()
      {
         autofill.hideList(id);
      });
   };

   this.showList = function(id, xx, yy)
   {
      if (xx == undefined) xx = 0;
      if (yy == undefined) yy = 0;
      var el = controls.$('list_' + id), size = controls.getSize(id), pos = controls.getPosition(id);
      el.style.position = 'absolute';
      el.style.display = 'block';
      controls.setPosition(el, {x: pos.x + xx, y: pos.y + size.y + yy});
   };

   this.hideList = function(id)
   {
      var list = controls.$('list_' + id);
      if (list) list.style.display = 'none';
   };

   this._addEvent = function(id, type, fn)
   {
      var el = (typeof id == 'string') ? document.getElementById(id) : id;
      if (el.addEventListener) el.addEventListener(type, fn, false);
      else el.attachEvent('on' + type, fn);
      return this;
   }
};

var autofill = new AutoFill();
