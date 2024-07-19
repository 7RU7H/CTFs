/**************************************************************************
 *
 * STATUS-JSON.C -  Nagios Status JSON CGI
 *
 * Copyright (c) 2010 Nagios Enterprises, LLC
 *
 * License:
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2 as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
 *************************************************************************/

#include "../include/config.h"
#include "../include/common.h"
#include "../include/objects.h"
#include "../include/comments.h"
#include "../include/downtime.h"
#include "../include/macros.h"
#include "../include/statusdata.h"

#include "../include/cgiutils.h"
#include "../include/getcgi.h"
#include "../include/cgiauth.h"

extern char main_config_file[MAX_FILENAME_LENGTH];

extern time_t program_start;


extern host *host_list;
extern service *service_list;
extern hostgroup *hostgroup_list;
extern servicegroup *servicegroup_list;
extern hoststatus *hoststatus_list;
extern servicestatus *servicestatus_list;
extern scheduled_downtime *scheduled_downtime_list;

authdata current_authdata;


char  *json_encode(char *);
scheduled_downtime *get_next_host_downtime(char *);
scheduled_downtime *get_next_service_downtime(char *, char *);
void find_hosts_causing_outages(void);
int is_route_to_host_blocked(host *hst);

int total_outages=0;



int main(void){
	host *temp_host=NULL;
	service *temp_service=NULL;
	hostgroup *temp_hostgroup=NULL;
	servicegroup *temp_servicegroup=NULL;
	hostsmember *temp_hmember=NULL;
	servicesmember *temp_smember=NULL;
	hoststatus *temp_hoststatus=NULL;
	servicestatus *temp_servicestatus=NULL;
	scheduled_downtime *temp_downtime=NULL;
	char *status_str="";
	char state_duration[48];
	char *str="";
	int result=OK;
	int x=0;
	int y=0;
	int t=0;
	int duration_error=FALSE;
	int days=0;
	int hours=0;
	int minutes=0;
	int seconds=0;
	time_t current_time;
	time_t expire_time;
	char date_time[MAX_DATETIME_LENGTH];
	
	time(&current_time);
	
	
	printf("Cache-Control: no-store\r\n");
	printf("Pragma: no-cache\r\n");

	get_time_string(&current_time,date_time,(int)sizeof(date_time),HTTP_DATE_TIME);
	printf("Last-Modified: %s\r\n",date_time);

	expire_time=(time_t)0L;
	get_time_string(&expire_time,date_time,(int)sizeof(date_time),HTTP_DATE_TIME);
	printf("Expires: %s\r\n",date_time);

	printf("Content-type: text/plain\r\n\r\n");
	
	
	/* reset internal variables */
	reset_cgi_vars();

	/* read the CGI configuration file */
	result=read_cgi_config_file(get_cgi_config_location(), NULL);
	if(result==ERROR){
		cgi_config_file_error(get_cgi_config_location());
		return ERROR;
	        }

	/* read the main configuration file */
	result=read_main_config_file(main_config_file);
	if(result==ERROR){
		main_config_file_error(main_config_file);
		return ERROR;
	        }

	/* read all object configuration data */
	result=read_all_object_configuration_data(main_config_file,READ_ALL_OBJECT_DATA);
	if(result==ERROR){
			object_data_error();
			return ERROR;
            }

	/* read all status data */
	result=read_all_status_data(get_cgi_config_location(),READ_ALL_STATUS_DATA);
	if(result==ERROR){
		status_data_error();
		free_memory();
		return ERROR;
        }

	/* get authentication information */
	get_authentication_information(&current_authdata);

	// TODO
	int network_outages=0;
	
	
	/* begin json */
	printf("{\n");
	printf("\t\"jsonVersion\" : \" 0.0.1\",\n");
	printf("\t\"nagiosVersion\" : \"%s\",\n",PROGRAM_VERSION);
	printf("\t\"networkOutages\" : %d,\n",total_outages);
	
	/* loop through hosts... */
	printf("\t\"hosts\" :\n");
	printf("\t[\n");
	x=0;
	for(temp_host=host_list;temp_host!=NULL;temp_host=temp_host->next){
	
		if(is_authorized_for_host(temp_host,&current_authdata)==FALSE)
			continue;
	
		/* find the host status */
		temp_hoststatus=find_hoststatus(temp_host->name);
		if(temp_hoststatus==NULL)
			continue;

		/* status string */
		status_str="";
		switch(temp_hoststatus->status){
			case HOST_UP:
				status_str="UP";
				break;
			case HOST_DOWN:
				status_str="DOWN";
				break;
			case HOST_UNREACHABLE:
				status_str="UNREACHABLE";
				break;
			default:
				break;
			}
			
		/* state duration calculation... */
		t=0;
		duration_error=FALSE;
		if(temp_hoststatus->last_state_change==(time_t)0){
			if(program_start>current_time)
				duration_error=TRUE;
			else
				t=current_time-program_start;
				}
		else{
			if(temp_hoststatus->last_state_change>current_time)
				duration_error=TRUE;
			else
				t=current_time-temp_hoststatus->last_state_change;
				}
		get_time_breakdown((unsigned long)t,&days,&hours,&minutes,&seconds);
		if(duration_error==TRUE)
			snprintf(state_duration,sizeof(state_duration)-1,"???");
		else
			snprintf(state_duration,sizeof(state_duration)-1,"%2dd %2dh %2dm %2ds%s",days,hours,minutes,seconds,(temp_hoststatus->last_state_change==(time_t)0)?"+":"");
		state_duration[sizeof(state_duration)-1]='\x0';

			
		if(x>0)
			printf(",\n");
	
		printf("\t\t{\n");
		
		printf("\t\t\t\"nodeType\" : \"host\",\n");
		printf("\t\t\t\"name\" : \"%s\",\n",json_encode(temp_host->name));
		/*printf("\t\t\t\"type\" : null,\n");*/
		printf("\t\t\t\"iconImage\" : \"%s\",\n",(temp_host->icon_image==NULL)?"":json_encode(temp_host->icon_image));
		printf("\t\t\t\"iconImageAlt\" : \"%s\",\n",(temp_host->icon_image_alt==NULL)?"":json_encode(temp_host->icon_image_alt));
		printf("\t\t\t\"status\" : \"%s\",\n",json_encode(status_str));
		printf("\t\t\t\"statusInfo\" : \"%s\",\n",json_encode(temp_hoststatus->plugin_output));
		printf("\t\t\t\"duration\" : \"%s\",\n",json_encode(state_duration));
		printf("\t\t\t\"performanceData\" : \"%s\",\n",(temp_hoststatus->perf_data==NULL)?"":json_encode(temp_hoststatus->perf_data));
		
		printf("\t\t\t\"currentAttempt\" : \"%d/%d\",\n",temp_hoststatus->current_attempt,temp_hoststatus->max_attempts);
		
		if(temp_hoststatus->state_type==SOFT_STATE)
			str="SOFT";
		else
			str="HARD";
		printf("\t\t\t\"stateType\" : \"%s\",\n",json_encode(str));
		
		get_time_string(&temp_hoststatus->last_check,date_time,(int)sizeof(date_time),SHORT_DATE_TIME);
		if(temp_hoststatus->last_check==0L)
			date_time[0]='\x0';
		printf("\t\t\t\"lastCheckTime\" : \"%s\",\n",json_encode(date_time));
		
		if(temp_hoststatus->check_type==HOST_CHECK_ACTIVE)
			str="ACTIVE";
		else
			str="PASSIVE";
		printf("\t\t\t\"checkType\" : \"%s\",\n",json_encode(str));
			
		printf("\t\t\t\"checkLatency\" : \"%.4f seconds\",\n",temp_hoststatus->latency);
		printf("\t\t\t\"executionTime\" : \"%.4f seconds\",\n",temp_hoststatus->execution_time);
	
		get_time_string(&temp_hoststatus->next_check,date_time,(int)sizeof(date_time),SHORT_DATE_TIME);
		if(temp_hoststatus->next_check==0L)
			date_time[0]='\x0';
		printf("\t\t\t\"nextScheduledActiveCheck\" : \"%s\",\n",json_encode(date_time));

		get_time_string(&temp_hoststatus->last_state_change,date_time,(int)sizeof(date_time),SHORT_DATE_TIME);
		if(temp_hoststatus->last_state_change==0L)
			date_time[0]='\x0';
		printf("\t\t\t\"lastStateChangeTime\" : \"%s\",\n",json_encode(date_time));

		get_time_string(&temp_hoststatus->last_notification,date_time,(int)sizeof(date_time),SHORT_DATE_TIME);
		if(temp_hoststatus->last_notification==0L)
			date_time[0]='\x0';
		printf("\t\t\t\"lastNotificationTime\" : \"%s\",\n",json_encode(date_time));

		/*printf("\t\t\t\"lastNotification\" : \"%lu\",\n",temp_hoststatus->last_notification_id);*/
		
		if(temp_hoststatus->is_flapping==1)
			str="true";
		else
			str="false";
		printf("\t\t\t\"isHostFlapping\" : %s,\n",str);
		
		if(temp_hoststatus->scheduled_downtime_depth>0)
			str="true";
		else
			str="false";
		printf("\t\t\t\"inScheduledDowntime\" : %s,\n",str);
		
		get_time_string(&temp_hoststatus->last_update,date_time,(int)sizeof(date_time),SHORT_DATE_TIME);
		if(temp_hoststatus->last_update==0L)
			date_time[0]='\x0';
		printf("\t\t\t\"lastUpdateTime\" : \"%s\",\n",json_encode(date_time));
		
		printf("\t\t\t\"commandStatus\" :\n");
		printf("\t\t\t{\n");
		
		if(temp_hoststatus->checks_enabled==1)
			str="true";
		else
			str="false";
		printf("\t\t\t\t\"activeChecks\" : %s,\n",str);
		
		if(temp_hoststatus->accept_passive_checks==1)
			str="true";
		else
			str="false";
		printf("\t\t\t\t\"passiveChecks\" : %s,\n",str);

		if(temp_hoststatus->obsess==1)
			str="true";
		else
			str="false";
		printf("\t\t\t\t\"obsessing\" : %s,\n",str);

		if(temp_hoststatus->notifications_enabled==1)
			str="true";
		else
			str="false";
		printf("\t\t\t\t\"notifications\" : %s,\n",str);

		if(temp_hoststatus->event_handler_enabled==1)
			str="true";
		else
			str="false";
		printf("\t\t\t\t\"eventHandler\" : %s,\n",str);

		if(temp_hoststatus->flap_detection_enabled==1)
			str="true";
		else
			str="false";
		printf("\t\t\t\t\"flapDetection\" : %s,\n",str);

		if(temp_hoststatus->problem_has_been_acknowledged==1)
			str="true";
		else
			str="false";
		printf("\t\t\t\t\"problemAcknowledged\" : %s,\n",str);
		
		printf("\t\t\t\t\"nextScheduledDowntime\" :\n");
		printf("\t\t\t\t{\n");

		/* find next downtime */
		temp_downtime=get_next_host_downtime(temp_host->name);
		if(temp_downtime==NULL){
			printf("\t\t\t\t\t\"trigger\" : 0,\n");
			printf("\t\t\t\t\t\"startTime\" : \"\",\n");
			printf("\t\t\t\t\t\"duration\" : \"00:00:00\",\n");
			printf("\t\t\t\t\t\"type\" : \"\",\n");
			printf("\t\t\t\t\t\"childHostAction\" : 0,\n");
			printf("\t\t\t\t\t\"comment\" : \"\"\n");			
			printf("\t\t\t\t\t\"author\" : \"\"\n");			
			printf("\t\t\t\t\t\"armed\" : false\n");			
			}
		else{
			printf("\t\t\t\t\t\"trigger\" : %lu,\n",temp_downtime->triggered_by);
			get_time_string(&temp_downtime->start_time,date_time,(int)sizeof(date_time),SHORT_DATE_TIME);
			if(temp_downtime->start_time==0L)
				date_time[0]='\x0';
			printf("\t\t\t\t\t\"startTime\" : \"%s\",\n",json_encode(date_time));
			printf("\t\t\t\t\t\"duration\" : \"%lu\",\n",temp_downtime->duration);
			if(temp_downtime->fixed==1)
				str="fixed";
			else
				str="flexible";
			printf("\t\t\t\t\t\"type\" : \"%s\",\n",str);
			printf("\t\t\t\t\t\"childHostAction\" : 0,\n");
			printf("\t\t\t\t\t\"author\" : \"%s\",\n",json_encode(temp_downtime->author));
			printf("\t\t\t\t\t\"comment\" : \"%s\",\n",json_encode(temp_downtime->comment));
			printf("\t\t\t\t\t\"armed\" : true\n");			
			}

		printf("\t\t\t\t}\n");

		printf("\t\t\t},\n");


		/* loop through host services... */
		printf("\t\t\t\"services\" :\n");
		printf("\t\t\t[\n");
		y=0;
		for(temp_service=service_list;temp_service!=NULL;temp_service=temp_service->next){

			if(is_authorized_for_service(temp_service,&current_authdata)==FALSE)
				continue;
				
			/* find the service status */
			temp_servicestatus=find_servicestatus(temp_service->host_name,temp_service->description);
			if(temp_servicestatus==NULL)
				continue;

			/* status string */
			status_str="";
			switch(temp_servicestatus->status){
				case STATE_OK:
					status_str="OK";
					break;
				case STATE_WARNING:
					status_str="WARNING";
					break;
				case STATE_UNKNOWN:
					status_str="UNKNOWN";
					break;
				case STATE_CRITICAL:
					status_str="CRITICAL";
					break;
				default:
					break;
				}
				
			/* state duration calculation... */
			t=0;
			duration_error=FALSE;
			if(temp_servicestatus->last_state_change==(time_t)0){
				if(program_start>current_time)
					duration_error=TRUE;
				else
					t=current_time-program_start;
					}
			else{
				if(temp_servicestatus->last_state_change>current_time)
					duration_error=TRUE;
				else
					t=current_time-temp_servicestatus->last_state_change;
					}
			get_time_breakdown((unsigned long)t,&days,&hours,&minutes,&seconds);
			if(duration_error==TRUE)
				snprintf(state_duration,sizeof(state_duration)-1,"???");
			else
				snprintf(state_duration,sizeof(state_duration)-1,"%2dd %2dh %2dm %2ds%s",days,hours,minutes,seconds,(temp_servicestatus->last_state_change==(time_t)0)?"+":"");
			state_duration[sizeof(state_duration)-1]='\x0';


			if(y>0)
				printf(",\n");
				
			printf("\t\t\t\t{\n");
			
			printf("\t\t\t\t\t\"nodeType\" : \"service\",\n");
			printf("\t\t\t\t\t\"name\" : \"%s\",\n",json_encode(temp_service->description));
			
			/*printf("\t\t\t\t\t\"type\" : null,\n");*/
			printf("\t\t\t\t\t\"iconImage\" : \"%s\",\n",(temp_service->icon_image==NULL)?"":json_encode(temp_service->icon_image));
			printf("\t\t\t\t\t\"iconImageAlt\" : \"%s\",\n",(temp_service->icon_image_alt==NULL)?"":json_encode(temp_service->icon_image_alt));
			printf("\t\t\t\t\t\"status\" : \"%s\",\n",json_encode(status_str));
			printf("\t\t\t\t\t\"statusInfo\" : \"%s\",\n",json_encode(temp_servicestatus->plugin_output));
			printf("\t\t\t\t\t\"duration\" : \"%s\",\n",json_encode(state_duration));
			printf("\t\t\t\t\t\"performanceData\" : \"%s\",\n",(temp_servicestatus->perf_data==NULL)?"":json_encode(temp_servicestatus->perf_data));
			
			printf("\t\t\t\t\t\"currentAttempt\" : \"%d/%d\",\n",temp_servicestatus->current_attempt,temp_servicestatus->max_attempts);
			
			if(temp_servicestatus->state_type==SOFT_STATE)
				str="SOFT";
			else
				str="HARD";
			printf("\t\t\t\t\t\"stateType\" : \"%s\",\n",json_encode(str));
			
			get_time_string(&temp_servicestatus->last_check,date_time,(int)sizeof(date_time),SHORT_DATE_TIME);
			if(temp_servicestatus->last_check==0L)
				date_time[0]='\x0';
			printf("\t\t\t\t\t\"lastCheckTime\" : \"%s\",\n",json_encode(date_time));
			
			if(temp_servicestatus->check_type==SERVICE_CHECK_ACTIVE)
				str="ACTIVE";
			else
				str="PASSIVE";
			printf("\t\t\t\t\t\"checkType\" : \"%s\",\n",json_encode(str));
				
			printf("\t\t\t\t\t\"checkLatency\" : \"%.4f seconds\",\n",temp_servicestatus->latency);
			printf("\t\t\t\t\t\"executionTime\" : \"%.4f seconds\",\n",temp_servicestatus->execution_time);
		
			get_time_string(&temp_servicestatus->next_check,date_time,(int)sizeof(date_time),SHORT_DATE_TIME);
			if(temp_servicestatus->next_check==0L)
				date_time[0]='\x0';
			printf("\t\t\t\t\t\"nextScheduledActiveCheck\" : \"%s\",\n",json_encode(date_time));

			get_time_string(&temp_servicestatus->last_state_change,date_time,(int)sizeof(date_time),SHORT_DATE_TIME);
			if(temp_servicestatus->last_state_change==0L)
				date_time[0]='\x0';
			printf("\t\t\t\t\t\"lastStateChangeTime\" : \"%s\",\n",json_encode(date_time));

			get_time_string(&temp_servicestatus->last_notification,date_time,(int)sizeof(date_time),SHORT_DATE_TIME);
			if(temp_servicestatus->last_notification==0L)
				date_time[0]='\x0';
			printf("\t\t\t\t\t\"lastNotificationTime\" : \"%s\",\n",json_encode(date_time));

			/*printf("\t\t\t\"lastNotification\" : \"%lu\",\n",temp_servicestatus->last_notification_id);*/
			
			if(temp_servicestatus->is_flapping==1)
				str="true";
			else
				str="false";
			printf("\t\t\t\t\t\"isServiceFlapping\" : %s,\n",str);
			
			if(temp_servicestatus->scheduled_downtime_depth>0)
				str="true";
			else
				str="false";
			printf("\t\t\t\t\t\"inScheduledDowntime\" : %s,\n",str);
			
			get_time_string(&temp_servicestatus->last_update,date_time,(int)sizeof(date_time),SHORT_DATE_TIME);
			if(temp_servicestatus->last_update==0L)
				date_time[0]='\x0';
			printf("\t\t\t\t\t\"lastUpdateTime\" : \"%s\",\n",json_encode(date_time));
			
			printf("\t\t\t\t\t\"commandStatus\" :\n");
			printf("\t\t\t\t\t{\n");
			
			if(temp_servicestatus->checks_enabled==1)
				str="true";
			else
				str="false";
			printf("\t\t\t\t\t\t\"activeChecks\" : %s,\n",str);
			
			if(temp_servicestatus->accept_passive_checks==1)
				str="true";
			else
				str="false";
			printf("\t\t\t\t\t\t\"passiveChecks\" : %s,\n",str);

			if(temp_servicestatus->obsess==1)
				str="true";
			else
				str="false";
			printf("\t\t\t\t\t\t\"obsessing\" : %s,\n",str);

			if(temp_servicestatus->notifications_enabled==1)
				str="true";
			else
				str="false";
			printf("\t\t\t\t\t\t\"notifications\" : %s,\n",str);

			if(temp_servicestatus->event_handler_enabled==1)
				str="true";
			else
				str="false";
			printf("\t\t\t\t\t\t\"eventHandler\" : %s,\n",str);

			if(temp_servicestatus->flap_detection_enabled==1)
				str="true";
			else
				str="false";
			printf("\t\t\t\t\t\t\"flapDetection\" : %s,\n",str);

			if(temp_servicestatus->problem_has_been_acknowledged==1)
				str="true";
			else
				str="false";
			printf("\t\t\t\t\t\t\"problemAcknowledged\" : %s,\n",str);
			
			printf("\t\t\t\t\t\t\"nextScheduledDowntime\" :\n");
			printf("\t\t\t\t\t\t{\n");

			/* find next downtime */
			temp_downtime=get_next_service_downtime(temp_service->host_name,temp_service->description);
			if(temp_downtime==NULL){
				printf("\t\t\t\t\t\t\t\"trigger\" : 0,\n");
				printf("\t\t\t\t\t\t\t\"startTime\" : \"\",\n");
				printf("\t\t\t\t\t\t\t\"duration\" : \"00:00:00\",\n");
				printf("\t\t\t\t\t\t\t\"type\" : \"\",\n");
				printf("\t\t\t\t\t\t\t\"childHostAction\" : 0,\n");
				printf("\t\t\t\t\t\t\t\"comment\" : \"\"\n");			
				printf("\t\t\t\t\t\t\t\"author\" : \"\"\n");			
				printf("\t\t\t\t\t\t\t\"armed\" : false\n");			
				}
			else{
				printf("\t\t\t\t\t\t\t\"trigger\" : %lu,\n",temp_downtime->triggered_by);
				get_time_string(&temp_downtime->start_time,date_time,(int)sizeof(date_time),SHORT_DATE_TIME);
				if(temp_downtime->start_time==0L)
					date_time[0]='\x0';
				printf("\t\t\t\t\t\"startTime\" : \"%s\",\n",json_encode(date_time));
				printf("\t\t\t\t\t\t\t\"duration\" : \"%lu\",\n",temp_downtime->duration);
				if(temp_downtime->fixed==1)
					str="fixed";
				else
					str="flexible";
				printf("\t\t\t\t\t\t\t\"type\" : \"%s\",\n",str);
				printf("\t\t\t\t\t\t\t\"childHostAction\" : 0,\n");
				printf("\t\t\t\t\t\t\t\"author\" : \"%s\",\n",json_encode(temp_downtime->author));
				printf("\t\t\t\t\t\t\t\"comment\" : \"%s\",\n",json_encode(temp_downtime->comment));
				printf("\t\t\t\t\t\t\t\"armed\" : true\n");			
				}

			printf("\t\t\t\t\t\t}\n");
			
			printf("\t\t\t\t\t}\n");
			
			
			
			/* end service... */
			printf("\t\t\t\t}");
	
			y++;
			}
		printf("\n");
		printf("\t\t\t]\n");
		
		printf("\t\t}");
		
		x++;
		}
	printf("\n");
	printf("\t],\n");
		
		
		
	/* loop through hostgroups... */
	printf("\t\"hostGroups\" :\n");
	printf("\t[\n");
	x=0;
	for(temp_hostgroup=hostgroup_list;temp_hostgroup!=NULL;temp_hostgroup=temp_hostgroup->next){

		/* make sure the user is authorized to view this hostgroup */
		if(is_authorized_for_hostgroup(temp_hostgroup,&current_authdata)==FALSE)
			continue;
			
		if(x>0)
			printf(",\n");
		printf("\t\t{\n");
		
		printf("\t\t\t\"name\" : \"%s\",\n",json_encode(temp_hostgroup->alias));
		printf("\t\t\t\"shortName\" : \"%s\",\n",json_encode(temp_hostgroup->group_name));
		printf("\t\t\t\"group\" :\n");
		printf("\t\t\t[\n");
			
	
		/* find all the hosts that belong to the hostgroup */
		y=0;
		for(temp_hmember=temp_hostgroup->members;temp_hmember!=NULL;temp_hmember=temp_hmember->next){
		
			/* find the host... */
			temp_host=find_host(temp_hmember->host_name);
			if(temp_host==NULL)
				continue;
				
			/* make sure user is authorized to view host */
			if(is_authorized_for_host(temp_host,&current_authdata)==FALSE)
				continue;
				
			if(y>0)
				printf(",\n");

			printf("\t\t\t\t\"%s\"",json_encode(temp_host->name));
			
			y++;
			}
		printf("\n");
			
		printf("\t\t\t]\n");
	
		printf("\t\t}");
		x++;
		}
	printf("\n\n");
	printf("\t],\n");

	
	/* loop through servicegroups... */
	printf("\t\"serviceGroups\" :\n");
	printf("\t[\n");
	x=0;
	for(temp_servicegroup=servicegroup_list;temp_servicegroup!=NULL;temp_servicegroup=temp_servicegroup->next){

		/* make sure the user is authorized to view this servicegroup */
		if(is_authorized_for_servicegroup(temp_servicegroup,&current_authdata)==FALSE)
			continue;
			
		if(x>0)
			printf(",\n");
		printf("\t\t{\n");
		
		printf("\t\t\t\"name\" : \"%s\",\n",json_encode(temp_servicegroup->alias));
		printf("\t\t\t\"shortName\" : \"%s\",\n",json_encode(temp_servicegroup->group_name));
		printf("\t\t\t\"group\" :\n");
		printf("\t\t\t[\n");
			
	
		/* find all the services that belong to the servicegroup */
		y=0;
		for(temp_smember=temp_servicegroup->members;temp_smember!=NULL;temp_smember=temp_smember->next){
		
				
			if(y>0)
				printf(",\n");

			printf("\t\t\t\t{\n");
			printf("\t\t\t\t\t\"host\" : \"%s\",\n",json_encode(temp_smember->host_name));
			printf("\t\t\t\t\t\"service\" : \"%s\"\n",json_encode(temp_smember->service_description));
			printf("\t\t\t\t}");
			
			y++;
			}
		printf("\n");
			
		printf("\t\t\t]\n");
	
		printf("\t\t}");
		x++;
		}
	printf("\n\n");
	printf("\t]\n");
		
		
	/* close JSON */
	printf("}\n");
	
	}
	
