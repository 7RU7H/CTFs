var strLanguageFile="";
var strLanguageFileRetrieved=0;
var repeat_refresh=true;

// returns the value of a given URL parameter (GET request), i.e. page.html?name=value&etcetera
function gup( name )
{
	name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
	var regexS = "[\\?&]"+name+"=([^&#]*)";
	var regex = new RegExp( regexS );
	var results = regex.exec( window.location.href );
	if( results == null )
		return "";
	else
		return results[1];
}

// disable submit on enter for text input in forms 
function noenter(e)
{
	var key;
	if (window.event)
		key = window.event.keyCode;     //IE
	else
		key = e.which;     //firefox
	if (key == 13)
		return false;
	else
		return true;
}

function clearLog()
{
	loadXMLDoc("/rpc/log_clear","");
	alert("Log file cleared");
}

function getLog()
{ 
	window.open('/rpc/log_getfile');
}

function stopServer()
{
	loadXMLDoc("/rpc/stop","");
}

function setClientEnable(mac,enabled)
{
	//alert(mac + ":" + enabled);
	if (enabled=='1') {
		loadXMLDoc("/rpc/client_enable?mac="+mac,"");
	}
	else {
		loadXMLDoc("/rpc/client_disable?mac="+mac,"");
	}
}


function addClient(mac,client_id,enabled,view)
{
	//alert(mac + ":" + client_id + ":" + enabled);
	loadXMLDoc("/rpc/client_add?mac="+mac+"?id="+client_id+"?enabled="+enabled+"?view="+view,"");
}

function removeClient(mac)
{
	//alert(mac);
	loadXMLDoc("/rpc/client_delete?mac="+mac,"");
}

function rescanDirectories()
{
	loadXMLDoc("/rpc/rescan","");
	var tmp_str = translateOption("rescanalert");
	alert(tmp_str);
	optionsShow(14,true);
}

function rebuiltDB()
{
	confirmed = window.confirm(translateOption("confirmrebuild"));
	if (confirmed) {
		waitServer("/rpc/rebuild");
		optionsShow(14,true);
	} 
}

var timerID = 0;


// on a restart
function tryLoadConfig()
{
	if (timerID) {
		clearTimeout(timerID);
	}
	resp="";
	try {
		resp = loadXMLDoc("/rpc/get_version","");
	} catch(e) {
		resp="";
	}
	repeat_refresh=false;
	if (resp && resp.length != 0) {
		hide_div("divPleaseWait");
		optionsShow(parent.actualPage, true);
		repeat_refresh=true;
		refresh_dynamic_divs();
		if (restartpending) {
			show_div("divRestartWarning");
			show_div("divRestart");
		}
		else {
			hide_div("divRestartWarning");
		}
	} else {
		timerID = setTimeout("tryLoadConfig()", 1000);
	}
}

function waitServer(msg)
{
	hideAllDivs();
	show_div("divPleaseWait")
	loadXMLDoc(msg,"");
	timerID = setTimeout("tryLoadConfig()", 5000);
}


function resetClients()
{
	waitServer("/rpc/resetclients");
}

function restartServer()
{
	waitServer("/rpc/restart");
}

function resetDefaults()
{
	confirmed = window.confirm(translateOption("resetdefaults")+"?");
	if (confirmed) {
		waitServer("/rpc/reset");
		iniPage(); // some browsers (e.g. firefox) do not automatically reload page...
	} 
}


function loadXMLDoc(my_url,strData) 
{
	var req;
	req = false;
	// branch for native XMLHttpRequest object
	if(window.XMLHttpRequest) {
		try {
			req = new XMLHttpRequest();
		} catch(e) {
			req = false;
		}
		// branch for IE/Windows ActiveX version
		} else if(window.ActiveXObject) {
		try {
			req = new ActiveXObject("Msxml2.XMLHTTP");
		} catch(e) {
			try {
				req = new ActiveXObject("Microsoft.XMLHTTP");
			} catch(e) {
				req = false;
			}
		}
	}
	if(req) {
//		my_url = "http://127.0.0.1:9000" + my_url;
		if (strData.length>0) {  // post request
			req.open("POST", my_url, false);
			//req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
			req.setRequestHeader('Content-Type', 'text/xml; charset=UTF-8');
			req.send(strData);
		}
		else { // get request
			req.open("GET", my_url, false);
			try {
				req.send("");
			} catch(e) {
				req = false;
			}
		}
		return req.responseText;
	}
	return "";
}

var name_array=new Array();
var value_array=new Array();
var values_loaded=0;

function getAllValues() 
{
	var nPos=0;
	var nTotal=0;

	var big_string=loadXMLDoc("/rpc/get_all","");
	all_values_array=big_string.split("\n");
	for (i=0;i<all_values_array.length;i++) {
		one_pair=all_values_array[i].split("=");
		if (one_pair.length>1) {
			name_array[i]=one_pair[0];
			//value_array[i]=one_pair[1];
			nTotal=one_pair[1].length;
			nPos=0;
			value_array[i]='';
			while((nPos < nTotal) && (one_pair[1].charAt(nPos)>=' ')) {
				value_array[i]=value_array[i]+one_pair[1].charAt(nPos);
				nPos++;
			}
		}
	}		
	values_loaded=1;
}

function getValue(strName) 
{
	// init call
	if (!values_loaded) getAllValues();
	// now look for the value
	for (i=0;i<name_array.length;i++) {
		if (name_array[i]==strName) 
			return value_array[i];
	}
	return "";
}

var new_values='';

function setValue(strName,strValue) 
{
	new_values=new_values+strName+"="+strValue+"\n";
	return strValue;
}


function setAllValues() 
{
	return loadXMLDoc("/rpc/set_all",new_values);
}

