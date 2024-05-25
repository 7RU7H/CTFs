(function(){var c=$("#usb-storage-widget");var d=RAINIER.applets.USBStorage;var b={none:0,one:1,multiple:2};function i(){if(!RAINIER.network.storageSupported()){RAINIER.connect.AppletManager.hideWidget("A2DB16C0-59B9-4C79-9BF2-E5A3A307F9C1","26BDD899-C17B-4032-B21C-3767AAB4FCCA");return}c.find(".app-link").click(function(){f("status")});c.find(".app-ftp-link").click(function(){f("ftp-server")});if(d.mediaServerSupported()){c.find(".app-media-link").click(function(){f("media-server")})}else{c.find("#media-server-stats").css("visibility","hidden")}c.find(".refresh-usb").click(g);RAINIER.ui.progressBar.build("usb-storage-widget div.progress-bar");RAINIER.event.connect("storage.updated",function(){g()});c.find("[tooltip]").each(function(){$(this).attr("tooltip",$(this).text())});RAINIER.ui.tooltip.add(c.find("[tooltip]"),"top");g()}i();function f(l){var k=RAINIER.connect.AppletManager.getAppletById("A2DB16C0-59B9-4C79-9BF2-E5A3A307F9C1");if(k){RAINIER.ui.MainMenu.launchApplet(k,l,"usb-storage")}else{console.warn("Widget: cannot locate external storage applet")}}function e(k,m){$(c.find(m+" .used-space-label")).html(d.computeUsedSpaceLabel(k.usedKB,k.availableKB));var l=(k.usedKB/(k.usedKB+k.availableKB))*100;RAINIER.ui.progressBar.update({element:$(c.find(m+" div.progress-bar")),percent:l})}function j(l,k){$(c.find("#drive-loading")).hide();if(l===b.one){setTimeout(function(){$(c.find("#drive-off")).hide();$(c.find("#drive-unsupported")).hide();$(c.find("#drive-on-single-partition")).show();$(c.find("#drive-on-multiple-partitions")).hide()},0)}else{if(l===b.multiple){setTimeout(function(){$(c.find("#drive-off")).hide();$(c.find("#drive-unsupported")).hide();$(c.find("#drive-on-multiple-partitions")).show();$(c.find("#drive-on-single-partition")).hide()},0)}else{if(k){setTimeout(function(){$(c.find("#drive-off")).hide();$(c.find("#drive-unsupported")).show();$(c.find("#drive-on-single-partition")).hide();$(c.find("#drive-on-multiple-partitions")).hide()},0)}else{setTimeout(function(){$(c.find("#drive-off")).show();$(c.find("#drive-unsupported")).hide();$(c.find("#drive-on-single-partition")).hide();$(c.find("#drive-on-multiple-partitions")).hide()},0)}}}}function g(){var k="/jnap/storage/GetPartitions";if(!RAINIER.shared.util.areServicesSupported(["/jnap/storage/Storage2"])){k="/jnap/storage/GetMountedPartitions"}a();RAINIER.jnap.send({action:k,data:{},cb:function(p){var m=b.none,l=[],n=false;if(p.result==="OK"){l=$.grep(p.output.partitions,function(o){return o.fileSystem!=="Unsupported"&&o.partitionTableFormat!=="Unsupported"});n=(p.output.partitions.length!==l.length);m=(l.length<=0)?b.none:(l.length===1)?b.one:b.multiple;if(m===b.one){e(l[0],"#partition-space");h()}else{if(m===b.multiple){e(l[0],"#partition-space-one");e(l[1],"#partition-space-two")}}}if(m!==b.one||n){setTimeout(function(){j(m,n)},1000)}}})}function a(){$(c.find("#drive-loading")).show();$(c.find("#drive-off")).hide();$(c.find("#drive-unsupported")).hide();$(c.find("#drive-on-single-partition")).hide()}function h(){var k=RAINIER.jnap.Transaction({onComplete:function(){j(b.one)}});k.add({action:"/jnap/storage/GetFTPServerSettings",data:{},cb:function(l){if(l.result==="OK"){if(l.output.isEnabled){$(c.find("#ftp-stats .on-state")).show();$(c.find("#ftp-stats .off-state")).hide()}else{$(c.find("#ftp-stats .on-state")).hide();$(c.find("#ftp-stats .off-state")).show()}}}});if(d.mediaServerSupported()){k.add({action:"/jnap/storage/GetUPnPMediaServerSettings",data:{},cb:function(l){if(l.result==="OK"){if(l.output.isEnabled){$(c.find("#media-server-stats .on-state")).show();$(c.find("#media-server-stats .off-state")).hide()}else{$(c.find("#media-server-stats .on-state")).hide();$(c.find("#media-server-stats .off-state")).show()}}}})}k.send()}}());