char *json_encode(char *str){
	char *newstr=NULL;
	char ch;
	int x=0;
	int y=0;
	
	if(str==NULL)
		return NULL;
		
	newstr=(char *)malloc((strlen(str)*4)+1);
	newstr[0]='\x0';
	
	/* walk the original string */
	for(x=0,y=0;str[x]!='\x0';x++){
		ch=str[x];
		/* replace slashes */
		if(ch=='\\'){
			newstr[y++]='\\';
			newstr[y++]=ch;
			}
		/* replace double quotes */
		else if(ch=='\"'){
			newstr[y++]='\\';
			newstr[y++]=ch;
			}
		else{
			newstr[y++]=ch;
			}
		}
	newstr[y]='\x0';	

	return str;
	}

scheduled_downtime *get_next_host_downtime(char *host_name){
	scheduled_downtime *temp_downtime=NULL;
	scheduled_downtime *the_downtime=NULL;
	time_t earliest_time=0L;

	/* find next downtime */
	for(temp_downtime=scheduled_downtime_list;temp_downtime!=NULL;temp_downtime=temp_downtime->next){
	
		if(temp_downtime->type==HOST_DOWNTIME && !strcmp(host_name,temp_downtime->host_name)){
		
			/* we found the first or earliest downtime */
			if(earliest_time==0L || (temp_downtime->start_time < earliest_time)){
				earliest_time=temp_downtime->start_time;
				the_downtime=temp_downtime;
				}
			}
			
		}

	return the_downtime;
	}
	

