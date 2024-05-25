(function(){var Q=$("#security-applet"),aj=Q.find("#dmz"),r=Q.find("#dmz-dhcpclienttable"),aa=Q.find("#ddnsSettings"),C=aa.find("#ddnsProvider"),E=Q.find("#ddns-DynDNS"),j=Q.find("#ddns-TZO"),W=Q.find("#ddns-No-IP"),aA=null,o=RAINIER.ui.buildInputValidBalloon,i={description:"",externalPort:"",internalPort:"",protocol:"Both",internalServerLastOctet:"",isEnabled:true},ai={description:"",firstExternalPort:"",lastExternalPort:"",protocol:"Both",internalServerLastOctet:"",isEnabled:true},ab={description:"",firstTriggerPort:"",lastTriggerPort:"",firstForwardedPort:"",lastForwardedPort:"",isEnabled:true},v={description:"",isEnabled:true,portRanges:[{protocol:"Both",firstPort:"",lastPort:""}]},aE=null,w=null,S=null,f=false,b=true,h=true,M={validationType:"utf8LengthTooLong",maxLen:32,whiteSpace:false,isRequired:true},y={validCharSet:"numeric",minLen:1,maxLen:5,minVal:0,maxVal:65535,isRequired:true},al={validCharSet:"numeric",minLen:1,maxLen:3,validationType:"validHost",isComposite:true,isBlankOnBlur:false};var O,T,aq,aK;var z=Q.find("#immutableTemplateSinglePortForwardingRow"),p=null,A=Q.find("#immutableTemplatePortRangeForwardingRow"),s=null,aI=Q.find("#immutableTemplatePortRangeTriggeringRow"),aD=null,av=Q.find("#immutableTemplateIPv6FirewallRuleRow"),am=null,ay=RAINIER.ui.template.createTemplate(Q.find("#templateClientRow tr"));var G={devices:[],firewall:{},ipv6firewallrules:{rules:[]},dmz:{},ddnssettings:{},ddnsproviders:{},ddnsstatus:{},singleportforwarding:{rules:[]},portrangeforwarding:{rules:[]},portrangetriggering:{rules:[]},wan:{},lanSettings:{}};var ax={firewall:{},ipv6firewallrulesSetup:{},ipv6firewallrules:{rules:[]},dmz:{},ddnssettings:{},singleportforwardingSetup:{},singleportforwarding:{rules:[]},portrangeforwardingSetup:{},portrangeforwarding:{rules:[]},portrangetriggeringSetup:{},portrangetriggering:{rules:[]}};var aL={"dynDNSSettings.isWildcardEnabled":"boolean","dynDNSSettings.isMailExchangeEnabled":"boolean","dynDNSSettings.mailExchangeSettings.isBackup":"boolean",externalPort:"number",internalPort:"number",isEnabled:"boolean",firstPort:"number",lastPort:"number",firstExternalPort:"number",lastExternalPort:"number",firstTriggerPort:"number",lastTriggerPort:"number",firstForwardedPort:"number",lastForwardedPort:"number"};var L={};var aB=function(aT){var a3,a4,a6,aR,aW,aO,a8,aN,aU,a1,a7,aV;function a0(a9){a3=a9.elTable;a4=a9.elAddButton;a6=a9.rowTemplate;aO=a9.emptyRecord;a8=a9.maxRecords;aN=a9.getRowData;aU=a9.setRowData;a1=a9.saveAction;a7=a9.rowAddValidation;aV=a9.isValidRow;a4.click(function(){if(a3.find("tr:last-child").index()<a8){aR.rules.push(aO);aY(aO,true)}})}a0(aT);function aS(bb,ba,a9){aR=bb;aW=ba;if(!a9){RAINIER.ui.clearTbl(a3);$.each(aR.rules,function(){var bc=$.extend({},this);aY(bc,false)})}}function aQ(){$(this).addClass("edit");$(this).removeClass("hover")}function a5(){var ba=this.index()-1,a9=$(this);a9.removeClass("edit");if($.compare(aR.rules[ba],aO)){aR.rules.splice(ba,1);a9.remove()}else{RAINIER.binder.toDom(aU(aR.rules[ba]),a9)}RAINIER.ui.inputBalloon.hide()}function aM(){var a9=this.index()-1;aR.rules.splice(a9,1);$(this).hide();$(this).remove()}function aZ(){var ba=this.index()-1,a9=$(this);if(aV&&!aV(a9)){return false}var bb=aN(a9);aR.rules[ba]=bb;a9.removeClass("edit");RAINIER.binder.toDom(aU(bb),a9);return true}function aY(bb,ba){var a9=RAINIER.ui.template.createBlock(a6,aU(bb));if(ba){a9.addClass("edit")}if(a7){a7(a9)}a3.append(a9);a9.find("td").hover(function(){var bc=$(this).parent();if(!bc.hasClass("edit")){bc.addClass("hover")}},function(){$(this).parent().removeClass("hover")});a9.find(".edit").click(aQ.bind(a9));a9.find(".delete").click(aM.bind(a9));a9.find(".save").click(aZ.bind(a9));a9.find(".cancel").click(a5.bind(a9));RAINIER.util.trapEnter(a9.find("input[type=text]"))}function aP(){var a9=false;if(a3.find(".save:visible").length>0||!$.compare(aW,aR)){a9=true}return a9}function a2(){a3.find(".save:visible").click();if(a1){a1()}}function aX(){var a9=true;if(aV){a3.find(".save:visible").closest("tr").each(function(bb,ba){a9=a9&&aV($(ba))})}return a9}return{hasChanges:aP,isValid:aX,save:a2,load:aS}};var x={onDdnsServiceChange:function(){var aM=C.val();Q.find('label[id="ddnsstatus.status"]').empty();aa.removeClass("None No-IP DynDNS TZO").addClass(aM);RAINIER.ui.fixIeForm(aa)},onDMZEnableChange:function(){var aM=aj.find("#dmz-enable").is(":checked");if(aM){if(aj.find("#sourceRestrictionSpecific").is(":checked")){aj.find("#sourceRestrictionSpecific").click()}else{aj.find("#sourceRestrictionAny").click()}if(aj.find("#destinationMACAddress").is(":checked")){aj.find("#destinationMACAddress").click()}else{aj.find("#destinationIPAddress").click()}}else{RAINIER.util.disable(aj.find("input[type=text]"),true)}RAINIER.util.disable(aj.find("#dmz-viewdhcpclienttable"),!aM);aj.find('[name="dmz.source"],[name="dmz.destination"]').prop("disabled",!aM).trigger("change")},onDMZSourceChange:function(){RAINIER.util.disable("#dmz-specified-range input",this.id==="sourceRestrictionAny");if(this.id==="sourceRestrictionSpecific"){L.dmz.sourceIpAddress.firstIPAddress.enable();L.dmz.sourceIpAddress.lastIPAddress.enable()}else{L.dmz.sourceIpAddress.firstIPAddress.disable();L.dmz.sourceIpAddress.lastIPAddress.disable()}},onDMZDestinationChange:function(){var aM=this.id==="destinationIPAddress";RAINIER.util.disable("#dmz-destinationIPAddress input",!aM);RAINIER.util.disable("#destination-mac-address input",aM)},onMACAddressSelected:function(){var aM={dmz:{destinationMACAddress:$(this).parents("tr").find('td[name="macAddress"]').text()}};RAINIER.binder.toDom(aM,aj,aL);aj.find("#destinationMACAddress").click();aA.close()},onClose:function(){RAINIER.ui.MainMenu.closeApplet()},onApply:function(){B()},onSave:function(){B(true)},onSaveComplete:function(){RAINIER.ui.hideWaiting();if(h){if(b){x.onClose()}else{if(f){S.click()}}}}};var Z={ddns2:false};function m(aO,aN){var aP=parseInt(aO.val());var aM=parseInt(aN.val());return isNaN(aP)||isNaN(aM)||aP<=aM}function ae(aO,aM){var aN=RAINIER.util.dot2num(aM);return aN===0||aN>=RAINIER.util.dot2num(aO)}function D(aM){var aQ=RAINIER.binder.fromDom(aM.parent()),aP=G.lanSettings.ipAddress.split("."),aN=aQ[aM.attr("name")].split(".");for(var aO=0;aO<4;aO++){if(aP[aO]!==aN[aO]){return true}}return false}function B(aN){var aM=false;b=aN||false;h=true;switch(U()){case"firewall":if(q()){aM=true;if(I()){O.save();ao()}}break;case"dmz":if(J()){aM=true;if(u()){ah()}}break;case"app-and-gaming":if(aw()){aM=true;if(aJ()){c()}}break}if(b&&!aM){x.onClose()}}function U(){return Q.find("section.tab-section .tab-content:visible").attr("id")}function R(){return Q.find("section.tab-section .tab-content:visible .sub-tab-content:visible").attr("id")}function X(){var aM=false;switch(U()){case"firewall":aM=q();break;case"dmz":aM=J();break;case"app-and-gaming":aM=aw();break}if(aM){aE.show()}return aM}function l(){switch(U()){case"firewall":ak();break;case"dmz":af();break;case"app-and-gaming":Y();break}}function H(aM){aM.add({action:"/jnap/firewall/GetFirewallSettings",data:{},cb:function(aN){if(aN.output){G.firewall=aN.output;G.firewall.enableIPSec=!G.firewall.blockIPSec;G.firewall.enablePPTP=!G.firewall.blockPPTP;G.firewall.enableL2TP=!G.firewall.blockL2TP;delete G.firewall.blockIPSec;delete G.firewall.blockPPTP;delete G.firewall.blockL2TP}else{}}});aM.add({action:"/jnap/firewall/GetIPv6FirewallRules",data:{},cb:function(aN){if(aN.output){G.ipv6firewallrules=aN.output;ax.ipv6firewallrules=$.extend(true,{},aN.output);am=RAINIER.ui.template.createTemplate(av.find("tbody").html().replace("{maxDescriptionLength}",G.ipv6firewallrules.maxDescriptionLength));ax.ipv6firewallrulesSetup.maxPortRanges=G.ipv6firewallrules.maxPortRanges;ax.ipv6firewallrulesSetup.maxDescriptionLength=G.ipv6firewallrules.maxDescriptionLength;ax.ipv6firewallrulesSetup.maxRules=G.ipv6firewallrules.maxRules;$.delAttr(G.ipv6firewallrules,"maxPortRanges","maxDescriptionLength","maxRules");$.delAttr(ax.ipv6firewallrules,"maxPortRanges","maxDescriptionLength","maxRules")}else{}}})}function ak(){RAINIER.binder.toDom(G,$("#firewall"),aL);ax.ipv6firewallrules=$.extend(true,{},G.ipv6firewallrules);O.load(ax.ipv6firewallrules,G.ipv6firewallrules)}function I(){return O.isValid()}function e(){O=aB({elTable:Q.find("#ipv6firewallrules-list"),elAddButton:Q.find("#add-ipv6firewall"),rowTemplate:am,emptyRecord:v,maxRecords:ax.ipv6firewallrulesSetup.maxRules,getRowData:function(aM){var aP={description:$.trim(aM.find('input[name="description"]').val()),ipv6Address:$.trim(aM.find(".ipv6firewall-ipv6Address").val()),isEnabled:aM.find(".rule-enabled").is(":checked")};var aQ=$.trim(aM.find('select[name="protocol"]').val()),aO=$.trim(aM.find(".ipv6firewall-firstPort").val()),aN=$.trim(aM.find(".ipv6firewall-lastPort").val());aP.portRanges=[{protocol:aQ,firstPort:parseInt(aO,10),lastPort:parseInt(aN,10)}];$.delAttr(aP,"isEnabledLabel","protocolLabel");return aP},setRowData:function(aN){var aM=$.extend(true,{},aN);aM.protocol=aN.portRanges[0].protocol;aM.protocolLabel=RAINIER.ui.common.strings.ipProtocol[aN.portRanges[0].protocol];aM.firstPort=aN.portRanges[0].firstPort;aM.lastPort=aN.portRanges[0].lastPort;aM.isEnabledLabel=aN.isEnabled?RAINIER.ui.common.strings["true"]:RAINIER.ui.common.strings["false"];delete aM.portRanges;return aM},rowAddValidation:function(aM){var aO={maxLen:63,validationType:"ipv6Address",isRequired:true},aN={};aN.description=o($.extend(true,{},M,{els:aM.find('input[name="description"]')}));aN.ipv6address=o($.extend(true,{},aO,{els:aM.find(".ipv6firewall-ipv6Address")}));aN.ports=o($.extend(true,{},y,{els:aM.find(".ipv6firewall-firstPort, .ipv6firewall-lastPort"),errors:[{test:m.curry(aM.find(".ipv6firewall-firstPort"),aM.find(".ipv6firewall-lastPort")),when:false,message:RAINIER.ui.validation.strings.security.portRangeError}]}));aM.data("checks",aN)},isValidRow:function(aM){var aN=aM.data("checks");return aN.description.isValid(true)&&aN.ipv6address.isValid(true)&&aN.ports.isValid(true)}});if(RAINIER.network.isBridgeMode()){Q.find("#firewall-ipv4-enable").attr("disabled",true);Q.find("#firewall-ipv6-enable").attr("disabled",true);Q.find("#blockAnonymous").attr("disabled",true);Q.find("#blockMulticast").attr("disabled",true);Q.find("#blockNASRedirect").attr("disabled",true);Q.find("#blockIDENT").attr("disabled",true)}ak()}function ac(){var aM=RAINIER.binder.fromDom($("#firewall"),aL);ax.firewall=aM.firewall}function q(){var aM=false;ac();if(!$.compare(ax.firewall,G.firewall)||O.hasChanges()){aM=true}return aM}function ao(){var aM=RAINIER.jnap.Transaction({onComplete:x.onSaveComplete,disableDefaultJnapErrHandler:true});if(!$.compare(G.firewall,ax.firewall)){var aN=$.extend(true,{},ax.firewall),aP="/jnap/firewall/SetFirewallSettings";aN.blockIPSec=!aN.enableIPSec;aN.blockPPTP=!aN.enablePPTP;aN.blockL2TP=!aN.enableL2TP;delete aN.enableIPSec;delete aN.enablePPTP;delete aN.enableL2TP;aM.add({action:aP,data:aN,cb:function(aQ){if(aQ.result==="OK"){G.firewall=$.extend(true,{},ax.firewall)}else{if(aQ.result!=="_AjaxError"){RAINIER.ajax().executeDefaultJnapErrorHandler(aQ,aP)}h=false}}})}else{}if(O.hasChanges()){var aO="/jnap/firewall/SetIPv6FirewallRules";aM.add({action:aO,data:ax.ipv6firewallrules,cb:function(aQ){if(aQ.result==="OK"){G.ipv6firewallrules=$.extend(true,{},ax.ipv6firewallrules);O.load(ax.ipv6firewallrules,G.ipv6firewallrules,true)}else{if(aQ.result!=="_AjaxError"){if(aQ.result==="ErrorRulesOverlap"){RAINIER.ui.alert("",RAINIER.ui.validation.strings.errors[aQ.result])}else{RAINIER.ajax().executeDefaultJnapErrorHandler(aQ,aO)}}h=false}}})}else{}RAINIER.ui.showWaiting();aM.send()}function t(aM){aM.add({action:"/jnap/router/GetLANSettings",data:{},cb:function(aP){if(aP.result==="OK"){if(aP.output){var aO=aP.output.ipAddress.split(".");for(var aN=1;aN<=aP.output.minNetworkPrefixLength/8;aN++){aj.find('div[name="dmz.destinationIPAddress"]').find("input:nth-child("+aN+")").replaceWith("<label>"+aO[aN-1]+"</label>")}}else{}}else{}}});aM.add({action:"/jnap/firewall/GetDMZSettings",data:{},cb:function(aN){if(aN.result==="OK"){if(aN.output){G.dmz=aN.output;if(G.dmz.isDMZEnabled&&G.dmz.sourceRestriction&&G.dmz.sourceRestriction.firstIPAddress&&typeof G.dmz.sourceRestriction.lastIPAddress==="undefined"){G.dmz.sourceRestriction.lastIPAddress=G.dmz.sourceRestriction.firstIPAddress}}else{}}else{}}})}function af(){RAINIER.binder.toDom(G,aj,aL);var aM=aj.find("#dmz-enable").is(":checked");if(aM){aj.find(":input:not(input[type=checkbox])").removeAttr("disabled");if(G.dmz.sourceRestriction){aj.find("#sourceRestrictionSpecific").click()}else{aj.find("#sourceRestrictionAny").click()}if(G.dmz.destinationIPAddress){aj.find("#destinationIPAddress").click()}else{aj.find("#destinationMACAddress").click()}}else{if(aj.find('input[name="dmz.source"]:checked').index()===-1){aj.find("#sourceRestrictionAny").attr("checked","checked")}if(aj.find('input[name="dmz.destination"]:checked').index()===-1){aj.find("#destinationIPAddress").attr("checked","checked")}RAINIER.util.disable(aj.find("input[type=text]"),true)}RAINIER.util.disable(aj.find('[name="dmz.source"],[name="dmz.destination"]'),!aM)}function d(){RAINIER.util.trapEnter(aj.find("input[type=text]"));aj.find("#sourceRestrictionAny").click(x.onDMZSourceChange);aj.find("#sourceRestrictionSpecific").click(x.onDMZSourceChange);aj.find("#destinationIPAddress").click(x.onDMZDestinationChange);aj.find("#destinationMACAddress").click(x.onDMZDestinationChange);aj.find("#dmz-enable").change(x.onDMZEnableChange);aj.find("#dmz-viewdhcpclienttable").click(function(){aA.show()});r.find(".close-button").click(function(){aA.close()});r.find(".refresh").click(function(){ad()})}function au(){var aO={minLen:2,maxLen:2,validationType:"macAddress",validCharSet:"hex",isRequired:true,isBlankOkOnBlur:false},aN={maxLen:3,validationType:"ipAddress",validCharSet:"numeric",isComposite:true},aM={sourceIpAddress:{}};aM.sourceIpAddress.firstIPAddress=o($.extend(true,{},aN,{elContainer:aj.find('div:[name="dmz.sourceRestriction.firstIPAddress"]'),allowZeroes:false}));aM.sourceIpAddress.lastIPAddress=o($.extend(true,{},aN,{elContainer:aj.find('div:[name="dmz.sourceRestriction.lastIPAddress"]'),allowZeroes:false}));aM.sourceIpAddress.range=o($.extend(true,{},{els:aj.find('div[name="dmz.sourceRestriction.firstIPAddress"], div[name="dmz.sourceRestriction.lastIPAddress"]'),errors:[{test:ae.curry(aj.find('div[name="dmz.sourceRestriction.firstIPAddress"]'),aj.find('div[name="dmz.sourceRestriction.lastIPAddress"]')),when:false,message:RAINIER.ui.validation.strings.security.ipRangeError}]}));aM.destinationIPAddress=o($.extend(true,{},al,{elContainer:aj.find('div[name="dmz.destinationIPAddress"]'),elInputSubnetMask:RAINIER.util.fromPrefixLengthToSubnet(G.lanSettings.networkPrefixLength),elInputRouterIPAddress:G.lanSettings.ipAddress}));aM.destinationMACAddress=o($.extend(true,{},aO,{els:aj.find('#destination-mac-address input[type="text"]'),elContainer:aj.find("#destination-mac-address")}));L.dmz=aM}function u(){var aN=true,aM=L.dmz;if(aj.find("#dmz-enable").is(":checked")){if(aj.find("#sourceRestrictionSpecific").is(":checked")){aN=aN&&aM.sourceIpAddress.firstIPAddress.isValid(true);aN=aN&&aM.sourceIpAddress.lastIPAddress.isValid(true);aN=aN&&aM.sourceIpAddress.range.isValid(true)}if(aj.find("#destinationIPAddress").is(":checked")){aN=aN&&aM.destinationIPAddress.isValid(true)}else{aN=aN&&aM.destinationMACAddress.isValid(true)}}return aN}function a(){aj.find("#destination-mac-address").find(" > input").val("00").attr("defaultValue","00");af();d();au()}function P(){if(aj.find("#dmz-enable").is(":checked")){ax.dmz={isDMZEnabled:true};if(aj.find("#sourceRestrictionSpecific").is(":checked")){$.extend(true,ax,RAINIER.binder.fromDom(aj.find('div[name="dmz.sourceRestriction.firstIPAddress"]').parent()))}if(aj.find("#destinationIPAddress").is(":checked")){$.extend(true,ax,RAINIER.binder.fromDom(aj.find('div[name="dmz.destinationIPAddress"]').parent()))}else{$.extend(true,ax,RAINIER.binder.fromDom(aj.find('div[name="dmz.destinationMACAddress"]').parent()))}}else{ax.dmz={isDMZEnabled:false}}}function J(){var aM=false;P();if(!$.compare(G.dmz,ax.dmz)){aM=true}return aM}function ah(){RAINIER.ui.showWaiting();RAINIER.jnap.send({action:"/jnap/firewall/SetDMZSettings",data:ax.dmz,cb:function(aM){if(aM.result==="OK"){G.dmz=$.extend(true,{},ax.dmz)}else{h=false}x.onSaveComplete()}})}function V(aN){RAINIER.ui.clearTbl(r);G.devices=aN;for(var aP=0;aP<G.devices.length;aP++){for(var aO=0;aO<G.devices[aP].data.connections.length;aO++){var aQ={friendlyName:G.devices[aP].friendlyName(),ipAddress:G.devices[aP].data.connections[aO].ipAddress,macAddress:G.devices[aP].data.connections[aO].macAddress,select:RAINIER.ui.button.strings.select};if(G.devices[aP].data.connections[aO].wireless){aQ.wireless=RAINIER.ui.common.strings.netType.wireless}else{aQ.wireless=RAINIER.ui.common.strings.netType.lan}var aM=RAINIER.ui.template.createBlock(ay,aQ);aM.find(".submit").click(x.onMACAddressSelected);r.find("tbody").append(aM)}}}function ad(){RAINIER.deviceManager.getDevices(V,{excludeAuthority:true},-1)}function aH(aM){aM.add({action:"/jnap/router/GetLANSettings",data:{},cb:function(aN){if(aN.result==="OK"){if(aN.output){G.lanSettings=aN.output}else{}}else{}}});aM.add({action:RAINIER.jnapActions.getWANStatus(),data:{},cb:function(aN){if(aN.result==="OK"){if(aN.output){G.wan=aN.output}else{}}else{}}});aM.add({action:"/jnap/ddns/GetDDNSSettings",data:{},cb:function(aN){if(aN.result==="OK"){if(aN.output){G.ddnssettings=aN.output}else{}}else{}}});if(Z.ddns2){aM.add({action:"/jnap/ddns/GetSupportedDDNSProviders",data:{},cb:function(aN){if(aN.result==="OK"){if(aN.output){G.ddnsproviders=aN.output.supportedDDNSProviders}else{}}else{}}})}aM.add({action:"/jnap/firewall/GetSinglePortForwardingRules",data:{},cb:function(aP){if(aP.result==="OK"){if(aP.output){G.singleportforwarding=aP.output;ax.singleportforwarding=$.extend(true,{},aP.output);var aO=G.lanSettings.ipAddress.split(".");for(var aN=1;aN<=G.lanSettings.minNetworkPrefixLength/8;aN++){$(z).find("span.spEdit > div.ip-input").find("input:nth-child("+aN+")").replaceWith("<label>"+aO[aN-1]+"</label>")}p=RAINIER.ui.template.createTemplate(z.find("tbody").html().replace("{maxDescriptionLength}",G.singleportforwarding.maxDescriptionLength));ax.singleportforwardingSetup.maxRules=G.singleportforwarding.maxRules;ax.singleportforwardingSetup.maxDescriptionLength=G.singleportforwarding.maxDescriptionLength;$.delAttr(G.singleportforwarding,"maxDescriptionLength","maxRules");$.delAttr(ax.singleportforwarding,"maxDescriptionLength","maxRules")}else{}}else{}}});aM.add({action:"/jnap/firewall/GetPortRangeForwardingRules",data:{},cb:function(aP){if(aP.result==="OK"){if(aP.output){G.portrangeforwarding=aP.output;ax.portrangeforwarding=$.extend(true,{},aP.output);var aO=G.lanSettings.ipAddress.split(".");for(var aN=1;aN<=G.lanSettings.minNetworkPrefixLength/8;aN++){$(A).find("span.spEdit > div.ip-input").find("input:nth-child("+aN+")").replaceWith("<label>"+aO[aN-1]+"</label>")}s=RAINIER.ui.template.createTemplate(A.find("tbody").html().replace("{maxDescriptionLength}",G.portrangeforwarding.maxDescriptionLength));ax.portrangeforwardingSetup.maxRules=G.portrangeforwarding.maxRules;ax.portrangeforwardingSetup.maxDescriptionLength=G.portrangeforwarding.maxDescriptionLength;$.delAttr(G.portrangeforwarding,"maxDescriptionLength","maxRules");$.delAttr(ax.portrangeforwarding,"maxDescriptionLength","maxRules")}else{}}else{}}});aM.add({action:"/jnap/firewall/GetPortRangeTriggeringRules",data:{},cb:function(aN){if(aN.result==="OK"){if(aN.output){G.portrangetriggering=aN.output;ax.portrangetriggering=$.extend(true,{},aN.output);aD=RAINIER.ui.template.createTemplate(aI.find("tbody").html().replace("{maxDescriptionLength}",G.portrangetriggering.maxDescriptionLength));ax.portrangetriggeringSetup.maxRules=G.portrangetriggering.maxRules;ax.portrangetriggeringSetup.maxDescriptionLength=G.portrangetriggering.maxDescriptionLength;$.delAttr(G.portrangetriggering,"maxDescriptionLength","maxRules");$.delAttr(ax.portrangetriggering,"maxDescriptionLength","maxRules")}else{}}else{}}});aM.add({action:RAINIER.jnapActions.getDDNSStatus(),data:{},cb:function(aN){if(aN.result==="OK"){if(aN.output){G.ddnsstatus=aN.output;if(G.ddnsstatus&&G.ddnsstatus.status){G.ddnsstatus.status=RAINIER.ui.common.strings.ddnsStatus[G.ddnsstatus.status]}}else{}}else{}}})}function Y(){var aM=Z.ddns2?G.ddnsproviders:["DynDNS","TZO"],aN='<option value="{id}">{description}</option>';C.empty();$.each(aM,function(){var aO={id:this,description:RAINIER.ui.common.strings.ddnsProviders[this]};C.append(aN.formatObj(aO))});C.append(aN.formatObj({id:"None",description:RAINIER.ui.common.strings.disabled}));RAINIER.binder.toDom(G.wan,$("#ddns-common"),aL);switch(G.ddnssettings.ddnsProvider){case"DynDNS":RAINIER.binder.toDom(G.ddnssettings,E,aL);E.find("select").val(G.ddnssettings.dynDNSSettings.mode);if(G.ddnssettings.dynDNSSettings.isMailExchangeEnabled){if(G.ddnssettings.dynDNSSettings.mailExchangeSettings.isBackup){E.find("dynDNSSettings.mailExchangeSettings.isBackup").attr("checked","checked")}}if(G.ddnssettings.dynDNSSettings.isWildcardEnabled){E.find("dynDNSSettings.isWildcardEnabled").attr("checked","checked")}break;case"TZO":RAINIER.binder.toDom(G.ddnssettings,j,aL);break;case"No-IP":RAINIER.binder.toDom(G.ddnssettings,W,aL);break;case"None":break}RAINIER.binder.toDom(G.ddnssettings,C,aL);C.val(G.ddnssettings.ddnsProvider);aa.removeClass("None No-IP DynDNS TZO").addClass(G.ddnssettings.ddnsProvider);ap(G.ddnsstatus);ax.singleportforwarding=$.extend(true,{},G.singleportforwarding);T.load(ax.singleportforwarding,G.singleportforwarding);ax.portrangeforwarding=$.extend(true,{},G.portrangeforwarding);aq.load(ax.portrangeforwarding,G.portrangeforwarding);ax.portrangetriggering=$.extend(true,{},G.portrangetriggering);aK.load(ax.portrangetriggering,G.portrangetriggering)}function aG(){C.change(x.onDdnsServiceChange);Q.find("#ddns-update").click(function(){ar()});RAINIER.util.trapEnter(aa.find("input[type=text]"))}function N(){aF()}function aF(){var aS={maxLen:32,validCharSet:"ascii",whiteSpace:false,isRequired:true},aO={maxLen:32,validCharSet:"ascii",whiteSpace:false,isRequired:true},aR={maxLen:128,validCharSet:"domainName",whiteSpace:false,isRequired:true},aP={maxLen:62,validCharSet:"ascii",whiteSpace:false,isRequired:false},aN={validationType:"email",maxLen:32,whiteSpace:false,isRequired:true},aQ={maxLen:48,validCharSet:"domainName",whiteSpace:false,isRequired:true},aM={dyndns:{},tzo:{},noip:{}};aM.dyndns.username=o($.extend(true,{},aS,{els:E.find('input:[name="dynDNSSettings.username"]')}));aM.dyndns.password=o($.extend(true,{},aO,{els:E.find('input:[name="dynDNSSettings.password"]')}));aM.dyndns.hostName=o($.extend(true,{},aR,{els:E.find('input:[name="dynDNSSettings.hostName"]')}));aM.dyndns.mailExchange=o($.extend(true,{},aP,{els:E.find('input:[name="dynDNSSettings.mailExchangeSettings.hostName"]')}));aM.tzo.username=o($.extend(true,{},aN,{els:j.find('input:[name="tzoSettings.username"]')}));aM.tzo.password=o($.extend(true,{},aO,{els:j.find('input:[name="tzoSettings.password"]')}));aM.tzo.hostName=o($.extend(true,{},aQ,{els:j.find('input:[name="tzoSettings.hostName"]')}));aM.noip.username=o($.extend(true,{},aS,{els:W.find('input:[name="noipSettings.username"]')}));aM.noip.password=o($.extend(true,{},aO,{els:W.find('input:[name="noipSettings.password"]')}));aM.noip.hostName=o($.extend(true,{},aR,{els:W.find('input:[name="noipSettings.hostName"]')}));L.ddns=aM}function aJ(){var aN=true,aM;switch(R()){case"singleportforwarding":aN=aN&&T.isValid();break;case"portrangeforwarding":aN=aN&&aq.isValid();break;case"portrangetriggering":aN=aN&&aK.isValid();break;default:switch(ax.ddnssettings.ddnsProvider){case"DynDNS":aM=L.ddns.dyndns;aN=aN&&aM.username.isValid(true);aN=aN&&aM.password.isValid(true);aN=aN&&aM.hostName.isValid(true);aN=aN&&aM.mailExchange.isValid(true);break;case"TZO":aM=L.ddns.tzo;aN=aN&&aM.username.isValid(true);aN=aN&&aM.password.isValid(true);aN=aN&&aM.hostName.isValid(true);break;case"No-IP":aM=L.ddns.noip;aN=aN&&aM.username.isValid(true);aN=aN&&aM.password.isValid(true);aN=aN&&aM.hostName.isValid(true);break;default:break}break}return aN}function K(){function aM(aO){var aP=false,aN;switch(aO){case"portrangeforwarding":aN=Q.find("#portrangeforwarding-list tbody tr");break;case"portrangetriggering":aN=Q.find("#portrangetriggering-list tbody tr");break;default:aN=Q.find("#singleportforwarding-list tbody tr");break}$.each(aN,function(){var aQ=RAINIER.binder.fromDom($(this),aL),aR=aN.filter(function(){var aS=RAINIER.binder.fromDom($(this),aL);if($.isEmptyObject(aQ)||$.isEmptyObject(aS)){return 0}switch(aO){case"portrangeforwarding":return RAINIER.ui.validation.tests.doesRangeOverlap(aQ.firstExternalPort,aQ.lastExternalPort,aS.firstExternalPort,aS.lastExternalPort)&&(aQ.protocol===aS.protocol||aS.protocol==="Both");case"portrangetriggering":return RAINIER.ui.validation.tests.doesRangeOverlap(aQ.firstTriggerPort,aQ.lastTriggerPort,aS.firstTriggerPort,aS.lastTriggerPort)||RAINIER.ui.validation.tests.doesRangeOverlap(aQ.firstForwardedPort,aQ.lastForwardedPort,aS.firstForwardedPort,aS.lastForwardedPort);default:return parseInt(aQ.externalPort)===parseInt(aS.externalPort)&&(aQ.protocol===aS.protocol||aS.protocol==="Both")}});if(aR.length>1){aP=true}});return aP}T=aB({elTable:Q.find("#singleportforwarding-list"),elAddButton:Q.find("#add-singleportforwarding"),rowTemplate:p,emptyRecord:i,maxRecords:ax.singleportforwardingSetup.maxRules,saveAction:n,getRowData:function(aN){var aO=RAINIER.binder.fromDom(aN,aL);$.delAttr(aO,"isEnabledLabel","protocolLabel");return aO},setRowData:function(aO){var aN=$.extend(true,{},aO);aN.protocolLabel=RAINIER.ui.common.strings.ipProtocol[aO.protocol];aN.isEnabledLabel=(aO.isEnabled?RAINIER.ui.common.strings["true"]:RAINIER.ui.common.strings["false"]);return aN},rowAddValidation:function(aN){var aO={};aO.description=o($.extend(true,{},M,{els:aN.find('input[name="description"]')}));aO.ports=o($.extend(true,{},y,{els:aN.find(".forwarding-externalPort, .forwarding-internalPort")}));aO.internalServerIPAddress=o($.extend(true,{},al,{elContainer:aN.find('.spEdit > div[name="internalServerIPAddress"]'),elInputSubnetMask:RAINIER.util.fromPrefixLengthToSubnet(G.lanSettings.networkPrefixLength),elInputRouterIPAddress:G.lanSettings.ipAddress,errors:[{test:D.curry(aN.find('.spEdit > div[name="internalServerIPAddress"]')),when:false,message:RAINIER.ui.validation.strings.invalidIPAddress}]}));aO.rulesOverlap=o($.extend(true,{},y,{els:aN.find(".forwarding-externalPort"),errors:[{test:aM,when:true,message:RAINIER.ui.validation.strings.errors.ErrorRulesOverlap}]}));aN.data("checks",aO)},isValidRow:function(aN){var aO=aN.data("checks");return aO.description.isValid(true)&&aO.ports.isValid(true)&&aO.rulesOverlap.isValid(true)&&aO.internalServerIPAddress.isValid(true)}});aq=aB({elTable:Q.find("#portrangeforwarding-list"),elAddButton:Q.find("#add-portrangeforwarding"),rowTemplate:s,emptyRecord:ai,maxRecords:ax.portrangeforwardingSetup.maxRules,saveAction:az,getRowData:function(aN){var aO=RAINIER.binder.fromDom(aN,aL);$.delAttr(aO,"isEnabledLabel","protocolLabel");return aO},setRowData:function(aO){var aN=$.extend(true,{},aO);aN.protocolLabel=RAINIER.ui.common.strings.ipProtocol[aO.protocol];aN.isEnabledLabel=(aO.isEnabled?RAINIER.ui.common.strings["true"]:RAINIER.ui.common.strings["false"]);return aN},rowAddValidation:function(aN){var aO={};aO.description=o($.extend(true,{},M,{els:aN.find('input[name="description"]')}));aO.ports=o($.extend(true,{},y,{els:aN.find(".forwarding-firstExternalPort, .forwarding-lastExternalPort"),errors:[{test:m.curry(aN.find(".forwarding-firstExternalPort"),aN.find(".forwarding-lastExternalPort")),when:false,message:RAINIER.ui.validation.strings.security.portRangeError}]}));aO.internalServerIPAddress=o($.extend(true,{},al,{elContainer:aN.find('.spEdit > div[name="internalServerIPAddress"]'),elInputSubnetMask:RAINIER.util.fromPrefixLengthToSubnet(G.lanSettings.networkPrefixLength),elInputRouterIPAddress:G.lanSettings.ipAddress,errors:[{test:D.curry(aN.find('.spEdit > div[name="internalServerIPAddress"]')),when:false,message:RAINIER.ui.validation.strings.invalidIPAddress}]}));aO.rulesOverlap=o($.extend(true,{},y,{els:aN.find(".forwarding-firstExternalPort, .forwarding-lastExternalPort"),errors:[{test:aM.curry("portrangeforwarding"),when:true,message:RAINIER.ui.validation.strings.errors.ErrorRulesOverlap}]}));aN.data("checks",aO)},isValidRow:function(aN){var aO=aN.data("checks");return aO.description.isValid(true)&&aO.ports.isValid(true)&&aO.rulesOverlap.isValid(true)&&aO.internalServerIPAddress.isValid(true)}});aK=aB({elTable:Q.find("#portrangetriggering-list"),elAddButton:Q.find("#add-portrangetriggering"),rowTemplate:aD,emptyRecord:ab,maxRecords:ax.portrangetriggeringSetup.maxRules,saveAction:aC,getRowData:function(aN){var aO=RAINIER.binder.fromDom(aN,aL);$.delAttr(aO,"isEnabledLabel");return aO},setRowData:function(aO){var aN=$.extend(true,{},aO);aN.isEnabledLabel=(aO.isEnabled?RAINIER.ui.common.strings["true"]:RAINIER.ui.common.strings["false"]);return aN},rowAddValidation:function(aN){var aO={};aO.description=o($.extend(true,{},M,{els:aN.find('input[name="description"]')}));aO.triggerports=o($.extend(true,{},y,{els:aN.find(".triggering-firstTriggerPort, .triggering-lastTriggerPort"),errors:[{test:m.curry(aN.find(".triggering-firstTriggerPort"),aN.find(".triggering-lastTriggerPort")),when:false,message:RAINIER.ui.validation.strings.security.portRangeError}]}));aO.forwardedports=o($.extend(true,{},y,{els:aN.find(".triggering-firstForwardedPort, .triggering-lastForwardedPort"),errors:[{test:m.curry(aN.find(".triggering-firstForwardedPort"),aN.find(".triggering-lastForwardedPort")),when:false,message:RAINIER.ui.validation.strings.security.portRangeError}]}));aO.rulesOverlap=o($.extend(true,{},y,{els:aN.find(".triggering-firstTriggerPort, .triggering-lastTriggerPort, .triggering-firstForwardedPort, .triggering-lastForwardedPort"),errors:[{test:aM.curry("portrangetriggering"),when:true,message:RAINIER.ui.validation.strings.errors.ErrorRulesOverlap}]}));aN.data("checks",aO)},isValidRow:function(aN){var aO=aN.data("checks");return aO.description.isValid(true)&&aO.triggerports.isValid(true)&&aO.rulesOverlap.isValid(true)&&aO.forwardedports.isValid(true)}});Y();aG();N()}function ap(aM){RAINIER.binder.toDom(aM,Q.find("#ddns-common"),aL)}function ar(){F(ap)}function F(aM){RAINIER.jnap.send({action:RAINIER.jnapActions.getDDNSStatus(),data:{},cb:function(aN){if(aN.result==="OK"){if(aN.output){G.ddnsstatus=aN.output;if(G.ddnsstatus&&G.ddnsstatus.status){G.ddnsstatus.status=RAINIER.ui.common.strings.ddnsStatus[G.ddnsstatus.status]}}else{}if(typeof aM==="function"){aM(G.ddnsstatus)}}else{}}})}function k(){switch(R()){case"singleportforwarding":case"portrangeforwarding":case"portrangetriggering":break;default:var aM=RAINIER.binder.fromDom(aa,aL);ax.ddnssettings={ddnsProvider:aM.ddnsProvider};switch(aM.ddnsProvider){case"DynDNS":ax.ddnssettings.dynDNSSettings=aM.dynDNSSettings;if(!ax.ddnssettings.dynDNSSettings.mailExchangeSettings.hostName){ax.ddnssettings.dynDNSSettings.isMailExchangeEnabled=false;delete ax.ddnssettings.dynDNSSettings.mailExchangeSettings}else{ax.ddnssettings.dynDNSSettings.isMailExchangeEnabled=true}break;case"TZO":ax.ddnssettings.tzoSettings=aM.tzoSettings;break;case"No-IP":ax.ddnssettings.noipSettings=aM.noipSettings;break;default:break}break}}function aw(){var aM=false;k();switch(R()){case"singleportforwarding":aM=T.hasChanges();break;case"portrangeforwarding":aM=aq.hasChanges();break;case"portrangetriggering":aM=aK.hasChanges();break;default:if(!$.compare(G.ddnssettings,ax.ddnssettings)){aM=true}break}return aM}function c(){switch(R()){case"singleportforwarding":T.save();break;case"portrangeforwarding":aq.save();break;case"portrangetriggering":aK.save();break;default:an();break}}function an(){RAINIER.ui.showWaiting();RAINIER.jnap.send({action:"/jnap/ddns/SetDDNSSettings",data:ax.ddnssettings,cb:function(aM){if(aM.result==="OK"){G.ddnssettings=$.extend(true,{},ax.ddnssettings)}else{h=false}x.onSaveComplete()}})}function n(){var aM="/jnap/firewall/SetSinglePortForwardingRules";RAINIER.ui.showWaiting();RAINIER.jnap.send({action:aM,data:ax.singleportforwarding,cb:function(aN){if(aN.result==="OK"){G.singleportforwarding=$.extend(true,{},ax.singleportforwarding);T.load(ax.singleportforwarding,G.singleportforwarding,true)}else{if(aN.result!=="_AjaxError"){if(aN.result==="ErrorRulesOverlap"){RAINIER.ui.alert("",RAINIER.ui.validation.strings.errors[aN.result])}else{RAINIER.ajax().executeDefaultJnapErrorHandler(aN,aM)}}h=false}x.onSaveComplete()},disableDefaultJnapErrHandler:true})}function az(){var aM="/jnap/firewall/SetPortRangeForwardingRules";RAINIER.ui.showWaiting();RAINIER.jnap.send({action:aM,data:ax.portrangeforwarding,cb:function(aN){if(aN.result==="OK"){G.portrangeforwarding=$.extend(true,{},ax.portrangeforwarding);aq.load(ax.portrangeforwarding,G.portrangeforwarding,true)}else{if(aN.result!=="_AjaxError"){if(aN.result==="ErrorRulesOverlap"){RAINIER.ui.alert("",RAINIER.ui.validation.strings.errors[aN.result])}else{RAINIER.ajax().executeDefaultJnapErrorHandler(aN,aM)}}h=false}x.onSaveComplete()},disableDefaultJnapErrHandler:true})}function aC(){var aM="/jnap/firewall/SetPortRangeTriggeringRules";RAINIER.ui.showWaiting();RAINIER.jnap.send({action:aM,data:ax.portrangetriggering,cb:function(aN){if(aN.result==="OK"){G.portrangetriggering=$.extend(true,{},ax.portrangetriggering);aK.load(ax.portrangetriggering,G.portrangetriggering,true)}else{if(aN.result!=="_AjaxError"){if(aN.result==="ErrorRulesOverlap"){RAINIER.ui.alert("",RAINIER.ui.validation.strings.errors[aN.result])}else{RAINIER.ajax().executeDefaultJnapErrorHandler(aN,aM)}}h=false}x.onSaveComplete()},disableDefaultJnapErrHandler:true})}function g(aM){H(aM);t(aM);aH(aM)}function at(){e();a();K();RAINIER.event.fire("applet.loaded")}function ag(){if(!RAINIER.shared.util.areServicesSupported(["/jnap/firewall/Firewall","/jnap/ddns/DDNS"])){RAINIER.event.fire("applet.unsupportedFirmware");return}Z.ddns2=RAINIER.shared.util.areServicesSupported(["/jnap/ddns/DDNS2"]);RAINIER.event.connect("tab.changed",function(aN,aO){switch(aO.destTabId){case"dmz":x.onDMZEnableChange();break;case"firewall":case"app-and-gaming":RAINIER.ui.fixIeForm("#"+aO.destTabId);break}});aA=RAINIER.ui.dialog(r,{onShow:function(){ad()}});aE=RAINIER.ui.dialog($("#dialog-tabchange"),{onSubmit:function(){f=true;B(false)}});$("#dialog-tabchange #no-save").unbind("click");$("#dialog-tabchange #no-save").click(function(){aE.close();l();S.click()});w=RAINIER.ui.tabs.init({fireEvents:true,initialTab:$.cookie("initial-tab")});w.connect(function(aO,aN){if(aN.srcIdx>-1){S=aN.destTab;if(X()){aO.cancelEvent=true}}});w.fireInit();var aM=RAINIER.jnap.Transaction({onComplete:at});g(aM);aM.send();Q.find("footer .cancel").click(x.onClose);Q.find("footer .submit").click(x.onSave);Q.find("footer .apply").click(x.onApply)}ag()}());