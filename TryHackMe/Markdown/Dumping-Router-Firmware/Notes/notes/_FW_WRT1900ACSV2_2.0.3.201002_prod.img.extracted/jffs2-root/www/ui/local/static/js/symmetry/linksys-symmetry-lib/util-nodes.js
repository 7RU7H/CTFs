(function(a,b){if(typeof define==="function"&&define.amd){define(["lodash","./symmetry-jnap","./device-manager"],b)}else{if(typeof module==="object"&&module.exports){module.exports=b(require("lodash"),require("./symmetry-jnap"),require("./device-manager"))}else{a.utilNodes=b((a.lodash||a._),a.JNAP,a.deviceManager)}}}(this,function(n,i,h){var g;var f=["Master"],p=["Slave"];var j=[{model:"WHW03B",baseModel:"WHW03",isMeshRouter:false,pattern:/whw03b/i},{model:"WHW03",baseModel:"WHW03",isMeshRouter:false,pattern:/nd0001|nodes|whw0301|whw03|a03/i},{model:"WHW01P",baseModel:"WHW01",isMeshRouter:false,pattern:/whw01p/i},{model:"WHW01B",baseModel:"WHW01",isMeshRouter:false,pattern:/whw01b|vlp01b/i},{model:"WHW01",baseModel:"WHW01",isMeshRouter:false,pattern:/whw01|vlp01|a01/i},{model:"MR7350",baseModel:"MR7350",isMeshRouter:true,pattern:/mr7350/i},{model:"MR6350",baseModel:"MR6350",isMeshRouter:true,pattern:/mr6350|mr6320|mr6330|mr6340/i},{model:"MR8300",baseModel:"MR8300",isMeshRouter:true,pattern:/mr8300|mr8250|mr9000|mr8900|mr8950|mr9100/i},{model:"MR9600",baseModel:"MR9600",isMeshRouter:true,pattern:/mr9600/i},{model:"MX5300",baseModel:"MX5300",isMeshRouter:false,pattern:/mx5400|mx5300/i}];function e(r){return n.result(n.find(j,function(s){return s.pattern.test(r)}),"model",null)}function l(r){return n.result(n.find(j,function(s){return s.pattern.test(r)}),"baseModel",null)}function q(r){return !!e(r)}function a(r){return n.result(n.find(j,function(s){return s.pattern.test(r)}),"isMeshRouter",null)}function m(r){i.send("/nodes/setup/GetSelectedChannels").success(function(s){if(s.output.isRunning){setTimeout(function(){console.warn("Polling GetSelectedChannels");m(r)},5000)}else{r.resolve(s.output)}}).fail(function(s){r.reject(s)})}function k(){var r=i.Deferred(),t=h.nodes.getSlaves(),s=45;t.forEach(function(u){if(u.isOnline){s+=60}});i.send("/nodes/setup/StartAutoChannelSelection",{},{timeoutDelayWirelessCheck:s*1000}).success(function(u){m(r)}).fail(function(u){r.reject(u)});return r}function b(t){var r=i.Deferred(),s=t?t:i;if(i.isServiceSupported("/nodes/smartmode/SmartMode2")){s.send("/nodes/smartmode/GetSupportedDeviceModes").success(function(u){var v=n.result(u,"output.supportedModes");console.warn("GetSupportedDeviceModes results:",v);r.resolve(v)}).fail(function(u){r.reject(u)})}else{r.resolve(n.union(f,p))}return r}function o(s){var r=i.Deferred();b(s).success(function(u){var t=n.isEqual(u,f);r.resolve(t)}).fail(function(t){r.reject(t)});return r}function d(s){var r=i.Deferred();b(s).success(function(u){var t=n.isEqual(u,p);r.resolve(t)}).fail(function(t){r.reject(t)});return r}function c(t){var r=i.Deferred(),s={supportedForCaching:{"/nodes/setup/GetNodesWirelessConnectionInfo":false}};i.send("/nodes/setup/GetNodesWirelessConnectionInfo",{deviceIDs:[t]},s).success(function(u){if(!u.output||!u.output.nodesWirelessConnectionInfo||!u.output.nodesWirelessConnectionInfo.length||!u.output.nodesWirelessConnectionInfo[0].bandwidth){r.reject("Unavailable")}else{var v={rangeIndicator:0,data:u.output};if(u.output.nodesWirelessConnectionInfo[0].rssi&&u.output.nodesWirelessConnectionInfo[0].rssi<0&&u.output.nodesWirelessConnectionInfo[0].rssi>-35){v.rangeIndicator=-1}else{if(u.output.nodesWirelessConnectionInfo[0].bandwidth<50){v.rangeIndicator=1}}r.resolve(v)}}).error(function(u){r.reject(u)});return r}g={baseModel:l,treatAsModel:e,isNodeModel:q,isMeshRouter:a,getSupportedDeviceModes:b,canOnlyBeMaster:o,canOnlyBeSlave:d,slaveSpotfinderRange:c,doAutoChannelSelection:k};return g}));