function translateOption(strName) 
{
	var strRC='';
	var nTotal=0;
	var nPos=0;
	var strSearch='';
	if (!strLanguageFileRetrieved) {
		strLanguageFileRetrieved=1;
		strLanguageFile=loadXMLDoc("/rpc/get_language_file","");
	}
	strSearch="|"+strName+"|";
	nPos=strLanguageFile.indexOf(strSearch);
	if (nPos<0) return strName; // not found
	nPos=nPos+strSearch.length;
	nTotal=strLanguageFile.length;
	while((nPos < nTotal) && (strLanguageFile.charAt(nPos)>=' ')) {
		strRC=strRC+strLanguageFile.charAt(nPos);
		nPos++;
	}
	return strRC;
}


function translateHelp(strName) 
{
	var strRC='';
	var nTotal=0;
	var nPos=0;
	var strSearch='';
	if (!strLanguageFileRetrieved) {
		strLanguageFileRetrieved=1;
		strLanguageFile=loadXMLDoc("/rpc/get_language_file","");
	}
	strSearch="|"+strName+"?";
	nPos=strLanguageFile.indexOf(strSearch);
	if (nPos<0) return strName; // not found
	nPos=nPos+strSearch.length;
	nTotal=strLanguageFile.length;
	while((nPos < nTotal) && (strLanguageFile.charAt(nPos)>=' ')) {
		strRC=strRC+strLanguageFile.charAt(nPos);
		nPos++;
	}
	return strRC;
}


function getVersion()
{
	return loadXMLDoc("/rpc/version","");
}

function getAutoUpdateInfo()
{
	return loadXMLDoc("/rpc/autoupdate?checkupdate","");
}

function isRemoteAccess()
{
	var isRemoteAccess = loadXMLDoc("/rpc/checkremoteaccess","");
	if(isRemoteAccess=="0") {
		return false;
	} else {
		return true;
	}
	
}

function getCopyrightMessage()
{
	return loadXMLDoc("/rpc/copyright_message","");
}

// Helper function to get element by id.
function getElement( element_id ) 
{
	return document.getElementById( element_id );
}

// remove div with help window
function hideHelp() 
{
	var divHelp = 'helpDialog_div';
	if ( getElement(divHelp))
		getElement(divHelp).parentNode.removeChild(getElement(divHelp));
}

var strDir = "";

function hideDir() 
{
	var divDir = 'dirDialog_div';
	if ( getElement(divDir)) 
		getElement(divDir).parentNode.removeChild(getElement(divDir));
}

function hideSelect()
{
	if (document.all)
		document.all.divContentdir.style.visibility="hidden";
}

function unhideSelect()
{
	if (document.all)
		document.all.divContentdir.style.visibility="visible";
}


function showFile(optionName) 
{
	var divDir = 'dirDialog_div';
	var newDiv = document.createElement('div');
	var object = document.getElementById( 'divPopup' );
	hideDir();
	hideSelect();
	newDiv.id = divDir;
	newDiv.className = 'dir_dialog';
	newDiv.style.zIndex = 99999;
	object.appendChild(newDiv);
	showDirselection('', '', optionName,1,0);
}

function showDir(optionName) 
{
	var divDir = 'dirDialog_div';
	var newDiv = document.createElement('div');
	var object = document.getElementById( 'divPopup' );
	hideSelect();
	hideDir();
	newDiv.id = divDir;
	newDiv.className = 'dir_dialog';
	newDiv.style.zIndex = 99999;
	object.appendChild(newDiv);
	showDirselection('', '', optionName,0,0);
}

function dirSelected(strDir,inputName)
{ 
	var contentObject = document.getElementById(inputName);
	//alert(inputName +":"+contentObject);
	contentObject.value = strDir;
	if (strDir.charAt(strDir.length-1) == ':') 
		contentObject.value += '\\'; // add backslash for drive volumes on windows
	hideDir();
	unhideSelect();
}

function dirQuit()
{
	hideDir();
	unhideSelect();
}

function setIniFileParamTrue()
{
	return loadXMLDoc("/rpc/set_option?autoupdateinstall=1","");
}
function startAutoUpdatePlugin()
{
	return loadXMLDoc("/rpc/autoupdate?installupdate","");
}

