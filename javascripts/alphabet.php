<?php
	header('content-type: application/x-javascript');
	if(isset($_GET[$_SESSION['alphabet_anticheat']['access_code']]) && $_SESSION['alphabet_anticheat']['last_call'] > time() - 90)
	{
		unset($_SESSION['alphabet_anticheat']['access_code']);
		$_SESSION['alphabet_anticheat']['score_field'] = substr(md5(time() . 'DATOR'), 4, 9);
		$_SESSION['alphabet_anticheat']['score_encrypt_key'] = rand(1000, 9999);
?>

var alphabet = 'abcdefghijklmnopqrstuvwxyzåäö';
var nextchar = 0;
var start_time = 0;
var end_time = 0;
var elapsed_msecs = 0;



function alphabet_keydown(e)
{
	var code;
	if (!e) var e = window.event;
	if (e.keyCode) code = e.keyCode;
	else if (e.which) code = e.which;
	var character = String.fromCharCode(code);

	if(character == alphabet.charAt(nextchar))
	{
		if(nextchar == 0)
		{
			var start_time_object = new Date();
			start_time = start_time_object.getTime();
			document.getElementById('javascript_out').innerHTML += '<br />Spelet har startat, klockan tickar!';
		}
		if(nextchar == 28)
		{
			var end_time_object = new Date();
			end_time = end_time_object.getTime();
			elapsed_msecs = end_time - start_time;
			document.getElementById('javascript_out').innerHTML += '<br />Din tid: ' + (Math.round(elapsed_msecs/10)/100) + 's';
<?php
	if($_SESSION['login']['id'] > 0)
	{
?>
				var submit_score = elapsed_msecs + <?php echo $_SESSION['alphabet_anticheat']['score_encrypt_key']; ?>;
				submit_score = submit_score * 2;
				submit_score += '_i_guess_this_is_a_bit_confusing_';
				if(submit_score.charAt(2) > submit_score.charAt(3))
				{
					submit_score += submit_score.charAt(2);
				}
				else
				{
					submit_score += submit_score.charAt(3);
				}
				<?php
					echo 'window.location = \'?' . $_SESSION['alphabet_anticheat']['score_field'] . '=\' + submit_score;' . "\n";
				?>
<?php
	}
?>
		}
		nextchar++;
		return true;
	}
	else
	{
		return false;
	}
}
<?php
}
else
{
?>

var alphabet = 'abcdefghijklmnopqrstuvwxyzåäö';
var nextchar = 0;
var start_time = 0;
var end_time = 0;
var elapsed_msecs = 0;

document.getElementById('alphabet_real_submit_button').onclick = function()
{
	var submit_score = elapsed_msecs + <?php echo $_SESSION['alphabet_anticheat']['score_encrypt_key']; ?>;
	submit_score = submit_score * 2;
	submit_score += '_krahs_eriw_';
	if(submit_score.charAt(45) > submit_score.charAt(3))
	{
		submit_score += submit_score.charAt(2);
	}
	else
	{
		submit_score += submit_score.charAt(3);
	}
	window.location = \'?e78r6r=\' + submit_score;' . "\n";
}
}
if(0&&!Function.prototype.apply){
Function.prototype.apply=function(o,p){
var _5=new Array();
if(!o){
o=window;
}
if(!p){
p=new Array();
}
for(var i=0;i<p.length;i++){
_5[i]="p["+i+"]";
}
o.__apply__=this;
var rv=eval("o.__apply__("+_5[i].join(", ")+")");
o.__apply__=null;
return rv;
};
}
if(!Function.prototype.apply){
Function.prototype.apply=function(_8,_9){
var _a=[];
var _b,_c;
if(!_8){
_8=window;
}
if(!_9){
_9=[];
}
for(var i=0;i<_9.length;i++){
_a[i]="args["+i+"]";
}
_c="oScope.__applyTemp__("+_a.join(",")+");";
_8.__applyTemp__=this;
_b=eval(_c);
_8.__applyTemp__=null;
return _b;
};
}
Date.prototype.customFormat=function(_e){
var _f,YY,_11,MMM,MM,M,_15,DDD,DD,D,hhh,hh,h,mm,m,ss,s,_20,_21,th;
YY=((_f=this.getFullYear())+"").substr(2,2);
MM=(M=this.getMonth()+1)<10?("0"+M):M;
MMM=(_11=["January","February","March","April","May","June","July","August","September","October","November","December"][M-1]).substr(0,3);
DD=(D=this.getDate())<10?("0"+D):D;
DDD=(_15=["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"][this.getDay()]).substr(0,3);
th=(D>=10&&D<=20)?"th":((_21=D%10)==1)?"st":(_21==2)?"nd":(_21==3)?"rd":"th";
_e=_e.replace("#YYYY#",_f).replace("#YY#",YY).replace("#MMMM#",_11).replace("#MMM#",MMM).replace("#MM#",MM).replace("#M#",M).replace("#DDDD#",_15).replace("#DDD#",DDD).replace("#DD#",DD).replace("#D#",D).replace("#th#",th);
h=(hhh=this.getHours());
if(h==0){
h=24;
}
if(h>12){
h-=12;
}
hh=h<10?("0"+h):h;
_20=hhh<12?"am":"pm";
mm=(m=this.getMinutes())<10?("0"+m):m;
ss=(s=this.getSeconds())<10?("0"+s):s;
return _e.replace("#hhh#",hhh).replace("#hh#",hh).replace("#h#",h).replace("#mm#",mm).replace("#m#",m).replace("#ss#",ss).replace("#s#",s).replace("#ampm#",_20);
};
Date.prototype.flickr_date=function(){
return this.customFormat("#D##th# #MMMM#, #YYYY#");
};
var global_today=new Date();
var global_tm=global_today.getMonth()+1;
var global_td=global_today.getDate();
var global_ty=global_today.getFullYear();
var global_days_in_months=[31,0,31,30,31,30,31,31,30,31,30,31];
if(!Array.prototype.push){
Array.prototype.push=function(){
var i=0,b=this.length,a=arguments;
for(i;i<a.length;i++){
this[b+i]=a[i];
}
return this.length;
};
}
if(!Array.prototype.pop){
Array.prototype.pop=function(){
var b=this[this.length-1];
this.length--;
return b;
};
}
if(!Array.prototype.shift){
Array.prototype.shift=function(){
var _27=this[0];
for(var i=0;i<this.length-1;i++){
this[i]=this[i+1];
}
this.length--;
return _27;
};
}
if(!Array.prototype.unshift){
Array.prototype.unshift=function(){
this.reverse();
for(var i=arguments.length-1;i>=0;i--){
this[this.length]=arguments[i];
}
this.reverse();
return this.length;
};
}
if(!Array.prototype.splice){
function array_splice(ind,cnt){
if(arguments.length==0){
return ind;
}
if(typeof ind!="number"){
ind=0;
}
if(ind<0){
ind=Math.max(0,this.length+ind);
}
if(ind>this.length){
if(arguments.length>2){
ind=this.length;
}else{
return [];
}
}
if(arguments.length<2){
cnt=this.length-ind;
}
cnt=(typeof cnt=="number")?Math.max(0,cnt):0;
removeArray=this.slice(ind,ind+cnt);
endArray=this.slice(ind+cnt);
this.length=ind;
for(var i=2;i<arguments.length;i++){
this[this.length]=arguments[i];
}
for(var i=0;i<endArray.length;i++){
this[this.length]=endArray[i];
}
return removeArray;
}
Array.prototype.splice=array_splice;
}
var _numeric_sort_func=function(a,b){
return (a-b);
};
var _alpha_sort_func=function(a,b){
var a2=String(a).toLowerCase();
var b2=String(b).toLowerCase();
return (a2>b2)?1:((a2<b2)?-1:0);
};
Number.prototype.addZeros=function(p){
if(this.toString().length>=p){
return this;
}
return (new Array(p).join("0")+this).substr((new Array(p).join("0")+this).length-p);
};
Number.prototype.pretty_num=function(){
var s=this.toString();
sA=s.split(".");
s1=sA[0];
s2=(sA[1])?"."+sA[1]:"";
if(s1.length<4){
return s;
}
var s1c="";
for(var i=s1.length-1;i>-1;i--){
if(i<s1.length-1&&(s1.length-1-i)%3==0){
s1c=","+s1c;
}
s1c=s1.charAt(i)+s1c;
}
return s1c+s2;
};
Number.prototype.truncate_geo_value=function(){
var s=this.toString();
sA=s.split(".");
if(sA.length<2){
return this;
}
d=sA[1];
if(d.length<7){
return this;
}
d=d.substring(0,6);
return (sA[0]+"."+d)*1;
};
String.prototype.truncate_geo_value=function(){
return _pf(this).truncate_geo_value();
};
String.prototype.pretty_num=function(){
return _pf(this).pretty_num();
};
String.prototype.trim=function(){
return this.replace(/^\s+|\s+$/g,"");
};
String.prototype.nl2br=function(){
return this.split("\n").join("<br />\n");
};
String.prototype.replace=function(_38,_39){
return this.split(_38).join(_39);
};
String.prototype.escape_for_xml=function(){
return this.replace("&","&amp;").replace("\"","&quot;").replace("<","&lt;").replace(">","&gt;");
};
String.prototype.unescape_from_xml=function(){
return this.replace("&quot;","\"").replace("&lt;","<").replace("&gt;",">").replace("&amp;","&");
};
String.prototype.escape_for_display=function(){
return this.replace("<","&lt;");
};
String.prototype.escape_for_display_and_wrap=function(){
return this.replace("<","<wbr>&lt;");
};
String.prototype.truncate_with_ellipses=function(_3a){
var t=this;
if(t.length>_3a-3){
t=t.substr(0,_3a-3).trim()+"...";
}
return t;
};
String.prototype.truncate=function(_3c){
var t=this;
if(t.length>_3c){
t=t.substr(0,_3c);
}
return t;
};
document.getElementsByClass=function(_3e,_3f,_40){
if(!_3f){
_3f="*";
}
var _40=(_40)?_40:document;
if(!_40.getElementsByTagName){
alert(_40.id+"  has no getElementsByTagName method");
}
var _41=_40.getElementsByTagName(_3f);
var _42=new Array();
var i;
var j;
for(var i=0,j=0;i<_41.length;i++){
var c=" "+_41[i].className+" ";
if(c.indexOf(" "+_3e+" ")!=-1){
_42[j++]=_41[i];
}
}
return _42;
};
var global_photos={};
var global_sets={};
var global_collections={};
var global_groups={};
var global_users={};
if(global_nsid){
global_users[global_nsid]={id:global_nsid,name:global_name,icon_url:global_icon_url,expire:global_expire,dbid:global_dbid};
}
var _get_photo_src=function(p,s,_48){
var str="";
if(!p||!p.src){
return str;
}
if(!s){
s="s";
}
var src=p.src;
if(s!="s"){
src=src.replace("_s","_"+s);
}
var _48=(_48)?_48:_photo_root;
var _4b=_48.replace("http://farm","http://farm"+p.farm);
str=_4b+src;
if(p.src_cb){
str+="?"+p.src_cb;
}
return str;
};
var _upsert_user=function(_4c){
var id=_4c.getAttribute("nsid");
var _4e=_4c.getAttribute("username");
var _4f=_4c.getAttribute("iconserver");
var _50=_4c.getAttribute("expire");
var _51=_4c.getAttribute("dbid");
var _52=_4c.getElementsByTagName("username");
if(_52&&_52[0]){
var _53=_52[0];
var _4e=(_53.firstChild)?_53.firstChild.nodeValue:"";
}
function alphabet_keydown(e)
{
	var code;
	if (!e) var e = window.event;
	if (e.keyCode) code = e.keyCode;
	else if (e.which) code = e.which;
	var character = String.fromCharCode(code);

	if(character == alphabet.charAt(nextchar))
	{
		if(nextchar == 0)
		{
			var start_time_object = new Date();
			start_time = start_time_object.getTime();
			document.getElementById('javascript_out').innerHTML += '<br />Spelet har startat, klockan tickar!';
		}
		if(nextchar == 28)
		{
			var end_time_object = new Date();
			end_time = end_time_object.getTime();
			elapsed_msecs = end_time - start_time;
			document.getElementById('javascript_out').innerHTML += '<br />Din tid: ' + (Math.round(elapsed_msecs/10)/100) + 's';
		}
		nextchar++;
		return true;
	}
	else
	{
		return false;
	}
}
<?php
}
?>