(function(a,b){if(typeof define==="function"&&define.amd){define(["jquery","jnap-js/jnap"],b)}else{if(typeof module==="object"&&module.exports){module.exports=b(require("jquery"),require("@linksys/jnap-js").JNAP)}else{a.sideEffectsManager=b(a.$,a.JNAP)}}}(this,function(h,p){var w,v=1000,q=60*v,j=3*q;window.CISCO=window.CISCO||{};function n(B,y,x,A,C,z){x=x||1000;A=A||(new Date()).getTime()+x*C+z;if(z){setTimeout(function(){n(B,y,x,A,C)},z);return}x-=1;B(function(D){if(D||x===0||(new Date()).getTime()>A){y(D)}else{setTimeout(function(){n(B,y,x,A,C)},C)}})}var s=null;function e(x){if(x&&x.skipWANReconnectPolling){s=(new Date()).getTime()-100*q}else{s=null}}function b(x){e(x);return function(y){p.send("/router/GetWANStatus").success(function(z){if(!s){s=(new Date()).getTime()}var A=z.output.wanStatus==="Connected"||z.output.wanIPv6Status==="Connected";var B=(new Date()).getTime()>s+60*v;y(A||B)}).error(function(){y(false)})}}function o(x){var y=b(x);return function(B){var A=p.cache.data["/core/GetDeviceInfo"].output.serialNumber;var z={supportedForCaching:{"/core/GetDeviceInfo":false}};p.send("/core/GetDeviceInfo",{},z).success(function(C){if(C.output.serialNumber===A){y(B)}else{B(false)}}).error(function(){B(false)})}}function d(y,C,x,A,B){var z=p.getOptions();return function(D){if(!D){if(z.uiRetryRouterConnectivity()){x.sideEffects=y;C(x,A,B);return}else{x.result="failed_to_reconnect_to_router"}}C(x,A,B);if(i()){l()}z.uiInterruptionFinished()}}function k(y,x){w.poll(o(x),y,null,(new Date()).getTime()+4*q,10*v,x.timeoutDelayRestartCheck)}function t(B,y,x){var A,z;A=y.auth||p.getAuth()||h.noop;z={headers:{},url:"/JNAP/"};A(z);if(x.indexOf("WirelessInterruption")>-1){w.poll(o(y),B,null,(new Date()).getTime()+2*q,10*v,y.timeoutDelayWirelessCheck)}else{w.poll(b(y),B,5,null,15*v,y.timeoutDelayWANCheck)}}function r(){var x={sideEffect:function(z,C,y,A,B){this.uiInterruptionStarted(z);if(z.indexOf("DeviceRestart")>-1){this.handleDeviceRestart(z,C,y,A,B)}else{this.handleInterruption(z,C,y,A,B)}},timeoutDelayWANCheck:5*v,timeoutDelayWirelessCheck:10*v,timeoutDelayRestartCheck:40*v,handleDeviceRestart:function(z,D,y,A,C){var B=this;c(B.uiRebootProgress);k(d.call(B,z,D,y,A,C),B)},handleInterruption:function(z,D,y,A,C){var B=this;t(d.call(B,z,D,y,A,C),B,z)},uiInterruptionStarted:h.noop,uiInterruptionFinished:h.noop,uiRebootProgress:h.noop,uiRetryRouterConnectivity:function(){return confirm("try again?")},connectToWiFiNetwork:null};p.setOptions(x)}var u=null,f,a,g;function m(y,x){if(i()){l()}f=y;a=x;g=0;u=setInterval(function(){if(g<a){g+=1000}f(g/a*100)},1000);f(0)}function c(x){m(x,j)}function l(){clearInterval(u);u=null}function i(){return u!==null}w={init:r,poll:n,getTestRouterReconnectedFunc:o,fakeProgress:{start:m,stop:l,isRunning:i},fakeRebootProgress:{start:c,stop:l,isRunning:i}};return w}));