function startAutoUpdate(button)
{
	button.disabled=true;
	var setAutoUpdate = setIniFileParamTrue();
	if (setAutoUpdate) {
		startAutoUpdatePlugin();
	}

}
function showDirselection(completePath, completePathIndex, optionName, fileSelector, fileSelected) 
{
	var str_html='';
	var str_line='';
	var	str_Delimiter='';
	var str_first_line='';
	var str_first_line_index='';
	var div_dirselect = document.getElementById( 'dirDialog_div' );
  
	if (div_dirselect) {
		var str_dir = loadXMLDoc("/rpc/getdir?path=" + completePathIndex,"")
		var str_dir_array=str_dir.split("\n");
		var str_slash=str_dir_array[0];

		if ((completePath.charAt(0)=='/') && (completePathIndex.charAt(0)!='/')) completePathIndex="/"+completePathIndex;

		var str_first_line_array=completePath.split(str_slash);
		var str_first_line_array_index=completePathIndex.split(str_slash);

		str_escaped_slash="";
		if (str_slash=='\\') {
			str_escaped_slash="\\";
		}

		if (completePath.length > 0) {
			// first entry - reset - start navigation
			str_html="<IMG src=\"/images/arrow_test_small.gif\" />" +
			  			 "<A href=\"javascript:void(0)\" onclick=\"showDirselection('','','"+optionName+"',"+fileSelector+","+fileSelected+")\">*</A>&nbsp;";
		}

		for (i=0;i<str_first_line_array.length;i++) {
			if (str_first_line_array[i].length > 0) {
				if (i>0) {
					str_first_line=str_first_line+str_slash+str_escaped_slash+str_first_line_array[i];
					str_first_line_index=str_first_line_index+str_slash+str_escaped_slash+str_first_line_array_index[i];
				} else {
					str_first_line=str_first_line_array[i];
					str_first_line_index=str_first_line_array_index[i];
	 			}
				if (fileSelected && (i==str_first_line_array.length-1)) {
					str_line="<IMG src=\"/images/arrow_test_small.gif\" />" + str_first_line_array[i] + "&nbsp;";
				} else {
					str_line="<IMG src=\"/images/arrow_test_small.gif\" />" +
						"<A href=\"javascript:void(0)\" onclick=\"showDirselection('"+
						str_first_line + "','" +
						str_first_line_index + "','" +
						optionName+ "',"  +
						fileSelector+ ",0" +
						")\">" + str_first_line_array[i] + "</A>&nbsp;";
				}
				str_html += str_line;
			}
		}

		// add select button
		if (fileSelector) {
			if (fileSelected) {
				str_html += "&nbsp;<input type=\"button\" name=\"selectdir\" value=\"" + translateOption("select") + "\" onClick=\"dirSelected('"+
				str_first_line+"','"+optionName+"')\">&nbsp;<input type=\"button\" name=\"quit\" value=\"" + translateOption("quit") + "\" onClick=\"dirQuit()\">"
			} else {
				str_html += "<input type=\"button\" name=\"quit\" value=\"" + translateOption("quit") + "\" onClick=\"dirQuit()\">"
			}
		} else {
			if (completePath.length>0) {
				str_html += "&nbsp;<input type=\"button\" name=\"selectdir\" value=\"" + translateOption("select") + "\" onClick=\"dirSelected('"+
				str_first_line+"','"+optionName+"')\">&nbsp;<input type=\"button\" name=\"quit\" value=\"" + translateOption("quit") + "\" onClick=\"dirQuit()\">"
			} else {
				str_html += "<input type=\"button\" name=\"quit\" Value=\"" + translateOption("quit") + "\" onClick=\"dirQuit()\">"
			}
		}
 
		str_html += "<hr>";

		if (!fileSelected) { // only show more selections if we do not have already seletced a file
			for (i=1;i<str_dir_array.length;i++) {
				if (str_dir_array[i].charAt(3) == 'D') {
	 				str_line = "<A href=\"javascript:void(0)\" onclick=\"showDirselection('"+ str_first_line;
	 				if (str_first_line.length > 0)  str_line=str_line+str_slash+str_escaped_slash;
	 				str_line += str_dir_array[i].substring(4);
	 				if (str_line.charAt(str_line.length-1) == str_slash) 
						str_line += str_escaped_slash;
	 				str_line += "','" + str_first_line_index;
	 				if (str_first_line_index.length > 0)  
						str_line += str_slash+str_escaped_slash;
	 				str_line += str_dir_array[i].substring(0,3)  + "','" ;
	 				str_line += optionName+"'," + fileSelector+ ",0" + ")\">" + str_dir_array[i].substring(4) + "</A>";
	 				str_html += str_line+"<br>";
	 			}
	 		}

			// show files if we have a file selector
			if (fileSelector) {
				for (i=1;i<str_dir_array.length;i++) {
					if (str_dir_array[i].charAt(3) == 'F') {
		 				str_line = "<A href=\"javascript:void(0)\" onclick=\"showDirselection('"+ str_first_line;
		 				if (str_first_line.length > 0)
							str_line += str_slash+str_escaped_slash;
		 				str_line += str_dir_array[i].substring(4);
		 				if (str_line.charAt(str_line.length-1) == str_slash)
							str_line += str_escaped_slash;
		 				str_line += "','" + str_first_line_index;
		 				if (str_first_line_index.length > 0)
							str_line += str_slash+str_escaped_slash;
		 				str_line += str_dir_array[i].substring(0,3)  + "','" ;
		 				str_line += optionName+"'," + fileSelector+ ",1" + ")\">" + str_dir_array[i].substring(4) + "</A>";
		 				str_html += str_line + "<br>";
		 			}
		 		}
			}
		}
		div_dirselect.innerHTML = str_html;
	}
}

function rescanDirs()
{
	return loadXMLDoc("/rpc/rescan","");
}

function getStatus()
{
	return loadXMLDoc("/rpc/info_status","");
}
  
function getClientInfo()
{
	return loadXMLDoc("/rpc/info_clients","");
}

function getConnectedClientInfo()
{
	return loadXMLDoc("/rpc/info_connected_clients","");
}
  
function getNicInfo()
{
	return loadXMLDoc("/rpc/info_nics","");
}

function getWebDavLink()
{
	return loadXMLDoc("/rpc/get_webdav_link","");
}
 
function getClientNames()
{
	return loadXMLDoc("/rpc/get_clients","");
}
  
 
function clientSelectionBox(clientnames,nr,nSelected)
{
	var temp = "";
	var clientname_array=clientnames.split(",");

	temp = "<select class=\"inputValue\" name=\"client_name"  +  parseInt(nr)  +"\" >";
	for (var i=0; i<clientname_array.length; i+=2) {
		var selected = "";
		if (parseInt(clientname_array[i],10)==nSelected) selected = " selected";
		temp += "<option" + selected +" value=\""+clientname_array[i]+"\">"+clientname_array[i+1]+"</option>";
	}
	temp += "</select>";
	return temp;
}
 
function viewSelectionBox()
{
	var temp = "<select class=\"inputValue\" name=\"defaultview\" >";
	var viewnames=loadXMLDoc("/rpc/view_names","");
	var viewname_array=viewnames.split(",");
	var selected_view=getValue("defaultview");

	for (var i=0; i<viewname_array.length; i+=1) {
		var selected = "";
		if (viewname_array[i]==selected_view) selected = " selected";
		temp += "<option" + selected + " value=\""+viewname_array[i]+"\">"+translateOption(viewname_array[i])+"</option>";
	}
	temp += "</select>";
	return temp;
}
 
