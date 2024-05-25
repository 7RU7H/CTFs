package.preload['util.encodings']=(function(...)
local function e()
error("Function not implemented");
end
local t=require"mime";
module"encodings"
stringprep={};
base64={encode=t.b64,decode=e};
return _M;
end)
package.preload['util.hashes']=(function(...)
local e=require"util.sha1";
return{sha1=e.sha1};
end)
package.preload['util.logger']=(function(...)
local i,t=select,tostring;
local n=io.write;
module"logger"
local function e(a,...)
local e,o=0,#arg;
return(a:gsub("%%(.)",function(a)if a~="%"and e<=o then e=e+1;return t(arg[e]);end end));
end
local function a(a,...)
local e,o=0,i('#',...);
local i={...};
return(a:gsub("%%(.)",function(a)if e<=o then e=e+1;return t(i[e]);end end));
end
function init(e)
return function(t,e,...)
n(t,"\t",a(e,...),"\n");
end
end
return _M;
end)
package.preload['util.sha1']=(function(...)
local u=string.len
local a=string.char
local q=string.byte
local j=string.sub
local c=math.floor
local t=require"bit"
local k=t.bnot
local e=t.band
local y=t.bor
local n=t.bxor
local i=t.lshift
local o=t.rshift
local s,h,r,d,l
local function p(e,t)
return i(e,t)+o(e,32-t)
end
local function m(i)
local t,o
local t=""
for n=1,8 do
o=e(i,15)
if(o<10)then
t=a(o+48)..t
else
t=a(o+87)..t
end
i=c(i/16)
end
return t
end
local function b(t)
local i,o
local n=""
i=u(t)*8
t=t..a(128)
o=56-e(u(t),63)
if(o<0)then
o=o+64
end
for e=1,o do
t=t..a(0)
end
for t=1,8 do
n=a(e(i,255))..n
i=c(i/256)
end
return t..n
end
local function g(f)
local m,t,o,i,w,u,c,v
local a,a
local a={}
while(f~="")do
for e=0,15 do
a[e]=0
for t=1,4 do
a[e]=a[e]*256+q(f,e*4+t)
end
end
for e=16,79 do
a[e]=p(n(n(a[e-3],a[e-8]),n(a[e-14],a[e-16])),1)
end
m=s
t=h
o=r
i=d
w=l
for h=0,79 do
if(h<20)then
u=y(e(t,o),e(k(t),i))
c=1518500249
elseif(h<40)then
u=n(n(t,o),i)
c=1859775393
elseif(h<60)then
u=y(y(e(t,o),e(t,i)),e(o,i))
c=2400959708
else
u=n(n(t,o),i)
c=3395469782
end
v=p(m,5)+u+w+c+a[h]
w=i
i=o
o=p(t,30)
t=m
m=v
end
s=e(s+m,4294967295)
h=e(h+t,4294967295)
r=e(r+o,4294967295)
d=e(d+i,4294967295)
l=e(l+w,4294967295)
f=j(f,65)
end
end
local function t(e,t)
e=b(e)
s=1732584193
h=4023233417
r=2562383102
d=271733878
l=3285377520
g(e)
local e=m(s)..m(h)..m(r)
..m(d)..m(l);
if t then
return e;
else
return(e:gsub("..",function(e)
return string.char(tonumber(e,16));
end));
end
end
_G.sha1={sha1=t};
return _G.sha1;
end)
package.preload['lib.adhoc']=(function(...)
local n,h=require"util.stanza",require"util.uuid";
local o="http://jabber.org/protocol/commands";
local i={}
local s={};
function _cmdtag(i,e,a,t)
local e=n.stanza("command",{xmlns=o,node=i.node,status=e});
if a then e.attr.sessionid=a;end
if t then e.attr.action=t;end
return e;
end
function s.new(o,a,t,e)
return{name=o,node=a,handler=t,cmdtag=_cmdtag,permission=(e or"user")};
end
function s.handle_cmd(o,s,a)
local e=a.tags[1].attr.sessionid or h.generate();
local t={};
t.to=a.attr.to;
t.from=a.attr.from;
t.action=a.tags[1].attr.action or"execute";
t.form=a.tags[1]:child_with_ns("jabber:x:data");
local t,h=o:handler(t,i[e]);
i[e]=h;
local a=n.reply(a);
if t.status=="completed"then
i[e]=nil;
cmdtag=o:cmdtag("completed",e);
elseif t.status=="canceled"then
i[e]=nil;
cmdtag=o:cmdtag("canceled",e);
elseif t.status=="error"then
i[e]=nil;
a=n.error_reply(a,t.error.type,t.error.condition,t.error.message);
s.send(a);
return true;
else
cmdtag=o:cmdtag("executing",e);
end
for t,e in pairs(t)do
if t=="info"then
cmdtag:tag("note",{type="info"}):text(e):up();
elseif t=="warn"then
cmdtag:tag("note",{type="warn"}):text(e):up();
elseif t=="error"then
cmdtag:tag("note",{type="error"}):text(e.message):up();
elseif t=="actions"then
local t=n.stanza("actions");
for a,e in ipairs(e)do
if(e=="prev")or(e=="next")or(e=="complete")then
t:tag(e):up();
else
module:log("error",'Command "'..o.name..
'" at node "'..o.node..'" provided an invalid action "'..e..'"');
end
end
cmdtag:add_child(t);
elseif t=="form"then
cmdtag:add_child((e.layout or e):form(e.values));
elseif t=="result"then
cmdtag:add_child((e.layout or e):form(e.values,"result"));
elseif t=="other"then
cmdtag:add_child(e);
end
end
a:add_child(cmdtag);
s.send(a);
return true;
end
return s;
end)
package.preload['util.stanza']=(function(...)
local t=table.insert;
local e=table.concat;
local s=table.remove;
local p=table.concat;
local h=string.format;
local y=string.match;
local c=tostring;
local u=setmetatable;
local e=getmetatable;
local i=pairs;
local n=ipairs;
local o=type;
local e=next;
local e=print;
local e=unpack;
local w=string.gsub;
local e=string.char;
local f=string.find;
local e=os;
local m=not e.getenv("WINDIR");
local d,a;
if m then
local t,e=pcall(require,"util.termcolours");
if t then
d,a=e.getstyle,e.getstring;
else
m=nil;
end
end
local v="urn:ietf:params:xml:ns:xmpp-stanzas";
module"stanza"
stanza_mt={__type="stanza"};
stanza_mt.__index=stanza_mt;
local e=stanza_mt;
function stanza(a,t)
local t={name=a,attr=t or{},tags={}};
return u(t,e);
end
local r=stanza;
function e:query(e)
return self:tag("query",{xmlns=e});
end
function e:body(t,e)
return self:tag("body",e):text(t);
end
function e:tag(e,a)
local a=r(e,a);
local e=self.last_add;
if not e then e={};self.last_add=e;end
(e[#e]or self):add_direct_child(a);
t(e,a);
return self;
end
function e:text(t)
local e=self.last_add;
(e and e[#e]or self):add_direct_child(t);
return self;
end
function e:up()
local e=self.last_add;
if e then s(e);end
return self;
end
function e:reset()
self.last_add=nil;
return self;
end
function e:add_direct_child(e)
if o(e)=="table"then
t(self.tags,e);
end
t(self,e);
end
function e:add_child(t)
local e=self.last_add;
(e and e[#e]or self):add_direct_child(t);
return self;
end
function e:get_child(t,a)
for o,e in n(self.tags)do
if(not t or e.name==t)
and((not a and self.attr.xmlns==e.attr.xmlns)
or e.attr.xmlns==a)then
return e;
end
end
end
function e:get_child_text(t,e)
local e=self:get_child(t,e);
if e then
return e:get_text();
end
return nil;
end
function e:child_with_name(t)
for a,e in n(self.tags)do
if e.name==t then return e;end
end
end
function e:child_with_ns(t)
for a,e in n(self.tags)do
if e.attr.xmlns==t then return e;end
end
end
function e:children()
local e=0;
return function(t)
e=e+1
return t[e];
end,self,e;
end
function e:childtags(a,e)
e=e or self.attr.xmlns;
local t=self.tags;
local i,o=1,#t;
return function()
for o=i,o do
local t=t[o];
if(not a or t.name==a)
and(not e or e==t.attr.xmlns)then
i=o+1;
return t;
end
end
end;
end
function e:maptags(i)
local a,t=self.tags,1;
local n,o=#self,#a;
local e=1;
while t<=o do
if self[e]==a[t]then
local i=i(self[e]);
if i==nil then
s(self,e);
s(a,t);
n=n-1;
o=o-1;
else
self[e]=i;
a[e]=i;
end
e=e+1;
t=t+1;
end
end
return self;
end
local l
do
local e={["'"]="&apos;",["\""]="&quot;",["<"]="&lt;",[">"]="&gt;",["&"]="&amp;"};
function l(t)return(w(t,"['&<>\"]",e));end
_M.xml_escape=l;
end
local function w(o,e,s,a,r)
local n=0;
local h=o.name
t(e,"<"..h);
for o,i in i(o.attr)do
if f(o,"\1",1,true)then
local s,o=y(o,"^([^\1]*)\1?(.*)$");
n=n+1;
t(e," xmlns:ns"..n.."='"..a(s).."' ".."ns"..n..":"..o.."='"..a(i).."'");
elseif not(o=="xmlns"and i==r)then
t(e," "..o.."='"..a(i).."'");
end
end
local i=#o;
if i==0 then
t(e,"/>");
else
t(e,">");
for i=1,i do
local i=o[i];
if i.name then
s(i,e,s,a,o.attr.xmlns);
else
t(e,a(i));
end
end
t(e,"</"..h..">");
end
end
function e.__tostring(t)
local e={};
w(t,e,w,l,nil);
return p(e);
end
function e.top_tag(t)
local e="";
if t.attr then
for t,a in i(t.attr)do if o(t)=="string"then e=e..h(" %s='%s'",t,l(c(a)));end end
end
return h("<%s%s>",t.name,e);
end
function e.get_text(e)
if#e.tags==0 then
return p(e);
end
end
function e.get_error(a)
local o,e,t;
local a=a:get_child("error");
if not a then
return nil,nil,nil;
end
o=a.attr.type;
for a in a:childtags()do
if a.attr.xmlns==v then
if not t and a.name=="text"then
t=a:get_text();
elseif not e then
e=a.name;
end
if e and t then
break;
end
end
end
return o,e or"undefined-condition",t;
end
do
local e=0;
function new_id()
e=e+1;
return"lx"..e;
end
end
function preserialize(e)
local a={name=e.name,attr=e.attr};
for i,e in n(e)do
if o(e)=="table"then
t(a,preserialize(e));
else
t(a,e);
end
end
return a;
end
function deserialize(a)
if a then
local s=a.attr;
for e=1,#s do s[e]=nil;end
local h={};
for e in i(s)do
if f(e,"|",1,true)and not f(e,"\1",1,true)then
local t,a=y(e,"^([^|]+)|(.+)$");
h[t.."\1"..a]=s[e];
s[e]=nil;
end
end
for t,e in i(h)do
s[t]=e;
end
u(a,e);
for t,e in n(a)do
if o(e)=="table"then
deserialize(e);
end
end
if not a.tags then
local i={};
for n,e in n(a)do
if o(e)=="table"then
t(i,e);
end
end
a.tags=i;
end
end
return a;
end
local function s(a)
local o,n={},{};
for t,e in i(a.attr)do o[t]=e;end
local o={name=a.name,attr=o,tags=n};
for e=1,#a do
local e=a[e];
if e.name then
e=s(e);
t(n,e);
end
t(o,e);
end
return u(o,e);
end
clone=s;
function message(t,e)
if not e then
return r("message",t);
else
return r("message",t):tag("body"):text(e):up();
end
end
function iq(e)
if e and not e.id then e.id=new_id();end
return r("iq",e or{id=new_id()});
end
function reply(e)
return r(e.name,e.attr and{to=e.attr.from,from=e.attr.to,id=e.attr.id,type=((e.name=="iq"and"result")or e.attr.type)});
end
do
local a={xmlns=v};
function error_reply(e,i,o,t)
local e=reply(e);
e.attr.type="error";
e:tag("error",{type=i})
:tag(o,a):up();
if(t)then e:tag("text",a):text(t):up();end
return e;
end
end
function presence(e)
return r("presence",e);
end
if m then
local r=d("yellow");
local s=d("red");
local u=d("red");
local t=d("magenta");
local r=" "..a(r,"%s")..a(t,"=")..a(s,"'%s'");
local s=a(t,"<")..a(u,"%s").."%s"..a(t,">");
local d=s.."%s"..a(t,"</")..a(u,"%s")..a(t,">");
function e.pretty_print(t)
local e="";
for a,t in n(t)do
if o(t)=="string"then
e=e..l(t);
else
e=e..t:pretty_print();
end
end
local a="";
if t.attr then
for e,t in i(t.attr)do if o(e)=="string"then a=a..h(r,e,c(t));end end
end
return h(d,t.name,a,e,t.name);
end
function e.pretty_top_tag(t)
local e="";
if t.attr then
for t,a in i(t.attr)do if o(t)=="string"then e=e..h(r,t,c(a));end end
end
return h(s,t.name,e);
end
else
e.pretty_print=e.__tostring;
e.pretty_top_tag=e.top_tag;
end
return _M;
end)
package.preload['util.timer']=(function(...)
local l=require"net.server".addtimer;
local i=require"net.server".event;
local u=require"net.server".event_base;
local r=math.min
local c=math.huge
local d=require"socket".gettime;
local h=table.insert;
local e=table.remove;
local e,n=ipairs,pairs;
local s=type;
local o={};
local t={};
module"timer"
local e;
if not i then
function e(a,o)
local i=d();
a=a+i;
if a>=i then
h(t,{a,o});
else
local t=o();
if t and s(t)=="number"then
return e(t,o);
end
end
end
l(function()
local a=d();
if#t>0 then
for a,e in n(t)do
h(o,e);
end
t={};
end
local t=c;
for h,i in n(o)do
local n,i=i[1],i[2];
if n<=a then
o[h]=nil;
local a=i(a);
if s(a)=="number"then
e(a,i);
t=r(t,a);
end
else
t=r(t,n-a);
end
end
return t;
end);
else
local o=(i.core and i.core.LEAVE)or-1;
function e(a,t)
local e;
e=u:addevent(nil,0,function()
local t=t();
if t then
return 0,t;
elseif e then
return o;
end
end
,a);
end
end
add_task=e;
return _M;
end)
package.preload['util.termcolours']=(function(...)
local l,h=table.concat,table.insert;
local t,s=string.char,string.format;
local r=ipairs;
local d=io.write;
local e;
if os.getenv("WINDIR")then
e=require"util.windows";
end
local o=e and e.get_consolecolor and e.get_consolecolor();
module"termcolours"
local i={
reset=0;bright=1,dim=2,underscore=4,blink=5,reverse=7,hidden=8;
black=30;red=31;green=32;yellow=33;blue=34;magenta=35;cyan=36;white=37;
["black background"]=40;["red background"]=41;["green background"]=42;["yellow background"]=43;["blue background"]=44;["magenta background"]=45;["cyan background"]=46;["white background"]=47;
bold=1,dark=2,underline=4,underlined=4,normal=0;
}
local n={
["0"]=o,
["1"]=7+8,
["1;33"]=2+4+8,
["1;31"]=4+8
}
local a=t(27).."[%sm%s"..t(27).."[0m";
function getstring(t,e)
if t then
return s(a,t,e);
else
return e;
end
end
function getstyle(...)
local e,t={...},{};
for a,e in r(e)do
e=i[e];
if e then
h(t,e);
end
end
return l(t,";");
end
local a="0";
function setstyle(e)
e=e or"0";
if e~=a then
d("\27["..e.."m");
a=e;
end
end
if e then
function setstyle(t)
t=t or"0";
if t~=a then
e.set_consolecolor(n[t]or o);
a=t;
end
end
if not o then
function setstyle(e)end
end
end
return _M;
end)
package.preload['util.uuid']=(function(...)
local e=math.random;
local n=tostring;
local e=os.time;
local o=os.clock;
local i=require"util.hashes".sha1;
module"uuid"
local t=0;
local function a()
local e=e();
if t>=e then e=t+1;end
t=e;
return e;
end
local function e(e)
return i(e..o()..n({}),true);
end
local t=e(a());
local function o(a)
t=e(t..a);
end
local function e(e)
if#t<e then o(a());end
local a=t:sub(0,e);
t=t:sub(e+1);
return a;
end
local function t()
return("%x"):format(e(1):byte()%4+8);
end
function generate()
return e(8).."-"..e(4).."-4"..e(3).."-"..(t())..e(3).."-"..e(12);
end
seed=o;
return _M;
end)
package.preload['net.dns']=(function(...)
local i=require"socket";
local q=require"util.timer";
local e,p=pcall(require,"util.windows");
local _=(e and p)or os.getenv("WINDIR");
local m,E,w,a,n=
coroutine,io,math,string,table;
local v,h,o,c,r,b,j,k,t,e,x=
ipairs,next,pairs,print,setmetatable,tostring,assert,error,unpack,select,type;
local e={
get=function(t,...)
local a=e('#',...);
for a=1,a do
t=t[e(a,...)];
if t==nil then break;end
end
return t;
end;
set=function(t,...)
local i=e('#',...);
local s,o=e(i-1,...);
local a,n;
for i=1,i-2 do
local i=e(i,...)
local e=t[i]
if o==nil then
if e==nil then
return;
elseif h(e,h(e))then
a=nil;n=nil;
elseif a==nil then
a=t;n=i;
end
elseif e==nil then
e={};
t[i]=e;
end
t=e
end
if o==nil and a then
a[n]=nil;
else
t[s]=o;
return o;
end
end;
};
local d,l=e.get,e.set;
local z=15;
module('dns')
local t=_M;
local s=n.insert
local function u(e)
return(e-(e%256))/256;
end
local function f(e)
local t={};
for o,e in o(e)do
t[o]=e;
t[e]=e;
t[a.lower(e)]=e;
end
return t;
end
local function y(i)
local e={};
for o,i in o(i)do
local t=a.char(u(o),o%256);
e[o]=t;
e[i]=t;
e[a.lower(i)]=t;
end
return e;
end
t.types={
'A','NS','MD','MF','CNAME','SOA','MB','MG','MR','NULL','WKS',
'PTR','HINFO','MINFO','MX','TXT',
[28]='AAAA',[29]='LOC',[33]='SRV',
[252]='AXFR',[253]='MAILB',[254]='MAILA',[255]='*'};
t.classes={'IN','CS','CH','HS',[255]='*'};
t.type=f(t.types);
t.class=f(t.classes);
t.typecode=y(t.types);
t.classcode=y(t.classes);
local function g(e,o,i)
if a.byte(e,-1)~=46 then e=e..'.';end
e=a.lower(e);
return e,t.type[o or'A'],t.class[i or'IN'];
end
local function y(a,t,s)
t=t or i.gettime();
for o,e in o(a)do
if e.tod then
e.ttl=w.floor(e.tod-t);
if e.ttl<=0 then
n.remove(a,o);
return y(a,t,s);
end
elseif s=='soft'then
j(e.ttl==0);
a[o]=nil;
end
end
end
local e={};
e.__index=e;
e.timeout=z;
local function j(e)
local e=e.type and e[e.type:lower()];
if x(e)~="string"then
return"<UNKNOWN RDATA TYPE>";
end
return e;
end
local f={
LOC=e.LOC_tostring;
MX=function(e)
return a.format('%2i %s',e.pref,e.mx);
end;
SRV=function(e)
local e=e.srv;
return a.format('%5d %5d %5d %s',e.priority,e.weight,e.port,e.target);
end;
};
local x={};
function x.__tostring(e)
local t=(f[e.type]or j)(e);
return a.format('%2s %-5s %6i %-28s %s',e.class,e.type,e.ttl,e.name,t);
end
local j={};
function j.__tostring(t)
local e={};
for a,t in o(t)do
s(e,b(t)..'\n');
end
return n.concat(e);
end
local f={};
function f.__tostring(e)
local a=i.gettime();
local t={};
for i,e in o(e)do
for i,e in o(e)do
for o,e in o(e)do
y(e,a);
s(t,b(e));
end
end
end
return n.concat(t);
end
function e:new()
local t={active={},cache={},unsorted={}};
r(t,e);
r(t.cache,f);
r(t.unsorted,{__mode='kv'});
return t;
end
function t.random(...)
w.randomseed(w.floor(1e4*i.gettime()));
t.random=w.random;
return t.random(...);
end
local function w(e)
e=e or{};
e.id=e.id or t.random(0,65535);
e.rd=e.rd or 1;
e.tc=e.tc or 0;
e.aa=e.aa or 0;
e.opcode=e.opcode or 0;
e.qr=e.qr or 0;
e.rcode=e.rcode or 0;
e.z=e.z or 0;
e.ra=e.ra or 0;
e.qdcount=e.qdcount or 1;
e.ancount=e.ancount or 0;
e.nscount=e.nscount or 0;
e.arcount=e.arcount or 0;
local t=a.char(
u(e.id),e.id%256,
e.rd+2*e.tc+4*e.aa+8*e.opcode+128*e.qr,
e.rcode+16*e.z+128*e.ra,
u(e.qdcount),e.qdcount%256,
u(e.ancount),e.ancount%256,
u(e.nscount),e.nscount%256,
u(e.arcount),e.arcount%256
);
return t,e.id;
end
local function u(t)
local e={};
for t in a.gmatch(t,'[^.]+')do
s(e,a.char(a.len(t)));
s(e,t);
end
s(e,a.char(0));
return n.concat(e);
end
local function z(o,a,e)
o=u(o);
a=t.typecode[a or'a'];
e=t.classcode[e or'in'];
return o..a..e;
end
function e:byte(e)
e=e or 1;
local o=self.offset;
local t=o+e-1;
if t>#self.packet then
k(a.format('out of bounds: %i>%i',t,#self.packet));
end
self.offset=o+e;
return a.byte(self.packet,o,t);
end
function e:word()
local t,e=self:byte(2);
return 256*t+e;
end
function e:dword()
local o,t,e,a=self:byte(4);
return 16777216*o+65536*t+256*e+a;
end
function e:sub(e)
e=e or 1;
local t=a.sub(self.packet,self.offset,self.offset+e-1);
self.offset=self.offset+e;
return t;
end
function e:header(t)
local e=self:word();
if not self.active[e]and not t then return nil;end
local e={id=e};
local t,a=self:byte(2);
e.rd=t%2;
e.tc=t/2%2;
e.aa=t/4%2;
e.opcode=t/8%16;
e.qr=t/128;
e.rcode=a%16;
e.z=a/16%8;
e.ra=a/128;
e.qdcount=self:word();
e.ancount=self:word();
e.nscount=self:word();
e.arcount=self:word();
for a,t in o(e)do e[a]=t-t%1;end
return e;
end
function e:name()
local t,a=nil,0;
local e=self:byte();
local o={};
if e==0 then return"."end
while e>0 do
if e>=192 then
a=a+1;
if a>=20 then k('dns error: 20 pointers');end;
local e=((e-192)*256)+self:byte();
t=t or self.offset;
self.offset=e+1;
else
s(o,self:sub(e)..'.');
end
e=self:byte();
end
self.offset=t or self.offset;
return n.concat(o);
end
function e:question()
local e={};
e.name=self:name();
e.type=t.type[self:word()];
e.class=t.class[self:word()];
return e;
end
function e:A(i)
local n,t,o,e=self:byte(4);
i.a=a.format('%i.%i.%i.%i',n,t,o,e);
end
function e:AAAA(a)
local e={};
for t=1,a.rdlength,2 do
local t,a=self:byte(2);
n.insert(e,("%02x%02x"):format(t,a));
end
e=n.concat(e,":"):gsub("%f[%x]0+(%x)","%1");
local t={};
for e in e:gmatch(":[0:]+:")do
n.insert(t,e)
end
if#t==0 then
a.aaaa=e;
return
elseif#t>1 then
n.sort(t,function(e,t)return#e>#t end);
end
a.aaaa=e:gsub(t[1],"::",1):gsub("^0::","::"):gsub("::0$","::");
end
function e:CNAME(e)
e.cname=self:name();
end
function e:MX(e)
e.pref=self:word();
e.mx=self:name();
end
function e:LOC_nibble_power()
local e=self:byte();
return((e-(e%16))/16)*(10^(e%16));
end
function e:LOC(e)
e.version=self:byte();
if e.version==0 then
e.loc=e.loc or{};
e.loc.size=self:LOC_nibble_power();
e.loc.horiz_pre=self:LOC_nibble_power();
e.loc.vert_pre=self:LOC_nibble_power();
e.loc.latitude=self:dword();
e.loc.longitude=self:dword();
e.loc.altitude=self:dword();
end
end
local function u(e,n,t)
e=e-2147483648;
if e<0 then n=t;e=-e;end
local i,o,t;
t=e%6e4;
e=(e-t)/6e4;
o=e%60;
i=(e-o)/60;
return a.format('%3d %2d %2.3f %s',i,o,t/1e3,n);
end
function e.LOC_tostring(e)
local t={};
s(t,a.format(
'%s    %s    %.2fm %.2fm %.2fm %.2fm',
u(e.loc.latitude,'N','S'),
u(e.loc.longitude,'E','W'),
(e.loc.altitude-1e7)/100,
e.loc.size/100,
e.loc.horiz_pre/100,
e.loc.vert_pre/100
));
return n.concat(t);
end
function e:NS(e)
e.ns=self:name();
end
function e:SOA(e)
end
function e:SRV(e)
e.srv={};
e.srv.priority=self:word();
e.srv.weight=self:word();
e.srv.port=self:word();
e.srv.target=self:name();
end
function e:PTR(e)
e.ptr=self:name();
end
function e:TXT(e)
e.txt=self:sub(self:byte());
end
function e:rr()
local e={};
r(e,x);
e.name=self:name(self);
e.type=t.type[self:word()]or e.type;
e.class=t.class[self:word()]or e.class;
e.ttl=65536*self:word()+self:word();
e.rdlength=self:word();
if e.ttl<=0 then
e.tod=self.time+30;
else
e.tod=self.time+e.ttl;
end
local a=self.offset;
local t=self[t.type[e.type]];
if t then t(self,e);end
self.offset=a;
e.rdata=self:sub(e.rdlength);
return e;
end
function e:rrs(t)
local e={};
for t=1,t do s(e,self:rr());end
return e;
end
function e:decode(t,o)
self.packet,self.offset=t,1;
local t=self:header(o);
if not t then return nil;end
local t={header=t};
t.question={};
local i=self.offset;
for e=1,t.header.qdcount do
s(t.question,self:question());
end
t.question.raw=a.sub(self.packet,i,self.offset-1);
if not o then
if not self.active[t.header.id]or not self.active[t.header.id][t.question.raw]then
return nil;
end
end
t.answer=self:rrs(t.header.ancount);
t.authority=self:rrs(t.header.nscount);
t.additional=self:rrs(t.header.arcount);
return t;
end
e.delays={1,3};
function e:addnameserver(e)
self.server=self.server or{};
s(self.server,e);
end
function e:setnameserver(e)
self.server={};
self:addnameserver(e);
end
function e:adddefaultnameservers()
if _ then
if p and p.get_nameservers then
for t,e in v(p.get_nameservers())do
self:addnameserver(e);
end
end
if not self.server or#self.server==0 then
self:addnameserver("208.67.222.222");
self:addnameserver("208.67.220.220");
end
else
local e=E.open("/etc/resolv.conf");
if e then
for e in e:lines()do
e=e:gsub("#.*$","")
:match('^%s*nameserver%s+(.*)%s*$');
if e then
e:gsub("%f[%d.](%d+%.%d+%.%d+%.%d+)%f[^%d.]",function(e)
self:addnameserver(e)
end);
end
end
end
if not self.server or#self.server==0 then
self:addnameserver("127.0.0.1");
end
end
end
function e:getsocket(t)
self.socket=self.socket or{};
self.socketset=self.socketset or{};
local e=self.socket[t];
if e then return e;end
local a;
e,a=i.udp();
if not e then
return nil,a;
end
if self.socket_wrapper then e=self.socket_wrapper(e,self);end
e:settimeout(0);
e:setsockname('*',0);
e:setpeername(self.server[t],53);
self.socket[t]=e;
self.socketset[e]=t;
return e;
end
function e:voidsocket(e)
if self.socket[e]then
self.socketset[self.socket[e]]=nil;
self.socket[e]=nil;
elseif self.socketset[e]then
self.socket[self.socketset[e]]=nil;
self.socketset[e]=nil;
end
end
function e:socket_wrapper_set(e)
self.socket_wrapper=e;
end
function e:closeall()
for t,e in v(self.socket)do
self.socket[t]=nil;
self.socketset[e]=nil;
e:close();
end
end
function e:remember(t,e)
local a,i,o=g(t.name,t.type,t.class);
if e~='*'then
e=i;
local e=d(self.cache,o,'*',a);
if e then s(e,t);end
end
self.cache=self.cache or r({},f);
local a=d(self.cache,o,e,a)or
l(self.cache,o,e,a,r({},j));
s(a,t);
if e=='MX'then self.unsorted[a]=true;end
end
local function s(t,e)
return(t.pref==e.pref)and(t.mx<e.mx)or(t.pref<e.pref);
end
function e:peek(a,t,o)
a,t,o=g(a,t,o);
local e=d(self.cache,o,t,a);
if not e then return nil;end
if y(e,i.gettime())and t=='*'or not h(e)then
l(self.cache,o,t,a,nil);
return nil;
end
if self.unsorted[e]then n.sort(e,s);end
return e;
end
function e:purge(e)
if e=='soft'then
self.time=i.gettime();
for t,e in o(self.cache or{})do
for t,e in o(e)do
for t,e in o(e)do
y(e,self.time,'soft')
end
end
end
else self.cache=r({},f);end
end
function e:query(a,t,e)
a,t,e=g(a,t,e)
if not self.server then self:adddefaultnameservers();end
local n=z(a,t,e);
local o=self:peek(a,t,e);
if o then return o;end
local s,o=w();
local i={
packet=s..n,
server=self.best_server,
delay=1,
retry=i.gettime()+self.delays[1]
};
self.active[o]=self.active[o]or{};
self.active[o][n]=i;
local n=m.running();
if n then
l(self.wanted,e,t,a,n,true);
end
local o,h=self:getsocket(i.server)
if not o then
return nil,h;
end
o:send(i.packet)
if q and self.timeout then
local r=#self.server;
local s=1;
q.add_task(self.timeout,function()
if d(self.wanted,e,t,a,n)then
if s<r then
s=s+1;
self:servfail(o);
i.server=self.best_server;
o,h=self:getsocket(i.server);
if o then
o:send(i.packet);
return self.timeout;
end
end
self:cancel(e,t,a,n,true);
end
end)
end
return true;
end
function e:servfail(e)
local a=self.socketset[e]
self:voidsocket(e);
self.time=i.gettime();
for e,t in o(self.active)do
for o,e in o(t)do
if e.server==a then
e.server=e.server+1
if e.server>#self.server then
e.server=1;
end
e.retries=(e.retries or 0)+1;
if e.retries>=#self.server then
t[o]=nil;
else
local t=self:getsocket(e.server);
if t then t:send(e.packet);end
end
end
end
end
if a==self.best_server then
self.best_server=self.best_server+1;
if self.best_server>#self.server then
self.best_server=1;
end
end
end
function e:settimeout(e)
self.timeout=e;
end
function e:receive(t)
self.time=i.gettime();
t=t or self.socket;
local e;
for a,t in o(t)do
if self.socketset[t]then
local t=t:receive();
if t then
e=self:decode(t);
if e and self.active[e.header.id]
and self.active[e.header.id][e.question.raw]then
for a,t in o(e.answer)do
if t.name:sub(-#e.question[1].name,-1)==e.question[1].name then
self:remember(t,e.question[1].type)
end
end
local t=self.active[e.header.id];
t[e.question.raw]=nil;
if not h(t)then self.active[e.header.id]=nil;end
if not h(self.active)then self:closeall();end
local e=e.question[1];
local t=d(self.wanted,e.class,e.type,e.name);
if t then
for t in o(t)do
l(self.yielded,t,e.class,e.type,e.name,nil);
if m.status(t)=="suspended"then m.resume(t);end
end
l(self.wanted,e.class,e.type,e.name,nil);
end
end
end
end
end
return e;
end
function e:feed(a,t,e)
self.time=i.gettime();
local e=self:decode(t,e);
if e and self.active[e.header.id]
and self.active[e.header.id][e.question.raw]then
for a,t in o(e.answer)do
self:remember(t,e.question[1].type);
end
local t=self.active[e.header.id];
t[e.question.raw]=nil;
if not h(t)then self.active[e.header.id]=nil;end
if not h(self.active)then self:closeall();end
local e=e.question[1];
if e then
local t=d(self.wanted,e.class,e.type,e.name);
if t then
for t in o(t)do
l(self.yielded,t,e.class,e.type,e.name,nil);
if m.status(t)=="suspended"then m.resume(t);end
end
l(self.wanted,e.class,e.type,e.name,nil);
end
end
end
return e;
end
function e:cancel(t,a,i,e,o)
local t=d(self.wanted,t,a,i);
if t then
if o then
m.resume(e);
end
t[e]=nil;
end
end
function e:pulse()
while self:receive()do end
if not h(self.active)then return nil;end
self.time=i.gettime();
for a,t in o(self.active)do
for o,e in o(t)do
if self.time>=e.retry then
e.server=e.server+1;
if e.server>#self.server then
e.server=1;
e.delay=e.delay+1;
end
if e.delay>#self.delays then
t[o]=nil;
if not h(t)then self.active[a]=nil;end
if not h(self.active)then return nil;end
else
local t=self.socket[e.server];
if t then t:send(e.packet);end
e.retry=self.time+self.delays[e.delay];
end
end
end
end
if h(self.active)then return true;end
return nil;
end
function e:lookup(e,t,a)
self:query(e,t,a)
while self:pulse()do
local e={}
for t,a in v(self.socket)do
e[t]=a
end
i.select(e,nil,4)
end
return self:peek(e,t,a);
end
function e:lookupex(o,e,a,t)
return self:peek(e,a,t)or self:query(e,a,t);
end
function e:tohostname(e)
return t.lookup(e:gsub("(%d+)%.(%d+)%.(%d+)%.(%d+)","%4.%3.%2.%1.in-addr.arpa."),"PTR");
end
local i={
qr={[0]='query','response'},
opcode={[0]='query','inverse query','server status request'},
aa={[0]='non-authoritative','authoritative'},
tc={[0]='complete','truncated'},
rd={[0]='recursion not desired','recursion desired'},
ra={[0]='recursion not available','recursion available'},
z={[0]='(reserved)'},
rcode={[0]='no error','format error','server failure','name error','not implemented'},
type=t.type,
class=t.class
};
local function s(t,e)
return(i[e]and i[e][t[e]])or'';
end
function e.print(e)
for o,t in o{'id','qr','opcode','aa','tc','rd','ra','z',
'rcode','qdcount','ancount','nscount','arcount'}do
c(a.format('%-30s','header.'..t),e.header[t],s(e.header,t));
end
for e,t in v(e.question)do
c(a.format('question[%i].name         ',e),t.name);
c(a.format('question[%i].type         ',e),t.type);
c(a.format('question[%i].class        ',e),t.class);
end
local h={name=1,type=1,class=1,ttl=1,rdlength=1,rdata=1};
local t;
for n,i in o({'answer','authority','additional'})do
for n,e in o(e[i])do
for h,o in o({'name','type','class','ttl','rdlength'})do
t=a.format('%s[%i].%s',i,n,o);
c(a.format('%-30s',t),e[o],s(e,o));
end
for e,o in o(e)do
if not h[e]then
t=a.format('%s[%i].%s',i,n,e);
c(a.format('%-30s  %s',b(t),b(o)));
end
end
end
end
end
function t.resolver()
local t={active={},cache={},unsorted={},wanted={},yielded={},best_server=1};
r(t,e);
r(t.cache,f);
r(t.unsorted,{__mode='kv'});
return t;
end
local e=t.resolver();
t._resolver=e;
function t.lookup(...)
return e:lookup(...);
end
function t.tohostname(...)
return e:tohostname(...);
end
function t.purge(...)
return e:purge(...);
end
function t.peek(...)
return e:peek(...);
end
function t.query(...)
return e:query(...);
end
function t.feed(...)
return e:feed(...);
end
function t.cancel(...)
return e:cancel(...);
end
function t.settimeout(...)
return e:settimeout(...);
end
function t.socket_wrapper_set(...)
return e:socket_wrapper_set(...);
end
return t;
end)
package.preload['net.adns']=(function(...)
local c=require"net.server";
local o=require"net.dns";
local e=require"util.logger".init("adns");
local t,t=table.insert,table.remove;
local a,s,l=coroutine,tostring,pcall;
local function u(a,a,e,t)return(t-e)+1;end
module"adns"
function lookup(d,t,r,h)
return a.wrap(function(i)
if i then
e("debug","Records for %s already cached, using those...",t);
d(i);
return;
end
e("debug","Records for %s not in cache, sending query (%s)...",t,s(a.running()));
local n,i=o.query(t,r,h);
if n then
a.yield({h or"IN",r or"A",t,a.running()});
e("debug","Reply for %s (%s)",t,s(a.running()));
end
if n then
n,i=l(d,o.peek(t,r,h));
else
e("error","Error sending DNS query: %s",i);
n,i=l(d,nil,i);
end
if not n then
e("error","Error in DNS response handler: %s",s(i));
end
end)(o.peek(t,r,h));
end
function cancel(t,a,i)
e("warn","Cancelling DNS lookup for %s",s(t[3]));
o.cancel(t[1],t[2],t[3],t[4],a);
end
function new_async_socket(a,i)
local s="<unknown>";
local n={};
local t={};
function n.onincoming(a,e)
if e then
o.feed(t,e);
end
end
function n.ondisconnect(o,a)
if a then
e("warn","DNS socket for %s disconnected: %s",s,a);
local t=i.server;
if i.socketset[o]==i.best_server and i.best_server==#t then
e("error","Exhausted all %d configured DNS servers, next lookup will try %s again",#t,t[1]);
end
i:servfail(o);
end
end
t=c.wrapclient(a,"dns",53,n);
if not t then
e("warn","handler is nil");
end
t.settimeout=function()end
t.setsockname=function(e,...)return a:setsockname(...);end
t.setpeername=function(e,...)s=(...);local a=a:setpeername(...);e:set_send(u);return a;end
t.connect=function(e,...)return a:connect(...)end
t.send=function(t,o)
local t=a.getpeername;
e("debug","Sending DNS query to %s",(t and t(a))or"<unconnected>");
return a:send(o);
end
return t;
end
o.socket_wrapper_set(new_async_socket);
return _M;
end)
package.preload['net.server']=(function(...)
local h=function(e)
return _G[e]
end
local X=function(e)
for t,a in pairs(e)do
e[t]=nil
end
end
local L,e=require("util.logger").init("socket"),table.concat;
local i=function(...)return L("debug",e{...});end
local re=function(...)return L("warn",e{...});end
local e=collectgarbage
local he=1
local U=h"type"
local E=h"pairs"
local de=h"ipairs"
local k=h"tonumber"
local l=h"tostring"
local e=h"collectgarbage"
local o=h"os"
local e=h"table"
local t=h"string"
local a=h"coroutine"
local Q=o.difftime
local J=math.min
local le=math.huge
local ue=e.concat
local e=e.remove
local ce=t.len
local ye=t.sub
local we=a.wrap
local fe=a.yield
local p=h"ssl"
local T=h"socket"or require"socket"
local K=T.gettime
local pe=(p and p.wrap)
local me=T.bind
local be=T.sleep
local ve=T.select
local e=(p and p.newcontext)
local G
local B
local se
local V
local Y
local ne
local c
local ie
local te
local ee
local Z
local P
local r
local ae
local e
local C
local oe
local v
local d
local H
local s
local n
local _
local g
local w
local f
local a
local o
local b
local M
local F
local z
local x
local W
local u
local j
local A
local I
local O
local S
local D
local R
local q
local N
v={}
d={}
s={}
H={}
n={}
g={}
w={}
_={}
a=0
o=0
b=0
M=0
F=0
z=1
x=0
j=51e3*1024
A=25e3*1024
I=12e5
O=6e4
S=6*60*60
D=false
q=1e3
N=30
ee=function(f,t,v,u,y,m,c)
c=c or q
local h=0
local w,e=f.onconnect,f.ondisconnect
local p=t.accept
local e={}
e.shutdown=function()end
e.ssl=function()
return m~=nil
end
e.sslctx=function()
return m
end
e.remove=function()
h=h-1
end
e.close=function()
for a,e in E(n)do
if e.serverport==u then
e.disconnect(e,"server closed")
e:close(true)
end
end
t:close()
o=r(s,t,o)
a=r(d,t,a)
n[t]=nil
e=nil
t=nil
i"server.lua: closed server handler and removed sockets from list"
end
e.ip=function()
return v
end
e.serverport=function()
return u
end
e.socket=function()
return t
end
e.readbuffer=function()
if h>c then
i("server.lua: refused new client connection: server full")
return false
end
local t,a=p(t)
if t then
local a,o=t:getpeername()
t:settimeout(0)
local e,n,t=C(e,f,t,a,u,o,y,m)
if t then
return false
end
h=h+1
i("server.lua: accepted new client connection from ",l(a),":",l(o)," to ",l(u))
if w then
return w(e);
end
return;
elseif a then
i("server.lua: error with new client connection: ",l(a))
return false
end
end
return e
end
C=function(Q,b,t,B,J,S,I,x)
t:settimeout(0)
local y
local O
local q
local W
local P=b.onincoming
local C=b.onstatus
local k=b.ondisconnect
local Y=b.ondrain
local v={}
local h=0
local K
local R
local H
local m=0
local z=false
local T=false
local L,U=0,0
local j=j
local E=A
local e=v
e.dispatch=function()
return P
end
e.disconnect=function()
return k
end
e.setlistener=function(a,t)
P=t.onincoming
k=t.ondisconnect
C=t.onstatus
Y=t.ondrain
end
e.getstats=function()
return U,L
end
e.ssl=function()
return W
end
e.sslctx=function()
return x
end
e.send=function(n,i,o,a)
return y(t,i,o,a)
end
e.receive=function(o,a)
return O(t,o,a)
end
e.shutdown=function(a)
return q(t,a)
end
e.setoption=function(i,o,a)
if t.setoption then
return t:setoption(o,a);
end
return false,"setoption not implemented";
end
e.close=function(u,l)
if not e then return true;end
a=r(d,t,a)
g[e]=nil
if h~=0 then
if not(l or R)then
e.sendbuffer()
if h~=0 then
if e then
e.write=nil
end
K=true
return false
end
else
y(t,ue(v,"",1,h),1,m)
end
end
if t then
f=q and q(t)
t:close()
o=r(s,t,o)
n[t]=nil
t=nil
else
i"server.lua: socket already closed"
end
if e then
w[e]=nil
_[e]=nil
e=nil
end
if Q then
Q.remove()
end
i"server.lua: closed client handler and removed socket from list"
return true
end
e.ip=function()
return B
end
e.serverport=function()
return J
end
e.clientport=function()
return S
end
local _=function(i,a)
m=m+ce(a)
if m>j then
_[e]="send buffer exceeded"
e.write=V
return false
elseif t and not s[t]then
o=c(s,t,o)
end
h=h+1
v[h]=a
if e then
w[e]=w[e]or u
end
return true
end
e.write=_
e.bufferqueue=function(t)
return v
end
e.socket=function(a)
return t
end
e.set_mode=function(a,t)
I=t or I
return I
end
e.set_send=function(a,t)
y=t or y
return y
end
e.bufferlen=function(o,a,t)
j=t or j
E=a or E
return m,E,j
end
e.lock_read=function(i,o)
if o==true then
local o=a
a=r(d,t,a)
g[e]=nil
if a~=o then
z=true
end
elseif o==false then
if z then
z=false
a=c(d,t,a)
g[e]=u
end
end
return z
end
e.pause=function(t)
return t:lock_read(true);
end
e.resume=function(t)
return t:lock_read(false);
end
e.lock=function(i,a)
e.lock_read(a)
if a==true then
e.write=V
local a=o
o=r(s,t,o)
w[e]=nil
if o~=a then
T=true
end
elseif a==false then
e.write=_
if T then
T=false
_("")
end
end
return z,T
end
local g=function()
local o,t,a=O(t,I)
if not t or(t=="wantread"or t=="timeout")then
local a=o or a or""
local o=ce(a)
if o>E then
k(e,"receive buffer exceeded")
e:close(true)
return false
end
local o=o*he
U=U+o
F=F+o
g[e]=u
return P(e,a,t)
else
i("server.lua: client ",l(B),":",l(S)," read error: ",l(t))
R=true
k(e,t)
f=e and e:close()
return false
end
end
local w=function()
local c,a,d,n,p;
local p;
if t then
n=ue(v,"",1,h)
c,a,d=y(t,n,1,m)
p=(c or d or 0)*he
L=L+p
M=M+p
f=D and X(v)
else
c,a,p=false,"closed",0;
end
if c then
h=0
m=0
o=r(s,t,o)
w[e]=nil
if Y then
Y(e)
end
f=H and e:starttls(nil)
f=K and e:close()
return true
elseif d and(a=="timeout"or a=="wantwrite")then
n=ye(n,d+1,m)
v[1]=n
h=1
m=m-d
w[e]=u
return true
else
i("server.lua: client ",l(B),":",l(S)," write error: ",l(a))
R=true
k(e,a)
f=e and e:close()
return false
end
end
local u;
function e.set_sslctx(y,t)
x=t;
local h,m
u=we(function(n)
local t
for l=1,N do
o=(m and r(s,n,o))or o
a=(h and r(d,n,a))or a
h,m=nil,nil
f,t=n:dohandshake()
if not t then
i("server.lua: ssl handshake done")
e.readbuffer=g
e.sendbuffer=w
f=C and C(e,"ssl-handshake-complete")
if y.autostart_ssl and b.onconnect then
b.onconnect(y);
end
a=c(d,n,a)
return true
else
if t=="wantwrite"then
o=c(s,n,o)
m=true
elseif t=="wantread"then
a=c(d,n,a)
h=true
else
break;
end
t=nil;
fe()
end
end
i("server.lua: ssl handshake error: ",l(t or"handshake too long"))
k(e,"ssl handshake failed")
f=e and e:close(true)
return false
end
)
end
if p then
e.starttls=function(f,m)
if m then
e:set_sslctx(m);
end
if h>0 then
i"server.lua: we need to do tls, but delaying until send buffer empty"
H=true
return
end
i("server.lua: attempting to start tls on "..l(t))
local m,h=t
t,h=pe(t,x)
if not t then
i("server.lua: error while starting tls on client: ",l(h or"unknown error"))
return nil,h
end
t:settimeout(0)
y=t.send
O=t.receive
q=G
n[t]=e
a=c(d,t,a)
a=r(d,m,a)
o=r(s,m,o)
n[m]=nil
e.starttls=nil
H=nil
W=true
e.readbuffer=u
e.sendbuffer=u
u(t)
end
end
e.readbuffer=g
e.sendbuffer=w
y=t.send
O=t.receive
q=(W and G)or t.shutdown
n[t]=e
a=c(d,t,a)
if x and p then
i"server.lua: auto-starting ssl negotiation..."
e.autostart_ssl=true;
e:starttls(x);
end
return e,t
end
G=function()
end
V=function()
return false
end
c=function(a,t,e)
if not a[t]then
e=e+1
a[e]=t
a[t]=e
end
return e;
end
r=function(e,i,t)
local o=e[i]
if o then
e[i]=nil
local a=e[t]
e[t]=nil
if a~=i then
e[a]=o
e[o]=a
end
return t-1
end
return t
end
P=function(e)
o=r(s,e,o)
a=r(d,e,a)
n[e]=nil
e:close()
end
local function y(e,t,o)
local a;
local i=t.sendbuffer;
function t.sendbuffer()
i();
if a and t.bufferlen()<o then
e:lock_read(false);
a=nil;
end
end
local i=e.readbuffer;
function e.readbuffer()
i();
if not a and t.bufferlen()>=o then
a=true;
e:lock_read(true);
end
end
end
ie=function(t,e,r,l,h)
local o
if U(r)~="table"then
o="invalid listener table"
end
if U(e)~="number"or not(e>=0 and e<=65535)then
o="invalid port"
elseif v[t..":"..e]then
o="listeners on '["..t.."]:"..e.."' already exist"
elseif h and not p then
o="luasec not found"
end
if o then
re("server.lua, [",t,"]:",e,": ",o)
return nil,o
end
t=t or"*"
local o,s=me(t,e)
if s then
re("server.lua, [",t,"]:",e,": ",s)
return nil,s
end
local s,r=ee(r,o,t,e,l,h,q)
if not s then
o:close()
return nil,r
end
o:settimeout(0)
a=c(d,o,a)
v[t..":"..e]=s
n[o]=s
i("server.lua: new "..(h and"ssl "or"").."server listener on '[",t,"]:",e,"'")
return s
end
te=function(e,t)
return v[e..":"..t];
end
ae=function(e,t)
local a=v[e..":"..t]
if not a then
return nil,"no server found on '["..e.."]:"..l(t).."'"
end
a:close()
v[e..":"..t]=nil
return true
end
ne=function()
for e,t in E(n)do
t:close()
n[e]=nil
end
a=0
o=0
b=0
v={}
d={}
s={}
H={}
n={}
end
Z=function()
return z,x,j,A,I,O,S,D,q,N
end
oe=function(e)
if U(e)~="table"then
return nil,"invalid settings table"
end
z=k(e.timeout)or z
x=k(e.sleeptime)or x
j=k(e.maxsendlen)or j
A=k(e.maxreadlen)or A
I=k(e.checkinterval)or I
O=k(e.sendtimeout)or O
S=k(e.readtimeout)or S
D=e.cleanqueue
q=e._maxclientsperserver or q
N=e._maxsslhandshake or N
return true
end
Y=function(e)
if U(e)~="function"then
return nil,"invalid listener function"
end
b=b+1
H[b]=e
return true
end
se=function()
return F,M,a,o,b
end
local t;
local function m(e)
t=not not e;
end
B=function(a)
if t then return"quitting";end
if a then t="once";end
local e=le;
repeat
local a,o,s=ve(d,s,J(z,e))
for t,e in de(o)do
local t=n[e]
if t then
t.sendbuffer()
else
P(e)
i"server.lua: found no handler and closed socket (writelist)"
end
end
for t,e in de(a)do
local t=n[e]
if t then
t.readbuffer()
else
P(e)
i"server.lua: found no handler and closed socket (readlist)"
end
end
for e,t in E(_)do
e.disconnect()(e,t)
e:close(true)
end
X(_)
u=K()
if u-R>=J(e,1)then
e=le;
for t=1,b do
local t=H[t](u)
if t then e=J(e,t);end
end
R=u
else
e=e-(u-R);
end
be(x)
until t;
if a and t=="once"then t=nil;return;end
return"quitting"
end
local function l()
return B(true);
end
local function d()
return"select";
end
local s=function(t,e,h,a,d,i)
local e=C(nil,a,t,e,h,"clientport",d,i)
n[t]=e
if not i then
o=c(s,t,o)
if a.onconnect then
local i=e.sendbuffer;
e.sendbuffer=function()
o=r(s,t,o);
e.sendbuffer=i;
a.onconnect(e);
if#e:bufferqueue()>0 then
return i();
end
end
end
end
return e,t
end
local t=function(o,a,i,r,n)
local e,t=T.tcp()
if t then
return nil,t
end
e:settimeout(0)
f,t=e:connect(o,a)
if t then
local e=s(e,o,a,i)
else
C(nil,i,e,o,a,"clientport",r,n)
end
end
h"setmetatable"(n,{__mode="k"})
h"setmetatable"(g,{__mode="k"})
h"setmetatable"(w,{__mode="k"})
R=K()
W=K()
Y(function()
local e=Q(u-W)
if e>I then
W=u
for e,t in E(w)do
if Q(u-t)>O then
e.disconnect()(e,"send timeout")
e:close(true)
end
end
for e,t in E(g)do
if Q(u-t)>S then
e.disconnect()(e,"read timeout")
e:close()
end
end
end
end
)
local function a(e)
local t=L;
if e then
L=e;
end
return t;
end
return{
addclient=t,
wrapclient=s,
loop=B,
link=y,
step=l,
stats=se,
closeall=ne,
addtimer=Y,
addserver=ie,
getserver=te,
setlogger=a,
getsettings=Z,
setquitting=m,
removeserver=ae,
get_backend=d,
changesettings=oe,
}
end)
package.preload['util.xmppstream']=(function(...)
local e=require"lxp";
local t=require"util.stanza";
local w=t.stanza_mt;
local f=error;
local t=tostring;
local s=table.insert;
local y=table.concat;
local v=table.remove;
local p=setmetatable;
local b=pcall(e.new,{StartDoctypeDecl=false});
module"xmppstream"
local m=e.new;
local g={
["http://www.w3.org/XML/1998/namespace\1lang"]="xml:lang";
["http://www.w3.org/XML/1998/namespace\1space"]="xml:space";
["http://www.w3.org/XML/1998/namespace\1base"]="xml:base";
["http://www.w3.org/XML/1998/namespace\1id"]="xml:id";
};
local o="http://etherx.jabber.org/streams";
local n="\1";
local c="^([^"..n.."]*)"..n.."?(.*)$";
_M.ns_separator=n;
_M.ns_pattern=c;
function new_sax_handlers(a,e)
local i={};
local l=e.streamopened;
local u=e.streamclosed;
local h=e.error or function(a,e)f("XML stream error: "..t(e));end;
local k=e.handlestanza;
local t=e.stream_ns or o;
local d=e.stream_tag or"stream";
if t~=""then
d=t..n..d;
end
local q=t..n..(e.error_tag or"error");
local m=e.default_ns;
local r={};
local o,e={};
local n=0;
function i:StartElement(u,t)
if e and#o>0 then
s(e,y(o));
o={};
end
local i,o=u:match(c);
if o==""then
i,o="",i;
end
if i~=m or n>0 then
t.xmlns=i;
n=n+1;
end
for a=1,#t do
local e=t[a];
t[a]=nil;
local a=g[e];
if a then
t[a]=t[e];
t[e]=nil;
end
end
if not e then
if a.notopen then
if u==d then
n=0;
if l then
l(a,t);
end
else
h(a,"no-stream");
end
return;
end
if i=="jabber:client"and o~="iq"and o~="presence"and o~="message"then
h(a,"invalid-top-level-element");
end
e=p({name=o,attr=t,tags={}},w);
else
s(r,e);
local a=e;
e=p({name=o,attr=t,tags={}},w);
s(a,e);
s(a.tags,e);
end
end
function i:CharacterData(t)
if e then
s(o,t);
end
end
function i:EndElement(t)
if n>0 then
n=n-1;
end
if e then
if#o>0 then
s(e,y(o));
o={};
end
if#r==0 then
if t~=q then
k(a,e);
else
h(a,"stream-error",e);
end
e=nil;
else
e=v(r);
end
else
if u then
u(a);
end
end
end
local function t(e)
h(a,"parse-error","restricted-xml","Restricted XML, see RFC 6120 section 11.1.");
if not e.stop or not e:stop()then
f("Failed to abort parsing");
end
end
if b then
i.StartDoctypeDecl=t;
end
i.Comment=t;
i.ProcessingInstruction=t;
local function t()
e,o=nil,{};
r={};
end
local function e(t,e)
a=e;
end
return i,{reset=t,set_session=e};
end
function new(e,t)
local a,o=new_sax_handlers(e,t);
local e=m(a,n);
local t=e.parse;
return{
reset=function()
e=m(a,n);
t=e.parse;
o.reset();
end,
feed=function(o,a)
return t(e,a);
end,
set_session=o.set_session;
};
end
return _M;
end)
package.preload['util.jid']=(function(...)
local a=string.match;
local s=require"util.encodings".stringprep.nodeprep;
local r=require"util.encodings".stringprep.nameprep;
local h=require"util.encodings".stringprep.resourceprep;
local o={
[" "]="\\20";['"']="\\22";
["&"]="\\26";["'"]="\\27";
["/"]="\\2f";[":"]="\\3a";
["<"]="\\3c";[">"]="\\3e";
["@"]="\\40";["\\"]="\\5c";
};
local i={};
for t,e in pairs(o)do i[e]=t;end
module"jid"
local function t(e)
if not e then return;end
local i,t=a(e,"^([^@/]+)@()");
local t,o=a(e,"^([^@/]+)()",t)
if i and not t then return nil,nil,nil;end
local a=a(e,"^/(.+)$",o);
if(not t)or((not a)and#e>=o)then return nil,nil,nil;end
return i,t,a;
end
split=t;
function bare(e)
local t,e=t(e);
if t and e then
return t.."@"..e;
end
return e;
end
local function n(e)
local a,e,t=t(e);
if e then
e=r(e);
if not e then return;end
if a then
a=s(a);
if not a then return;end
end
if t then
t=h(t);
if not t then return;end
end
return a,e,t;
end
end
prepped_split=n;
function prep(e)
local a,e,t=n(e);
if e then
if a then
e=a.."@"..e;
end
if t then
e=e.."/"..t;
end
end
return e;
end
function join(t,e,a)
if t and e and a then
return t.."@"..e.."/"..a;
elseif t and e then
return t.."@"..e;
elseif e and a then
return e.."/"..a;
elseif e then
return e;
end
return nil;
end
function compare(a,e)
local n,o,i=t(a);
local e,t,a=t(e);
if((e~=nil and e==n)or e==nil)and
((t~=nil and t==o)or t==nil)and
((a~=nil and a==i)or a==nil)then
return true
end
return false
end
function escape(e)return e and(e:gsub(".",o));end
function unescape(e)return e and(e:gsub("\\%x%x",i));end
return _M;
end)
package.preload['util.events']=(function(...)
local i=pairs;
local h=table.insert;
local r=table.sort;
local s=setmetatable;
local n=next;
module"events"
function new()
local t={};
local e={};
local function o(o,a)
local e=e[a];
if not e or n(e)==nil then return;end
local t={};
for e in i(e)do
h(t,e);
end
r(t,function(t,a)return e[t]>e[a];end);
o[a]=t;
return t;
end;
s(t,{__index=o});
local function h(o,n,i)
local a=e[o];
if a then
a[n]=i or 0;
else
a={[n]=i or 0};
e[o]=a;
end
t[o]=nil;
end;
local function s(a,i)
local o=e[a];
if o then
o[i]=nil;
t[a]=nil;
if n(o)==nil then
e[a]=nil;
end
end
end;
local function a(e)
for t,e in i(e)do
h(t,e);
end
end;
local function n(e)
for e,t in i(e)do
s(e,t);
end
end;
local function o(e,...)
local e=t[e];
if e then
for t=1,#e do
local e=e[t](...);
if e~=nil then return e;end
end
end
end;
return{
add_handler=h;
remove_handler=s;
add_handlers=a;
remove_handlers=n;
fire_event=o;
_handlers=t;
_event_map=e;
};
end
return _M;
end)
package.preload['util.caps']=(function(...)
local l=require"util.encodings".base64.encode;
local d=require"util.hashes".sha1;
local n,h,s=table.insert,table.sort,table.concat;
local r=ipairs;
module"caps"
function calculate_hash(e)
local a,o,i={},{},{};
for t,e in r(e)do
if e.name=="identity"then
n(a,(e.attr.category or"").."\0"..(e.attr.type or"").."\0"..(e.attr["xml:lang"]or"").."\0"..(e.attr.name or""));
elseif e.name=="feature"then
n(o,e.attr.var or"");
elseif e.name=="x"and e.attr.xmlns=="jabber:x:data"then
local t={};
local o;
for a,e in r(e.tags)do
if e.name=="field"and e.attr.var then
local a={};
for t,e in r(e.tags)do
e=#e.tags==0 and e:get_text();
if e then n(a,e);end
end
h(a);
if e.attr.var=="FORM_TYPE"then
o=a[1];
elseif#a>0 then
n(t,e.attr.var.."\0"..s(a,"<"));
else
n(t,e.attr.var);
end
end
end
h(t);
t=s(t,"<");
if o then t=o.."\0"..t;end
n(i,t);
end
end
h(a);
h(o);
h(i);
if#a>0 then a=s(a,"<"):gsub("%z","/").."<";else a="";end
if#o>0 then o=s(o,"<").."<";else o="";end
if#i>0 then i=s(i,"<"):gsub("%z","<").."<";else i="";end
local e=a..o..i;
local t=l(d(e));
return t,e;
end
return _M;
end)
package.preload['verse.plugins.tls']=(function(...)
local t="urn:ietf:params:xml:ns:xmpp-tls";
function verse.plugins.tls(e)
local function i(a)
if e.authenticated then return;end
if a:get_child("starttls",t)and e.conn.starttls then
e:debug("Negotiating TLS...");
e:send(verse.stanza("starttls",{xmlns=t}));
return true;
elseif not e.conn.starttls and not e.secure then
e:warn("SSL libary (LuaSec) not loaded, so TLS not available");
elseif not e.secure then
e:debug("Server doesn't offer TLS :(");
end
end
local function a(t)
if t.name=="proceed"then
e:debug("Server says proceed, handshake starting...");
e.conn:starttls({mode="client",protocol="sslv23",options="no_sslv2"},true);
end
end
local function o(t)
if t=="ssl-handshake-complete"then
e.secure=true;
e:debug("Re-opening stream...");
e:reopen();
end
end
e:hook("stream-features",i,400);
e:hook("stream/"..t,a);
e:hook("status",o,400);
return true;
end
end)
package.preload['verse.plugins.sasl']=(function(...)
local t=require"mime".b64;
local a="urn:ietf:params:xml:ns:xmpp-sasl";
function verse.plugins.sasl(e)
local function i(o)
if e.authenticated then return;end
e:debug("Authenticating with SASL...");
local o=t("\0"..e.username.."\0"..e.password);
e:debug("Selecting PLAIN mechanism...");
local t=verse.stanza("auth",{xmlns=a,mechanism="PLAIN"});
if o then
t:text(o);
end
e:send(t);
return true;
end
local function o(t)
if t.name=="success"then
e.authenticated=true;
e:event("authentication-success");
elseif t.name=="failure"then
local t=t.tags[1];
e:event("authentication-failure",{condition=t.name});
end
e:reopen();
return true;
end
e:hook("stream-features",i,300);
e:hook("stream/"..a,o);
return true;
end
end)
package.preload['verse.plugins.bind']=(function(...)
local a="urn:ietf:params:xml:ns:xmpp-bind";
function verse.plugins.bind(e)
local function o(t)
if e.bound then return;end
e:debug("Binding resource...");
e:send_iq(verse.iq({type="set"}):tag("bind",{xmlns=a}):tag("resource"):text(e.resource),
function(t)
if t.attr.type=="result"then
local t=t
:get_child("bind",a)
:get_child("jid")
:get_text();
e.username,e.host,e.resource=jid.split(t);
e.jid,e.bound=t,true;
e:event("bind-success",{jid=t});
elseif t.attr.type=="error"then
local a=t:child_with_name("error");
local t,a,o=t:get_error();
e:event("bind-failure",{error=a,text=o,type=t});
end
end);
end
e:hook("stream-features",o,200);
return true;
end
end)
package.preload['verse.plugins.ping']=(function(...)
local o="urn:xmpp:ping";
function verse.plugins.ping(e)
function e:ping(t,a)
local n=socket.gettime();
e:send_iq(verse.iq{to=t,type="get"}:tag("ping",{xmlns=o}),
function(e)
if e.attr.type=="error"then
local o,e,i=e:get_error();
if e~="service-unavailable"and e~="feature-not-implemented"then
a(nil,t,{type=o,condition=e,text=i});
return;
end
end
a(socket.gettime()-n,t);
end);
end
return true;
end
end)
package.preload['verse.plugins.session']=(function(...)
local t="urn:ietf:params:xml:ns:xmpp-session";
function verse.plugins.session(e)
local function o(a)
local a=a:get_child("session",t);
if a and not a:get_child("optional")then
local function a(a)
e:debug("Establishing Session...");
e:send_iq(verse.iq({type="set"}):tag("session",{xmlns=t}),
function(t)
if t.attr.type=="result"then
e:event("session-success");
elseif t.attr.type=="error"then
local a=t:child_with_name("error");
local a,t,o=t:get_error();
e:event("session-failure",{error=t,text=o,type=a});
end
end);
return true;
end
e:hook("bind-success",a);
end
end
e:hook("stream-features",o);
return true;
end
end)
package.preload['verse.plugins.compression']=(function(...)
local o=require"zlib";
local e="http://jabber.org/features/compress"
local t="http://jabber.org/protocol/compress"
local e="http://etherx.jabber.org/streams";
local a=9;
local function i(e)
local o,a=pcall(o.deflate,a);
if o==false then
local t=verse.stanza("failure",{xmlns=t}):tag("setup-failed");
e:send(t);
e:error("Failed to create zlib.deflate filter: %s",tostring(a));
return
end
return a
end
local function r(e)
local o,a=pcall(o.inflate);
if o==false then
local t=verse.stanza("failure",{xmlns=t}):tag("setup-failed");
e:send(t);
e:error("Failed to create zlib.inflate filter: %s",tostring(a));
return
end
return a
end
local function h(e,a)
function e:send(o)
local o,a,i=pcall(a,tostring(o),'sync');
if o==false then
e:close({
condition="undefined-condition";
text=a;
extra=verse.stanza("failure",{xmlns=t}):tag("processing-failed");
});
e:warn("Compressed send failed: %s",tostring(a));
return;
end
e.conn:write(a);
end;
end
local function d(e,o)
local n=e.data
e.data=function(i,a)
e:debug("Decompressing data...");
local o,a,s=pcall(o,a);
if o==false then
e:close({
condition="undefined-condition";
text=a;
extra=verse.stanza("failure",{xmlns=t}):tag("processing-failed");
});
stream:warn("%s",tostring(a));
return;
end
return n(i,a);
end;
end
function verse.plugins.compression(e)
local function s(a)
if not e.compressed then
local a=a:child_with_name("compression");
if a then
for a in a:children()do
local a=a[1]
if a=="zlib"then
e:send(verse.stanza("compress",{xmlns=t}):tag("method"):text("zlib"))
e:debug("Enabled compression using zlib.")
return true;
end
end
session:debug("Remote server supports no compression algorithm we support.")
end
end
end
local function n(o)
if o.name=="compressed"then
e:debug("Activating compression...")
local a=i(e);
if not a then return end
local t=r(e);
if not t then return end
h(e,a);
d(e,t);
e.compressed=true;
e:reopen();
elseif o.name=="failure"then
e:warn("Failed to establish compression");
end
end
e:hook("stream-features",s,250);
e:hook("stream/"..t,n);
end
end)
package.preload['verse.plugins.presence']=(function(...)
function verse.plugins.presence(t)
t.last_presence=nil;
t:hook("presence-out",function(e)
if not e.attr.to then
t.last_presence=e;
end
end,1);
function t:resend_presence()
if last_presence then
t:send(last_presence);
end
end
function t:set_status(e)
local a=verse.presence();
if type(e)=="table"then
if e.show then
a:tag("show"):text(e.show):up();
end
if e.prio then
a:tag("priority"):text(tostring(e.prio)):up();
end
if e.msg then
a:tag("status"):text(e.msg):up();
end
end
t:send(a);
end
end
end)
package.preload['verse.plugins.disco']=(function(...)
local n=require("mime").b64
local s=require("util.sha1").sha1
local h="http://jabber.org/protocol/caps";
local e="http://jabber.org/protocol/disco";
local o=e.."#info";
local a=e.."#items";
function verse.plugins.disco(e)
e:add_plugin("presence");
e.disco={cache={},info={}}
e.disco.info.identities={
{category='client',type='pc',name='Verse'},
}
e.disco.info.features={
{var=h},
{var=o},
{var=a},
}
e.disco.items={}
e.disco.nodes={}
e.caps={}
e.caps.node='http://code.matthewwild.co.uk/verse/'
local function i(t,e)
if t.category<e.category then
return true;
elseif e.category<t.category then
return false;
end
if t.type<e.type then
return true;
elseif e.type<t.type then
return false;
end
if(not t['xml:lang']and e['xml:lang'])or
(e['xml:lang']and t['xml:lang']<e['xml:lang'])then
return true
end
return false
end
local function t(e,t)
return e.var<t.var
end
local function r()
table.sort(e.disco.info.identities,i)
table.sort(e.disco.info.features,t)
local t=''
for a,e in pairs(e.disco.info.identities)do
t=t..string.format(
'%s/%s/%s/%s',e.category,e.type,
e['xml:lang']or'',e.name or''
)..'<'
end
for a,e in pairs(e.disco.info.features)do
t=t..e.var..'<'
end
return(n(s(t)))
end
setmetatable(e.caps,{
__call=function(...)
local t=r()
return verse.stanza('c',{
xmlns=h,
hash='sha-1',
node=e.caps.node,
ver=t
})
end
})
function e:add_disco_feature(t)
table.insert(self.disco.info.features,{var=t});
e:resend_presence();
end
function e:remove_disco_feature(o)
for a,t in ipairs(self.disco.info.features)do
if t.var==o then
table.remove(self.disco.info.features,a);
e:resend_presence();
return true;
end
end
end
function e:add_disco_item(a,t)
local e=self.disco.items;
if t then
e=self.disco.nodes[t];
if not e then
e={features={},items={}};
self.disco.nodes[t]=e;
e=e.items;
else
e=e.items;
end
end
table.insert(e,a);
end
function e:jid_has_identity(e,a,t)
local o=self.disco.cache[e];
if not o then
return nil,"no-cache";
end
local e=self.disco.cache[e].identities;
if t then
return e[a.."/"..t]or false;
end
for e in pairs(e)do
if e:match("^(.*)/")==a then
return true;
end
end
end
function e:jid_supports(e,t)
local e=self.disco.cache[e];
if not e or not e.features then
return nil,"no-cache";
end
return e.features[t]or false;
end
function e:get_local_services(o,a)
local e=self.disco.cache[self.host];
if not(e)or not(e.items)then
return nil,"no-cache";
end
local t={};
for i,e in ipairs(e.items)do
if self:jid_has_identity(e.jid,o,a)then
table.insert(t,e.jid);
end
end
return t;
end
function e:disco_local_services(a)
self:disco_items(self.host,nil,function(t)
if not t then
return a({});
end
local e=0;
local function o()
e=e-1;
if e==0 then
return a(t);
end
end
for a,t in ipairs(t)do
if t.jid then
e=e+1;
self:disco_info(t.jid,nil,o);
end
end
if e==0 then
return a(t);
end
end);
end
function e:disco_info(e,t,s)
local a=verse.iq({to=e,type="get"})
:tag("query",{xmlns=o,node=t});
self:send_iq(a,function(a)
if a.attr.type=="error"then
return s(nil,a:get_error());
end
local n,i={},{};
for e in a:get_child("query",o):childtags()do
if e.name=="identity"then
n[e.attr.category.."/"..e.attr.type]=e.attr.name or true;
elseif e.name=="feature"then
i[e.attr.var]=true;
end
end
if not self.disco.cache[e]then
self.disco.cache[e]={nodes={}};
end
if t then
if not self.disco.cache[e].nodes[t]then
self.disco.cache[e].nodes[t]={nodes={}};
end
self.disco.cache[e].nodes[t].identities=n;
self.disco.cache[e].nodes[t].features=i;
else
self.disco.cache[e].identities=n;
self.disco.cache[e].features=i;
end
return s(self.disco.cache[e]);
end);
end
function e:disco_items(t,o,n)
local i=verse.iq({to=t,type="get"})
:tag("query",{xmlns=a,node=o});
self:send_iq(i,function(e)
if e.attr.type=="error"then
return n(nil,e:get_error());
end
local i={};
for e in e:get_child("query",a):childtags()do
if e.name=="item"then
table.insert(i,{
name=e.attr.name;
jid=e.attr.jid;
node=e.attr.node;
});
end
end
if not self.disco.cache[t]then
self.disco.cache[t]={nodes={}};
end
if o then
if not self.disco.cache[t].nodes[o]then
self.disco.cache[t].nodes[o]={nodes={}};
end
self.disco.cache[t].nodes[o].items=i;
else
self.disco.cache[t].items=i;
end
return n(i);
end);
end
e:hook("iq/"..o,function(t)
if t.attr.type=='get'then
local a=t:child_with_name('query')
if not a then return;end
local n
local s
if a.attr.node then
local h=r()
local i=e.disco.nodes[a.attr.node]
if i and i.info then
n=i.info.identities or{}
s=i.info.identities or{}
elseif a.attr.node==e.caps.node..'#'..h then
n=e.disco.info.identities
s=e.disco.info.features
else
local t=verse.stanza('iq',{
to=t.attr.from,
from=t.attr.to,
id=t.attr.id,
type='error'
})
t:tag('query',{xmlns=o}):reset()
t:tag('error',{type='cancel'}):tag(
'item-not-found',{xmlns='urn:ietf:params:xml:ns:xmpp-stanzas'}
)
e:send(t)
return true
end
else
n=e.disco.info.identities
s=e.disco.info.features
end
local a=verse.stanza('query',{
xmlns=o,
node=a.attr.node
})
for t,e in pairs(n)do
a:tag('identity',e):reset()
end
for o,t in pairs(s)do
a:tag('feature',t):reset()
end
e:send(verse.stanza('iq',{
to=t.attr.from,
from=t.attr.to,
id=t.attr.id,
type='result'
}):add_child(a))
return true
end
end);
e:hook("iq/"..a,function(t)
if t.attr.type=='get'then
local o=t:child_with_name('query')
if not o then return;end
local i
if o.attr.node then
local o=e.disco.nodes[o.attr.node]
if o then
i=o.items or{}
else
local t=verse.stanza('iq',{
to=t.attr.from,
from=t.attr.to,
id=t.attr.id,
type='error'
})
t:tag('query',{xmlns=a}):reset()
t:tag('error',{type='cancel'}):tag(
'item-not-found',{xmlns='urn:ietf:params:xml:ns:xmpp-stanzas'}
)
e:send(t)
return true
end
else
i=e.disco.items
end
local a=verse.stanza('query',{
xmlns=a,
node=o.attr.node
})
for o,t in pairs(i)do
a:tag('item',t):reset()
end
e:send(verse.stanza('iq',{
to=t.attr.from,
from=t.attr.to,
id=t.attr.id,
type='result'
}):add_child(a))
return true
end
end);
local t;
e:hook("ready",function()
if t then return;end
t=true;
e:disco_local_services(function(t)
for t,a in ipairs(t)do
local t=e.disco.cache[a.jid];
if t then
for t in pairs(t.identities)do
local t,o=t:match("^(.*)/(.*)$");
e:event("disco/service-discovered/"..t,{
type=o,jid=a.jid;
});
end
end
end
e:event("ready");
end);
return true;
end,50);
e:hook("presence-out",function(t)
if not t:get_child("c",h)then
t:reset():add_child(e:caps()):reset();
end
end,10);
end
end)
package.preload['verse.plugins.uptime']=(function(...)
local t="jabber:iq:last";
local function a(t,e)
t.starttime=e.starttime;
end
function verse.plugins.uptime(e)
e.uptime={set=a};
e:hook("iq/"..t,function(a)
if a.attr.type~="get"then return;end
local t=verse.reply(a)
:tag("query",{seconds=tostring(os.difftime(os.time(),e.uptime.starttime)),xmlns=t});
e:send(t);
return true;
end);
function e:query_uptime(o,a)
a=a or function(t)return e:event("uptime/response",t);end
e:send_iq(verse.iq({type="get",to=o})
:tag("query",{xmlns=t}),
function(e)
local t=e:get_child("query",t);
if e.attr.type=="result"then
local e=t.attr.seconds;
a({
seconds=e or nil;
});
else
local e,t,o=e:get_error();
a({
error=true;
condition=t;
text=o;
type=e;
});
end
end);
end
return true;
end
end)
package.preload['verse.plugins.keepalive']=(function(...)
function verse.plugins.keepalive(e)
e.keepalive_timeout=e.keepalive_timeout or 300;
verse.add_task(e.keepalive_timeout,function()
e.conn:write(" ");
return e.keepalive_timeout;
end);
end
end)
package.preload['verse.plugins.roster']=(function(...)
local a="jabber:iq:roster";
local o="urn:xmpp:features:rosterver";
local d=require"util.jid".bare;
local i=table.insert;
function verse.plugins.roster(t)
local s=false;
local e={
items={};
ver="";
};
t.roster=e;
t:hook("stream-features",function(e)
if e:get_child("ver",o)then
s=true;
end
end);
local function n(e)
local t=verse.stanza("item",{xmlns=a});
for a,e in pairs(e)do
if a~="groups"then
t.attr[a]=e;
else
for a=1,#e do
t:tag("group"):text(e[a]):up();
end
end
end
return t;
end
local function r(t)
local e={};
local a={};
e.groups=a;
local o=t.attr.jid;
for t,a in pairs(t.attr)do
if t~="xmlns"then
e[t]=a
end
end
for e in t:childtags("group")do
i(a,e:get_text())
end
return e;
end
function e:load(t)
e.ver,e.items=t.ver,t.items;
end
function e:dump()
return{
ver=e.ver,
items=e.items,
};
end
function e:add_contact(s,i,o,e)
local o={jid=s,name=i,groups=o};
local a=verse.iq({type="set"})
:tag("query",{xmlns=a})
:add_child(n(o));
t:send_iq(a,function(t)
if not e then return end
if t.attr.type=="result"then
e(true);
else
type,condition,text=t:get_error();
e(nil,{type,condition,text});
end
end);
end
function e:delete_contact(o,i)
o=(type(o)=="table"and o.jid)or o;
local s={jid=o,subscription="remove"}
if not e.items[o]then return false,"item-not-found";end
t:send_iq(verse.iq({type="set"})
:tag("query",{xmlns=a})
:add_child(n(s)),
function(e)
if not i then return end
if e.attr.type=="result"then
i(true);
else
type,condition,text=e:get_error();
i(nil,{type,condition,text});
end
end);
end
local function h(t)
local t=r(t);
e.items[t.jid]=t;
end
local function r(t)
local a=e.items[t];
e.items[t]=nil;
return a;
end
function e:fetch(o)
t:send_iq(verse.iq({type="get"}):tag("query",{xmlns=a,ver=s and e.ver or nil}),
function(t)
if t.attr.type=="result"then
local t=t:get_child("query",a);
if t then
e.items={};
for t in t:childtags("item")do
h(t)
end
e.ver=t.attr.ver or"";
end
o(e);
else
type,condition,text=stanza:get_error();
o(nil,{type,condition,text});
end
end);
end
t:hook("iq/"..a,function(o)
local n,i=o.attr.type,o.attr.from;
if n=="set"and(not i or i==d(t.jid))then
local s=o:get_child("query",a);
local a=s and s:get_child("item");
if a then
local n,o;
local i=a.attr.jid;
if a.attr.subscription=="remove"then
n="removed"
o=r(i);
else
n=e.items[i]and"changed"or"added";
h(a)
o=e.items[i];
end
e.ver=s.attr.ver;
if o then
t:event("roster/item-"..n,o);
end
end
t:send(verse.reply(o))
return true;
end
end);
end
end)
package.preload['net.connlisteners']=(function(...)
local h=(CFG_SOURCEDIR or".").."/net/";
local u=require"net.server";
local o=require"util.logger".init("connlisteners");
local s=tostring;
local c=type
local l=ipairs
local r,d,n=
dofile,xpcall,error
local i=debug.traceback;
module"connlisteners"
local e={};
function register(t,a)
if e[t]and e[t]~=a then
o("debug","Listener %s is already registered, not registering any more",t);
return false;
end
e[t]=a;
o("debug","Registered connection listener %s",t);
return true;
end
function deregister(t)
e[t]=nil;
end
function get(t)
local a=e[t];
if not a then
local n,i=d(function()r(h..t:gsub("[^%w%-]","_").."_listener.lua")end,i);
if not n then
o("error","Error while loading listener '%s': %s",s(t),s(i));
return nil,i;
end
a=e[t];
end
return a;
end
function start(i,e)
local a,t=get(i);
if not a then
n("No such connection module: "..i..(t and(" ("..t..")")or""),0);
end
local o=(e and e.interface)or a.default_interface or"*";
if c(o)=="string"then o={o};end
local h=(e and e.port)or a.default_port or n("Can't start listener "..i.." because no port was specified, and it has no default port",0);
local s=(e and e.mode)or a.default_mode or 1;
local n=(e and e.ssl)or nil;
local i=e and e.type=="ssl";
if i and not n then
return nil,"no ssl context";
end
ok,t=true,{};
for e,o in l(o)do
local e
e,t[o]=u.addserver(o,h,a,s,i and n or nil);
ok=ok and e;
end
return ok,t;
end
return _M;
end)
package.preload['verse.client']=(function(...)
local t=require"verse";
local o=t.stream_mt;
local r=require"util.jid".split;
local d=require"net.adns";
local e=require"lxp";
local a=require"util.stanza";
t.message,t.presence,t.iq,t.stanza,t.reply,t.error_reply=
a.message,a.presence,a.iq,a.stanza,a.reply,a.error_reply;
local h=require"util.xmppstream".new;
local n="http://etherx.jabber.org/streams";
local function s(t,e)
return t.priority<e.priority or(t.priority==e.priority and t.weight>e.weight);
end
local i={
stream_ns=n,
stream_tag="stream",
default_ns="jabber:client"};
function i.streamopened(e,t)
e.stream_id=t.id;
if not e:event("opened",t)then
e.notopen=nil;
end
return true;
end
function i.streamclosed(e)
return e:event("closed");
end
function i.handlestanza(t,e)
if e.attr.xmlns==n then
return t:event("stream-"..e.name,e);
elseif e.attr.xmlns then
return t:event("stream/"..e.attr.xmlns,e);
end
return t:event("stanza",e);
end
function o:reset()
if self.stream then
self.stream:reset();
else
self.stream=h(self,i);
end
self.notopen=true;
return true;
end
function o:connect_client(e,a)
self.jid,self.password=e,a;
self.username,self.host,self.resource=r(e);
self:add_plugin("tls");
self:add_plugin("sasl");
self:add_plugin("bind");
self:add_plugin("session");
function self.data(t,e)
local t,a=self.stream:feed(e);
if t then return;end
self:debug("debug","Received invalid XML (%s) %d bytes: %s",tostring(a),#e,e:sub(1,300):gsub("[\r\n]+"," "));
self:close("xml-not-well-formed");
end
self:hook("connected",function()self:reopen();end);
self:hook("incoming-raw",function(e)return self.data(self.conn,e);end);
self.curr_id=0;
self.tracked_iqs={};
self:hook("stanza",function(t)
local e,a=t.attr.id,t.attr.type;
if e and t.name=="iq"and(a=="result"or a=="error")and self.tracked_iqs[e]then
self.tracked_iqs[e](t);
self.tracked_iqs[e]=nil;
return true;
end
end);
self:hook("stanza",function(e)
if e.attr.xmlns==nil or e.attr.xmlns=="jabber:client"then
if e.name=="iq"and(e.attr.type=="get"or e.attr.type=="set")then
local a=e.tags[1]and e.tags[1].attr.xmlns;
if a then
ret=self:event("iq/"..a,e);
if not ret then
ret=self:event("iq",e);
end
end
if ret==nil then
self:send(t.error_reply(e,"cancel","service-unavailable"));
return true;
end
else
ret=self:event(e.name,e);
end
end
return ret;
end,-1);
self:hook("outgoing",function(e)
if e.name then
self:event("stanza-out",e);
end
end);
self:hook("stanza-out",function(e)
if not e.attr.xmlns then
self:event(e.name.."-out",e);
end
end);
local function e()
self:event("ready");
end
self:hook("session-success",e,-1)
self:hook("bind-success",e,-1);
local e=self.close;
function self:close(t)
if not self.notopen then
self:send("</stream:stream>");
end
return e(self);
end
local function a()
self:connect(self.connect_host or self.host,self.connect_port or 5222);
end
if not(self.connect_host or self.connect_port)then
d.lookup(function(t)
if t then
local e={};
self.srv_hosts=e;
for a,t in ipairs(t)do
table.insert(e,t.srv);
end
table.sort(e,s);
local t=e[1];
self.srv_choice=1;
if t then
self.connect_host,self.connect_port=t.target,t.port;
self:debug("Best record found, will connect to %s:%d",self.connect_host or self.host,self.connect_port or 5222);
end
self:hook("disconnected",function()
if self.srv_hosts and self.srv_choice<#self.srv_hosts then
self.srv_choice=self.srv_choice+1;
local e=e[self.srv_choice];
self.connect_host,self.connect_port=e.target,e.port;
a();
return true;
end
end,1e3);
self:hook("connected",function()
self.srv_hosts=nil;
end,1e3);
end
a();
end,"_xmpp-client._tcp."..(self.host)..".","SRV");
else
a();
end
end
function o:reopen()
self:reset();
self:send(a.stanza("stream:stream",{to=self.host,["xmlns:stream"]='http://etherx.jabber.org/streams',
xmlns="jabber:client",version="1.0"}):top_tag());
end
function o:send_iq(t,a)
local e=self:new_id();
self.tracked_iqs[e]=a;
t.attr.id=e;
self:send(t);
end
function o:new_id()
self.curr_id=self.curr_id+1;
return tostring(self.curr_id);
end
end)
package.preload['verse.component']=(function(...)
local t=require"verse";
local o=t.stream_mt;
local h=require"util.jid".split;
local e=require"lxp";
local a=require"util.stanza";
local r=require"util.sha1".sha1;
t.message,t.presence,t.iq,t.stanza,t.reply,t.error_reply=
a.message,a.presence,a.iq,a.stanza,a.reply,a.error_reply;
local d=require"util.xmppstream".new;
local s="http://etherx.jabber.org/streams";
local i="jabber:component:accept";
local n={
stream_ns=s,
stream_tag="stream",
default_ns=i};
function n.streamopened(e,t)
e.stream_id=t.id;
if not e:event("opened",t)then
e.notopen=nil;
end
return true;
end
function n.streamclosed(e)
return e:event("closed");
end
function n.handlestanza(t,e)
if e.attr.xmlns==s then
return t:event("stream-"..e.name,e);
elseif e.attr.xmlns or e.name=="handshake"then
return t:event("stream/"..(e.attr.xmlns or i),e);
end
return t:event("stanza",e);
end
function o:reset()
if self.stream then
self.stream:reset();
else
self.stream=d(self,n);
end
self.notopen=true;
return true;
end
function o:connect_component(e,n)
self.jid,self.password=e,n;
self.username,self.host,self.resource=h(e);
function self.data(t,e)
local a,t=self.stream:feed(e);
if a then return;end
o:debug("debug","Received invalid XML (%s) %d bytes: %s",tostring(t),#e,e:sub(1,300):gsub("[\r\n]+"," "));
o:close("xml-not-well-formed");
end
self:hook("incoming-raw",function(e)return self.data(self.conn,e);end);
self.curr_id=0;
self.tracked_iqs={};
self:hook("stanza",function(t)
local e,a=t.attr.id,t.attr.type;
if e and t.name=="iq"and(a=="result"or a=="error")and self.tracked_iqs[e]then
self.tracked_iqs[e](t);
self.tracked_iqs[e]=nil;
return true;
end
end);
self:hook("stanza",function(e)
if e.attr.xmlns==nil or e.attr.xmlns=="jabber:client"then
if e.name=="iq"and(e.attr.type=="get"or e.attr.type=="set")then
local a=e.tags[1]and e.tags[1].attr.xmlns;
if a then
ret=self:event("iq/"..a,e);
if not ret then
ret=self:event("iq",e);
end
end
if ret==nil then
self:send(t.error_reply(e,"cancel","service-unavailable"));
return true;
end
else
ret=self:event(e.name,e);
end
end
return ret;
end,-1);
self:hook("opened",function(e)
print(self.jid,self.stream_id,e.id);
local e=r(self.stream_id..n,true);
self:send(a.stanza("handshake",{xmlns=i}):text(e));
self:hook("stream/"..i,function(e)
if e.name=="handshake"then
self:event("authentication-success");
end
end);
end);
local function e()
self:event("ready");
end
self:hook("authentication-success",e,-1);
self:connect(self.connect_host or self.host,self.connect_port or 5347);
self:reopen();
end
function o:reopen()
self:reset();
self:send(a.stanza("stream:stream",{to=self.host,["xmlns:stream"]='http://etherx.jabber.org/streams',
xmlns=i,version="1.0"}):top_tag());
end
function o:close(e)
if not self.notopen then
self:send("</stream:stream>");
end
local t=self.conn.disconnect();
self.conn:close();
t(conn,e);
end
function o:send_iq(t,a)
local e=self:new_id();
self.tracked_iqs[e]=a;
t.attr.id=e;
self:send(t);
end
function o:new_id()
self.curr_id=self.curr_id+1;
return tostring(self.curr_id);
end
end)
pcall(require,"luarocks.require");
pcall(require,"ssl");
local a=require"net.server";
local n=require"util.events";
module("verse",package.seeall);
local e=_M;
_M.server=a;
local t={};
t.__index=t;
stream_mt=t;
e.plugins={};
function e.new(a,o)
local t=setmetatable(o or{},t);
t.id=tostring(t):match("%x*$");
t:set_logger(a,true);
t.events=n.new();
t.plugins={};
return t;
end
e.add_task=require"util.timer".add_task;
e.logger=logger.init;
e.log=e.logger("verse");
function e.set_logger(t)
e.log=t;
a.setlogger(t);
end
function e.filter_log(t,o)
local e={};
for a,t in ipairs(t)do
e[t]=true;
end
return function(t,a,...)
if e[t]then
return o(t,a,...);
end
end;
end
local function o(t)
e.log("error","Error: %s",t);
e.log("error","Traceback: %s",debug.traceback());
end
function e.set_error_handler(e)
o=e;
end
function e.loop()
return xpcall(a.loop,o);
end
function e.step()
return xpcall(a.step,o);
end
function e.quit()
return a.setquitting(true);
end
function t:connect(o,t)
o=o or"localhost";
t=tonumber(t)or 5222;
local i=socket.tcp()
i:settimeout(0);
local n,e=i:connect(o,t);
if not n and e~="timeout"then
self:warn("connect() to %s:%d failed: %s",o,t,e);
return self:event("disconnected",{reason=e})or false,e;
end
local t=a.wrapclient(i,o,t,new_listener(self),"*a");
if not t then
self:warn("connection initialisation failed: %s",e);
return self:event("disconnected",{reason=e})or false,e;
end
self.conn=t;
self.send=function(a,e)
self:event("outgoing",e);
e=tostring(e);
self:event("outgoing-raw",e);
return t:write(e);
end;
return true;
end
function t:close()
if not self.conn then
e.log("error","Attempt to close disconnected connection - possibly a bug");
return;
end
local e=self.conn.disconnect();
self.conn:close();
e(conn,reason);
end
function t:debug(...)
if self.logger and self.log.debug then
return self.logger("debug",...);
end
end
function t:warn(...)
if self.logger and self.log.warn then
return self.logger("warn",...);
end
end
function t:error(...)
if self.logger and self.log.error then
return self.logger("error",...);
end
end
function t:set_logger(t,e)
local a=self.logger;
if t then
self.logger=t;
end
if e then
if e==true then
e={"debug","info","warn","error"};
end
self.log={};
for t,e in ipairs(e)do
self.log[e]=true;
end
end
return a;
end
function stream_mt:set_log_levels(e)
self:set_logger(nil,e);
end
function t:event(e,...)
self:debug("Firing event: "..tostring(e));
return self.events.fire_event(e,...);
end
function t:hook(e,...)
return self.events.add_handler(e,...);
end
function t:unhook(e,t)
return self.events.remove_handler(e,t);
end
function e.eventable(e)
e.events=n.new();
e.hook,e.unhook=t.hook,t.unhook;
local t=e.events.fire_event;
function e:event(e,...)
return t(e,...);
end
return e;
end
function t:add_plugin(t)
if self.plugins[t]then return true;end
if require("verse.plugins."..t)then
local a,e=e.plugins[t](self);
if a~=false then
self:debug("Loaded %s plugin",t);
self.plugins[t]=true;
else
self:warn("Failed to load %s plugin: %s",t,e);
end
end
return self;
end
function new_listener(e)
local t={};
function t.onconnect(t)
e.connected=true;
e:event("connected");
end
function t.onincoming(a,t)
e:event("incoming-raw",t);
end
function t.ondisconnect(a,t)
e.connected=false;
e:event("disconnected",{reason=t});
end
function t.ondrain(t)
e:event("drained");
end
function t.onstatus(a,t)
e:event("status",t);
end
return t;
end
local t=require"util.logger".init("verse");
return e;
