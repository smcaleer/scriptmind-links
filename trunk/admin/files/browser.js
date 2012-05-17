function sp_Browser(){
 d=document;
 this.agt=navigator.userAgent.toLowerCase();
 this.major=parseInt(navigator.appVersion);
 this.dom=(d.getElementById);
 this.ns=(d.layers);
 this.ns4up=(this.ns && this.major>=4);
 this.ns4=(this.ns && this.major==4);
 this.ns6=(this.dom&&navigator.appName=="Netscape");
 this.gk = (this.agt.indexOf("gecko")!=-1);
 this.op=(window.opera);
 if(d.all)this.ie=1;else this.ie=0;
 this.ie4=(d.all&&!this.dom);
 this.ie4up=(this.ie&&this.major>=4);
 this.ie5=(d.all&&this.dom);
 this.ie6=(d.nodeType);
 this.sf=(this.agt.indexOf("safari")!=-1);
 this.win=((this.agt.indexOf("win")!=-1)||(this.agt.indexOf("16bit")!=-1));
 this.winme=(this.agt.indexOf("win 9x 4.90")!=-1);
 this.xpsp2=(this.agt.indexOf("sv1")!=-1);
 this.mac=(this.agt.indexOf("mac")!=-1);
 this.dyn = (document.all || document.layers || document.getElementById) ? true : false;

}
var oBw=new sp_Browser();

function sp_getH(o) { return (oBw.ns)?((o.height)?o.height:o.clip.height):((oBw.op&&typeof o.style.pixelHeight!='undefined')?o.style.pixelHeight:o.offsetHeight); }
function sp_setH(o,h) { if(o.clip) o.clip.height=h; else if(oBw.op && typeof o.style.pixelHeight != 'undefined') o.style.pixelHeight=h+"px"; else o.style.height=h+"px"; }
function sp_getW(o) { return (oBw.ns)?((o.width)?o.width:o.clip.width):((oBw.op&&typeof o.style.pixelWidth!='undefined')?w=o.style.pixelWidth:o.offsetWidth); }
function sp_setW(o,w) { if(o.clip) o.clip.width=w; else if(oBw.op && typeof o.style.pixelWidth != 'undefined') o.style.pixelWidth=w+"px"; else o.style.width=w+"px"; }
function sp_getX(o) { return (oBw.ns)?o.left:((o.style.pixelLeft)?o.style.pixelLeft:o.offsetLeft); }
function sp_setX(o,x) { if(oBw.ns) o.left=x; else {if(oBw.gk) x=x+"px"; if(typeof o.style.pixelLeft != 'undefined') o.style.pixelLeft=x; else o.style.left=x;} }
function sp_getY(o) { return (oBw.ns)?o.top:((o.style.pixelTop)?o.style.pixelTop:o.offsetTop); }
function sp_setY(o,y) { if(oBw.ns) o.top=y; else {if(oBw.gk) y=y+"px";if(typeof o.style.pixelTop != 'undefined') o.style.pixelTop=y; else o.style.top=y;} }
function sp_getPageX(o) { var x=0; if(oBw.ns) x=o.pageX; else { while(eval(o)) { x+=o.offsetLeft; o=o.offsetParent; } } return x; }
function sp_getPageY(o) { var y=0; if(oBw.ns) y=o.pageY; else { while(eval(o)) { y+=o.offsetTop; o=o.offsetParent; } } return y; }
function sp_getZ(o) { return (oBw.ns)?o.zIndex:o.style.zIndex; }
function sp_moveTo(o,x,y) { sp_setX(o,x);sp_setY(o,y); }
function sp_moveBy(o,x,y) { sp_setX(o,sp_getPageX(o)+x);sp_setY(o,sp_getPageY(o)+y); }
function sp_setZ(o,z) { if(oBw.ns)o.zIndex=z;else o.style.zIndex=z; }