function viewSelectionBoxPerClient(viewnames,clientID,clientViewName,hasdefaultview)
{
	var temp = "<select class=\"inputValue\" name=\"defaultview" + parseInt(clientID) + "\" "+(hasdefaultview?"disabled":"")+">";
	var viewname_array=viewnames.split(",");
	var found = false;

	for (var j=0; j<viewname_array.length; j+=1) {
		var selected = "";
		if (viewname_array[j]==clientViewName) {
			found=true;
			selected = " selected";
		}
		temp += "<option" + selected + " value=\""+viewname_array[j]+"\">"+translateOption(viewname_array[j])+"</option>";
	}
	if (! found && clientViewName.length) {
		temp += "<option selected value=\""+clientViewName+"\">"+translateOption(clientViewName)+"</option>";
	}
	temp += "</select>";
	return temp;
}

function clientInfo() 
{
	var clientString = getConnectedClientInfo();
	var clientnames=getClientNames();
	var viewnames=loadXMLDoc("/rpc/view_names","");
	var temp = "";
	var n=0;

	clientEntryArray = clientString.split("\n");
	for ( var i = 0; i < (clientEntryArray.length-1); i+=11)
	{ 
		clientID          = clientEntryArray[i];
		clientMAC         = clientEntryArray[i+1];
		clientIP          = clientEntryArray[i+2];
		clientUUID        = clientEntryArray[i+3];
		clientEnabled     = clientEntryArray[i+4];
		clientFriendyName = clientEntryArray[i+5];
		clientIcon        = clientEntryArray[i+6];
		clientMimetype    = clientEntryArray[i+7];
		clientViewName    = clientEntryArray[i+8];
		clientDV          = clientEntryArray[i+9];
		clientDelimiter   = clientEntryArray[i+10];
		n++;
		var checked = "";
		if (clientEnabled == '1') checked = " checked"
		temp += "<tr>";
		temp += "<td class=\"optionName\">";
		temp += "<input type=\"checkbox\" name=\"client_enabled" + parseInt(n) + "\" " + checked + ">";
		temp += "&nbsp;&nbsp;<input type=\"text\" readonly onkeypress=\"return noenter(event)\" name=\"client_mac"  +  parseInt(n) +"\" size=\"20\" value=\""+clientMAC +"\">";
		temp += "&nbsp;&nbsp;<input type=\"text\" readonly size=\"16\" value=\""+clientIP +"\">";
		temp += "&nbsp;&nbsp;" + clientSelectionBox(clientnames,n,clientID) 
		temp += "&nbsp;&nbsp;" + viewSelectionBoxPerClient(viewnames,clientID,clientViewName,clientDV==1?true:false);
		temp += "</td>";
		temp += "</tr>";
	}
	n=999;
	temp += "<tr>";
	temp += "<td class=\"optionName\">";
	temp += "<input type=\"checkbox\" name=\"client_enabled" +  parseInt(n) + "\" >";
	temp += "&nbsp;&nbsp;<input type=\"text\" onkeypress=\"return noenter(event)\" name=\"new_client_mac\" size=\"20\" value=\"\">";
	temp += "&nbsp;&nbsp;<input type=\"text\" readonly size=\"16\" value=\"\">";
	temp += "&nbsp;&nbsp;" + clientSelectionBox(clientnames,i,-1) 
	temp += "&nbsp;&nbsp;" + viewSelectionBoxPerClient(viewnames,i,"",false);
	temp += "</td>";
	temp += "</tr>";

	return temp;
}

//global variable to store remaining number of days for trial version
var daysleft = 0;
var restartpending = false;
var licensestatus=0;
var showerror = false;
var error_str="";
var showCDKey=false;

function licenseStatus() {
	return licensestatus;
}

function errorStr() {
	statusInfo();
	return error_str;
}

function writeMediaFusionTable() {
	var mfTable="<TABLE cellspacing=\"3\" width=\"750\" border=\"0\">" 
		+"<tr><td>"
		+ loadXMLDoc("/rpc/mediafusionform","")
		+"</td></tr>"
		+"</table>";
	var mf_div;

	mf_div =  parent.CONT_FRAME.document.getElementById("divMediaFusion");
	mf_div.innerHTML = mfTable;
} 

function setTranscoding() {
	var str="";
	var tr_div = parent.CONT_FRAME.document.getElementById("divTranscoding");
	var tr= loadXMLDoc("/rpc/set_transcoding","");

	var targets = tr.split("+");
	var type="";
	var title="";
	var name = "";
	var hasprinted=0
	for ( var i = 0; i < targets.length; i++) {
		var t = targets[i].split("|"); //name=gui | from | to | desc | param | width | height
		if (t[2] == null) break;
		if (name == t[0]) continue;
		name=t[0];
		var from = t[1];
		var to = t[2];
		if (to && to.substring(0,5) == "image") title ="transcodingpictures"
		if (to && to.substring(0,5) == "audio") title ="transcodingmusic"
		if (to && to.substring(0,5) == "video") title ="transcodingvideos"
		if (type != title) {
			type = title;
			hasprinted=0;
		}
		var nt=name.split("=");
		var na = nt[0];
		var gui = nt[1];
		if (na=="") continue;
		var check = "";
		var color = "";
		switch (gui) {
		case "00": check ="disabled"; color="color=\"#808080\""; break;
		case "01": check =""; color="color=\"#000000\""; break;
		case "10": check ="checked disabled"; color="color=\"#808080\""; break;
		case "11": check ="checked"; color="color=\"#000000\""; break;
		}
		str +="<tr>";
		str += "<td class=\"optionValue\">";
		if (hasprinted==0) str += "<br>" + translateOption(title) + ":<br>"
		hasprinted++;
		str += "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input " + check + " type=\"checkbox\" name=\"bgtrans" + na + "\" > <font " + color + ">" + na + "</font>";
		str += "</td>";
		str += "</tr>";
	}
	return str;
} 

