<?php
// BACKEND API URL COMPONENT
//
// Copyright (c) 2010 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: backendapiurl.inc.php 901 2012-10-26 21:11:02Z mguthrie $

require_once(dirname(__FILE__).'/../componenthelper.inc.php');

// respect the name
$backendapiurl_component_name="backendapiurl";

// run the initialization function
backendapiurl_component_init();

////////////////////////////////////////////////////////////////////////
// COMPONENT INIT FUNCTIONS
////////////////////////////////////////////////////////////////////////

function backendapiurl_component_init(){
	global $backendapiurl_component_name;
	
	$args=array(

		// need a name
		COMPONENT_NAME => $backendapiurl_component_name,
		COMPONENT_VERSION => '1.0',
		// informative information
		COMPONENT_AUTHOR => "Nagios Enterprises, LLC",
		COMPONENT_DESCRIPTION => gettext("Provides information on the URLs used to access the Nagios XI backend API."),
		COMPONENT_TITLE => "Backend API URL",

		// configuration function (optional)
		COMPONENT_CONFIGFUNCTION => "backendapiurl_component_config_func",
		);
		
	register_component($backendapiurl_component_name,$args);
	
	}
	



///////////////////////////////////////////////////////////////////////////////////////////
//CONFIG FUNCTIONS
///////////////////////////////////////////////////////////////////////////////////////////

function backendapiurl_component_config_func($mode="",$inargs,&$outargs,&$result){
	global $backendapiurl_component_name;

	// initialize return code and output
	$result=0;
	$output="";
	
	//delete_option("backendapiurl_component_options");
	

	switch($mode){
		case COMPONENT_CONFIGMODE_GETSETTINGSHTML:
		
			// values passed to us
			$username=grab_array_var($inargs,"username","");
						
			$component_url=get_component_url_base($backendapiurl_component_name);
	

			// get list of users from the backend
			$args=array(
				"cmd" => "getusers",
				);
			$xmlusers=get_backend_xml_data($args);

			//print_r($xmlusers);
			
			$output='';
			
			$output.='
				<p>
				<b>'.gettext('Developers').':</b> 
				'.gettext('The Nagios XI backend API can be used to access current and historical information on monitored hosts and services for integration into third-party frontends.  In order to access XML data via the backend API you must pass a username and a backend ticket to identify yourself.  Without the proper credentials, no data is returned.').'
				</p>
				';
			
			$base_url=get_base_url();
			
			if($username!=""){
			
				$uid=get_user_id($username);
				if($uid==0)
					$backend_ticket="";
				else
					$backend_ticket=get_user_attr($uid,"backend_ticket");
				
				$output.='

				<div class="sectionTitle">'.gettext('Backend API URLs').'</div>
				
				<p>'.gettext('You can use the URLs below to fetch information from the Nagios XI backend API.').'  
				<b>'.gettext('Note').':</b> 
				'.gettext('It is important to retain the <em>username</em> and <em>ticket</em> query parameters.').'</p>
				
				<table class="standardtable">
				
				<tr>
				<th>'.gettext('Data Type').'</th>
				<th>URL</th>
				</tr>
				';
				
				$opts=array(
					gettext("Current Host Status") => "gethoststatus",
					gettext("Current Service Status") => "getservicestatus",
					gettext("Current Program Status") => "getprogramstatus",
					gettext("Current Program Performance") => "getprogramperformance",
					gettext("System Statistics") => "getsysstat",
					gettext("Log Entries") => "getlogentries",
					gettext("State History") => "getstatehistory",
					gettext("Comments") => "getcomments",
					gettext("Scheduled Downtime") => "getscheduleddowntime",
					gettext("Users") => "getusers",
					gettext("Contact") => "getcontacts",
					gettext("Hosts") => "gethosts",
					gettext("Services") => "getservices",
					gettext("Hostgroups") => "gethostgroups",
					gettext("Servicegroups") => "getservicegroups",
					gettext("Contactgroups") => "getcontactgroups",
					gettext("Hostgroup Members") => "gethostgroupmembers",
					gettext("Servicegroup Members") => "getservicegroupmembers",
					gettext("Contactgroup Members") => "getcontactgroupmembers",
					);
					
				$x=0;
				foreach($opts as $desc => $urlopts){
					$x++;

					$output.='
				<tr>
				<td valign="top"><label>'.$desc.':</label></td>
				<td valign="top">
				<input type="text" size="80" name="url'.$x.'" value="'.$base_url.'backend/?cmd='.$urlopts.'&username='.htmlentities($username).'&ticket='.htmlentities($backend_ticket).'">
				</td>
				</tr>
				';
					}
				
				$output.='
				</table>
				';
				
				}
			
			$output.='

			<div class="sectionTitle">'.gettext('Account Selection').'</div>
			
			<p>'.gettext('Select the user account you would like to get backend API URLs for.').'</p>
				
			<table>

			<tr>
			<td valign="top"><label>'.gettext('User').':</label></td>
			<td valign="top">
			<select name="username">
			<option value="">'.gettext('SELECT ONE').'</option>
			';
			
			if($xmlusers){
				foreach($xmlusers->user as $u){
					//print_r($u);
					
					$uid=get_user_id($u->username);
					
					$output.="<option value='".$u->username."' ".is_selected($username,strval($u->username)).">".$u->name." (".$u->username.")</option>\n";
					}
				}
	
			$output.='
			</select>
			</td>
			</tr>
			
			</table>
			';
		

			break;
			
		case COMPONENT_CONFIGMODE_SAVESETTINGS:
		
			$username=grab_array_var($inargs,"username");
			if($username==""){
				$result=1;
				$errmsg=array();
				$errmsg[]=gettext("Please select a username to obtain backend URL information.");
				$outargs[COMPONENT_ERROR_MESSAGES]=$errmsg;
				}
		
					
			// info messages
			$okmsg=array();
			$okmsg[]=gettext("Backend API URLs are shown below.");
			$outargs[COMPONENT_INFO_MESSAGES]=$okmsg;
			
			break;
		
		default:
			break;
			
		}
		
	return $output;
	}



?>