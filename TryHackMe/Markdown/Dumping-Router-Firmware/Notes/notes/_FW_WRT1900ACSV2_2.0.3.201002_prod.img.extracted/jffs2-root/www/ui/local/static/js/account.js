var Account=(function(){var a=RAINIER.ui.build,c=RAINIER.ui.inputBalloon;function e(){if(!window.location.search){return null}var b=RAINIER.util.getUrlVars(window.location.href);if(!b.client_id){return null}return[{name:"X-Linksys-Client-Type-Id",value:b.client_id}]}return{create:function(f,g,b){RAINIER.cloud.send({url:"/cloud/user-service/rest/accounts/u",type:"POST",data:f,cb:g,cbError:function(i){var h=RAINIER.util.parseJSON(i.responseText);if(h&&h.errors){if(b){return b(h.errors[0].error)}else{RAINIER.ui.alert(h.errors[0].error.message);return false}}}})},validate:function(b,i,g){var f={verification:{status:"ACCEPTED",parameters:[]}};var h={};if(g){f.verification.parameters.push(g)}h["X-Linksys-Client-Type-Id"]=RAINIER.shared.util.getCiscoHNClientID();h.accept="application/json";jQuery.ajax({headers:h,type:"PUT",data:JSON.stringify(f),url:"/cloud/user-service/rest/verifications/"+b+"/status/",success:i,contentType:"application/json; charset=UTF-8",dataType:"json",error:function(k){var j=RAINIER.util.parseJSON(k.responseText);if(k.status===409&&k.statusText==="Conflict"||k.status===404&&k.statusText==="Not Found"){if(j.errors&&j.errors[0]&&j.errors[0].error.code==="PASSWORD_REUSED"){RAINIER.ui.alert(RAINIER.login.strings.changePasswordReusedPassword,RAINIER.login.strings.changePasswordTokenInvalidTitle)}else{RAINIER.ui.alert(RAINIER.login.strings.changePasswordTokenInvalid,RAINIER.login.strings.changePasswordTokenInvalidTitle,function(){window.top.location.href="/"})}}else{if(k.status===400&&k.statusText==="Bad Request"){RAINIER.ui.alert(RAINIER.login.strings.changePasswordTokenExpired,RAINIER.login.strings.changePasswordTokenInvalidTitle,function(){window.top.location.href="http://"+window.location.host.replace("linksysremotemanagement.com","linksyssmartwifi.com")+"/ui/dynamic/password-reset.html"})}else{var l=window.RAINIER.login.strings.unexpectedCloudError;if(j&&j.errors){l=j.errors[0].error.message}RAINIER.ui.alert(l)}}}})},loginErrorHandler:function(l){RAINIER.ui.wait(false);var f=null,n=RAINIER.login.strings,g=null,i=$("#username"),h=RAINIER.util.parseJSON(l.responseText);if(!h||!h.errors){RAINIER.ui.alert(window.RAINIER.login.strings.unexpectedCloudError);return false}g=h.errors[0].error;switch(l.status){case 400:switch(g.code){default:f=n.invalidCloudLogin;break}break;case 403:switch(g.code){case"ACCOUNT_DISABLED":f=n.accountDisabled;break;case"ACCOUNT_PENDING":function m(o){$("#email-not-validated #email-not-validated-content").removeClass("none sending sent failed");$("#email-not-validated #email-not-validated-content").addClass(o)}function k(){$("#email-not-validated .cancel, #email-not-validated .submit").removeAttr("disabled")}var j;var b={cancelClose:true,onSubmit:function(){var o={};o["X-Linksys-Client-Type-Id"]=RAINIER.shared.util.getCiscoHNClientID();o.accept="application/json";m("sending");$("#email-not-validated .cancel, #email-not-validated .submit").attr("disabled","disabled");jQuery.ajax({headers:o,type:"POST",data:JSON.stringify({verification:{type:"ACCOUNT_CREATION",parameters:[{parameter:{name:"username",value:$("#username").val()}}]}}),url:"/cloud/user-service/rest/verifications",success:function(){m("sent");$("#email-not-validated .cancel").removeAttr("disabled");setTimeout(function(){j.close()},4000)},contentType:"application/json; charset=UTF-8",dataType:"json",error:function(){m("failed");k()}})}};j=RAINIER.ui.dialog($("#email-not-validated"),b);m("none");k();j.show();return true;case"INVALID_ACCOUNT_CREDENTIALS":f=n.invalidCloudLogin;break;case"ACCOUNT_LOCKED_OUT":window.top.location.href="account-lockout.html";return true}break;case 404:switch(g.code){case"ACCOUNT_NOT_FOUND":f=n.accountNotFound;break}break;case 417:switch(g.code){case"ACCOUNT_SECURITY_LOCK":window.top.location.href="account-security-lock.html";return true}break}if(f!==null){c.add({els:i,isBlankOkOnBlur:false,balloonContent:a.DIV({"class":"flow"},[a.SPAN({"class":"icon warning"}),a.SPAN({"class":"msg"},f.replace("{username}",i.val()))])},true);c.hide(8000);return true}return false},onlineLogin:function d(h,g,i,f){if(!i){i=Account.loginErrorHandler}RAINIER.ui.wait();var b=e();$.cookie("current-network-id",null,{path:"/"});RAINIER.cloud.send({url:"/cloud/user-service/rest/v2/sessions",headers:b,type:"POST",data:{session:{account:{username:h,password:g}}},cb:function(k){var j=k.session.token;$.cookie("user-auth-token",j,{expires:null,path:"/"});$.cookie("admin-auth",null,{path:"/"});$.cookie("user-name",$("#remember-me:checked").length>0?h:"",{expires:new Date().addYears(10),path:"/"});RAINIER.network.discoverAuthority(function(){if(RAINIER.network.isBehindAuthority()&&!RAINIER.network.getAuthorityNetworkID()){var m=$.cookie("rp");$.cookie("rp",null,{path:"/"});if(m){m=window.atob(m);RAINIER.deviceManager.getAuthorityDevice(function l(n){RAINIER.jnap.send({action:"/jnap/ownednetwork/SetNetworkOwner",data:{ownerSessionToken:$.cookie("user-auth-token"),friendlyName:n.friendlyName()},cb:function(o){if(o.result==="OK"){RAINIER.network.discoverAuthority(function(){if(RAINIER.network.isBehindAuthority()){if(!RAINIER.network.getAuthorityNetworkID()){window.location.replace("/ui/dynamic/welcome.html")}else{RAINIER.network.setRemoteSetting(true,function(){RAINIER.shared.ui.authenticatedGoToIndex(j)})}}else{window.location.replace("/ui/dynamic/no-associated-router.html")}})}else{window.location.replace("/ui/dynamic/welcome.html")}},disableDefaultJnapErrHandler:true,disableDefaultAjaxErrHandler:true,disableDefaultRebootErrHandler:true,useAdminAuth:true,adminPasswordOverride:"Basic "+window.btoa("admin:"+$.trim(m)),forceLocal:true})})}else{window.location.replace("/ui/dynamic/welcome.html")}}else{RAINIER.network.setRemoteSetting(true,function(){if(typeof f==="function"){f()}else{RAINIER.shared.ui.authenticatedGoToIndex(j)}})}})},cbError:i})}}}());