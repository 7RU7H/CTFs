RAINIER=RAINIER||{};RAINIER.network=(function(){var c={applets:[{appletId:"6B749D0D-1E3B-4C2A-B0CE-D19EDA0225AC",name:"Parental Controls",description:"Restrict Internet access and content",isFree:true,deviceType:"ROUTER",version:"1.0.0.0",url:"/ui/dynamic/applets/parental_controls/main.html",defaultPosition:3,enableInBridgeMode:false,appletCategory:{appletCategoryId:"AFAC7A48-662B-4333-884F-D7F5496EAF12",name:"Apps",defaultPosition:1}},{appletId:"BDBC8297-8982-4BCF-84EC-91783FF9013C",name:"Connectivity",description:"View and change basic router configuration",isFree:true,deviceType:"ROUTER",version:"1.0.0.0",url:"/ui/dynamic/applets/connectivity/main.html",defaultPosition:1,enableInBridgeMode:true,appletCategory:{appletCategoryId:"68980747-C5AA-4C8B-AF53-FC1023DE2567",name:"Router Settings",defaultPosition:2}},{appletId:"1BED1B78-87DA-4871-A731-8F54B7D3DC00",name:"Troubleshooting",description:"Access status reports and diagnostic functions",isFree:true,deviceType:"ROUTER",version:"1.0.0.0",url:"/ui/dynamic/applets/troubleshooting/main.html",defaultPosition:2,enableInBridgeMode:true,appletCategory:{appletCategoryId:"68980747-C5AA-4C8B-AF53-FC1023DE2567",name:"Router Settings",defaultPosition:2}},{appletId:"7B1F462B-1A78-4AF6-8FBB-0C221703BEA4",name:"Wireless",description:"View and change wireless configuration",isFree:true,deviceType:"ROUTER",version:"1.0.0.0",url:"/ui/dynamic/applets/wireless/main.html",defaultPosition:3,enableInBridgeMode:true,appletCategory:{appletCategoryId:"68980747-C5AA-4C8B-AF53-FC1023DE2567",name:"Router Settings",defaultPosition:2}},{appletId:"23FA4143-22B4-49D7-BDC5-3ED54EDF69CB",name:"Security",description:"View and change security configuration",isFree:true,deviceType:"ROUTER",version:"1.0.0.0",url:"/ui/dynamic/applets/security/main.html",defaultPosition:4,enableInBridgeMode:false,appletCategory:{appletCategoryId:"68980747-C5AA-4C8B-AF53-FC1023DE2567",name:"Router Settings",defaultPosition:2}},{appletId:"9BDB14C9-C328-40ED-89A9-A3A822B8B5D2",name:"Media Prioritization",description:"Prioritize devices and applications bandwidth",isFree:true,deviceType:"ROUTER",version:"1.0.0.0",url:"/ui/dynamic/applets/media_prioritization/main.html",defaultPosition:4,enableInBridgeMode:false,appletCategory:{appletCategoryId:"AFAC7A48-662B-4333-884F-D7F5496EAF12",name:"Apps",defaultPosition:1}}],appletGuestAccess:{appletId:"B24C693D-4DFF-417D-A10F-A8212051E60E",name:"Guest Access",description:"Give Internet access to guests in your home",isFree:true,deviceType:"ROUTER",version:"1.0.0.0",url:"/ui/dynamic/applets/guest_access/main.html",defaultPosition:2,enableInBridgeMode:false,appletCategory:{appletCategoryId:"AFAC7A48-662B-4333-884F-D7F5496EAF12",name:"Apps",defaultPosition:1}},appletUsbStorage:{appletId:"A2DB16C0-59B9-4C79-9BF2-E5A3A307F9C1",name:"External Storage",description:"Access an external storage device",isFree:true,deviceType:"ROUTER",version:"1.0.0.0",url:"/ui/dynamic/applets/usb_storage/main.html",defaultPosition:6,appletCategory:{appletCategoryId:"AFAC7A48-662B-4333-884F-D7F5496EAF12",name:"Apps",defaultPosition:1}},appletExternalStorage:{appletId:"96015299-6364-4ABE-A68E-969E5BE093E9",name:"External Storage",description:"Access an external storage device",isFree:true,deviceType:"ROUTER",version:"1.0.0.0",url:"/ui/dynamic/applets/external_storage/main.html",defaultPosition:6,appletCategory:{appletCategoryId:"AFAC7A48-662B-4333-884F-D7F5496EAF12",name:"Apps",defaultPosition:1}},appletDeviceList:{appletId:"691E0149-A1D4-450C-9922-82CAA65B16C0",name:"Device List",description:"View devices connected to your network",isFree:true,deviceType:"ROUTER",version:"1.0.0.0",url:"/ui/dynamic/applets/device_list/main.html",defaultPosition:1,enableInBridgeMode:false,appletCategory:{appletCategoryId:"AFAC7A48-662B-4333-884F-D7F5496EAF12",name:"Apps",defaultPosition:1}},appletNetworkMap:{appletId:"EEBEB3F4-5482-4DAE-8D77-1BA5AC7D19BE",name:"Network Map",description:"View devices connected to your network",isFree:true,deviceType:"ROUTER",version:"1.0.0.0",url:"/ui/dynamic/applets/device_map/main.html",defaultPosition:1,enableInBridgeMode:false,appletCategory:{appletCategoryId:"AFAC7A48-662B-4333-884F-D7F5496EAF12",name:"Apps",defaultPosition:1}},appletOpenVpn:{appletId:"0C5FBA3C-B3F0-476E-BF9B-D6A27BD7C1E6",name:"OpenVPN Server",description:"Manage OpenVPN settings",isFree:true,deviceType:"ROUTER",version:"1.0.0.0",url:"/ui/dynamic/applets/openvpn/main.html",defaultPosition:5,appletCategory:{appletCategoryId:"68980747-C5AA-4C8B-AF53-FC1023DE2567",name:"Router Settings",defaultPosition:2}},appletSpeedTest:{appletId:"7C23A10B-3C02-4F64-B954-44A148144423",name:"Speed Check",description:"Test your speed",isFree:true,deviceType:"ROUTER",version:"1.0.0.0",url:"/ui/dynamic/applets/speed_test/main.html",defaultPosition:5,enableInBridgeMode:false,appletCategory:{appletCategoryId:"AFAC7A48-662B-4333-884F-D7F5496EAF12",name:"Apps",defaultPosition:1}},appletDiagnostics:{appletId:"CD34B015-6EA6-4FE3-89CA-B47B6F18EF92",name:"Diagnostics",description:"Diagnostics applet for Remote Assistance",isFree:true,deviceType:"ROUTER",version:"1.0.0.0",url:"/ui/dynamic/applets/diagnostics/main.html",defaultPosition:1,enableInBridgeMode:true,appletCategory:{appletCategoryId:"59BA631A-065D-41B7-91CA-46BCE1044B4A",name:"Remote Assistance",defaultPosition:3}}},D=null,d=false,f=false,k=null,C=[],R=null,I=null,v={wanType:""},h={isWirelessSchedulerEnabled:false},n={wanStatus:"",detectedWANType:""},o=null,y=null,E=null,S=null,x="cisco.com",N="linksys.com",b=2500,F=["EA7200","EA7300","EA7400","EA7450","EA7500","EA8100","EA8250","EA8300","EA8500","EA9200","EA9300","EA9350","EA9400","EA9500","WRT1200AC","WRT1900AC","WRT1900ACS","WRT3200ACM"];var i=null,J=true,g=false;function l(W){RAINIER.shared.pingTests.start(RAINIER.shared.pingTests.allTests,function(Y,X){if(X>1){return}if(i===null||i===false){RAINIER.event.fire("rainier.lanConnected");RAINIER.event.fire("rainier.wanConnected");i=true}if(typeof W==="function"){W(true)}},function(Y,X,Z){if(Y.url===RAINIER.shared.pingTests.allTests[0].url){RAINIER.shared.pingTests.start([RAINIER.shared.pingTests.allTests[0]],function(){},function(){RAINIER.event.fire("rainier.cloudDown")})}if(Z===RAINIER.shared.pingTests.allTests.length){if(i===null||i===true){RAINIER.event.fire("rainier.wanDisconnected");i=false}if(typeof W==="function"){W(false)}}})}function r(W,aa,Y){var Z=3;function X(ab,ac){l(function(ad){if(ad){if(ac<Z){setTimeout(function(){X(ab,++ac)},b)}else{if(typeof ab==="function"){ab(true)}}}else{setTimeout(function(){X(ab,0)},b)}})}if(J||aa){if(RAINIER.network.isBehindAuthority()){RAINIER.network.checkLinkToRouterAndGetWanStatus(function(ab){if(ab){if(RAINIER.network.getCurrentWanStatus().wanStatus!=="Connected"){RAINIER.event.fire("rainier.wanDisconnected");if(typeof W==="function"){W(false)}return}}else{if(!ab||Y){setTimeout(function(){X(W,0)},2500);return}}l(W)})}else{l(W)}}}function V(Y){var X="/jnap/ownednetwork/GetOwnedNetworkID";function W(){RAINIER.jnap.send({action:X,data:{},cb:function(aa,Z){if(Z.status===200){if(!aa||!aa.result){RAINIER.jnap.executeDefaultJnapErrorHandler(aa,X)}else{if(aa.result==="OK"){f=true;k=aa.output.ownedNetworkID||null}else{f=false;k=null}}}else{f=false;k=null}Y()},disableDefaultAjaxErrHandler:true,disableDefaultJnapErrHandler:true,disableDefaultRebootErrHandler:true,forceLocal:true})}if(RAINIER.shared.util.areServicesSupported(["/jnap/ownednetwork/OwnedNetwork2"])){RAINIER.jnap.send({action:"/jnap/ownednetwork/IsOwnedNetwork",data:{},cb:function(Z){if(Z.result==="OK"){if(!Z.output.isOwnedNetwork){X="/jnap/ownednetwork/IsOwnedNetwork"}W()}else{}},disableDefaultAjaxErrHandler:true,disableDefaultJnapErrHandler:true,disableDefaultRebootErrHandler:true,forceLocal:true})}else{W()}}function B(){if(RAINIER.network.uiLoadedFromCloud()){O()}else{s()}RAINIER.event.fire("RAINIER.network.initted")}function K(){RAINIER.ui.MainMenu.initialize();RAINIER.ui.TopMenu.initialize(C)}function O(){if(R==="OK"){if(C.length===0){window.location.replace("/ui/dynamic/no-associated-router.html")}else{if(!RAINIER.network.getCurrentNetworkID()){window.location.replace("/ui/dynamic/no-remote-router.html")}else{RAINIER.connect.initialize();if(!RAINIER.network.isBridgeMode()){RAINIER.connect.initializeWidgets()}K()}}}else{if(R==="ACCOUNT_LOCKED_OUT"){window.location.replace("/ui/dynamic/account-lockout.html")}else{if(R==="ACCOUNT_SECURITY_LOCK"){window.location.replace("/ui/dynamic/account-security-lock.html")}else{RAINIER.ui.dialogError({result:R},null,true)}}}I()}function s(){if(R==="OK"){RAINIER.connect.initialize();if(!RAINIER.network.isBridgeMode()){RAINIER.connect.initializeWidgets()}K()}I()}function w(aa){RAINIER.event.connect("connection.disableCheck",function(){J=false});RAINIER.event.connect("connection.enableCheck",function(){J=true});var W=false,Z=false;RAINIER.event.connect("initializeApplicationFinished",function(){W=true;RAINIER.symmetryLib.jnapInit();RAINIER.applets.wireless.initialize();RAINIER.applets.USBStorage.initialize();if(Z){RAINIER.ui.hideMasterWaiting()}});RAINIER.event.connect("initialInternetCheckCompleted",function(){if(RAINIER.network.getCurrentWanStatus().lastConnectionFailure){var ab=RAINIER.ui.dialog("#wireless-bridge-not-connected");ab.show()}});RAINIER.event.connect("uiInitCompleted",function(){Z=true;if(W){RAINIER.ui.hideMasterWaiting()}});var Y=$.cookie("admin-auth")!==null,X=$.cookie("user-auth-token")!==null;if(Y===X){RAINIER.network.signOut()}else{if(Y&&RAINIER.network.uiLoadedFromCloud()){t(aa)}else{if(RAINIER.network.uiLoadedFromCloud()){a(aa)}else{t(aa)}}}}function p(W){W.add(function(){var X=this;RAINIER.cloud.send({url:"/cloud/device-service/rest/accounts/self/networks",type:"GET",request:{},cb:function(Y){C=Y.networkAccountAssociations;X.completed()},cbError:function(Y){R="ERROR_GET_NETWORKS";X.failed()}})});W.add(function(){var X=this;RAINIER.cloud.send({url:"/cloud/user-service/rest/accounts/self",type:"GET",request:{},cb:function(Y){S=Y.account;X.completed()},cbError:function(Y){X.failed()}})});W.add(function(){var X=this;$.ajax({url:"/cloud/user-service/version.jsp",success:function(Y){E=Y;X.completed()},error:function(){X.failed()}})});W.add(function(){var X=this;z(function(Y){if(!Y){X.failed()}else{X.completed()}},true)})}function a(X){R="OK";I=X;var W=RAINIER.taskmanager({parallel:true,callback:function(){m()}});W.add(function(){var Y=this;V(Y.completed)});p(W);W()}function t(X){R="OK";I=X;C=[];var W=RAINIER.taskmanager({parallel:false,callback:function(){B()}});W.add(function(){var Y=this;RAINIER.deviceManager.getAuthorityDevice(function(Z){C.push({networkAccountAssociation:{network:{friendlyName:Z.friendlyName()},online:true}});Y.completed()})});W.add(function(){var Y=this,Z=RAINIER.jnapCache.get(RAINIER.jnapActions.getDeviceInfo());if(Z){Y.completed()}else{R="ERROR_FAIL_GET_DEVICE_INFO";Y.failed()}});W.add(function(){var Y=this;RAINIER.connect.AppletManager.initialize(function(){Y.completed()})});W.add(function(){var Y=this;U(function(Z){if(!Z){R="ERROR_FAIL_GET_WAN_SETTINGS";Y.failed()}else{Y.completed()}})});W.add(function(){var Y=this;P(function(Z){if(!Z){R="ERROR_FAIL_GET_WFS_SETTINGS";Y.failed()}else{Y.completed()}})});W()}function m(W){W=W||false;if(R==="OK"){var X=RAINIER.taskmanager({parallel:true,callback:function(){if(R==="OK"&&C.length>0&&!RAINIER.network.getCurrentNetworkID()){var Z=false,Y=0;for(Y=0;Y<C.length;Y++){if(k===C[Y].networkAccountAssociation.network.networkId){RAINIER.network.setCurrentNetworkID(C[Y].networkAccountAssociation.network.networkId);Z=true;break}}if(!Z){for(Y=0;Y<C.length;Y++){if(C[Y].networkAccountAssociation.online){if(!RAINIER.network.getCurrentNetworkID()||C[Y].networkAccountAssociation.defaultNetwork){RAINIER.network.setCurrentNetworkID(C[Y].networkAccountAssociation.network.networkId);break}}}}}RAINIER.event.fire("networkstatuses.complete");if(!W){Q()}}});C.forEach(function(Y){X.add(function(){var aa=this,ad=Y,Z={},ac=0;var ab={action:"/jnap/core/IsAdminPasswordDefault",data:{},cb:function(af){var ae=false;if(af.result==="OK"){ae=true}if(af.result==="_ErrorUnknownAction"&&ac===0){RAINIER.jnap.send($.extend({},Z,{altJNAPHost:true}));ac++}else{ad.networkAccountAssociation.online=ae;aa.completed()}},disableDefaultAjaxErrHandler:true,disableDefaultJnapErrHandler:true,disableDefaultRebootErrHandler:true,networkID:ad.networkAccountAssociation.network.networkId};Z=ab;RAINIER.jnap.send(ab)})});X()}else{if(!W){B()}}}function Q(){if(R==="OK"&&RAINIER.network.getCurrentNetworkID()){var W=RAINIER.taskmanager({parallel:false,callback:function(){if(RAINIER.network.getAuthorityNetworkID()!==RAINIER.network.getCurrentNetworkID()){console.warn("[talking to remote Router through another LSWF router, resetting Device Manager]");RAINIER.deviceManager.clearDevices()}B()}});W.add(function(){var X=this;z(function(Y){if(!Y){R="ERROR_FAIL_GET_DEVICE_INFO";X.failed()}else{X.completed()}})});W.add(function(){var X=this;U(function(Y){if(!Y){R="ERROR_FAIL_GET_WAN_SETTINGS";X.failed()}else{X.completed()}})});W.add(function(){var X=this;P(function(Y){if(!Y){R="ERROR_FAIL_GET_WFS_SETTINGS";X.failed()}else{X.completed()}})});W.add(function(){var X=this;RAINIER.connect.AppletManager.initialize(function(Y){if(!Y){R="ERROR_GET_APPLET_LIST";X.failed()}else{X.completed()}})});W()}else{B()}}function z(W,ab){var Z="/jnap/core/GetDeviceInfo",aa=true;function X(ac){RAINIER.network.setUiRemoteSetting(ac);RAINIER.event.fire("ui.RetrievedRemoteSetting")}function Y(){if(typeof W==="function"){W(aa)}}RAINIER.jnap.send({action:Z,data:{},cb:function(ac){if(!ac||!ac.result){aa=false;RAINIER.jnap.executeDefaultJnapErrorHandler(ac,Z)}else{if(ac.result==="OK"){if(ab){Y();return}RAINIER.jnapCache.set(RAINIER.jnapActions.getDeviceInfo(),ac.output);JNAP.cache.data["/core/GetDeviceInfo"]=ac;if($.grep(ac.output.services,function(ad){if(ad.indexOf("linksys.com")>-1){return true}}).length>0){x="linksys.com";N="cisco.com"}if(RAINIER.network.isPowerModemSupported()){b=5000}if(!d){if(RAINIER.network.getCurrentNetworkID()&&!RAINIER.network.isBehindAuthority()){X(true)}else{if(RAINIER.network.isRemoteSettingSupported()){RAINIER.jnap.send({action:"/jnap/ui/GetRemoteSetting",data:{},cb:function(ad){if(ad.result==="OK"){X(ad.output.isEnabled)}else{aa=false}}})}else{X(true)}}}}else{X(true)}}if(aa){RAINIER.event.fire("RAINIER.network.deviceInfoCache:Set")}Y()},disableDefaultAjaxErrHandler:true,disableDefaultJnapErrHandler:true,disableDefaultRebootErrHandler:true})}function T(W){RAINIER.jnap.send({action:"/jnap/core/GetAdminPasswordHint",data:{},cb:function(X){W(X.result==="OK"?X.output:"")},disableDefaultAjaxErrHandler:true,disableDefaultJnapErrHandler:true,disableDefaultRebootErrHandler:true})}function U(W){v={wanType:""};RAINIER.jnap.send({action:RAINIER.jnapActions.getWANSettings(),data:{},cb:function(X){if(X.result==="OK"){v=X.output;if(RAINIER.network.isPowerModemSupported()){RAINIER.jnap.send({action:"/jnap/powermodem/GetDSLSettings",data:{},cb:function(Y){if(X.result==="OK"){if(Y.output.isPowerModemEnabled){RAINIER.network.setCurrentWanSettings(Y.output.dslSettings)}W(true)}else{W(false)}},disableDefaultAjaxErrHandler:true,disableDefaultJnapErrHandler:true,disableDefaultRebootErrHandler:true})}else{W(true)}}else{W(false)}},disableDefaultAjaxErrHandler:true,disableDefaultJnapErrHandler:true,disableDefaultRebootErrHandler:true})}function P(W){if(RAINIER.network.isWirelessSchedulerSupported()){RAINIER.jnap.send({action:"/jnap/wirelessscheduler/GetWirelessSchedulerSettings",data:{},cb:function(X){if(X.result==="OK"){h=X.output;W(true)}else{W(false)}},disableDefaultAjaxErrHandler:true,disableDefaultJnapErrHandler:true,disableDefaultRebootErrHandler:true})}else{W(true)}}function j(W){function X(Y){n=Y||{wanStatus:"",detectedWANType:""};W(Boolean(Y))}RAINIER.jnap.send({action:RAINIER.jnapActions.getWANStatus(),data:{},forcelocal:true,cb:function(Z,Y){if(Y.status===200&&Z&&Z.result==="OK"){X(Z.output)}else{X()}},disableDefaultAjaxErrHandler:true,disableDefaultJnapErrHandler:true,disableDefaultRebootErrHandler:true})}function M(W){var X=RAINIER.taskmanager({parallel:true,callback:function(){m(true);D=D||function(){W(C)};if(typeof W==="function"){RAINIER.event.connect("networkstatuses.complete",D)}}});p(X);X()}function e(){return d}function L(W){d=W}function H(X,W){if(RAINIER.network.isRemoteSettingSupported()){if(RAINIER.network.uiLoadedFromCloud()!==X){RAINIER.shared.util.setRemoteSetting(RAINIER.jnap,X,W,{forceLocal:!RAINIER.network.uiLoadedFromCloud()});RAINIER.network.setUIProxyPathCookie(X?"remote":"local");return}}if(typeof W==="function"){W()}}function A(W,X){function Y(){if(typeof W==="function"){W()}}if(X){$.cookie("admin-auth","Basic "+window.btoa("admin:"+X),{expires:null,path:"/"})}RAINIER.deviceManager.getAuthorityDevice({cb:function(Z){var aa=Z.getCustomProperty("setupState");if(X){$.cookie("admin-auth",null,{path:"/"})}if(aa){if(aa==="start"){RAINIER.shared.util.setCookie("page",window.location,null,"/");RAINIER.shared.util.setCookie("errCode","2318",null,"/");window.location.replace("/ui/dynamic/setup/generic_error.html")}else{if(aa==="wifiReconnect"){window.location.replace("/ui/dynamic/setup/change_router_password.html")}}return}Y()},cbError:function(){if(X){$.cookie("admin-auth",null,{path:"/"})}Y()}})}function u(W){if(RAINIER.shared.util.areServicesSupported(["/jnap/locale/Locale2"])){RAINIER.jnap.send({action:"/jnap/locale/GetLocalTime",data:{},cb:function(X){if(X.result==="OK"){W(X.output.currentTime)}else{}}})}else{RAINIER.jnap.send({action:"/jnap/locale/GetTimeSettings",data:{},cb:function(aa){if(aa.result==="OK"){var Z=aa.output||{},Y=0;for(var X=0;X<Z.supportedTimeZones.length;X++){if(Z.timeZoneID===Z.supportedTimeZones[X].timeZoneID){Y=Z.supportedTimeZones[X].utcOffsetMinutes}}W(RAINIER.util.utcTimeWithOffsetToCurrentTimeString(RAINIER.util.utcTimeToDate(Z.currentTime),Y))}else{}}})}}function G(){return RAINIER.shared.util.areServicesSupported(["/jnap/powermodem/PowerModem"])}function q(){return RAINIER.shared.util.areServicesSupported(["/jnap/networksecurity/NetworkSecurity"])}return{discoverAuthority:V,initializeApplication:w,storageSupported:function(){return RAINIER.shared.util.areServicesSupported(["/jnap/storage/FTPServer","/jnap/storage/SMBServer","/jnap/storage/Storage"])},nodesStorageSupported:function(){return RAINIER.shared.util.areServicesSupported(["/jnap/nodes/storage/SMBServer","/jnap/nodes/storage/Storage"])},networkMapSupported:function(){var W=false;if($.grep(F,function(X){return X===RAINIER.network.getBaseNetworkModelNumber()}).length>0){W=true}return W},isAuthorityRemote:function(){return RAINIER.network.getAuthorityNetworkID()!==RAINIER.network.getCurrentNetworkID()},isBehindAuthority:function(){return f||!RAINIER.network.uiLoadedFromCloud()},getAuthorityNetworkID:function(){return k},getCurrentNetworkID:function(){return $.cookie("current-network-id")},setCurrentNetworkID:function(W){$.cookie("current-network-id",W,{expires:null,path:"/"})},getBaseNetworkModelNumber:function(){var X=RAINIER.jnapCache.get(RAINIER.jnapActions.getDeviceInfo());if(X){var W=X.modelNumber;if(RAINIER.network.isBehindNode()){return symmetryUtil.nodes.treatAsModel(W)||W}return W}return null},getCurrentNetworkModelNumber:function(){var W=RAINIER.jnapCache.get(RAINIER.jnapActions.getDeviceInfo());if(W){return W.modelNumber}return null},getCurrentNetworkHardwareVersion:function(){var W=RAINIER.jnapCache.get(RAINIER.jnapActions.getDeviceInfo());if(W){return W.hardwareVersion}return null},getCurrentNetworkFirmwareVersion:function(){var W=RAINIER.jnapCache.get(RAINIER.jnapActions.getDeviceInfo());if(W){return W.firmwareVersion}return null},getCloudVersion:function(){return E},isBridgeMode:function(W){if(!W){W=RAINIER.network.getCurrentWanType()}return(W==="Bridge")||(W==="WirelessRepeater")||(W==="WirelessBridge")},isRadioLocked:function(W){return(v.wanType==="Bridge"||v.wanType==="WirelessBridge")&&v.wirelessModeSettings&&(v.wirelessModeSettings.band===W)},isWirelessSchedulerEnabled:function(){return h.isWirelessSchedulerEnabled},setWirelessSchedulerSettings:function(W){h=$.extend(true,{},W)},isRemoteSettingSupported:function(){return RAINIER.shared.util.areServicesSupported(["/jnap/ui/Settings"])},isWirelessSchedulerSupported:function(){return !RAINIER.network.isBehindNode()&&RAINIER.shared.util.areServicesSupported(["/jnap/wirelessscheduler/WirelessScheduler"])},isOpenVPNSupported:function(){return RAINIER.shared.util.areServicesSupported(["/jnap/openvpn/OpenVPN"])},isHealthCheckerSupported:function(){return RAINIER.shared.util.areServicesSupported(["/jnap/healthcheck/HealthCheckManager"])||RAINIER.shared.util.areServicesSupported(["/jnap/nodes/healthcheck/HealthCheckManager"])},isAirtimeFairnessPresent:function(){return RAINIER.shared.util.areServicesSupported(["/jnap/wirelessap/AirtimeFairness"])},isTopologyOptimizationPresent:function(){return RAINIER.shared.util.areServicesSupported(["/jnap/nodes/topologyoptimization/TopologyOptimization"])},isLinkAggregationPresent:function(){return RAINIER.shared.util.areServicesSupported(["/jnap/linkaggregation/LinkAggregation"])},getWirelessSchedulerSettings:function(){return h},updateWirelessSchedulerSettings:P,areRadiosDisabledByWFS:function(W){if(h.isWirelessSchedulerEnabled){u(function(X){var ad=RAINIER.util.routerTimeToDate(X),Z=RAINIER.util.getDaysOfWeek()[ad.getUTCDay()],ab=h.wirelessSchedule[Z.toLowerCase()],ac=(ad.getUTCMinutes()<30)?0:1,Y=ad.getUTCHours()*2+ac,aa=ab.charAt(Y);W((aa==="0")?true:false)})}else{W(false)}},getRouterCurrentTime:u,getUserFirstName:function(){return S.firstName},getUserLastName:function(){return S.lastName},getUserName:function(){return S.username},getFormattedUserName:function(){return S.firstName+RAINIER.util.padIfNotNull(" ",RAINIER.util.fixNoE(S.lastName," ").substr(0,1),".")},getUserLocale:function(){return S.locale},setCloudAccount:function(W){S=W},checkInternetConnection:r,checkLinkToRouterAndGetWanStatus:j,clearUIProxyPathCookie:function(){$.cookie("ui-proxy-path",null,{path:"/",domain:"."+window.location.host});$.cookie("ui-proxy-path",null,{path:"/"})},setUIProxyPathCookie:function(W){$.cookie("ui-proxy-path",null,{path:"/",domain:"."+window.location.host});$.cookie("ui-proxy-path",W,{path:"/"})},readOAuthToken:function(){var X=window.location.hash;if(!X){return}X="/?"+X.substr(1);var W=RAINIER.util.getUrlVars(X);if(!W.access_token){return}$.cookie("user-auth-token",W.access_token,{expires:null,path:"/"})},goToIndex:function(){window.location.href="/"},signOut:function(){if($.cookie("agent-remote-assistance-session")||$.cookie("client-remote-assistance-session")){RAINIER.remote.assistance.endRemoteAssistanceSession();return}RAINIER.network.setBeforeUnload(true);RAINIER.network.deleteUserSession();$.cookie("admin-auth",null,{path:"/"});$.cookie("user-auth-token",null,{path:"/"});$.cookie("current-network-id",null,{path:"/"});$.cookie("current-applet",null,{path:"/"});RAINIER.ui.herald.closeAll();RAINIER.network.clearUIProxyPathCookie();window.location.replace("/ui/dynamic/index.html")},deleteUserSession:function(W,X){function Y(){if(typeof W==="function"){W()}}if(RAINIER.cloud&&$.cookie("user-auth-token")){RAINIER.cloud.send({url:"/cloud/user-service/rest/sessions/"+(X||$.cookie("user-auth-token")),type:"delete",cb:Y,cbError:function(){Y();return true}})}else{Y()}},getLocalAppletInfo:function(X){var W=null;$.each(c.applets,function(Y,Z){if(Z.appletId===X){W=Z;return false}});return W},getLocalAppletList:function(){var W;function X(Y){if(RAINIER.network.isBehindNode()&&Y.velop){_.extend(Y,Y.velop)}}if(RAINIER.network.networkMapSupported()){c.applets.unshift(c.appletNetworkMap)}else{c.applets.unshift(c.appletDeviceList)}c.applets.push(c.appletGuestAccess);c.applets.push(c.appletSpeedTest);if(RAINIER.network.isBehindNode()){c.appletGuestAccess.enableInBridgeMode=true}if(RAINIER.network.storageSupported()){c.applets.push(c.appletUsbStorage)}if(RAINIER.network.nodesStorageSupported()){c.applets.push(c.appletExternalStorage)}if(RAINIER.network.isOpenVPNSupported()){c.applets.push(c.appletOpenVpn)}c.applets.push(c.appletDiagnostics);$.each(c.applets,function(){var Y=this;W=Y.url.replace("main.html","config.json");Y.urlAccess=Y.name.toLowerCase().replace(/[^a-z0-9_]+/ig,"-");$.ajax({async:false,url:RAINIER.shared.util.uniqueURL(W),dataType:"json",success:function(Z){X(Z);Y.name=Z.name;Y.description=Z.description;Y.appletCategory.name=Z.categoryName},error:function(){}})});return c},uiLoadedFromCloud:e,setUiRemoteSetting:L,getCurrentWanType:function(){return v.wanType},setCurrentWanSettings:function(W){v=W},getCurrentWanStatus:function(){return n},getCiscoHNClientID:function(){return"BB426FA7-16A9-5C1C-55AF-63A4167B26AD"},handleFirmwareUpdate:function(W){z(W,true)},getDeviceInfo:z,getPasswordHint:T,updateUserDetails:M,setRemoteSetting:H,getAltJNAPHost:function(){return N},getJNAPHost:function(){return x},hasCloudAbility:function(){return !RAINIER.shared.util.areServicesSupported(["/jnap/ui/ForceLocal"])},isBeforeUnload:function(){return g},setBeforeUnload:function(W){g=W},handleRemoteConnectionProblem:function(X){function W(){if(!RAINIER.dialogRemoteUserLostConnection||!RAINIER.dialogRemoteUserLostConnection.isShowing()){RAINIER.dialogRemoteUserLostConnection=RAINIER.ui.dialogRemoteUserLostConnection()}}if(RAINIER.dialogRemoteUserLostConnection&&RAINIER.dialogRemoteUserLostConnection.isShowing()){return}if(X==="timeout"){l(function(Y){if(Y){window.location.replace("/ui/dynamic/no-remote-router.html")}else{W()}})}else{if(X==="error"){W()}}},checkIfSetupInProgress:A,isPowerModemSupported:G,isNetworkSecuritySupported:q,isBehindNode:function(){var X=RAINIER.jnapCache.get(RAINIER.jnapActions.getDeviceInfo());if(_.isEmpty(X)){return false}var W=_.find(X.services,function(Y){return Y.indexOf("/jnap/nodes/")!==-1});return !_.isEmpty(W)},checkNodesCurrentFirmwareVersions:function(){symmetryUtil.firmwareUpdate.getNodesFirmwareVersions().success(function(W){o=W;RAINIER.event.fire("nodes-current-fw-versions",o)})},getNodesCurrentFirmwareVersions:function(){return o},clearNodesCurrentFirmwareVersions:function(){o=null},checkNodesFwUpdate:function(X,W){W=W||$.noop;symmetryUtil.firmwareUpdate.check({success:function(Y){y=Y;X(Y)},error:W})},getNodesAvailableUpdates:function(){if(sessionStorage&&sessionStorage.getItem("nodesFWUpdateStatus")){y=JSON.parse(sessionStorage.getItem("nodesFWUpdateStatus"));sessionStorage.removeItem("nodesFWUpdateStatus")}return y},isGuestNetworkClientUsed:function(){return RAINIER.shared.util.areServicesSupported(["/jnap/guestnetwork/GuestNetwork2"])||RAINIER.shared.util.areServicesSupported(["/jnap/guestnetwork/GuestNetwork3"])}}}());