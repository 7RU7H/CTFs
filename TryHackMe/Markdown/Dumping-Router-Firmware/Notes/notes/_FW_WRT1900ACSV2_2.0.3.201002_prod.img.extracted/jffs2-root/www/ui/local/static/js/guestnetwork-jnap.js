window.RAINIER=window.RAINIER||{};RAINIER.applets=RAINIER.applets||{};RAINIER.applets.guestNetwork=(function(){function a(g,f,h){var j=RAINIER.jnapActions.getGuestRadioSettings();var i={action:j,data:{},cb:function(k){if(k){if(k.result==="OK"){g=k.output;g.radio={};if(RAINIER.shared.util.areServicesSupported(["/jnap/guestnetwork/GuestNetwork3"])){$.each(g.radios,function(){switch(this.radioID){case"RADIO_2.4GHz":g.radio.band24=this;break;case"RADIO_5GHz":g.radio.band50=this;break}});g.canEnableGuestNetwork=g.radio.band24.canEnableRadio||g.radio.band50.canEnableRadio}else{g.radio.band24={radioID:"RADIO_2.4GHz",isEnabled:g.isGuestNetworkEnabled,broadcastGuestSSID:g.broadcastGuestSSID,guestSSID:g.guestSSID,guestPassword:g.guestPassword,canEnableRadio:g.canEnableRadio}}if(typeof f==="function"){f(g)}}else{console.warn(j+' : result is not "OK".',k)}}else{}}};if(h){h.add(i)}else{RAINIER.jnap.send(i)}}function d(g,i,n,l,m){var j=RAINIER.jnapActions.setGuestRadioSettings(),k={isGuestNetworkEnabled:g.isGuestNetworkEnabled,maxSimultaneousGuests:parseInt(g.maxSimultaneousGuests,10),guestSSID:g.radio.band24.guestSSID,guestPassword:g.radio.band24.guestPassword,broadcastGuestSSID:g.isGuestNetworkEnabled},f;if(!symmetryUtil.guestAccess.isCaptivePortal(g)){$.delAttr(k,"maxSimultaneousGuests")}l=!!l;m=!!m;if(RAINIER.shared.util.areServicesSupported(["/jnap/guestnetwork/GuestNetwork3"])){$.delAttr(k,["guestSSID","guestPassword","broadcastGuestSSID","canEnableGuestNetwork"]);k.radios=[];if(g.radio.band24){if(RAINIER.network.isBehindNode()){f={radioID:"RADIO_2.4GHz",isEnabled:g.isGuestNetworkEnabled,broadcastGuestSSID:g.isGuestNetworkEnabled,guestSSID:g.radio.band24.guestSSID,guestWPAPassphrase:g.radio.band24.guestWPAPassphrase};k.radios.push(f)}else{k.radios.push({radioID:"RADIO_2.4GHz",isEnabled:g.radio.band24.isEnabled,broadcastGuestSSID:g.radio.band24.isEnabled,guestSSID:g.radio.band24.guestSSID,guestPassword:g.radio.band24.guestPassword})}}if(RAINIER.network.isBehindNode()){k.radios.push($.extend({radioID:"RADIO_5GHz"},_.omit(f,"radioID")))}else{if(g.radio.band50){k.radios.push({radioID:"RADIO_5GHz",isEnabled:g.radio.band50.isEnabled,broadcastGuestSSID:g.radio.band50.isEnabled,guestSSID:g.radio.band50.guestSSID,guestPassword:g.radio.band24.guestPassword})}}}var h={action:j,data:k,cb:function(p){if(!l){RAINIER.ui.hideWaiting()}if(p){if(p.result==="OK"){if(RAINIER.network.isBehindNode()||!RAINIER.shared.util.areServicesSupported(["/jnap/guestnetwork/GuestNetwork3"])){g.radio.band24.isEnabled=g.isGuestNetworkEnabled}RAINIER.event.fire("guestNetwork.updated",g)}else{console.warn(j+' : result is not "OK".',p)}}else{}if(typeof i==="function"){i(p)}}};if(!l){RAINIER.ui.showWaiting()}if(m){h.disableDefaultAjaxErrHandler=true;h.disableDefaultRebootErrHandler=true}if(n){n.add(h)}else{RAINIER.jnap.send(h)}}function c(f,i){var h=false;function g(j,k){return(j.settings&&j.settings.ssid===k)}if(f.isGuestNetworkEnabled){$.each(i,function(k,j){$.each(f.radio,function(m,l){if((f.radio.band24&&!f.radio.band50)||RAINIER.network.isBehindNode()){l.isEnabled=f.isGuestNetworkEnabled}if(!h&&l.isEnabled){h=g(j,l.guestSSID)}if(h){return false}});if(h){return false}})}return h}function e(g,f){var h=symmetryUtil.guestAccess.isCaptivePortal(g.guestSettings);return(!!g.guestSettings.radio.band24&&(f.guestSettings.radio.band24.isEnabled!==g.guestSettings.radio.band24.isEnabled||f.guestSettings.radio.band24.guestSSID!==g.guestSettings.radio.band24.guestSSID||(h&&f.guestSettings.radio.band24.guestPassword!==g.guestSettings.radio.band24.guestPassword)||(!h&&f.guestSettings.radio.band24.guestWPAPassphrase!==g.guestSettings.radio.band24.guestWPAPassphrase)))}function b(g,f){return(!!g.guestSettings.radio.band50&&(f.guestSettings.radio.band50.isEnabled!==g.guestSettings.radio.band50.isEnabled||f.guestSettings.radio.band50.guestSSID!==g.guestSettings.radio.band50.guestSSID))}return{getSettings:a,setSettings:d,isGuestSSIDConflict:c,isBand24Changing:e,isBand50Changing:b}}());