scheduled_downtime *get_next_service_downtime(char *host_name, char *service_name){
	scheduled_downtime *temp_downtime=NULL;
	scheduled_downtime *the_downtime=NULL;
	time_t earliest_time=0L;

	/* find next downtime */
	for(temp_downtime=scheduled_downtime_list;temp_downtime!=NULL;temp_downtime=temp_downtime->next){
	
		if(temp_downtime->type==SERVICE_DOWNTIME && !strcmp(host_name,temp_downtime->host_name) && !strcmp(service_name,temp_downtime->service_description)){
		
			/* we found the first or earliest downtime */
			if(earliest_time==0L || (temp_downtime->start_time < earliest_time)){
				earliest_time=temp_downtime->start_time;
				the_downtime=temp_downtime;
				}
			}
			
		}

	return the_downtime;
	}
	
	
void find_hosts_causing_outages(void){
	host *temp_host=NULL;
	hoststatus *temp_hoststatus=NULL;

	/* check all hosts */
	for(temp_hoststatus=hoststatus_list;temp_hoststatus!=NULL;temp_hoststatus=temp_hoststatus->next){

		/* check only hosts that are not up and not pending */
		if(temp_hoststatus->status!=HOST_UP && temp_hoststatus->status!=HOST_PENDING){

			/* find the host entry */
			temp_host=find_host(temp_hoststatus->host_name);

			if(temp_host==NULL)
				continue;

			/* if the route to this host is not blocked, it is a causing an outage */
			if(is_route_to_host_blocked(temp_host)==FALSE)
				total_outages++;
		        }
	        }

	return;
        }



/* tests whether or not a host is "blocked" by upstream parents (host is already assumed to be down or unreachable) */
int is_route_to_host_blocked(host *hst){
	hostsmember *temp_hostsmember=NULL;
	hoststatus *temp_hoststatus=NULL;

	/* if the host has no parents, it is not being blocked by anyone */
	if(hst->parent_hosts==NULL)
		return FALSE;

	/* check all parent hosts */
	for(temp_hostsmember=hst->parent_hosts;temp_hostsmember!=NULL;temp_hostsmember=temp_hostsmember->next){

		/* find the parent host's status */
		temp_hoststatus=find_hoststatus(temp_hostsmember->host_name);

		if(temp_hoststatus==NULL)
			continue;

		/* at least one parent it up (or pending), so this host is not blocked */
		if(temp_hoststatus->status==HOST_UP || temp_hoststatus->status==HOST_PENDING)
			return FALSE;
	        }

	return TRUE;
        }
