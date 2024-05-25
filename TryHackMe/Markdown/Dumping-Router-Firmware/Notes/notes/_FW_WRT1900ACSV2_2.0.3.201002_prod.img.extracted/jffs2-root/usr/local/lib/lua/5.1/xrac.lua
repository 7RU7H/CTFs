#!/usr/bin/env lua
local base_char,keywords=128,{"and","break","do","else","elseif","end","false","for","function","if","in","local","nil","not","or","repeat","return","then","true","until","while","require","\"sysevent set \"","string","attr","\"Network ID is no longer valid, clearing it up...\"","\"-status stopped\"","verse","respond","package","execute","preload","priority","text","\"unknown error\"","IQ_NS","condition","\"Authentication failure (\"","\"error\"","tostring","hook","'string'","type","write","close","modules","Name","getboolean","keep_alive","os","validated","pcall","raclient_connect","math","stderr","getNextInterval","get","\"Closing now...\"","connect_client","\"processmodules\"","\"result\"","request","debug","print","error","time","exit","pairs","table","connect_port","\"bind-failure\"","connect_host","\"outgoing-raw\"","\"incoming-raw\"","\"libsysctxlua\"","getIteration","\"disconnected\"","length","arg","logger","isValidJID","reset","tonumber","\"\"","add_task","IQ","conn","tag","\"session\"","sub","io","source","method","reason","ipairs","random","weight","remove",}; function prettify(code) return code:gsub("["..string.char(base_char).."-"..string.char(base_char+#keywords).."]", 
	function (c) return keywords[c:byte()-base_char]; end) end return assert(loadstring(prettify[===[�.�['retry']=(�(...)� �()� n={{5,.08},{30,.05,.25},{20,5},{40,10},{40,30},{120,60},{1e3,720}}� e,t,o
� r={}� � a(r)�(r)�
o=r
� o=0
� l=1,#n �
o=o+n[l][1]�(r<=o)�
t=n[l][1]+r-o
e=l
�
�
�
�(� t)� a()�
�
e=1
t=0
o=0
�
�
� � i(n,e)� �.floor(n*�.pow(10,e)+.5)/�.pow(10,e)�
� � c()�(� e)�
a()�
t=t+1
o=o+1
�(e<=#n � n[e][1]� t>n[e][1])�
e=e+1
t=1
�
� r,l
�(e<=#n)�
� t=n[e][2]�(n[e][3]�(n[e][2]<=n[e][3]))�
t=i(n[e][2]+(n[e][3]-n[e][2])*�.�(),3)�
r,l=t,"retry called for "..o.." times. Will retry in "..t.." min"�
r,l=�,"reached last line, returning nil"�
� r,l
�
� � e()� o
�
r.�,r.�,r.�=c,a,e
� r
�
�)�.�['cache']=(�(...)� �()� l=300
� e={}� n={}� � a(e)n[e]=�
�
� � i(t)� o
�(n[t])�
�(n[t]>�.�())�
o=n[t]�
e.�(t)�
�
� o
�
� � r(o,t)t=�.�()+(t � l)n[o]=t
�
e.�=a
e.�=i
e.add=r
� e
�
�)�.�['modulehandler']=(�(...)� � l(e)� �(e)=='table'�
e.� � �(e.�)==��
e.NS � �(e.NS)==��
e.� � �(e.�)==��
e.� � �(e.�)==��
e.� � �(e.�)=='function'�
�
�
� � o(e)� e,n � �(e)� �(e);� e,n � �(n)� �("\t",e,n)� �
�
� � r()� t,n={},0
� e � �(�.�)�
� e:match("^xrac%.")�
� o,e=�(�,e);�(o)�
� o=l(e)� o �
t[e.NS]={�=e.�,�=e.�,�=e.�,�=e.�}n=n+1
�
�
�
�
�
�
� t,n
�
� n,e=r()�{�=n,�=o,�=e}�)�.�['xrac.jnap']=(�(...)�{�="JNAP Handler",NS="http://cisco.com/remoteaccess/",�="jnap",�="http://cisco.com/jnap/",�=�(e)� l=�("jnap")� n
� e,r,o,t=e.�["action"],e.�[�],e.�["request"],e.�["role"]�"BASIC"�(e)�
n=l.�(e,o,r,t,�)�
� n,'200',�
�}�)�.�['xrac.http']=(�(...)� n="http://cisco.com/http/"� e="http://cisco.com/httphandler/"�{�="Generic Http Handler",NS=e,�="http",�=n,�=�(e)� o,r="\r\n",":"� l=�("socket.http")� n,t,i,a,c,d=e.�["method"]�"GET",e.�["url"],e.�["body"]��,e.�["header"]��,e.�["server"]��,e.�["port"]�Ԍ � s(e,n,o)� t,n={},"([^"..(n �"%s").."]+)"� e � �.gmatch(e,n)�
� n,o=�.find(e,o,1,�)�(n)�
t[�.�(e,1,n-1)]=�.�(e,o+1)�
�
� t
�
� s=s(a,o,r)� e,r,o
�(n � t)�
� a=�.format("http://%s:%s/%s",c,d,t)� t={}� l,n,a,a=l.�{url=a,sink=ltn12.sink.�(t),�=n,headers=s,�=ltn12.�.�(i)}� l==� � o=n � r=�(n)�
e=�.concat(t)�
� e,r,o
�}�)� t="xrac"� Q='{"result": "_ErrorUnauthorized", "error": "Invalid Session Token"}'� y="/router"� e="/var/run/"..t..".pid"� b="/var/log/xrac"� S=60
� w,r,i,u,m,a,d,f,g,_,h,v
�"verse"�"verse.client"� N=�("socket")� p=�("retry")()� k=�("cache")()� s=�("modulehandler")� c={}� � x(e,...)� e=�.date().." "..t.." "� t,n � �(�)�
e=e..�(n).." "�
e=e.."\n"� e
�
� � l(n)� e=assert(�.open(d,"a"))�(e)�
e:�(n)�.�(e)�
�.�:�("Could not open log file("..d.."):"..n)�
�
� � o(...)� e=x(�,...)�.�:�(e)l(e)�
� � n(...)� a �
� e=x("debug",...)�(e)l(e)�
�
� � I(c,a)� � i(n,e)� e==''�(�.�(n,-�.len(e))==e � �.�(n,1,�.len(n)-�.len(e)))�
� � d(n,e)� n.�<e.� �(n.�==e.� � n.�>e.�)�
� e=�"net.adns"� t,o
� r=�
e.lookup(�(l)n("adns answer is :\n",l)� l �
� e={}� t,n � �(l)�
�.insert(e,n.srv)�
�.sort(e,d)� l=1
�(#e>1)�
� t=1,#e �
�(e[t].�~=e[1].�)�
�.randomseed(�.�())l=�.�(t-1)n("First records that have same priority:",t-1,"bestrecord:",l)�
�
�
�
� e=e[l]t,o=i(e.target,"."),e.port
r=t � o
n("Best record found, will connect to :",t,o)�
�(a)�
n("calling callback with ",r,t,o)a(r,t,o)�
�,"_xmpp-client._tcp."..(c)..".","SRV")�
� � x()� e=1,#� �
� �[e]=="--retry"�
� e=�(�[e+1])n("Retry iteration is ",e)p.�(e)� �[e]=="--debug"�
a=�
�
�
�
� � q()� e=�(�)� e=e.new()e:readlock()d=e:�("xmpp_log_file",b)w=e:�("xmpp_enabled",�)v=e:�("xmpp_use_dns",�)r=e:�("owned_network_id")i=e:�("owned_network_password")u=e:�("xmpp_host")m=e:�("xmpp_port")a=e:�("xmpp_logging")g=�(e:�("xmpp_keep_alive",S))� n=�("device")f=n.getCloudHost(e)h=n.getVerifyCloudHost(e)e:rollback()�
� � l(o)� e,r=p.�()n("try_reconnect called (from "..o.."):"..r)�(e � e>0)�
�.�(�..t.."-status sleeping")N.sleep(e*60)�.�(�..t.."-restart "..(p.�()��))�.�(1)�
�.�(�..t..�)�.�(1)�
� � N(...)o(...)l("handle_error")�
x()� � x()� e,a=�(q)� � e �
d=d � b
o("could not read syscfg, exiting...",a)l("init-readsyscfg")� e
�
� � w �
o("XRAC is not enabled, exiting...")�.�(�..t..�)�.�(1)�
� � r � � i �
o("Device is not associated, exiting...")�.�(�..t..�)�.�(1)�
n("XRAC:modules.length=",s.�)� � s.� �(s.�==0)�
o("Could not find any valid module, exiting...")�.�(�..t..�)�.�(1)�
� e
�
� � d(n)� o=�("cloud")� e,t
�(n � k.�(n))�
e,t=�,"session exists in cache"�
e,t=o.isValidSession(f,r,n,h)�(e)�
k.add(n)�
�
� e,t
�
� �(t,n,o,l)� r='/etc/certs/root'� e=�("cloud")� n,t,e=e.callCloud({host=t,path='/device-service/rest/networkcredentialvalidator',�='POST',�={networkCredentialValidator={network={networkId=n,password=o}}},verifyPath=l � r � �})� n=�
� t==404 � e~=� �
� t=�('libhdkjsonlua')�(�()e=t.parse(e)n=e.errors[1].�.code=="NETWORK_NOT_FOUND"�)�
� � n,t
�
�.�[�]=(�(...)� �(e)� � _(i)n("connected using(jid, host, port, endpoint, verifyEndpoint, keep_alive_timeout): ",r,u,m �"default",f,�(h),g)�.�(�..t.."-status started")p.�()� � u(e)n("on_iq(stanza) RECEIVED: ",e)� e.name=="iq"� e.�.�=="set"�
� t=e.�["xmlns"]n("IQ of interest received: ",t)� r=s.�[t]�(r)�
n("Found the module to handle:"..r.�)� t=e:get_child(r.�,r.�)�(t)�
� c,a,o=t.�[�],�,�
�(c)�
� l
�,l=d(c)n("Validating sessionToken. Result:",�,l)�(�)�
� e=�.�()l,a,o=r.�(t)t:�(�):�(l)� e=�.�()�
o,a=Q,"401"t:�(�):�(o)�
� e=�.stanza("iq",{to=e.�.from,from=e.�.to,id=e.�.id,�=�}):add_child(t)e:�("status"):�(a ��):up():�(�):�(o ��)i:send(e)n("on_iq: response iq SENT: ",e)�
�
�
o("Could not find any module for "..e.�["xmlns"])�
�
�
� e,n � �(s.�)�
i:�("stream/"..e,u)�
�
� �(_)�
�)� �(s)�.set_logger(a � �.�()� �.filter_log({�},�.�()))� e=�.new(�.�())� d=�()�(e)�
e:�(�.date().." 'm alive")e.�:�(" ")�.�(g,c.�)�
�
c.�=d
�.set_error_handler(N)e.log.�=a
e:�("authentication-failure",�(t)e:�(�..(t.� ��)..")"..(t.� �(": "..t.�)��))n(�,�..(t.� ��)..")"..(t.� �(": "..t.�)��))e:�()�(t � t.�=="not-authorized")�
� t=�(f,r,i,h)� � t �
e:�(�)n(�,�)� e=�(�)� e=e.new()e:writelock()e:set_xrac_owned_network_id(�)e:set_xrac_owned_network_password(�)e:setevent('xrac_provision_error','Network ID is no longer valid, so xrac cleared it out')e:commit()�
�
�)e:�("ready",�()e:send(�.presence())�(� _)�
�.�(g,c.�)_=�
�
� s(e)�)e:�(�,�(n)e:�("Resource binding failure ("..(n.� ��)..")"..(n.� �(": "..n.�)��))e:�()l(�)�)e:�(�,�(n)� n.� �
e:warn("Disconnecting: %s",�(n.�))�
l(�)�)�(a)�
e:�(�,�(e)n(�,e)�)e:�(�,�(e)n(�,e)�)�
e.�,e.�=u,m
�(v)�
I(u,�(t,o,l)n("ok, best_server, best_host =",t,o,l)� t �
e.�,e.�=o,l
�
e:�(r..y,i)�)�
e:�(r..y,i)�
� r=e.�
� e:�(o)n("conn:close called.force_close:",o)e:�("Delaying close...")� � t()� r �
n(�)e:�(�)� n=r
r=�
n(e)�
�
� e.�:bufferlen()==0 � o �
t()�
e:�("drained",t)�
�
� e:kill()� e:�(�)�
c.�=e
� r,e=�(�.loop)� � r �
o("Verse loop failed...")� � e:match("interrupted!$")�
�.�:�("Fatal error: ",e,"\n")�
�.�:�("Exiting "..t.."...","\n")�
l("verse.loop")�
� o � 0
�
n("Starting XRAC using JID:",r)x()� n,e=�(�,�)� � n �
o("Could not load processmodules w/ error:",e)l("main")�
c.init=x
� e(�)� 0
]===], '@/hudson/build/shelbyv2/output/production/xmppraclient/build/xrac.lua'))()