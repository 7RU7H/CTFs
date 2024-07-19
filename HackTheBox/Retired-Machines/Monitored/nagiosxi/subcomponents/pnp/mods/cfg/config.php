<?php
##
## Program: PNP , Performance Data Addon for Nagios(r)
## Version: $Id$
## License: GPL
## Copyright (c) 2005-2008 Joerg Linge (http://www.pnp4nagios.org)
##
## This program is free software; you can redistribute it and/or
## modify it under the terms of the GNU General Public License
## as published by the Free Software Foundation; either version 2
## of the License, or (at your option) any later version.
##
## This program is distributed in the hope that it will be useful,
## but WITHOUT ANY WARRANTY; without even the implied warranty of
## MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
## GNU General Public License for more details.
##
## You should have received a copy of the GNU General Public License
## along with this program; if not, write to the Free Software
## Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
##
# Credit:  Tobi Oetiker, http://people.ee.ethz.ch/~oetiker/webtools/rrdtool/
#
# Path to rrdtool
#
$conf['rrdtool'] = "/usr/bin/rrdtool";
#
# RRDTool graph Image Size
#
$conf['graph_width'] = "500";
$conf['graph_height'] = "100";
#
# Additional Options for RRDTool
#
$conf['graph_opt'] = ""; 
#
# Directory where the RRD Files will be stored
#
$conf['rrdbase'] = "/usr/local/nagios/share/perfdata/";
#
# Page Config Location
#
$conf['page_dir'] = "/usr/local/nagios/etc/pnp/pages/";
#
# Site Refresh Time in Secounds
#
$conf['refresh'] = "90";
#
# Max Age for RRD Files in Secounds
# 
$conf['max_age'] = 60*60*6;   
#
# Directory for Temporary Files used for PDF creation 
#
$conf['temp'] = "/var/tmp";
#
# Link to Nagios CGIs
#
$conf['nagios_base'] = "/nagiosxi/includes/components/coreui";
#
# Which User is allowed to see additional Service Links ?
# Keywords: EVERYONE NONE <USERNAME>
# Example: conf['allowed_for_service_links'] = "nagiosadmin,operator";
# 
$conf['allowed_for_service_links'] = "EVERYONE";
#
# Who can use the Host Search Funktion ?
# Keywords: EVERYONE NONE <USERNAME>
#
$conf['allowed_for_host_search'] = "EVERYONE";
#
# Who can use the Host Overview ?
# This Funktion is called if no Service Description is given.  
#
$conf['allowed_for_host_overview'] = "EVERYONE";
#
#Who can use the Pages function?
# Keywords: EVERYONE NONE <USERNAME>
# Example: conf['allowed_for_pages'] = "nagiosadmin,operator";
#
$conf['allowed_for_pages'] = "EVERYONE";
#
# Which Time Rage should be used for the Host Overview Site ? 
# Use a Key from Array $views[]
#
$conf['overview-range'] = 1 ;
#
# Use Language lang/lang_['lang'].php .
# Valid Options are de,en,nl,se,fr
#
$conf['lang'] = "en";
#
# Date Format
#
//$conf['date_fmt'] = "d.m.y G:i";
$conf['date_fmt'] = "m-d-Y G:i";
#
# Use FPDF Lib for PDF Creation ?
#
$conf['use_fpdf'] = 1;
#
# Use this file as PDF Background.
#
$conf['background_pdf'] = '/usr/local/nagios/etc/pnp/background.pdf' ;
#
# Enable JSCalendar
#
$conf['use_calendar'] = 1;
#
# Define default views with Title and start Timerange in seconds 
#
# remarks: required escape on " with backslash
#
$views[0]["title"] = "4 Hours";
$views[0]["start"] = ( 60*60*4 );

$views[1]["title"] = "24 Hours";
$views[1]["start"] = ( 60*60*24 );

$views[2]["title"] = "One Week";
$views[2]["start"] = ( 60*60*24*7 );

$views[3]["title"] = "One Month";
$views[3]["start"] = ( 60*60*24*30 );

$views[4]["title"] = "One Year";
$views[4]["start"] = ( 60*60*24*365 );



?>
