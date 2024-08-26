var UploadButton = function()
{
   this.c = new Array();

   this.initialize = function(id, x, y)
   {
      this.c[id] = {'x': x || 0, 'y': y || 0};
      var upload = document.getElementById(id);
      var bind = this;
      var container = document.getElementById('container_' + id);
      if (!container) return;
      container.onmousemove = function(e)
      {
         e = e || event;
         var pos = controls.getClientPosition(e);
         var coor = controls.getCoordinates('container_' + id);
         var scroll = controls.getWindowScroll();
         if (pos.x < coor.left - scroll.x || pos.x > coor.right - scroll.x || pos.y < coor.top - scroll.y || pos.y > coor.bottom - scroll.y) upload.style.top = '-5000px';
         else controls.setPosition(upload, {x: pos.x + scroll.x - 45 + bind.c[id].x, y: pos.y + scroll.y - 15 + bind.c[id].y});
      };
   };
};

var uploadbutton = new UploadButton();