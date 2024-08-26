var Validators = function()
{
   this.validators = new Array();
   this.results = new Array();

   this.add = function(vid, params)
   {
      params.cids = params.cids.split(',');
      params.groups = params.groups.split(',');
      var vals = new Array();
      vals[vid] = params;
      for (vid in this.validators)
      {
         if (typeof this.validators[vid] != 'object') continue;
         vals[vid] = this.validators[vid];
      }
      this.validators = vals;
   };

   this.remove = function(vid)
   {
      delete this.validators[vid];
   };

   this.clean = function(group, class1, class2)
   {
      if (group == undefined) group = 'default';
      for (var cid in this.results) this.results[cid] = 1;
      for (vid in this.validators)
      {
         if (typeof this.validators[vid] != 'object' || !document.getElementById(vid)) continue;
         if (group == '' || this._contains(this.validators[vid].groups, group)) 
		 {
		    if (this.validators[vid].exparam['hiding']) document.getElementById(vid).style.display = 'none';
            else document.getElementById(vid).innerHTML = '';
		 }
         if (this.validators[vid].unaction) eval(this.validators[vid].unaction);
      }
      this._showResult(true, class1, class2);
   };

   this.validate = function(groups, class1, class2, isAll)
   {
      if (isAll != undefined && !isAll) 
	  {
	     if (groups == undefined) groups = 'default';
	     grps = groups.split(',');
		 for (groupIdx in grps)
         {
            group = grps[groupIdx].replace(/^\s+|\s+$/g, '');
		    this.clean(group, class1, class2); 
		 }
      }
      return this._showResult(this.isValid(groups, isAll), class1, class2);
   };

   this.isValid = function(groups, isAll)
   {
      if (groups == undefined) groups = 'default';
      groups = groups.split(',');
      if (isAll == undefined) isAll = true;
      this.results = new Array();
      this._sortValidators();
      if (!isAll)
      {
         for (groupIdx in groups)
         {
            group = groups[groupIdx].replace(/^\s+|\s+$/g, '');
            for (vid in this.validators)
            {
               if (typeof this.validators[vid] != 'object') continue;
               if ((group == '' || this._contains(this.validators[vid].groups, group)) && !this._validate(vid)) return false;
            }
         }
         return true;
      }
      var flag = true;
      for (groupIdx in groups)
      {
      	if (typeof groups[groupIdx] != "function") {
         group = groups[groupIdx].replace(/^\s+|\s+$/g, '');
         for (vid in this.validators)
         {
            if (typeof this.validators[vid] != 'object') continue;
            if ((group == '' || this._contains(this.validators[vid].groups, group)) && !this._validate(vid)) flag = false;
         }
        }
      }
      return flag;
   };

   this._validate = function(vid)
   {
      if (!document.getElementById(vid)) return true;
      var val = this.validators[vid];
      if (typeof val != 'object') return true;
      var flag = true, valtype = val.type.toLowerCase();
      switch (valtype)
      {
         case 'required':
         case 'email':
         case 'regularexpression':
           switch (val.mode.toUpperCase())
           {
              case 'AND':
                for (cid in val.cids) flag &= this.check(val.cids[cid], valtype, vid);
                break;
              case 'OR':
                flag = false;
                for (cid in val.cids) flag |= this.check(val.cids[cid], valtype, vid);
                break;
              case 'XOR':
                var n = 0;
                for (cid in val.cids) if (this.check(val.cids[cid], valtype, vid)) n++;
                flag = (n == 1);
                break;
           }
           break;
         case 'compare':
           var ctrl1, ctrl2;
           switch (val.mode.toUpperCase())
           {
              case 'AND':
                for (cid1 in val.cids)
                {
                   ctrl1 = document.getElementById(val.cids[cid1]);
                   if (!ctrl1) continue;
                   for (cid2 in val.cids)
                   {
                      ctrl2 = document.getElementById(val.cids[cid2]);
                      if (!ctrl2 || ctrl1.id == ctrl2.id) continue;
                      flag &= (!val.exparam['caseInsensitive'] && ctrl1.value == ctrl2.value || val.exparam['caseInsensitive'] && new String(ctrl1.value).toLowerCase() == new String(ctrl2.value).toLowerCase());
                   }
                }
                break;
              case 'OR':
                flag = false;
                for (cid1 in val.cids)
                {
                   ctrl1 = document.getElementById(val.cids[cid1]);
                   if (!ctrl1) continue;
                   for (cid2 in val.cids)
                   {
                      ctrl2 = document.getElementById(val.cids[cid2]);
                      if (!ctrl2) continue;
                      flag |= (ctrl1.id != ctrl2.id && (!val.exparam['caseInsensitive'] && ctrl1.value == ctrl2.value || val.exparam['caseInsensitive'] && new String(ctrl1.value).toLowerCase() == new String(ctrl2.value).toLowerCase()));
                      if (flag) break;
                   }
                   if (flag) break;
                }
                break;
              case 'XOR':
                var n = 0;
                for (var i = 0; i < val.cids.length; i++)
                {
                   ctrl1 = document.getElementById(val.cids[i]);
                   if (!ctrl1) continue;
                   for (var j = i + 1; j < val.cids.length; j++)
                   {
                      ctrl2 = document.getElementById(val.cids[j]);
                      if (!ctrl2) continue;
                      if (ctrl1.id != ctrl2.id && (!val.exparam['caseInsensitive'] && ctrl1.value == ctrl2.value || val.exparam['caseInsensitive'] && new String(ctrl1.value).toLowerCase() == new String(ctrl2.value).toLowerCase())) n++;
                   }
                }
                flag = (n == 1);
                break;
           }
           for (id in val.cids)
           {
              var cid = document.getElementById(val.cids[id]);
              if (!cid) break;
              if (this.results[cid.id] === undefined || this.results[cid.id] == true) {
                this.results[cid.id] = flag;
              }
           }
           break;
         case 'custom':
           if (val.exparam['clientFunction'] != '') flag = val.exparam['clientFunction'](val.cids, val.mode.toUpperCase());
           else
           {
              for (id in val.cids)
              {
                 var cid = document.getElementById(val.cids[id]);
                 if (!cid) break;
                 this.results[cid.id] = true;
              }
           }
           break;
      }
      if (val.exparam['hiding'])
      {
         if (!flag) document.getElementById(vid).style.display = '';
         else document.getElementById(vid).style.display = 'none';
      }
      else
      {
         if (!flag) document.getElementById(vid).innerHTML = val.message;
         else document.getElementById(vid).innerHTML = '';
      }
      if (!flag && val.action != '') eval(val.action);
      if (flag && val.unaction != '') eval(val.unaction);
      return flag;
   };

   this.check = function(cid, type, vid)
   {
      var cid = document.getElementById(cid);
      if (!cid) return true;
      var flag = true;
      switch (type.toLowerCase())
      {
         case 'required':
           switch (cid.type)
           {
              case 'text':
              case 'password':
              case 'file':
              case 'textarea':
                if (typeof(CKEDITOR) != 'undefined' && CKEDITOR.instances[cid.id]) flag = !(!CKEDITOR.instances[cid.id].getData());
                else if (typeof(tinyMCE) != 'undefined' && tinyMCE.get(cid.id)) value = tinyMCE.get(cid.id).getContent();
                else flag = !(!cid.value);
                break;
              case 'select-one':
              case 'select-multiple':
                flag = (cid.value != '');
                break;
              case 'checkbox':
              case 'radio':
                flag = cid.checked;
                break;
              default:
                var elements = ajax.getFormElements(cid.id);
                if (elements.length == 0) flag = true;
                else
                {
                   flag = false;
                   for (var i = 0; i < elements.length; i++)
                     if (elements[i].type == 'radio' || elements[i].type == 'checkbox') flag |= elements[i].checked;
                }
                break;
           }
           break;
         case 'email':
         case 'regularexpression':
           var value = cid.value;
           if (typeof(CKEDITOR) != 'undefined' && CKEDITOR.instances[cid.id]) value = CKEDITOR.instances[cid.id].getData();
           else if (typeof(tinyMCE) != 'undefined' && tinyMCE.get(cid.id)) value = tinyMCE.get(cid.id).getContent();
           if (value.length > 0)
           {
              if (this.validators[vid].exparam.expression.charAt(0) == 'i') eval("var re = " + this.validators[vid].exparam.expression.substr(1) + "; flag = !re.test(value);");
              else eval("var re = " + this.validators[vid].exparam.expression + "; flag = re.test(value);");
           }
           else flag = true;
           break;
      }
      if (this.results[cid.id] == undefined) this.results[cid.id] = true;
      this.results[cid.id] &= flag;
      return flag;
   };

   this._showResult = function(flag, class1, class2)
   {
      var first, el;
      for (var cid in this.results)
      {
         if (typeof(this.results[cid]) != 'number') continue;
         el = document.getElementById(cid);
         if (this.results[cid])
         {
            if (class1 != undefined && class2 != undefined)
            {
               el.className = el.className.replace(new RegExp('(^|\\s)' + class1 + '(?:\\s|$)'), '$1');
               if (class1 && (' ' + el.className + ' ').indexOf(' ' + class2 + ' ') == -1)
               {
                  el.className = (el.className + ' ' + class2).replace(/\s+/g, ' ').replace(/^\s+|\s+$/g, '');
               }
            }
         }
         else
         {
            if (!first || typeof(controls) != 'undefined' && controls.getPosition(el).y < controls.getPosition(first).y) first = el;
            if (class1 != undefined && class2 != undefined && (' ' + el.className + ' ').indexOf(' ' + class1 + ' ') == -1)
            {
               el.className = (el.className + ' ' + class1).replace(/\s+/g, ' ').replace(/^\s+|\s+$/g, '');
            }
         }
      }
      if (!flag && first)
      {
         if (typeof(CKEDITOR) != 'undefined' && CKEDITOR.instances[first.id]) CKEDITOR.instances[first.id].focus();
         else if (first.style.display != 'none')
         {
            if (typeof(controls) != 'undefined') {
              if (!controls.hasClass(first, 'nofocus')) {
                controls.focus(first);
              }
            } else {
              if (!$( first ).hasClass( 'nofocus' )) {
                first.focus()
              }
            };
         }
      }
      return flag;
   };

   this._contains = function(arr, el)
   {
      for (var i = 0; i < arr.length; i++) if (arr[i] === el) return true;
      return false;
   };

   this._sortValidators = function()
   {
      var keys = vals = new Array(), bind = this;
      for (var i in this.validators) keys[keys.length] = i;
      keys.sort(function(a,b){ return ((typeof(bind.validators[a].order) == 'undefined') ? 0 : bind.validators[a].order) - ((typeof(bind.validators[b].order) == 'undefined') ? 0 : bind.validators[b].order); });
      for (var i = 0; i < keys.length; i++) vals[keys[i]] = this.validators[keys[i]];
      this.validators = vals;
   }
};

var validators = new Validators();
