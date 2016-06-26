var TR29 = navigator.userAgent.toLowerCase();
var TR21 = TR29.indexOf("opera")!=-1;
var TRisChrome = TR29.indexOf("chrome") != -1;
var TR03=1236;   //id РїР°СЂС‚РЅРµСЂРєРё
var TRctime = "12";      // РІСЂРµРјСЏ Р¶РёР·РЅРё СЃРµСЃСЃРёРё
var min_otodiv = 450;

var TR28 = (TR29.indexOf("msie") != -1) && !TR21;

var TRurl_adv = "http://samoe-pokupaemoe.com.ua/";

var second_click = 4;   // РІС‚РѕСЂРѕР№ РєР»РёРє
var nuber_clicks=((!TR02('TR_CL'))||(TR02('TR_CL')<(second_click-1))||(second_click == 0)) ? "1":"2";

function TR01(TR14, TR15, TR17) {
var TR25 = new Date();
TR25.setTime(TR25.getTime());
var TR13 = new Date(TR25.getTime() + (3600000 * TR17));
document.cookie = TR14 + "=" + escape(TR15) + "; expires=" + TR13.toGMTString() + "; path=/;";
}

function TR02(TR14) {
var dc = document.cookie;
var TR18 = TR14 + "=";
var TR19 = dc.indexOf("; " + TR18);
if (TR19 == -1) {
TR19 = dc.indexOf(TR18);
if (TR19 != 0)return null;
}
else TR19 += 2;
var TR20 = document.cookie.indexOf(";", TR19);
if (TR20 == -1) TR20 = dc.length;
return unescape(dc.substring(TR19 + TR18.length, TR20));
}

function bodyClick(){

var q=TR02('TR_CL')?parseInt(TR02('TR_CL')):0;
q++;
TR01('TR_CL',q,TRctime);
if (!TR02('TR_ID' + TR03 +'.'+ nuber_clicks)) {
TRshow();
}
if(q == (second_click - 1)){
nuber_clicks='2';
add_otodiv('iframe');
add_otodiv('object');
add_otodiv('embed');
}
}

function TRshow() {
TR01('TR_ID' + TR03 +'.'+ nuber_clicks, 1, TRctime);
openNewTab();
del_otodiv();
}

function add_otodiv(elem) {
try {
if (!TR02('TR_ID' + TR03 +'.'+ nuber_clicks)) {
var TRm=document.getElementsByTagName(elem);
var k = TRm.length;
for (var key=0; key < k; key++){
var w = TRm[key].offsetWidth;
var h = TRm[key].offsetHeight;
if (w > min_otodiv){
otodiv = document.createElement("div");
otodiv.className = "otodiv";
var xy = getOffsetRect(TRm[key]);
otodiv.setAttribute("style", "position: absolute;left:"+xy['left']+"px;top:"+xy['top']+"px;height:"+h+"px;width:"+w+"px;z-index:899");
document.body.appendChild(otodiv);
}
}
}
}
catch (e) {
}
}

function del_otodiv() {
var TRm = document.getElementsByClassName('otodiv');
var k = TRm.length;
for (var key=0; key < k; key++){
if((typeof TRm[key])=='object'){
TRm[key].setAttribute("style", "position:none;left:0px;top:0px;height:0;width:0;z-index:0;display:none;");
}
}
}


function getOffsetRect(elem) {

var box = elem.getBoundingClientRect();

var body = document.body;
var docElem = document.documentElement;

var scrollTop = window.pageYOffset || docElem.scrollTop || body.scrollTop;
var scrollLeft = window.pageXOffset || docElem.scrollLeft || body.scrollLeft;

var clientTop = docElem.clientTop || body.clientTop || 0;
var clientLeft = docElem.clientLeft || body.clientLeft || 0;

var top  = box.top +  scrollTop - clientTop;
var left = box.left + scrollLeft - clientLeft;

return { top: Math.round(top), left: Math.round(left) }
}

function openNewTab(){
if(document.createEvent && (TR21 || TRisChrome)) {
var a = document.createElement("a");
a.href = TRurl_adv;
a.target='_blank';
var evObj = document.createEvent('MouseEvents');
evObj.initMouseEvent("click", true, true, window, 1, 0, 0, 0, 0, true, false, false,  false, 1, null);
a.dispatchEvent( evObj );
} else {
window.open(TRurl_adv);
}
}

setTimeout(function() {
add_otodiv('iframe');
add_otodiv('object');
add_otodiv('embed');

// ID, РіРґРµ РЅРµ Р±СѓРґРµС‚ СЂР°Р±РѕС‚Р°С‚СЊ РєР»РёРєР°РЅРґРµСЂ
var branding_blocks = new Array('br_block', 'brandingBox', 'bg-banner','multi-17', 'PC_Teaser_Block_21314', 'MarketGidScriptRootC29830', 'as_block', 'MarketGidComposite18990', 'lx_219100');
var k = branding_blocks.length;
for (var key=0; key < k; key++){
var br = document.getElementById(branding_blocks[key]);
if ( br != null) {
br.onmouseup = function(event) {var event = event || window.event;event.stopPropagation ? event.stopPropagation() : (event.cancelBubble = true)};
}
}

if (document.addEventListener){
document.addEventListener("mouseup", bodyClick, false)
} else {
document.attachEvent("onmouseup", bodyClick)
}
},1000);


document.getElementsByClassName = function(cl) {
var retnode = [];
var myclass = new RegExp('\\b'+cl+'\\b');
var elem = this.getElementsByTagName('*');
for (var i = 0; i < elem.length; i++) {
var classes = elem[i].className;
if (myclass.test(classes)) retnode.push(elem[i]);
}
return retnode;
};