function autoUpdateInfo(showInfo) {
	if(showInfo) {
		return getAutoUpdateInfo();
		}
		else return "";
}

function updateStatusNow() {
	if (parent.CONT_FRAME) {
		var st_div = parent.CONT_FRAME.document.getElementById("divStatus");
		if (st_div) st_div.innerHTML="<table cellspacing=\"3\" width=\"750\" border=\"0\"><tr><td>"+ statusInfo(true) + "</td></tr></table>"; 
	}
}


function statusInfo(updateStatus) {
	var temp = "";
	var statusName ="";
	var statusValue ="";
	var statusString = getStatus();
	var nicString = getNicInfo();
	var cdkey_str="";
 	var statusEntryArray = statusString.split("\n");
	var first = true;
	var autoUpdateStatus ="";
	showerror = false;
	showCDKey = false;

	for ( var i = 0; i < statusEntryArray.length; i++) { 
		if (statusEntryArray[i].length>0) {
			if (first) {
				var starttag="";
				var endtag="";
				var buttons="";
				var infolink="<a href=\"javascript:optionsShow(20,true)\"><script type=\"text/javascript\">document.write(translateOption(\"updateinfo\"))</script></a>";
	
				autoUpdateStatus=getAutoUpdateInfo();
				if (autoUpdateStatus != ""){
					buttons="<input type=\"button\" value=\""+translateOption("updatenow")+"\" onClick=\"startAutoUpdate(this);\">"
					starttag="<a ";
					endtag="\" target=\"_blank\">"+infolink+"</a>";	
				}
				temp += "<tr><td class=\"optionName\">" +translateOption("version") + ":</td><td class=\"inputValue\">"+getVersion()+"&nbsp;"+buttons+ starttag + endtag + "</td></tr>" ;  
				first = false;
			}
			var statusEntries = statusEntryArray[i].split("|");
			statusName = translateOption(statusEntries[0]);
			statusValue = statusEntries[1];
			switch (statusEntries[0]) {
				case "usedmem":
					temp += "<tr><td class=\"optionName\">" + statusName + ":</td><td class=\"inputValue\">" + statusValue + " KB</td></tr>";
				break;

				case "musictracks":
					temp += "<tr><td class=\"optionName\">" + statusName + ":</td><td class=\"inputValue\">" + statusValue + "</td></tr>";
				break;

				case "pictures":
					temp += "<tr><td class=\"optionName\">" + statusName + ":</td><td class=\"inputValue\">" + statusValue + "</td></tr>";
				break;

				case "videos":	
					temp += "<tr><td class=\"optionName\">" + statusName + ":</td><td class=\"inputValue\">" + statusValue + "</td></tr>";
				break;

				case "licensedays":
				if (licensestatus < 2) {  // 2 is a registered version
					temp += "<tr><td>&nbsp;</td></tr><tr><td class=\"optionName\">" + translateHelp("trialinfo")+"</td></tr>";
					temp += "<tr><td class=\"optionName\">" + statusName + ":</td><td class=\"inputValue\">";
					temp += statusValue  + "</td></tr>";
				} else {
					temp += "<tr><td>&nbsp;</td></tr><tr><td class=\"optionName\">" + translateOption("licenseinfo") + ":</td><td class=\"inputValue\">";
					temp += translateHelp("registeredaccount")  + "</td></tr>";
				}
				daysleft = parseInt(statusValue);
				cdkey_str=getValue("cdkey");
				if (cdkey_str.length>0)
					temp += "<tr><td class=\"optionName\">" + translateOption("cdkey") + ":</td><td  class=\"inputValue\">" + cdkey_str  + "</td></tr>";
				break;

				case "licensestatus":
					licensestatus=parseInt(statusValue);
					//ACCOUNTING_TRIAL_VERSION				1
					showCDKey=true;
					if (licensestatus==-201) {   // Key malformed
						error_str = "<div class=\"alert\">"+translateHelp("invalidkey")+"<\/div>";
						showerror = true;
					}
					if (licensestatus==-202) {   // Music Key for Media Server
						error_str = "<div class=\"alert\">"+translateHelp("wrongversion")+"<\/div>";
						showerror = true;
					}
					if (licensestatus==-203) {   // Wrong OEM
						error_str = "<div class=\"alert\">"+translateHelp("keymismatch")+"<\/div>";
						showerror = true;
					}
					if (licensestatus==-204) {   // Key expired
						error_str = "<div class=\"alert\">"+translateHelp("keymismatch")+"<\/div>";
						showerror = true;
					}
					if (licensestatus==-205) {   // Key in use
						error_str = "<div class=\"alert\">"+translateHelp("keyinuse")+"<\/div>";
						showerror = true;
					}
					if (licensestatus==-206) {   // Trial expired
						error_str = "<div class=\"alert\">"+translateHelp("expired")+"<\/div>";
						showerror = true;
					}
					if (licensestatus>=2) {   // ACCOUNTING_REGISTERED_VERSION
						error_str = "<div class=\"alert\">"+translateHelp("keymismatch")+"<\/div>";
						showCDKey = false;
					}
				break;

				case "uptime":
					temp += "<tr><td class=\"optionName\">" + statusName + ":</td><td class=\"inputValue\">" + statusValue + " " + translateOption("days");
					temp += ", " + statusEntries[2] + " " + translateOption("hours") + "</td></tr>";
				break;

				case "restartpending":
					temp += "<tr><td class=\"optionName\">" + statusName + ":</td><td class=\"inputValue\">";
					if (statusValue == "0") {
						restartpending = false; 
						temp += translateOption("no") + "</td></tr>";
					} else {
						restartpending = true; 
						temp += translateOption("yes") + "</td></tr>";
					}
				break;

				case "dbupdate":
					temp += "<tr><td class=\"optionName\">" + statusName + ":</td><td class=\"inputValue\">" + statusValue + "</td></tr>";
				break;

				case "builddate":
					temp += "<tr><td class=\"optionName\">" + statusName + ":</td><td class=\"inputValue\">" + statusValue + "</td></tr>";
				break;

				case "wmdrmstatus":
					temp += "<tr><td class=\"optionName\">" + statusName + ":</td><td class=\"inputValue\">" + statusValue + "</td></tr>";
				break;

				default:
				break;
			}
		}
	}
	temp += "<tr><td>&nbsp;</td></tr>" 

	var first_nic = true;
	statusEntryArray = nicString.split("\n");
	if (statusEntryArray.length > 0) 
		temp += "<tr><td class=\"optionName\">" + translateOption("networkinterfaces") + ":</td>";
	for ( var i = 0; i < statusEntryArray.length; i++) {
		if (statusEntryArray[i].length>0) {
			if (first_nic) {
				temp += "<td class=\"inputValue\">" + statusEntryArray[i];
				first_nic = false;
			} else {
				temp += "<tr><td><\/td><td class=\"inputValue\">" + statusEntryArray[i];
			}
		} 
	}
	temp += "<tr><td>&nbsp;"
	return temp;
}


