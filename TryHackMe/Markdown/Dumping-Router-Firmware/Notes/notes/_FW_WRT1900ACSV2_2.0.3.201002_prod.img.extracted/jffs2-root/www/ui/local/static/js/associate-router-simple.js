function recheckOwnedNetwork(a){RAINIER.jnap.send({action:"/jnap/ownednetwork/GetOwnedNetworkID",data:{},cb:function(b){if(b.result==="OK"){if(b.output.ownedNetworkID&&b.output.ownedNetworkID.length>0){RAINIER.shared.ui.authenticatedGoToIndex(RAINIER.shared.util.getCookie("user-auth-token"))}else{RAINIER.shared.ui.showGenericError()}}},disableDefaultJnapErrHandler:true,useAdminAuth:true,adminPasswordOverride:"Basic "+window.btoa("admin:"+a)})}function setNetworkOwner(b,a){RAINIER.jnap.send({action:"/jnap/ownednetwork/SetNetworkOwner",data:{ownerSessionToken:RAINIER.shared.util.getCookie("user-auth-token"),friendlyName:b},cb:function(c){if(c.result==="OK"){recheckOwnedNetwork(a)}else{switch(c.result){case"ErrorCloudUnavailable":showInputValidationError("passwordInputValidation",true,RAINIER.login.strings.cloudUnavailable);break;case"_ErrorUnauthorized":showInputValidationError("passwordInputValidation",true,RAINIER.login.strings.invalidRouterPassword);break;default:showInputValidationError("passwordInputValidation",true,RAINIER.login.strings.unexpectedError);break}showView("landing")}},disableDefaultJnapErrHandler:true,useAdminAuth:true,adminPasswordOverride:"Basic "+window.btoa("admin:"+a)})}function associateRouter(){var a=document.getElementById("routerPassword").value;if(!RAINIER.shared.util.isAdminPasswordValid(a)){showInputValidationError("passwordInputValidation",true,RAINIER.login.strings.invalidRouterPassword);return}showView("working");RAINIER.jnap.send({action:"/jnap/devicelist/GetDevices",data:{},cb:function(d){if(d.result==="OK"){var b=d.output.devices;for(var c=0;c<b.length;c++){if(b[c].isAuthority===true){setNetworkOwner(b[c].friendlyName,a);break}}}},disableDefaultJnapErrHandler:true,useAdminAuth:true,adminPasswordOverride:"Basic "+window.btoa("admin:"+a)})}window.onload=function(){showView("landing");try{document.getElementById("loginButton").onclick=associateRouter;document.getElementById("routerPassword").focus()}catch(a){}};