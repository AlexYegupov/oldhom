var isDOM=document.getElementById;
var isIE=document.all && document.all.item;
var isMZ=isDOM && (navigator.appName=="Netscape");
var isO=window.opera && isDOM;

function undef(param) { return param; }

// плавная моталка по странице. Это супер-фича.
function travelA( Aname, quick, noplus )
{
  if (isMZ && navigator.userAgent.substr(navigator.userAgent.indexOf("Gecko/")+6,4)=="2003" ) isMZ=false;
  if (isMZ && navigator.userAgent.substr(navigator.userAgent.indexOf("Gecko/")+6,4)=="2002" ) isMZ=false;
  if (!isIE && !isMZ) return true;
  var value=10;
  if (noplus) value=0;
  if (document.all)
   z = document.all[Aname];
  else
  {
   a = document.getElementsByTagName("A");
   aLength = a.length;
   for (var i = 0; i < aLength; i++)
   {
    an = a[i].getAttribute("name");  
    if (an!=null && an==Aname)
     break;
   }
   z = a[i];
  }
//  z = document.all ? document.all[Aname] : document.getElementById(Aname);
  if (z == undef()) return true;
  var x=0;
  var y=0;
  do {
    x += parseInt(isNaN(parseInt(z.offsetLeft))?0:z.offsetLeft);
    y += parseInt(isNaN(parseInt(z.offsetTop))?0:z.offsetTop);
  } while (z=z.offsetParent)
  travelto( x,  y-value, quick );
  return true;
}

// часть прикольной, завораживающей моталки
function travelto(x, y, quick )
{
  if (document.documentElement) var d = document.documentElement;
  else var d = document.body;
  if (quick)
  {
      ox = d.scrollLeft;
      oy = d.scrollTop;
      dx = (x - ox);
      dx = sign(dx) * Math.ceil(Math.abs(dx));
      dy = (y - oy);
      dy = sign(dy) * Math.ceil(Math.abs(dy));
      window.scrollBy(dx, dy);
    return;
  }
  do
    {
      ox = d.scrollLeft;
      oy = d.scrollTop;
      dx = (x - ox) / 10;
      dx = sign(dx) * Math.ceil(Math.abs(dx));
      dy = (y - oy) / 10;
      dy = sign(dy) * Math.ceil(Math.abs(dy));
      window.scrollBy(dx, dy);
      cx = d.scrollLeft;
      cy = d.scrollTop;
    }
  while (( (ox-cx) != 0 ) || ( (oy-cy) != 0 ));
}

var ok; 

function sign(x)
{
  if (x > 0) return 1;
  if (x < 0) return -1;
  return 0;
}


 var wikiedit;
 var dbclick;
 var edit;

 function all_init () //инициализация всяких штук, которые требуют инициализации
 {
  if (wikiedit) 
   we_init(wikiedit);
  if (dbclick) 
   dclick(dbclick);
  init_travel();
 }

 function dclick(frame)
 {
  if (edit)
  if(isIE || isO){
    document.ondblclick=function(){
      op = event.srcElement;
      while (op!=null && op.className!=frame && op.tagName!="BODY")
        op=op.parentElement;
      if (op.className==frame) {
       document.location=edit;
      }
      return true;
    }
  }else if (isMZ) {
  document.addEventListener("dblclick", mouseClick, true);
  }

 }

 function mouseClick(event) 
 {
     op = event.target;
     while (op!=null && op.className!=dbclick && op.tagName!="BODY")
       op=op.parentNode;
     if (op!=null && op.className==dbclick) {
      document.location=edit;
     }
 }

 function init_travel() 
 {
  a = document.all ? document.all : document.getElementsByTagName("*");
  aLength = a.length;
  l = window.location.href;
  if (l.indexOf("#")!=-1) l = l.substr(0,l.indexOf("#"));
  for (var i = 0; i < aLength; i++)
  {
   if (a[i].tagName == "A" || a[i].tagName == "a")
   {
    ahref = a[i].getAttribute("href");
    
    if (ahref!=null && ((ahref.substr(0, l.length)==l && ahref.charAt(l.length)=="#") || ahref.charAt(0)=="#"))// && ahref.charAt(l.length+1)=="#") 
    {
      if (ahref.charAt(0)=="#") ah = ahref.substr(1, ahref.length-1);
      else ah = ahref.substr(l.length+1, ahref.length-l.length-1);
      a[i].setAttribute("travel", ah);
      a[i].onclick = function (e) { return travel(e); };
//      if (a[i].addEventListener) a[i].addEventListener("click", travel, false);
//      else if (a[i].attachEvent) a[i].attachEvent("onclick", travel);
    }
   }
  }
 }

 function travel(e)
 {
  d = window.event ? window.event.srcElement : e.currentTarget;
  if (!d.getAttribute("travel")) return;
  s = d.getAttribute("travel");
  return travelA(s);
 }

 function weSave()
 {
  if (confirm("Really save?"))
  {
   document.forms.edit[0].click();
  }
 }

var DOTS = "#define x_width 2\n#define x_height 1\nstatic char x_bits[]={0x01}";
