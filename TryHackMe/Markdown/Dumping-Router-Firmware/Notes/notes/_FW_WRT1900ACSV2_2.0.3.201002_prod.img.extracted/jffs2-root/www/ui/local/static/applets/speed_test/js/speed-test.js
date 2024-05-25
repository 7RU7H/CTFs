(function(){var g=$("#speed-test-applet"),j=$("#speedtestSWF"),i=$("#speedtestEmbbeded"),c=RAINIER.ui.strings.speedtest;var m={onClose:function(){RAINIER.ui.MainMenu.closeApplet()}};function h(){var n=$.cookie("ui-language");if(!n){n="en"}if(n.indexOf("-")!==-1){n=n.substr(0,n.indexOf("-"))}return"http://linksysconnect.speedtest.net/?bg=4&lang="+n}function b(n){var q=!n?$("#speed-test-unsupported-offline"):$("#speed-test-unsupported-remotely"),p,o={parent:$("#generic-dialog-wrapper"),dlgClass:"generic-dialog",onClose:function(){RAINIER.ui.MainMenu.closeApplet()}};p=RAINIER.ui.dialog(q,o);p.show()}function e(n){var o=$("#speed-test-error");if(n==="ErrorHealthCheckAlreadyRunning"){o=$("#speed-test-busy")}o.find(".close").unbind("click").click(function(){o.hide()});i.find("#speedtestStep").hide();i.find("#speedtestActionDesc").hide();o.show();k("error");i.find("#retry-speedtest").show()}function d(){var n=RAINIER.network.isBehindNode()?"node":"router";g.find(".error-herald").hide();i.find("#speedtestResults, .result-ui").hide();k("ping");i.find("#speedtestStep").text(c.ping.action).show();i.find("#speedtestActionDesc").text(c.ping.description[n]).show();i.find(".divider, .speedtestGraphics, .pingAnimation").show();symmetryUtil.monitors.healthCheck.start("SpeedTest",a,e,{timeout:35000,retry:1})}function k(o){var n=i.find(".divider");n.removeClass("ping download upload error");n.find("#dot-line2 span").addClass("animate");if(o==="ping"){n.addClass("ping")}else{if(o==="download"){n.find("#dot-line2 span:nth-child(8)").removeClass("animate");n.addClass("download")}else{if(o==="upload"){n.find("#dot-line2 span:nth-child(1)").removeClass("animate");setTimeout(function(){n.addClass("upload")},10)}else{n.addClass("error")}}}}function f(p){var o=RAINIER.ui.strings.speedtest.results,n;if(p>o.optimal.downloadBandwidth){n=o.ultra.description}else{if(p>o.fast.downloadBandwidth){n=o.optimal.description}else{if(p>o.good.downloadBandwidth){n=o.fast.description}else{if(p>o.ok.downloadBandwidth){n=o.good.description}else{n=o.ok.description}}}}return n}function a(n){var r=i.find("#speedtestStep"),q=i.find("#speedtestActionDesc");divider=i.find(".divider"),results=i.find("#speedtestResults");function p(t){var u=t.output.healthCheckResults[0].speedTestResult;var s={ping:u.latency,downstreamBandwidth:(u.downloadBandwidth/1000).toFixed(1),upstreamBandwidth:(u.uploadBandwidth/1000).toFixed(1),description:f(u.downloadBandwidth/1000)};r.hide();q.hide();i.find(".speedtestGraphics").hide();RAINIER.binder.toDom(s,i,{});i.find(".result-ui").show();results.show()}if(n.allStepsDone){setTimeout(function(){symmetryUtil.monitors.healthCheck.getResults("SpeedTest",1).success(p).error(e)},5000)}else{var o=RAINIER.network.isBehindNode()?"node":"router";switch(n.stepNumber){case 1:break;case 2:r.text(c.download.action);q.text(c.download.description[o]);k("download");break;case 3:r.text(c.upload.action);q.text(c.upload.description[o]);k("upload");break;default:e()}}}function l(n){g.find("footer .cancel").click(m.onClose);if(RAINIER.network.isHealthCheckerSupported()){RAINIER.deviceManager.getAuthorityDevice(function(q){var o,p=RAINIER.ui.getDeviceIcon(q);i.show();i.find("#retry-speedtest").unbind("click").click(function(){d()});if(RAINIER.network.isBehindNode()){o=q.friendlyName()}else{o=q.modelNumber()}i.find(".device > label").text(o);RAINIER.event.fire("applet.loaded");d()})}else{g.find("header h4").hide();g.find(".embeddedNotSupported").show();if(!n||RAINIER.network.isAuthorityRemote()){b(n)}else{j.attr("src",h());g.find("#speedtestSWF").css("display","inline")}RAINIER.event.fire("applet.loaded")}}RAINIER.network.checkInternetConnection(l)}());