function updateStreamInfoNow() {
	if (parent.CONT_FRAME) {
		var st_div = parent.CONT_FRAME.document.getElementById("divStreamInfo");
		if (st_div) st_div.innerHTML="<hr /><table cellspacing=\"3\" width=\"750\" border=\"0\">"
											+ streamInfo() +
											"</table>"; 
	}
}

function streamInfo() {
	var temp = "";
	var streamString = loadXMLDoc("/rpc/stream_info","");
 	var streamEntryArray = streamString.split("\n");
	var bFirst=1;
	//	statusName = translateOption(statusEntries[0]);

	for ( var i = 0; i < streamEntryArray.length; i++) { 
		if (streamEntryArray[i].length>0) {
			var streamEntries = streamEntryArray[i].split("\t");
			if (streamEntries.length>3) {
				var strTime = streamEntries[0];
				var strID   = streamEntries[1];
				var strName = streamEntries[2];
				var strIP   = streamEntries[3];
				var strFile = streamEntries[4];
				if (bFirst) {
					bFirst=0;
					temp += "<tr><td class=\"optionName\">" + translateOption("streaminfo") + ":</td></tr>";
				}	
				temp += "<tr><td class=\"optionName\">" + strName + "</td><td class=\"inputValue\">" +strTime + "&nbsp;" + strFile + "</td></tr>";
			}
		}
	}
	if (bFirst) {
		temp += "<tr><td class=\"optionName\">" + translateOption("streamnone") + "</td></tr>";
	}	
	return temp;
}


