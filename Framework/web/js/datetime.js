var DT = function()
{
   this.initialize = function(date, format)
   {
      if (!date)
      { 
         date = this.date(new Date().valueOf(), 'm/d/Y');
         format = 'm/d/Y';
      }
      this.info = this.getDateTimeArray(date, format);
      this.informat = format;
   };
   
   this.addDays = function(day)
   {
      this.info['day'] += this.intval(day);
      return this;
   };
   
   this.addMonths = function(month)
   {
      this.info['month'] += this.intval(month);
      return this;
   };
   
   this.addYears = function(year)
   {
      this.info['year'] += this.intval(year);
      return this;
   };
   
   this.addHours = function(hour)
   {
      this.info['hour'] += this.intval(hour);
      return this;
   };
   
   this.addMinutes = function(minute)
   {
      this.info['minute'] += this.intval(minute);
      return this;
   };
   
   this.addSeconds = function(second)
   {
      this.info['second'] += this.intval(second);
      return this;
   };
   
   this.getDate = function(out)
   {
      if (!out) out = this.informat;
      return this.date(new Date(this.intval(this.info['year']), this.intval(this.info['month'] - 1), this.intval(this.info['day']), this.intval(this.info['hour']), this.intval(this.info['minute']), this.intval(this.info['second'])), out);
   };

   this.intval = function(n)
   {
      if (typeof(n) != 'undefined' && n.length > 1 && n.charAt(0) == '0') n = n.charAt(1);
      n = parseInt(n);
      return isNaN(n) ? 0 : n;
   };

   this.padint = function(n)
   {
      var sn = new String(n);
      return (sn.length > 1) ? n : '0' + n;
   };

   this.getPattern = function(datetime, format, n)
   {
      var t = new Array('GH', 'i', 's', 'mn', 'dj', 'Y');
      var f = format.replace(new RegExp('[' + t[n] + ']'), '(.*)');
      for (var i = 0; i < t.length; i++) if (i != n) f = f.replace(new RegExp('[' + t[i] + ']'), '.*');
      return f;
   };

   this.getComponent = function(datetime, format, n)
   {
      return this.intval(datetime.match(new RegExp(this.getPattern(datetime, format, n)))[1]);
   };

   this.getHour = function(datetime, format)
   {
      return this.getComponent(datetime, format, 0);
   };

   this.getMinute = function(datetime, format)
   {
      return this.getComponent(datetime, format, 1);
   };

   this.getSecond = function(datetime, format)
   {
      return this.getComponent(datetime, format, 2);
   };

   this.getMonth = function(datetime, format)
   {
      return this.getComponent(datetime, format, 3);
   };

   this.getDay = function(datetime, format)
   {
      return this.getComponent(datetime, format, 4);
   };

   this.getYear = function(datetime, format)
   {
      return this.getComponent(datetime, format, 5);
   };

   this.getDateTimeArray = function(datetime, format)
   {
      a = new Array();
      a['hour'] = this.getHour(datetime, format);
      a['minute'] = this.getMinute(datetime, format);
      a['second'] = this.getSecond(datetime, format);
      a['month'] = this.getMonth(datetime, format);
      a['day'] = this.getDay(datetime, format);
      a['year'] = this.getYear(datetime, format);
      return a;
   };

   this.getDays = function(year, month)
   {
      var days = 28;
      if (month == 2)
      {
         if ((year % 4) == 0 && (year % 100) != 0 || (year % 100) == 0) days++;
      }
      else if (month < 8) days = 30 + month % 2;
      else days = 31 - month % 2;
      return days;
   };

   this.timestamp = function(datetime, format)
   {
      var d = this.getDateTimeArray(datetime, format);
      d = new Date(this.intval(d['year']), this.intval(d['month'] - 1), this.intval(d['day']), this.intval(d['hour']), this.intval(d['minute']), this.intval(d['second']));
      return d.valueOf();
   };

   this.date = function(timestamp, format)
   {
      var d = new Date(timestamp);
      var f = format;
      for (var i = 0; i < format.length; i++)
      {
         if (i > 0 && format.charAt(i - 1) == '\\') continue;
         switch (format.charAt(i))
         {
            case 'd':
              f = f.replace(RegExp('d'), this.padint(d.getDate()));
              break;
            case 'j':
              f = f.replace(RegExp('j'), d.getDate());
              break;
            case 'm':
              f = f.replace(RegExp('m'), this.padint(d.getMonth() + 1));
              break;
            case 'n':
              f = f.replace(RegExp('n'), d.getMonth() + 1);
              break;
            case 'Y':
              f = f.replace(RegExp('Y'), d.getFullYear());
              break;
            case 's':
              f = f.replace(RegExp('s'), this.padint(d.getSeconds()));
              break;
            case 'i':
              f = f.replace(RegExp('i'), this.padint(d.getMinutes()));
              break;
            case 'H':
              f = f.replace(RegExp('H'), this.padint(d.getHours()));
              break;
            case 'G':
              f = f.replace(RegExp('G'), d.getHours());
              break;
         }
      }
      return f;
   };

   this.format = function(datetime, informat, outformat)
   {
      return this.date(new Date(this.timestamp(datetime, informat)), outformat);
   };

   this.compare = function(date1, date2, format)
   {
      var v1 = this.timestamp(date1, format);
      var v2 = this.timestamp(date2, format);
      if (v1 > v2) return 1;
      if (v2 > v1) return -1;
      return 0;
   }
};

var dt = new DT();