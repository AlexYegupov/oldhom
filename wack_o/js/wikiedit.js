/* 
////////////////////////////////////////////////////////////////////////
// WikiEdit                                                           //
// v. 1.20                                                            //
// supported: MZ1.4+, MSIE5+                                          //
//                                                                    //
// (c) Roman "Kukutz" Ivanov <thingol@mail.ru>, 2003                  //
//   based on AutoIndent for textarea                                 //
//   (c) Roman "Kukutz" Ivanov, Evgeny Nedelko, 2003                  //
// Many thanks to Alexander Babaev, Sergey Kruglov and Evgeny Nedelko //
// http://ar.sky.ru/wikiedit                                          //
//                                                                    //
////////////////////////////////////////////////////////////////////////

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:
1. Redistributions of source code must retain the above copyright
   notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright
   notice, this list of conditions and the following disclaimer in the
   documentation and/or other materials provided with the distribution.
3. The name of the author may not be used to endorse or promote products
   derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,
INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

var mark = "##inspoint##";
var begin = "##startpoint##";
var rbegin = new RegExp(begin);
var end = "##endpoint##";
var rend = new RegExp(end);
var rendb = new RegExp("^" + end);
var area;
var mzBugFixed;
var mzOld;
var weEnabled = true;
var weTab = false;
var scrltr;
var audit;
var enterpressed = false;
var isDOM=document.getElementById;
var isIE=document.all && document.all.item;
var isMZ=isDOM && (navigator.appName=="Netscape")
var isO=window.opera;


function we_init(textArea) {
 if (isMZ) {
  mzBugFixed=(navigator.userAgent.substr(navigator.userAgent.indexOf("Gecko/")+6,8)>20030510);
  mzOld=(navigator.userAgent.substr(navigator.userAgent.indexOf("Gecko/")+6,8)<20030110);
  if (mzOld) isMZ=false;
 }

 area = textArea;
 if(isIE && !isO){
   textArea.onkeydown=ieKeyDown;
 }else if (isMZ) {
   textArea.addEventListener("keypress", mzKeyDown, true);
   textArea.addEventListener("keyup", mzKeyDown, true);
 }
}

function weSwitch() {
 weEnabled = !weEnabled;
}

function weSwitchTab() {
 weTab = !weTab;
}

function LSum(Tag,Text, Skip)
{
 if (Skip)
 {
  var w = new RegExp("^([ ]*)(([*]|[1-9a-zA-Z]([.]|[)]))( |))(.*)$");
  q = Text.match(w);
  if (q!=null)
  {
   Text = q[1]+q[2]+Tag+q[6];
   return Text;
  }
 }
 var w = new RegExp("^([ ]*)(.*)$");
 q = Text.match(w);
 Text = q[1]+Tag+q[2];
 return Text;
}

function RSum(Text,Tag)
{
 var w  = new RegExp("^(.*)([ ]*)$");
 q = Text.match(w);
 Text = q[1]+Tag+q[2];
 return Text;
}

function TSum(Text,Tag,Tag2,Skip)
{
 var w = new RegExp("^([ ]*)"+begin+"([ ]*)(([*]|[1-9a-zA-Z]([.]|[)]))( |))(.*)$");
 q = Text.match(w);
 if (Skip && q!=null)
 {
  Text = q[1]+begin+q[2]+q[3]+Tag+q[7];
 }
 else 
 {
  var w = new RegExp("^(.*)"+begin+"([ ]*)(.*)$");
  var q = Text.match(w);
  if (q!=null) 
  {
   Text = q[1]+begin+q[2]+Tag+q[3];
  }
 }
 var w = new RegExp("([ ]*)"+end+"(.*)$");
 var q = Text.match(w);
 if (q!=null) 
 {
  var w = new RegExp("^(.*)"+end);
  var q1 = Text.match(w);
  var s = q1[1];
  ch = s.substring(s.length-1, s.length);
  while (ch == " ") {
     s = s.substring(0, s.length-1);
     ch = s.substring(s.length-1, s.length);
  }
  Text = s+Tag2+q[1]+end+q[2];
 }
 return Text;
}

function ltrim(str)
{
 var ch = str.substring(0, 1);
 while (ch == " ") {
  str = str.substring(1, str.length);
  ch = str.substring(0, 1);
 }
 return str;
}

function MarkUp(Tag,Text,Tag2,onNewLine,expand,strip)
{
if (onNewLine == null) onNewLine = 0;
if (expand == null) expand = 0;
if (strip == null) strip = 0;
var skip = 0;
if (expand == 0) skip = 1;
/*
onNewLine:
0 - add tags on every line inside selection
1 - add tags only on the first line of selection
2 - add tags before and after selection
//3 - add tags only if there's one line -- not implemented

expand:
0 - add tags on selection
1 - add tags on full line(s)
*/
 var r = '';
 var fIn = false;
 var fOut = false;
 var add = 0;
 var f = false;
 var w = new RegExp("^  ( *)(([*]|[1-9a-zA-Z]([.]|[)]))( |))");
 Text = Text.replace(new RegExp("\r", "g"), "");
 var lines = Text.split('\n');
 for(var i = 0; i < lines.length; i++) {
   if (rbegin.test(lines[i])) 
     fIn = true;
   if (rendb.test(lines[i])) 
     fIn = false;
   if (rend.test(lines[i]))
     fOut = true;
   if (rendb.test(lines[i+1])) {
     fOut = true;
     lines[i+1]=lines[i+1].replace(rend, "");
     lines[i]=lines[i]+end;
   }
   if (r != '') 
     r += '\n';

  if (fIn && strip==1) {
    if (rbegin.test(lines[i])) 
    {
     lines[i] = lines[i].replace(rbegin, "");
     f = true;
    } else f=false;
//  alert(lines[i].replace(new RegExp("\n","g"),"|").replace(new RegExp(" ","g"),"_"));
    lines[i] = lines[i].replace(w, "$1");
//  alert(lines[i].replace(new RegExp("\n","g"),"|").replace(new RegExp(" ","g"),"_"));
    if (f) lines[i] = begin+lines[i];
  }
/*
 fIn &&
  onNewLine==0 //добавляем таги.
  onNewLine==1 //добавляем таги, если первая строка
  onNewLine==2 //добавляем таги, если первая_и_последняя строка, иначе
   //добавляем первый таг, если первая либо добавляем последний, если последняя 
 //иначе добавляем неизменный текст
*/
  if (fIn && (onNewLine==0 | (onNewLine==1 && add==0) | (onNewLine==2 && (add==0 || fOut)))) {
  //добавляем таги
    if (expand==1) {
      l = lines[i];
      if (add==0) l = LSum(Tag, l, skip);
      if (fOut)   l = RSum(l, Tag2);
      if (add!=0 && onNewLine!=2) l = LSum(Tag, l, skip);
      if (!fOut  && onNewLine!=2) l = RSum(l, Tag2);
      r += l;
    } else {
/*
  не экспанд. это значит, что
  если первая строка, то добавляем реплейсом первый и суммой второй
  если последняя, то добавляем суммой первый и реплейсом второй
  если первая и последняя, то оба реплейсом
  иначе суммой
*/
//    alert(lines[i].replace(new RegExp("\n","g"),"|").replace(new RegExp(" ","g"),"_"));
//    alert(lines[i+1].replace(new RegExp("\n","g"),"|").replace(new RegExp(" ","g"),"_"));
      l = TSum(lines[i],Tag,Tag2,skip);
      if (add!=0 && onNewLine!=2) l = LSum(Tag, l, skip);
      if (!fOut  && onNewLine!=2) l = RSum(l, Tag2);
      r += l;
    }
    add++;
  } else {
  //добавляем неизменный текст
    r += lines[i];
  }
  if (fOut) 
   fIn = false;
 }
 return r;
}