function showContentdirs()
{
	var contentStr    = "";
	var input_id      = "";
	var contentDirs   = "";

	var allTypes      = translateOption("allcontenttypes");
	var musicOnly     = translateOption("musiconly");
	var picturesOnly  = translateOption("picturesonly");
	var videosOnly    = translateOption("videosonly");
	var musicandpictures  = translateOption("music") + " & " + translateOption("pictures");
	var musicandvideos    = translateOption("music") + " & " + translateOption("videos");
	var picturesandvideos = translateOption("pictures") + " & " + translateOption("videos");

	if (enable_cancel) 
		contentDirs = newContentDirs;
	else 
		contentDirs = getValue("contentdir");

	contentStr += "<table class=\"optionItem\">";
	contentStr += "<tr><TD class=\"optionName\" colspan=\"2\">" + translateOption("contentdir")+ ":</TD></tr>";

	if (contentDirs != "") {
		var contentDirs_Array = contentDirs.split(",");
		for ( var i = 0; i < contentDirs_Array.length; i++) {
			var checked = "";
			var singleDir = contentDirs_Array[i].split("|");
			var dirString = singleDir[1];
			var dirType = singleDir[0].charAt(1);
			var dirEnabled = singleDir[0].charAt(0);
			//if (i>0) contentStr += "<br>";
			if (dirEnabled=="+") checked = " checked";
			contentStr += "<tr>";
			contentStr += "<td>";
			contentStr += "<input type=\"checkbox\" name=\"cdir_checkbox" + parseInt(i) + "\" " + checked + ">";
			contentStr += "&nbsp;&nbsp;<input type=\"text\" onkeypress=\"return noenter(event)\"name=\"my_contentdir" + parseInt(i) + "\" id=\"my_contentdir" + parseInt(i) + "\"size=\"70\" value=\"" + dirString + "\" >";
			contentStr += "&nbsp;&nbsp;<select class=\"inputValue\" name=\"content_type\">";
			contentStr += "<option value=\"A|\">" + allTypes + "</option>";
			if (dirType == "M") {contentStr += "<option selected value=\"M|\">" + musicOnly    + "</option>";} else {contentStr += "<option value=\"M|\">" + musicOnly    + "</option>";}
			if (dirType == "P") {contentStr += "<option selected value=\"P|\">" + picturesOnly + "</option>";} else {contentStr += "<option value=\"P|\">" + picturesOnly + "</option>";}
			if (dirType == "V") {contentStr += "<option selected value=\"V|\">" + videosOnly   + "</option>";} else {contentStr += "<option value=\"V|\">" + videosOnly   + "</option>";}
			if (dirType == "m") {contentStr += "<option selected value=\"m|\">" + picturesandvideos   + "</option>";} else {contentStr += "<option value=\"m|\">" + picturesandvideos   + "</option>";}
			if (dirType == "p") {contentStr += "<option selected value=\"p|\">" + musicandvideos   + "</option>";} else {contentStr += "<option value=\"p|\">" + musicandvideos   + "</option>";}
			if (dirType == "v") {contentStr += "<option selected value=\"v|\">" + musicandpictures   + "</option>";} else {contentStr += "<option value=\"v|\">" + musicandpictures   + "</option>";}
			contentStr += "</select>";
			contentStr += "&nbsp;&nbsp;<input type=\"button\" value=\"" + translateOption("browse")+"\" onClick=\"showDir('my_contentdir" + parseInt(i) + "');\" >";
			contentStr += "</td>";
			contentStr += "</tr>";
		}
		// add empty field for new content dir
		input_id = "my_contentdir" + parseInt(i);
		contentStr += "<tr>";
		contentStr += "<td>";
		contentStr += "<input type=\"checkbox\" name=\"cdir_checkbox" + parseInt(i) + "\" checked>";
		contentStr += "&nbsp;&nbsp;<input type=\"text\" onkeypress=\"return noenter(event)\"name=\"" + input_id + "\" id=\"" + input_id + "\" size=\"70\" value=\"\">";
		contentStr += "&nbsp;&nbsp;<select class=\"inputValue\" name=\"content_type\">";
		contentStr += "<option selected value=\"A|\">" + allTypes     + "</option>";
		contentStr += "<option  value=\"M|\">"         + musicOnly    + "</option>";
		contentStr += "<option  value=\"P|\">"         + picturesOnly + "</option>";
		contentStr += "<option  value=\"V|\">"         + videosOnly   + "</option>";
		contentStr += "<option  value=\"m|\">"         + picturesandvideos   + "</option>";
		contentStr += "<option  value=\"p|\">"         + musicandvideos      + "</option>";
		contentStr += "<option  value=\"v|\">"         + musicandpictures    + "</option>";
		contentStr += "</select>";
		contentStr += "&nbsp;&nbsp;<input type=\"button\" value=\"" + translateOption("browse")+"\" onClick=\"showDir('my_contentdir" + parseInt(i) + "');\">";   
		contentStr += "<br><input type=\"button\" value=\"" + translateOption("addcontentdir") + "\"" + "onClick=\"addContentDir(\'" + input_id + "\');\">";
		contentStr += "</td>";
		contentStr += "</tr>";
	}
	// No content dir specified
	else  {
		input_id = "my_contentdir0";
		contentStr += "<tr>";
		contentStr += "<td>";
		contentStr += "<input type=\"checkbox\" name=\"cdir_checkbox0" + "\" checked>";
		contentStr += "&nbsp;&nbsp;<input type=\"text\"  onkeypress=\"return noenter(event)\"name=\"" + input_id + "\" id=\"" + input_id + "\" size=\"70\" value=\"\">";
		contentStr += "&nbsp;&nbsp;<select class=inputValue name=\"content_type\">";
		contentStr += "<option selected value=\"A|\">" + allTypes     + "</option>";
		contentStr += "<option value=\"M|\">"          + musicOnly    + "</option>";
		contentStr += "<option value=\"P|\">"          + picturesOnly + "</option>";
		contentStr += "<option value=\"V|\">"          + videosOnly   + "</option>";
		contentStr += "<option  value=\"m|\">"         + picturesandvideos   + "</option>";
		contentStr += "<option  value=\"p|\">"         + musicandvideos      + "</option>";
		contentStr += "<option  value=\"v|\">"         + musicandpictures    + "</option>";
		contentStr += "</select>";
		contentStr += "&nbsp;&nbsp;<input type=\"button\" value=\"" + translateOption("browse")+"\" onClick=\"showDir('my_contentdir0');\">";
		contentStr += "<br><input type=\"button\" value=\"" + translateOption("addcontentdir") + "\" onClick=\"addContentDir(\'" + input_id + "\');\">";
		contentStr += "</td>";
		contentStr += "</tr>";
	}
	contentStr += "<br>";
	contentStr += "<tr><td class=\"helpText\" colspan=\"2\">" + translateHelp("contentdir") + "</td></tr>";
	contentStr += "</table>";
	contentStr += "<hr>";
	return contentStr;
}

// for auto tab on cd-key
function KeyPress(what,e,max,action) {
	if (document.layers) {
		if (e.target.value.length >= max) {
			if (what.name == "cdkey1") {  // enable copy & paste on cd key
				if (what.value.length > 20) {
					egal.cdkey2.value=egal.cdkey1.value.substring(5,9);
					egal.cdkey3.value=egal.cdkey1.value.substring(10,14);
					egal.cdkey4.value=egal.cdkey1.value.substring(15,19);
					egal.cdkey5.value=egal.cdkey1.value.substring(20,24);
					egal.cdkey6.value=egal.cdkey1.value.substring(25,29);
					egal.cdkey7.value=egal.cdkey1.value.substring(30,34);
					egal.cdkey8.value=egal.cdkey1.value.substring(35,39);
					egal.cdkey1.value=egal.cdkey1.value.substring(0,4);
				}
			}
			eval(action);
		}
	}
	else if (document.all) {
		if (what.value.length > (max-1)) {
			if (what.name == "cdkey1") {  // enable copy & paste on cd key
				if (what.value.length > 20) {
					egal.cdkey2.value=egal.cdkey1.value.substring(5,9);
					egal.cdkey3.value=egal.cdkey1.value.substring(10,14);
					egal.cdkey4.value=egal.cdkey1.value.substring(15,19);
					egal.cdkey5.value=egal.cdkey1.value.substring(20,24);
					egal.cdkey6.value=egal.cdkey1.value.substring(25,29);
					egal.cdkey7.value=egal.cdkey1.value.substring(30,34);
					egal.cdkey8.value=egal.cdkey1.value.substring(35,39);
					egal.cdkey1.value=egal.cdkey1.value.substring(0,4);
				}
			}
			eval(action);
		}
	}
}


