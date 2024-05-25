var expired = false;
var div_array = new Array (
	"divPleaseWait",
	"divRestartWarning",
	"divEMPTY",
	"divButtons",
	"divStatus",
	"divAutoUpdate",
	"divLanguage",
	"divMediaFusion",
	"divFriendlyname",
	"divContentdir",
	"divScantime",
	"divCompilationsdir",  
	"divPlaylistnumentries",
	"divRemoteaccess",
	"divNicrestart",
	"divClientDB",
	"divTrouble",
	"divLogfile",
	"divExit",
	"divRestart",
	"divRescan",
	"divTreetype",
	"divError",
	"divFirstHR",
	"divCDKey",
	"divTranscoding",
	"divAutoShare",
	"divWebaccess",
	"divAggregation",
	"divStreamInfo",
	"divWebDav"
);
  
function hideAllDivs() 
{
	var hide ="";

	for (i = 0; i < div_array.length; i++) {
		hide = parent.CONT_FRAME.document.getElementById(div_array[i]);
		if (hide != null) {hide.style.display = "none";	} 
	}
}

function showAllDivs()  
{
	var show ="";

	for (i = 0; i < div_array.length; i++) {
		show = parent.CONT_FRAME.document.getElementById(div_array[i]);
		if (show != null) {show.style.display = "block";}
	}
}

function hide_div(div)
{
	var hide = parent.CONT_FRAME.document.getElementById(div);
	if (hide != null) hide.style.display = "none";
}

function show_div(div)
{
	var show = parent.CONT_FRAME.document.getElementById(div);
	if (getValue("suppressmenu").indexOf(div)<0) {
		if (show != null) show.style.display = "block";
	}
}