function unindent(str) 
{
  var r = '';
  var fIn = false;
  var lines = str.split('\n');
  var rbeginb = new RegExp("^" + begin);
  for(var i = 0; i < lines.length; i++) 
  {
    var line = lines[i];
    if (rbegin.test(line)) {
      fIn = true;
      var rbeginb = new RegExp("^"+begin+"([ ]*)");
      line = line.replace(rbeginb, '$1'+begin); //catch first line
    }
    if (rendb.test(line)) {
      fIn = false;
    }
    if (r != '') {
      r += '\n';
    }
    if (fIn) {
      r += line.replace(/^(  )|\t/, '');
    } else {
      r += line;
    }
    if (rend.test(line)) {
      fIn = false;
    }
  }       
  return r;
}

function ieMark(tr) 
{
  //trying to remember our scrollpos - не получилось. =(
/*  scrltr = document.selection.createRange();
  var op = area;
  var tp = 0; var lf = 0;
  do {
    tp+=op.offsetTop;
    lf+=op.offsetLeft;
  } while (op=op.offsetParent)
  scrltr.moveToPoint(lf+1, tp+1);
  scrltr.expand("word");
  audit = [scrltr.offsetTop, scrltr.offsetLeft, scrltr.boundingHeight];
*/
   // mark ends
   tr2 = tr.duplicate();
   tr2.collapse(false);
   tr2.text = end;
   tr2 = tr.duplicate();
   tr2.collapse(true);
   tr2.text = begin;
}