function isAggregationServer() 
{
	var rc=loadXMLDoc("/rpc/aggregation","");
	return (rc.indexOf("active") > -1)
}


function updateAggregationNow() {
	if (parent.CONT_FRAME) {
		var ag_div = parent.CONT_FRAME.document.getElementById("divAggregationTable");
		var newText=
			"<table class=\"optionItem\">"
			+	"</tr>"
			+		aggregationInfo()
			+	"</tr>"
			+"</table>"
			+"<hr>";
		if (ag_div) ag_div.innerHTML=newText;
	}
}


function enableserver(strUUID,nEnable)
{
	loadXMLDoc("/rpc/aggregatedserverswitch?uuid="+strUUID+"&enabled="+nEnable,"");
	updateAggregationNow();
}

var firstAggLoad=true;


function aggregationInfo() 
{
	var aggregationString = loadXMLDoc("/rpc/listaggregatedservers","");
	var i=0;
	var oneBusy=false;
	var temp = "";

	serverArray = aggregationString.split("\n");
	while(i < (serverArray.length-1)) {
		if (serverArray[i].substring(0,6) == "<br>S:") {
			serverUUID=serverArray[i].substring(6);
		}
		else if (serverArray[i].substring(0,6) == "<br>N:") {
			serverFriendlyName=serverArray[i].substring(6);
		}
		else if (serverArray[i].substring(0,6) == "<br>A:") {
			serverPresent=serverArray[i].substring(6);
		}
		else if (serverArray[i].substring(0,6) == "<br>B:") {
			serverBusy=serverArray[i].substring(6);
			oneBusy=true;
		}
		else if (serverArray[i].substring(0,6) == "<br>E:") {
			serverEnabled=serverArray[i].substring(6);
		}
		else if (serverArray[i].substring(0,6) == "<br>M:") {
			serverMusicCount=serverArray[i].substring(6);
		}
		else if (serverArray[i].substring(0,6) == "<br>P:") {
			serverPictureCount=serverArray[i].substring(6);
		}
		else if (serverArray[i].substring(0,6) == "<br>V:") {
			serverVideoCount=serverArray[i].substring(6);
		}
		else if (serverArray[i].substring(0,6) == "<br>--") {
			// process data
			temp += "<tr><td colspan=\"1\"><hr></td></tr>";
			temp += "<tr><td class=\"optionname\" colspan=\"3\">";
			if (serverBusy=="1") {
				temp += "<img src=\"/images/wait.gif\" />";
			}
			else if (serverPresent=="0") {
				temp += "<img src=\"/images/agg_not_ok.gif\" />";
			}
			else if (serverEnabled=="0") {
				temp += "<img src=\"/images/agg_not_ok.gif\" />";
			}
			else {
				temp += "<img src=\"/images/agg_ok.gif\" />";
			}
			temp += "&nbsp;" + serverFriendlyName +"</td></tr>";
			temp += "<tr><td class=\"optionName\">" + translateOption("musictracks") + ":</td><td class=\"inputValue\">" + serverMusicCount + "</td></tr>";
			temp += "<tr><td class=\"optionName\">" + translateOption("pictures") + ":</td><td class=\"inputValue\">" + serverPictureCount + "</td></tr>";
			temp += "<tr><td class=\"optionName\">" + translateOption("videos") + ":</td><td class=\"inputValue\">" + serverVideoCount + "</td></tr>";
			

			if (serverEnabled=="1") {
			temp += "<tr><td colspan=\"2\"><input type=\"radio\" name=\"" + serverUUID + "choice\" value=\"ignore\" onClick=\"enableserver('"+serverUUID+"',0);\" />"+translateOption("ignore")+"&nbsp;&nbsp;";
			temp += "<input type=\"radio\" name=\"" + serverUUID + "choice\" value=\"aggregate\" checked />"+translateOption("aggregate")+"&nbsp;&nbsp;";
			temp += "<input type=\"radio\" name=\"" + serverUUID + "choice\" value=\"mirror\" onClick=\"enableserver('"+serverUUID+"',2);\" />"+translateOption("mirror");
			temp += "</td></tr>";
			}
			else if (serverEnabled=="2") {
			temp += "<tr><td colspan=\"2\"><input type=\"radio\" name=\"" + serverUUID + "choice\" value=\"ignore\" onClick=\"enableserver('"+serverUUID+"',0);\" />"+translateOption("ignore")+"&nbsp;&nbsp;";
			temp += "<input type=\"radio\" name=\"" + serverUUID + "choice\" value=\"aggregate\" onClick=\"enableserver('"+serverUUID+"',1);\" />"+translateOption("aggregate")+"&nbsp;&nbsp;";
			temp += "<input type=\"radio\" name=\"" + serverUUID + "choice\" value=\"mirror\" checked />"+translateOption("mirror");
			temp += "</td></tr>";
			}
			else {
			temp += "<tr><td colspan=\"2\"><input type=\"radio\" name=\"" + serverUUID + "choice\" value=\"ignore\" checked />"+translateOption("ignore")+"&nbsp;&nbsp;";
			temp += "<input type=\"radio\" name=\"" + serverUUID + "choice\" value=\"aggregate\" onClick=\"enableserver('"+serverUUID+"',1);\" />"+translateOption("aggregate")+"&nbsp;&nbsp;";
			temp += "<input type=\"radio\" name=\"" + serverUUID + "choice\" value=\"mirror\" onClick=\"enableserver('"+serverUUID+"',2);\" />"+translateOption("mirror");
			temp += "</td></tr>";
		  }
		}
		i++;
	}
	return temp;
}

function refresh_dynamic_divs() {
	updateStatusNow();
	updateStreamInfoNow();
	updateAggregationNow();
	if (repeat_refresh) {
		setTimeout("refresh_dynamic_divs()",5000);
	}
}
