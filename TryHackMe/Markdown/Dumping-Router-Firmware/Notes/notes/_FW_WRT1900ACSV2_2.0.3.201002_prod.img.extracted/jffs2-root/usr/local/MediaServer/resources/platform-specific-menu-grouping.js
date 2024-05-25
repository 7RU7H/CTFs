function optionsShow(inputOptionSet, reload) 
{
	hideAllDivs();
	var optionSet = inputOptionSet;
	var title = parent.HEAD_FRAME.document.getElementById( 'divPageTitle' );
	var configTitle = "";
	parent.actualPage = optionSet;

	if (reload) {
		top.frames['CONT_FRAME'].location.reload();
		return;
	}

	switch (optionSet) {
	case 0: // 0: sharing
		show_div("divButtons");
		show_div("divFirstHR");
//		show_div("divEMPTY");
		show_div("divContentdir");
		if (getWebDavLink() != "NoWebdav") {
			show_div("divWebDav");
		}
		show_div("divCompilationsdir");
		show_div("divScantime");
		show_div("divAutoShare");
		configTitle = configTitle + translateOption('sharing') ;
	break;
 
	case 1:
		if (isAggregationServer()) {
			show_div("divButtons");
			show_div("divRestart");
			show_div("divFirstHR");
			show_div("divAggregation");
			configTitle = configTitle + translateOption('aggmenu') ;
		}
	break;

	case 15: //15: first steps
		show_div("divButtons");
		show_div("divFirstHR");
		show_div("divRestart");
		show_div("divLanguage");
		show_div("divFriendlyname");
		show_div("divTreetype");
		show_div("divWebaccess");
		configTitle = configTitle + translateOption('quickstartup') ;
	break;

	case 16: //16: clients
		show_div("divButtons");
		show_div("divFirstHR");
		show_div("divClientDB");
		configTitle = configTitle + translateOption('clients') ;
	break;

	case 9: // 9: network
		show_div("divButtons");
		show_div("divFirstHR");
		show_div("divNicrestart");
		show_div("divRestart");
		configTitle = configTitle + translateOption('network') ;
	break;

	case 10: // 10: remote access
		
		show_div("divButtons");
		show_div("divRestart");
		show_div("divFirstHR");
		show_div("divRemoteaccess");
		configTitle = configTitle + translateOption('remoteaccess') ;
	break;

	case 13: //13: troubleshooting
		show_div("divButtons");
		show_div("divFirstHR");
		show_div("divLogfile");
		show_div("divRestart");
		show_div("divRescan");
		show_div("divTrouble");
		configTitle = configTitle + translateOption('troubleshooting');
	break;

	case 14: //14: status
		show_div("divStatus");
		show_div("divStreamInfo");
		if (showCDKey) show_div("divCDKey");
		if (showCDKey) show_div("divButtons");
		if (showCDKey) show_div("divFirstHR");
		if (showerror) show_div("divError");
		configTitle = configTitle + translateOption('status') ;
	break;

	case 17: //17: Media Feeds
		writeMediaFusionTable();
		show_div("divMediaFusion");	
		configTitle = configTitle + translateOption('mediafeeds') ;
	break;

	case 18: //18: Quick Config
		show_div("divButtons");	
		show_div("divFirstHR");
		show_div("divCDKey");
		show_div("divFriendlyname"); 
		show_div("divContentdir");
		configTitle = configTitle + translateOption('quickstartup') ;
	break;

	case 19: //19: Transcoding
		show_div("divButtons");
		show_div("divFirstHR");
		show_div("divTranscoding");	
		configTitle = configTitle + translateOption('transcoding') ;
	break;
	
	case 20: //20: AutoUpdate
		show_div("divAutoUpdate");
		configTitle = configTitle + translateOption('autoupdate') ;
	break;

	default:  
	}
	title.innerHTML = configTitle;
} 