function ieSel(t, tr) 
{
   // restore selection
   t.createTextRange().select(); // select whole text
   var wholeText = document.selection.createRange();
   tr1 = wholeText.duplicate();
   tr1.findText(begin);
   tr.setEndPoint("StartToStart", tr1);
   tr2 = wholeText.duplicate();
   tr2.findText(end);
   tr.setEndPoint("EndToEnd", tr2);
   
   // remove marks
   str = tr.text;
   str = str.replace(rbegin, "");
   str = str.replace(rend, "");
   tr.text = str;
   
   tr.setEndPoint("StartToStart", tr1);
   tr.setEndPoint("EndToEnd", tr2);
   tr.select();
//   scrltr.scrollIntoView(true);
//   alert (scrltr.offsetTop + "|" + audit[0] + "|" + scrltr.offsetLeft + "|" + audit[1] + "|" + scrltr.boundingHeight + "|" + audit[2]);
   return false;
}

function ieKeyDown(){
  if (!weEnabled) return;
  var res, tr, str, t, tr2, tr1, r1, re, q, e;
  var Tab = 9;
  var justenter = false;
  if (!weTab) Tab=1109;
  res = true;
  tr = document.selection.createRange();
  t = tr.parentElement();
  str = tr.text;

  var fol = event.ctrlKey && (str.length > 0);
  var Key=event.keyCode;
  if (event.altKey && !event.ctrlKey) Key=Key+1024;
 switch (Key) {
  case Tab:
  case 1109: //U
  case 1097: //I
    ieMark(tr);
    // process
    if (event.shiftKey || Key==1109) {
      t.value = unindent(t.value);
    } else {
      t.value = MarkUp("  ", t.value, "", 0, 1);
    }
    res = ieSel(t, tr);
    break;
  case 66: //B
    if (fol) { 
      ieMark(tr);
      t.value = MarkUp("**", t.value, "**");
      res = ieSel(t, tr);
    }
  break;
  case 73: //I
    if (fol) { 
      ieMark(tr);
      t.value = MarkUp("//", t.value, "//");
      res = ieSel(t, tr);
    }
  break;
  case 85: //U
    if (fol) { 
      ieMark(tr);
      t.value = MarkUp("__", t.value, "__");
      res = ieSel(t, tr);
    }
  break;
  case 83: //S
    if (fol && event.shiftKey) { 
      ieMark(tr);
      t.value = MarkUp("--", t.value, "--");
      res = ieSel(t, tr);
    }
  break;
  case 72: //H
    if (fol) { 
      ieMark(tr);
      t.value = MarkUp("??", t.value, "??", 2);
      res = ieSel(t, tr);
    }
  break;
  case 74: //J
    if (fol) { 
      ieMark(tr);
      t.value = MarkUp("!!", t.value, "!!", 2);
      res = ieSel(t, tr);
    }
  break;
  case 76: //L
  case 1100: //Alt+L
    if (event.shiftKey && event.ctrlKey) {
      ieMark(tr);
      t.value = MarkUp("  * ", t.value, "", 0, 1, 1);
      res = ieSel(t, tr);
    } else if (event.altKey || event.ctrlKey) {
      var n = new RegExp("\n");
      sel = str;
      if (!n.test(sel)) {
        if (!event.altKey) {
         lnk = prompt("Link:", sel); 
         if (lnk==null) lnk = sel;
         sl = prompt("Text for linking:", sel); 
         if (sl==null) sl = "";
         sel = lnk+" "+sl
        };
        ieMark(tr);
        var s = new RegExp("("+begin+")(.*)("+end+")");
        t.value = t.value.replace(s, "$1(("+ltrim(sel)+"))$3");
        res = ieSel(t, tr);
      }
    }
  break;
  case 79: //O
  case 78: //N
   if (event.ctrlKey && event.shiftKey) {
      ieMark(tr);
      t.value = MarkUp("  1. ", t.value, "", 0, 1, 1);
      res = ieSel(t, tr);
   }
  break;
  case 49: //1
   if (event.ctrlKey) {
    ieMark(tr);
    t.value = MarkUp("==", t.value, "==", 0, 1);
    res = ieSel(t, tr);
   }
  break;
  case 50: //2
   if (event.ctrlKey) {
    ieMark(tr);
    t.value = MarkUp("===", t.value, "===", 0, 1);
    res = ieSel(t, tr);
   }
  break;
  case 51: //3
   if (event.ctrlKey) {
    ieMark(tr);
    t.value = MarkUp("====", t.value, "====", 0, 1);
    res = ieSel(t, tr);
   }
  break;
  case 52: //4
   if (event.ctrlKey) {
    ieMark(tr);
    t.value = MarkUp("=====", t.value, "=====", 0, 1);
    res = ieSel(t, tr);
   }
  break;
  case 1107: //Alt+S
    try {
      if (weSave!=null) weSave();
    }
    catch(e){
    };
  break;
  case 13:
   if (event.ctrlKey) {//Ctrl+Enter
    try {
      if (weSave!=null) weSave();
    }
    catch(e){
    };
   } else if (event.shiftKey) { //Shift+Enter
    res = true;
   } else if (!enterpressed)
   {
    tr.text = mark;
    tr.expand("textedit");
    str = tr.text;
    re = new RegExp("(^|\n)( +((([*]|[1-9a-zA-Z]([.]|[)]))( |))|)).*"+mark, "");
    q = str.match(re);
    if (q==null) {
      tr.findText(mark);
      tr.text="";
    } else {
      tr.findText(mark);
      tr.text="\n"+q[2];
      var op = area;
      var tp = 0; var lf = 0;
      do {
        tp+=op.offsetTop;
        lf+=op.offsetLeft;
      } while (op=op.offsetParent)
      //alert (tr.boundingHeight+"|"+(tp+area.clientHeight)+"|"+area.offsetTop+"|"+tr.offsetTop+"|"+tr.getBoundingClientRect().top);
      if (tr.offsetTop>=area.clientHeight+tp) tr.scrollIntoView(false);
      res = false;
    }
    var justenter = true;
   }
  break;
  }
  e = window.event;
  e.returnValue = res;
  enterpressed=justenter;
  return res;
}

