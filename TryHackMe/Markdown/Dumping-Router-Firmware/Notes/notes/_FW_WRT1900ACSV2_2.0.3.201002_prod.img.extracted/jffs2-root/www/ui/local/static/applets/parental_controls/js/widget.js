(function(){var e=$("#parental-controls.widget"),b={},d={},c=[],f=[],g=0;function j(){b.isParentalControlEnabled=e.find("#pcEnabled").is(":checked");if(b.isParentalControlEnabled!==d.isParentalControlEnabled){RAINIER.applets.ParentalControls.SetParentalControlSettings(b,m)}else{}}function l(o){g++;if(o){c.push(o)}if(g===f.length){a()}}function a(){if(c&&c.length>0){$("#pcDevices").children().remove();for(var q=0;q<c.length&&q<3;q++){var p=e.find("#pcDeviceTemplate > li").clone(),o=c[q].friendlyName();p.attr("tooltip",o);p.find(".device-icon").addClass(RAINIER.ui.getDeviceIcon(c[q]));p.find(".pcDeviceName").text(o);e.find("#pcDevices").append(p);RAINIER.ui.tooltip.add(p)}e.find("#pcDevicesList").css("display","block");e.find("#pcDevicesNone").css("display","none")}else{e.find("#pcDevicesList").css("display","none");e.find("#pcDevicesNone").css("display","block")}}function i(){var o;g=0;c=[];f=[];if(b.rules.length===0){a();return}for(o=0;o<b.rules.length;o++){if(b.rules[o].macAddresses.length>0){f.push(b.rules[o].macAddresses[0])}}for(o=0;o<f.length;o++){RAINIER.deviceManager.getDeviceByMACAddress(l,f[o],-1)}}function k(){if($(this).hasClass("disabled")){return false}var o=RAINIER.connect.AppletManager.getAppletById("6B749D0D-1E3B-4C2A-B0CE-D19EDA0225AC");if(o){RAINIER.ui.MainMenu.launchApplet(o)}else{console.warn("Widget: cannot locate Parental Controls applet")}}function m(o){b=o||b;d=$.extend(true,{},b);i();e.find("#pcEnabled").attr("checked",b.isParentalControlEnabled);if(b.isParentalControlEnabled){e.find("#on-state").css("display","block");e.find("#off-state").css("display","none")}else{e.find("#off-state").css("display","block");e.find("#on-state").css("display","none")}}function h(){RAINIER.applets.ParentalControls.GetParentalControlSettings(b,m)}function n(){if(!RAINIER.shared.util.areServicesSupported(["/jnap/parentalcontrol/ParentalControl"])){RAINIER.connect.AppletManager.hideWidget("6B749D0D-1E3B-4C2A-B0CE-D19EDA0225AC","6F46F21B-3C02-4F64-B954-95B800375512");return}e.find("#pcEnabled").click(j);e.find("#pcViewAll").click(k);RAINIER.event.connect("Parental Controls Updated",h);h()}n()}());