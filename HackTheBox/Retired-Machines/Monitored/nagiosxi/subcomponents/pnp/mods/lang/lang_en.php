<?php
# 
# $Id$
#
#
define("_RRDFILE","RRD Database");
define("_ERRHOSTNAMENOTSET","Variable <b>host</b> not defined.<br><b>Example:</b> index.php?srv=ping&host=server1<br>");
define("_ERRSERVICEDESCNOTSET","Variable <b>srv</b> not defined<br><b>Example:</b> index.php?srv=ping?host=server1<br>");
define("_ERRORSFOUND","<H3>Errors found...</H3><br>");
define("_NOTREADABLE","not readable ");
define("_NOTEXIST","does not exist");
# Service List
define("_HOST","Host: ");
define("_HOSTSTATE","Hoststate: ");
define("_SERVICE","Service: ");
define("_SERVICESTATE","Servicestate: ");
define("_TIMET","Created: ");
define("_SEARCH","Search: ");
define("_NOTES","Notes: ");
define("_DOKU","Documentation");
define("_DATASOURCE", "Datasource: ");
define("_HOSTPERFDATA", "Host Perfdata");

# PDF
define("_PDFPAGE","Page ");
define("_PDFTITLE","");  // MOD
define("_PDFHOST","Host: ");
define("_PDFSERVICE","Service: ");
define("_PDFTIMET","Last Check: ");
#MouseOver
define("_OVERNOTPL","No template found for this service<br><strong>Link deaktivated</strong>");
define("_OVERINACT","Data for this service is too old");
#Debugger
define("_STYLE_CRIT_S", "<p id=\"critical\">");
define("_STYLE_WARN_S", "<p id=\"warning\">");
define("_STYLE_OK_S", "<p id=\"ok\">");
define("_STYLE_E", "</p>");
define('_CRIT', 'Critical: ');
define('_WARN', 'Warning: ');
define('_OK', 'Ok: ');
define('_NOTFOUND', ' not found.');
define('_FOUND', ' found.');
define("_NOTEXECUTABLE"," not executable ");
define("_EXECUTABLE"," is executable ");
define('_SET', ' is set.');
define('_NOTSET', ' is not set.');
define('_RRDBASE', ' RRD Base Directory ');
define('_RRDDEF', ' RRD Definition File ');
define('_TEMPLATE', ' Template ');
define('_HOSTNAME', ' Hostname ');
define('_SERVICEDESC', ' Service Description ');
define('_RRDTOOL', ' RRDTool ');
define('_FUNCTION', ' PHP Function ');
define('_DISABLED', ' is disabled ');
define('_ENABLED', ' is enabled ');
define('_DIRECTORY', ' Directory ');
define('_USEING', ' Using ');
define('_INIT', ' Initalising ');
define('_INC', ' Include ');
define('_TEMPLATE_ERR', ' Template contains errors. Possible Syntax Error or blanks outside PHP Tags ');
define('_DATA_FOUND', ' Output found ');
define('_RRDTOOL_ERR', ' RRDTool exits with errors ->  ');
define('_RRDTOOL_CALL', ' RRDTool was called like -><br>  ');
#0.4
define('_NO_RRD_FILES', ' No RRD Files found ');
define('_ZLIB_SUPPORT', ' PHP zlib Support ');
define('_GDLIB_SUPPORT', ' PHP GD Support ');
?>