function mzSel(str) 
{
  t = area;
  q = str.match(new RegExp("((?:.|\n)*)"+begin));
  l = q[1].length;
  q = str.match(new RegExp(begin+"((?:.|\n)*)"+end));
  l1 = q[1].length;
  str = str.replace(rbegin, "");
  str = str.replace(rend, "");
  t.value = str;
  t.setSelectionRange(l, l + l1);
  return true;
}

function mzKeyDown(event) {
  if (!weEnabled) return;
  var Key, t, sel1, sel2, sel, processedEvent, str, l, q, l1, re; 
  var justenter = false;
  Key = event.keyCode;
  if (Key==0) {
   Key = event.charCode;
  }
  t = area;

  var scroll = t.scrollTop;

  if (event.altKey) Key=Key+4096;
  if (event.ctrlKey) Key=Key+2048;

  var Tab = 9;
  if (!weTab) Tab=4181;

//  alert(Key);

  if (event.type == "keypress" && (Key==1109+3072 || Key==1097+3072 || Key==2097 || Key==2098 || Key==2099 || 
    Key==2100 || Key==1100+3072 || Key==2124 || Key==2126 || Key==2127 || Key==2114 || Key==2131 || 
    Key==2133 || Key==2121 || Key==2120 || Key==2122  | 
    Key==2124+32 || Key==2126+32 || Key==2127+32 || Key==2114+32 || Key==2131+32 || 
    Key==2133+32 || Key==2121+32 || Key==2120+32 || Key==2122+32))
  {
    event.preventDefault();
    event.stopPropagation();
    return false;
  }
  if (event.type == "keyup" && (Key==9 || Key==13))
    return false; 

  sel1 = t.value.substr(0, t.selectionStart);
  sel2 = t.value.substr(t.selectionEnd);
  sel = t.value.substr(t.selectionStart, t.selectionEnd - t.selectionStart);
  str = sel1+begin+sel+end+sel2;

  processedEvent = false;
  switch (Key)
  {
  case Tab:    //Tab
  case 4181: //U
  case 4169: //I
   if (event.shiftKey || Key==1109) {
     str = unindent(str);
   } else {
     str = MarkUp("  ", str, "", 0, 1);
   }
   processedEvent = mzSel(str);
  break;
  case 2097:   //1
    str = MarkUp("==", str, "==", 0, 1);
    processedEvent = mzSel(str);
  break;
  case 2098:   //2
    str = MarkUp("===", str, "===", 0, 1);
    processedEvent = mzSel(str);
  break;
  case 2099:   //3
    str = MarkUp("====", str, "====", 0, 1);
    processedEvent = mzSel(str);
  break;
  case 2100:   //4
    str = MarkUp("=====", str, "=====", 0, 1);
    processedEvent = mzSel(str);
  break;
  case 2124:   //L
  case 4172:
    if (event.shiftKey && event.ctrlKey) {
      str = MarkUp("  * ", str, "", 0, 1, 1);
      processedEvent = mzSel(str);
    } else {
     var n = new RegExp("\n");
     if (!n.test(sel)) {
       if (!event.altKey) {
        lnk = prompt("Link:", sel); 
        if (lnk==null) lnk = sel;
        sl = prompt("Text for linking:", sel); 
        if (sl==null) sl = "";
        sel = lnk+" "+sl;
       };
       str = sel1+"(("+ltrim(sel)+"))"+sel2;
       t.value = str;
       t.setSelectionRange(sel1.length, str.length-sel2.length);
       processedEvent = true; 
     }
    }
  break;
  case 2127: //O
  case 2126: //N
    str = MarkUp("  1. ", str, "", 0, 1, 1);
    processedEvent = mzSel(str);
  break;
  case 2114: //B
   if (sel.length > 1) {
    str = MarkUp("**", str, "**");
    processedEvent = mzSel(str);
   } 
  break;
  case 2131:  //S
   if (sel.length > 1) {
    str = MarkUp("--", str, "--");
    processedEvent = mzSel(str);
   } 
  break;
  case 2133: //U
   if (sel.length > 1) {
    str = MarkUp("__", str, "__");
    processedEvent = mzSel(str);
   } 
  break;
  case 2121: //I
   if (sel.length > 1) {
    str = MarkUp("//", str, "//");
    processedEvent = mzSel(str);
   } 
  break;
  case 2122: //J   
   if (sel.length > 1) {
    str = MarkUp("!!", str, "!!", 2);
    processedEvent = mzSel(str);
   } 
  break;
  case 2120: //H   
   if (sel.length > 1) {
    str = MarkUp("??", str, "??", 2);
    processedEvent = mzSel(str);
   } 
  break;
  case 4179: //Alt+S
    try {
      if (weSave!=null) weSave();
    }
    catch(e){
    };
  break;
  case 13:
  case 2061:
  case 4109:
   if (event.ctrlKey) {//Ctrl+Enter
    try {
      if (weSave!=null) weSave();
    }
    catch(e){
    };
   } else if (event.shiftKey) { //Shift+Enter
    processedEvent = false; 
   } else if (!enterpressed)
   {
    str = sel1;
    re = new RegExp("(^|\n)( +((([*]|[1-9a-zA-Z]([.]|[)]))( |))|))([^\n]*)"+(mzBugFixed?"":"\n?")+"$");
    q = str.match(re);
    if (q!=null) {
     t.value=sel1+(mzBugFixed?"\n":"")+q[2]+sel2;
     sel = q[2].length + sel1.length +(mzBugFixed?1:0);
     t.setSelectionRange(sel, sel);
     processedEvent = true; 
    }
    var justenter = true;
   }
  break;
  }

  enterpressed = justenter;

  if (processedEvent)
  {
    event.cancelBubble = true;
    event.preventDefault();
    event.stopPropagation();
    t.scrollTop = scroll;
    return false;
  }
  //event.cancelBubble = false;
}