function sp_getDocW() {var w = 0;if( document.documentElement &&( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {w = document.documentElement.clientWidth;} else if( typeof( window.innerWidth ) == 'number' ) {w = window.innerWidth;} else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {w = document.body.clientWidth;}return w;}
function sp_getDocH() {var h = 0;if( document.documentElement &&( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {h = document.documentElement.clientHeight;} else if( typeof( window.innerWidth ) == 'number' ) {h = window.innerHeight;} else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {h = document.body.clientHeight;}return h;}

function sp_getScrollX() {
  var scrOfX = 0;
  if( typeof( window.pageYOffset ) == 'number' ) {
    //Netscape compliant
    scrOfX = window.pageXOffset;
  } else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ) {
    //DOM compliant
    scrOfX = document.body.scrollLeft;
  } else if( document.documentElement &&
      ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) ) {
    //IE6 standards compliant mode
    scrOfX = document.documentElement.scrollLeft;
  }
  return scrOfX;
}

function sp_getScrollY() {
  var scrOfY = 0;
  if( typeof( window.pageYOffset ) == 'number' ) {
    //Netscape compliant
    scrOfY = window.pageYOffset;
  } else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ) {
    //DOM compliant
    scrOfY = document.body.scrollTop;
  } else if( document.documentElement &&
      ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) ) {
    //IE6 standards compliant mode
    scrOfY = document.documentElement.scrollTop;
  }
  return scrOfY;
}

function sp_addEvt(o,e,f){ eval("o.on"+e+"="+f)}


function sp_show(o,disp) {
 (oBw.ns)? '':(!disp)? o.style.display="inline":o.style.display=disp;
 (oBw.ns)? o.visibility='show':o.style.visibility='visible';
}
function sp_hide(o,disp) {
 (oBw.ns)? '':(arguments.length!=2)? o.style.display="none":o.style.display=disp;
 (oBw.ns)? o.visibility='hide':o.style.visibility='hidden';
}

function sp_getObj(id,d) {
  var i,x;  if(!d) d=document;
  if(!(x=d[id])&&d.all) x=d.all[id];
  for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][id];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=yg_getObj(id,d.layers[i].document);
  if(!x && document.getElementById) x=document.getElementById(id);
  return x;
}

function setLayerHTML(LAYEROBJ,STR) {
   if (navigator.userAgent.indexOf('MSIE 5.0') && navigator.userAgent.indexOf('Mac') != -1) STR += '\n';
   if (oBw.ns4) {
      LAYEROBJ.document.open();
      LAYEROBJ.document.write(STR);
      LAYEROBJ.document.close();
      }
   else if (oBw.dyn) LAYEROBJ.innerHTML = STR;
   }


function addLayer(LAYERID,PARENTLAYEROBJ) {
   if (oBw.ie4up) {
      if (isBlank(PARENTLAYEROBJ)) PARENTLAYEROBJ = document.body;
      PARENTLAYEROBJ.insertAdjacentHTML('BeforeEnd','<div id="' + LAYERID + '" </div>');
      return (document.all) ? document.all[LAYERID] : document.getElementById(LAYERID);
      }

   else if (oBw.ns6) {
      if (isBlank(PARENTLAYEROBJ)) PARENTLAYEROBJ = document.body;
      var tempLayer = document.createElement('div');
      tempLayer.setAttribute('id',LAYERID);
      PARENTLAYEROBJ.appendChild(tempLayer);
      return document.getElementById(LAYERID);
      }
   }

function isBlank(STR) {
   if (oBw.dyn) {
      if (STR == null) STR = '';
      STR += '';
      STR = STR.replace(/^\s+|\s+$/g,'');
      return (STR == '') ? true : false;
      }
   }

var u;
var d;
var s;
var w;
function javascriptemail(u, d, s, w){
  document.write('<a h' + 'ref="' + 'mailto:' + u + '@' + d + '.' + s + '">' + w + '</a>');
}