#!/usr/bin/env lua
local base_char,keywords=128,{"and","break","do","else","elseif","end","false","for","function","if","in","local","nil","not","or","repeat","return","then","true","until","while","require","\"sysevent set \"","string","attr","\"Network ID is no longer valid, clearing it up...\"","\"-status stopped\"","verse","respond","package","execute","preload","priority","text","\"unknown error\"","IQ_NS","condition","\"Authentication failure (\"","\"error\"","tostring","hook","'string'","type","write","close","modules","Name","getboolean","keep_alive","os","validated","pcall","raclient_connect","math","stderr","getNextInterval","get","\"Closing now...\"","connect_client","\"processmodules\"","\"result\"","request","debug","print","error","time","exit","pairs","table","connect_port","\"bind-failure\"","connect_host","\"outgoing-raw\"","\"incoming-raw\"","\"libsysctxlua\"","getIteration","\"disconnected\"","length","arg","logger","isValidJID","reset","tonumber","\"\"","add_task","IQ","conn","tag","\"session\"","sub","io","source","method","reason","ipairs","random","weight","remove",}; function prettify(code) return code:gsub("["..string.char(base_char).."-"..string.char(base_char+#keywords).."]", 
	function (c) return keywords[c:byte()-base_char]; end) end return assert(loadstring(prettify[===[û.†['retry']=(â(...)ë â()å n={{5,.08},{30,.05,.25},{20,5},{40,10},{40,30},{120,60},{1e3,720}}å e,t,o
å r={}å â a(r)ä(r)í
o=r
å o=0
à l=1,#n É
o=o+n[l][1]ä(r<=o)í
t=n[l][1]+r-o
e=l
Ç
Ü
Ü
ä(é t)í a()Ü
Ñ
e=1
t=0
o=0
Ü
Ü
å â i(n,e)ë ∂.floor(n*∂.pow(10,e)+.5)/∂.pow(10,e)Ü
å â c()ä(é e)í
a()Ü
t=t+1
o=o+1
ä(e<=#n Å n[e][1]Å t>n[e][1])í
e=e+1
t=1
Ü
å r,l
ä(e<=#n)í
å t=n[e][2]ä(n[e][3]Å(n[e][2]<=n[e][3]))í
t=i(n[e][2]+(n[e][3]-n[e][2])*∂.‡(),3)Ü
r,l=t,"retry called for "..o.." times. Will retry in "..t.." min"Ñ
r,l=ç,"reached last line, returning nil"Ü
ë r,l
Ü
å â e()ë o
Ü
r.∏,r.“,r.Ã=c,a,e
ë r
Ü
Ü)û.†['cache']=(â(...)ë â()å l=300
å e={}å n={}å â a(e)n[e]=ç
Ü
å â i(t)å o
ä(n[t])í
ä(n[t]>≤.¬())í
o=n[t]Ñ
e.‚(t)Ü
Ü
ë o
Ü
å â r(o,t)t=≤.¬()+(t è l)n[o]=t
Ü
e.‚=a
e.π=i
e.add=r
ë e
Ü
Ü)û.†['modulehandler']=(â(...)å â l(e)ë ´(e)=='table'Å
e.Ø Å ´(e.Ø)==™Å
e.NS Å ´(e.NS)==™Å
e.÷ Å ´(e.÷)==™Å
e.§ Å ´(e.§)==™Å
e.ù Å ´(e.ù)=='function'Å
ì
Ü
å â o(e)à e,n ã ƒ(e)É ¿(e);à e,n ã ƒ(n)É ¿("\t",e,n)Ü Ü
Ü
å â r()å t,n={},0
à e ã ƒ(û.†)É
ä e:match("^xrac%.")í
å o,e=¥(ñ,e);ä(o)í
å o=l(e)ä o í
t[e.NS]={Ø=e.Ø,÷=e.÷,§=e.§,ù=e.ù}n=n+1
Ñ
Ü
Ñ
Ü
Ü
Ü
ë t,n
Ü
å n,e=r()ë{Æ=n,¿=o,Œ=e}Ü)û.†['xrac.jnap']=(â(...)ë{Ø="JNAP Handler",NS="http://cisco.com/remoteaccess/",÷="jnap",§="http://cisco.com/jnap/",ù=â(e)å l=ñ("jnap")å n
å e,r,o,t=e.ô["action"],e.ô[Ÿ],e.ô["request"],e.ô["role"]è"BASIC"ä(e)í
n=l.æ(e,o,r,t,ì)Ü
ë n,'200',ç
Ü}Ü)û.†['xrac.http']=(â(...)å n="http://cisco.com/http/"å e="http://cisco.com/httphandler/"ë{Ø="Generic Http Handler",NS=e,÷="http",§=n,ù=â(e)å o,r="\r\n",":"å l=ñ("socket.http")å n,t,i,a,c,d=e.ô["method"]è"GET",e.ô["url"],e.ô["body"]è‘,e.ô["header"]è‘,e.ô["server"]è‘,e.ô["port"]è‘å â s(e,n,o)å t,n={},"([^"..(n è"%s").."]+)"à e ã ò.gmatch(e,n)É
å n,o=ò.find(e,o,1,ì)ä(n)í
t[ò.⁄(e,1,n-1)]=ò.⁄(e,o+1)Ü
Ü
ë t
Ü
å s=s(a,o,r)å e,r,o
ä(n Å t)í
å a=ò.format("http://%s:%s/%s",c,d,t)å t={}å l,n,a,a=l.æ{url=a,sink=ltn12.sink.≈(t),›=n,headers=s,‹=ltn12.‹.ò(i)}ä l==ç í o=n Ñ r=®(n)Ü
e=≈.concat(t)Ü
ë e,r,o
Ü}Ü)å t="xrac"å Q='{"result": "_ErrorUnauthorized", "error": "Invalid Session Token"}'å y="/router"å e="/var/run/"..t..".pid"å b="/var/log/xrac"å S=60
å w,r,i,u,m,a,d,f,g,_,h,v
ñ"verse"ñ"verse.client"å N=ñ("socket")å p=ñ("retry")()å k=ñ("cache")()å s=ñ("modulehandler")å c={}å â x(e,...)å e=≤.date().." "..t.." "à t,n ã ﬂ(œ)É
e=e..®(n).." "Ü
e=e.."\n"ë e
Ü
å â l(n)å e=assert(€.open(d,"a"))ä(e)í
e:¨(n)€.≠(e)Ñ
€.∑:¨("Could not open log file("..d.."):"..n)Ü
Ü
å â o(...)å e=x(ß,...)€.∑:¨(e)l(e)Ü
å â n(...)ä a í
å e=x("debug",...)¿(e)l(e)Ü
Ü
å â I(c,a)å â i(n,e)ë e==''è(ò.⁄(n,-ò.len(e))==e Å ò.⁄(n,1,ò.len(n)-ò.len(e)))Ü
å â d(n,e)ë n.°<e.° è(n.°==e.° Å n.·>e.·)Ü
å e=ñ"net.adns"å t,o
å r=á
e.lookup(â(l)n("adns answer is :\n",l)ä l í
å e={}à t,n ã ﬂ(l)É
≈.insert(e,n.srv)Ü
≈.sort(e,d)å l=1
ä(#e>1)í
à t=1,#e É
ä(e[t].°~=e[1].°)í
∂.randomseed(≤.¬())l=∂.‡(t-1)n("First records that have same priority:",t-1,"bestrecord:",l)Ç
Ü
Ü
Ü
å e=e[l]t,o=i(e.target,"."),e.port
r=t Å o
n("Best record found, will connect to :",t,o)Ü
ä(a)í
n("calling callback with ",r,t,o)a(r,t,o)Ü
Ü,"_xmpp-client._tcp."..(c)..".","SRV")Ü
å â x()à e=1,#œ É
ä œ[e]=="--retry"í
å e=”(œ[e+1])n("Retry iteration is ",e)p.“(e)Ö œ[e]=="--debug"í
a=ì
Ü
Ü
Ü
å â q()å e=ñ(À)å e=e.new()e:readlock()d=e:π("xmpp_log_file",b)w=e:∞("xmpp_enabled",ì)v=e:∞("xmpp_use_dns",ì)r=e:π("owned_network_id")i=e:π("owned_network_password")u=e:π("xmpp_host")m=e:π("xmpp_port")a=e:∞("xmpp_logging")g=”(e:π("xmpp_keep_alive",S))å n=ñ("device")f=n.getCloudHost(e)h=n.getVerifyCloudHost(e)e:rollback()Ü
å â l(o)å e,r=p.∏()n("try_reconnect called (from "..o.."):"..r)ä(e Å e>0)í
≤.ü(ó..t.."-status sleeping")N.sleep(e*60)≤.ü(ó..t.."-restart "..(p.Ã()è‘))≤.√(1)Ü
≤.ü(ó..t..õ)≤.√(1)Ü
å â N(...)o(...)l("handle_error")Ü
x()å â x()å e,a=¥(q)ä é e í
d=d è b
o("could not read syscfg, exiting...",a)l("init-readsyscfg")ë e
Ü
ä é w í
o("XRAC is not enabled, exiting...")≤.ü(ó..t..õ)≤.√(1)Ü
ä é r è é i í
o("Device is not associated, exiting...")≤.ü(ó..t..õ)≤.√(1)Ü
n("XRAC:modules.length=",s.Œ)ä é s.Æ è(s.Œ==0)í
o("Could not find any valid module, exiting...")≤.ü(ó..t..õ)≤.√(1)Ü
ë e
Ü
å â d(n)å o=ñ("cloud")å e,t
ä(n Å k.π(n))í
e,t=ì,"session exists in cache"Ñ
e,t=o.isValidSession(f,r,n,h)ä(e)í
k.add(n)Ü
Ü
ë e,t
Ü
â —(t,n,o,l)å r='/etc/certs/root'å e=ñ("cloud")å n,t,e=e.callCloud({host=t,path='/device-service/rest/networkcredentialvalidator',›='POST',æ={networkCredentialValidator={network={networkId=n,password=o}}},verifyPath=l Å r è ç})å n=á
ä t==404 Å e~=ç í
å t=ñ('libhdkjsonlua')¥(â()e=t.parse(e)n=e.errors[1].¡.code=="NETWORK_NOT_FOUND"Ü)Ü
ë é n,t
Ü
û.†[º]=(â(...)ë â(e)å â _(i)n("connected using(jid, host, port, endpoint, verifyEndpoint, keep_alive_timeout): ",r,u,m è"default",f,®(h),g)≤.ü(ó..t.."-status started")p.“()å â u(e)n("on_iq(stanza) RECEIVED: ",e)ä e.name=="iq"Å e.ô.´=="set"í
å t=e.ô["xmlns"]n("IQ of interest received: ",t)å r=s.Æ[t]ä(r)í
n("Found the module to handle:"..r.Ø)å t=e:get_child(r.÷,r.§)ä(t)í
å c,a,o=t.ô[Ÿ],ç,ç
ä(c)í
å l
≥,l=d(c)n("Validating sessionToken. Result:",≥,l)ä(≥)í
å e=≤.¬()l,a,o=r.ù(t)t:ÿ(Ω):¢(l)å e=≤.¬()Ñ
o,a=Q,"401"t:ÿ(Ω):¢(o)Ü
å e=ú.stanza("iq",{to=e.ô.from,from=e.ô.to,id=e.ô.id,´=Ω}):add_child(t)e:ÿ("status"):¢(a è‘):up():ÿ(ß):¢(o è‘)i:send(e)n("on_iq: response iq SENT: ",e)Ü
Ü
Ñ
o("Could not find any module for "..e.ô["xmlns"])Ü
Ü
Ü
à e,n ã ƒ(s.Æ)É
i:©("stream/"..e,u)Ü
Ü
ë µ(_)Ü
Ü)â µ(s)ú.set_logger(a Å ú.–()è ú.filter_log({ß},ú.–()))å e=ú.new(ú.–())å d=â()ä(e)í
e:ø(≤.date().." 'm alive")e.◊:¨(" ")ú.’(g,c.±)Ü
Ü
c.±=d
ú.set_error_handler(N)e.log.ø=a
e:©("authentication-failure",â(t)e:¡(¶..(t.• è£)..")"..(t.¢ Å(": "..t.¢)è‘))n(ß,¶..(t.• è£)..")"..(t.¢ Å(": "..t.¢)è‘))e:≠()ä(t Å t.•=="not-authorized")í
å t=—(f,r,i,h)ä é t í
e:¡(ö)n(ß,ö)å e=ñ(À)å e=e.new()e:writelock()e:set_xrac_owned_network_id(ç)e:set_xrac_owned_network_password(ç)e:setevent('xrac_provision_error','Network ID is no longer valid, so xrac cleared it out')e:commit()Ü
Ü
Ü)e:©("ready",â()e:send(ú.presence())ä(é _)í
ú.’(g,c.±)_=ì
Ü
ë s(e)Ü)e:©(«,â(n)e:¡("Resource binding failure ("..(n.• è£)..")"..(n.¢ Å(": "..n.¢)è‘))e:≠()l(«)Ü)e:©(Õ,â(n)ä n.ﬁ í
e:warn("Disconnecting: %s",®(n.ﬁ))Ü
l(Õ)Ü)ä(a)í
e:©( ,â(e)n( ,e)Ü)e:©(…,â(e)n(…,e)Ü)Ü
e.»,e.∆=u,m
ä(v)í
I(u,â(t,o,l)n("ok, best_server, best_host =",t,o,l)ä t í
e.»,e.∆=o,l
Ü
e:ª(r..y,i)Ü)Ñ
e:ª(r..y,i)Ü
å r=e.≠
â e:≠(o)n("conn:close called.force_close:",o)e:ø("Delaying close...")å â t()ä r í
n(∫)e:ø(∫)å n=r
r=ç
n(e)Ü
Ü
ä e.◊:bufferlen()==0 è o í
t()Ñ
e:©("drained",t)Ü
Ü
â e:kill()ë e:≠(ì)Ü
c.◊=e
å r,e=¥(ú.loop)ä é r í
o("Verse loop failed...")ä é e:match("interrupted!$")í
€.∑:¨("Fatal error: ",e,"\n")Ñ
€.∑:¨("Exiting "..t.."...","\n")Ü
l("verse.loop")Ü
ë o è 0
Ü
n("Starting XRAC using JID:",r)x()å n,e=¥(ñ,º)ä é n í
o("Could not load processmodules w/ error:",e)l("main")Ü
c.init=x
ë e(œ)è 0
]===], '@/hudson/build/shelbyv2/output/production/xmppraclient/build/xrac.lua'))()