/***********************************************************************
 *
 * TAC-XML.C - Nagios Tactical Monitoring Overview CGI
 *
 * Copyright (c) 2010 Nagios Enterprises, LLC (www.nagios.com)
 * Copyright (c) 2001-2008 Ethan Galstad (egalstad@nagios.org)
 * Last Modified: 08-07-2010
 *
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
 ***********************************************************************/

#include "../include/config.h"
#include "../include/common.h"
#include "../include/objects.h"
#include "../include/statusdata.h"

#include "../include/getcgi.h"
#include "../include/cgiutils.h"
#include "../include/cgiauth.h"


/* HOSTOUTAGE structure */
typedef struct hostoutage_struct{
	host *hst;
	int  affected_child_hosts;
	struct hostoutage_struct *next;
        }hostoutage;


extern char   main_config_file[MAX_FILENAME_LENGTH];
extern char   url_html_path[MAX_FILENAME_LENGTH];
extern char   url_images_path[MAX_FILENAME_LENGTH];
extern char   url_stylesheets_path[MAX_FILENAME_LENGTH];
extern char   url_media_path[MAX_FILENAME_LENGTH];

extern int    refresh_rate;

extern char *service_critical_sound;
extern char *service_warning_sound;
extern char *service_unknown_sound;
extern char *host_down_sound;
extern char *host_unreachable_sound;
extern char *normal_sound;

extern host *host_list;
extern hostgroup *hostgroup_list;
extern hoststatus *hoststatus_list;
extern servicestatus *servicestatus_list;

extern int enable_notifications;
extern int execute_service_checks;
extern int accept_passive_service_checks;
extern int enable_event_handlers;
extern int enable_flap_detection;

extern int nagios_process_state;




void analyze_status_data(void);
void display_tac_overview(void);

void find_hosts_causing_outages(void);
void calculate_outage_effect_of_host(host *,int *);
int is_route_to_host_blocked(host *);
int number_of_host_services(host *);
void add_hostoutage(host *);
void free_hostoutage_list(void);

void document_header(int);
void document_footer(void);
int process_cgivars(void);

authdata current_authdata;

int embedded=FALSE;
int display_header=FALSE;

hostoutage *hostoutage_list=NULL;

int total_blocking_outages=0;
int total_nonblocking_outages=0;

int total_service_health=0;
int total_host_health=0;
int potential_service_health=0;
int potential_host_health=0;
double percent_service_health=0.0;
double percent_host_health=0.0;

int total_hosts=0;
int total_services=0;

int total_active_service_checks=0;
int total_active_host_checks=0;
int total_passive_service_checks=0;
int total_passive_host_checks=0;

double min_service_execution_time=-1.0;
double max_service_execution_time=-1.0;
double total_service_execution_time=0.0;
double average_service_execution_time=-1.0;
double min_host_execution_time=-1.0;
double max_host_execution_time=-1.0;
double total_host_execution_time=0.0;
double average_host_execution_time=-1.0;
double min_service_latency=-1.0;
double max_service_latency=-1.0;
double total_service_latency=0.0;
double average_service_latency=-1.0;
double min_host_latency=-1.0;
double max_host_latency=-1.0;
double total_host_latency=0.0;
double average_host_latency=-1.0;

int flapping_services=0;
int flapping_hosts=0;
int flap_disabled_services=0;
int flap_disabled_hosts=0;
int notification_disabled_services=0;
int notification_disabled_hosts=0;
int event_handler_disabled_services=0;
int event_handler_disabled_hosts=0;
int active_checks_disabled_services=0;
int active_checks_disabled_hosts=0;
int passive_checks_disabled_services=0;
int passive_checks_disabled_hosts=0;

int hosts_pending=0;
int hosts_pending_disabled=0;
int hosts_up_disabled=0;
int hosts_up_unacknowledged=0;
int hosts_up=0;
int hosts_down_scheduled=0;
int hosts_down_acknowledged=0;
int hosts_down_disabled=0;
int hosts_down_unacknowledged=0;
int hosts_down=0;
int hosts_unreachable_scheduled=0;
int hosts_unreachable_acknowledged=0;
int hosts_unreachable_disabled=0;
int hosts_unreachable_unacknowledged=0;
int hosts_unreachable=0;

int services_pending=0;
int services_pending_disabled=0;
int services_ok_disabled=0;
int services_ok_unacknowledged=0;
int services_ok=0;
int services_warning_host_problem=0;
int services_warning_scheduled=0;
int services_warning_acknowledged=0;
int services_warning_disabled=0;
int services_warning_unacknowledged=0;
int services_warning=0;
int services_unknown_host_problem=0;
int services_unknown_scheduled=0;
int services_unknown_acknowledged=0;
int services_unknown_disabled=0;
int services_unknown_unacknowledged=0;
int services_unknown=0;
int services_critical_host_problem=0;
int services_critical_scheduled=0;
int services_critical_acknowledged=0;
int services_critical_disabled=0;
int services_critical_unacknowledged=0;
int services_critical=0;


/*efine DEBUG 1*/

int main(void){
	int result=OK;
	char *sound=NULL;
#ifdef DEBUG
	time_t t1,t2,t3,t4,t5,t6,t7,t8,t9;
#endif


#ifdef DEBUG
	time(&t1);
#endif

	/* get the CGI variables passed in the URL */
	process_cgivars();

	/* reset internal variables */
	reset_cgi_vars();

	/* read the CGI configuration file */
	result=read_cgi_config_file(get_cgi_config_location(), NULL);
	if(result==ERROR){
		document_header(FALSE);
		cgi_config_file_error(get_cgi_config_location());
		document_footer();
		return ERROR;
	        }

#ifdef DEBUG
	time(&t2);
#endif

	/* read the main configuration file */
	result=read_main_config_file(main_config_file);
	if(result==ERROR){
		document_header(FALSE);
		main_config_file_error(main_config_file);
		document_footer();
		return ERROR;
	        }

#ifdef DEBUG
	time(&t3);
#endif

	/* read all object configuration data */
	result=read_all_object_configuration_data(main_config_file,READ_ALL_OBJECT_DATA);
	if(result==ERROR){
		document_header(FALSE);
		object_data_error();
		document_footer();
		return ERROR;
                }

#ifdef DEBUG
	time(&t4);
#endif

	/* read all status data */
	result=read_all_status_data(get_cgi_config_location(),READ_ALL_STATUS_DATA);
	if(result==ERROR){
		document_header(FALSE);
		status_data_error();
		document_footer();
		free_memory();
		return ERROR;
                }

#ifdef DEBUG
	time(&t5);
#endif

	document_header(TRUE);

	/* get authentication information */
	get_authentication_information(&current_authdata);

#ifdef DEBUG
	time(&t6);
#endif

	/* analyze current host and service status data for tac overview */
	analyze_status_data();

#ifdef DEBUG
	time(&t7);
#endif

	/* find all hosts that are causing network outages */
	find_hosts_causing_outages();


#ifdef DEBUG
	time(&t8);
#endif

	/**** display main tac screen ****/
	display_tac_overview();

#ifdef DEBUG
	time(&t9);
#endif

	document_footer();

	/* free memory allocated to the host outage list */
	free_hostoutage_list();

	/* free allocated memory */
	free_memory();

#ifdef DEBUG
	printf("T1: %lu\n",(unsigned long)t1);
	printf("T2: %lu\n",(unsigned long)t2);
	printf("T3: %lu\n",(unsigned long)t3);
	printf("T4: %lu\n",(unsigned long)t4);
	printf("T5: %lu\n",(unsigned long)t5);
	printf("T6: %lu\n",(unsigned long)t6);
	printf("T7: %lu\n",(unsigned long)t7);
	printf("T8: %lu\n",(unsigned long)t8);
	printf("T9: %lu\n",(unsigned long)t9);
#endif
	
	return OK;
        }




void document_header(int use_stylesheet){
	char date_time[MAX_DATETIME_LENGTH];
	time_t current_time;
	time_t expire_time;

	printf("Cache-Control: no-store\r\n");
	printf("Pragma: no-cache\r\n");
	printf("Refresh: %d\r\n",refresh_rate);

	time(&current_time);
	get_time_string(&current_time,date_time,(int)sizeof(date_time),HTTP_DATE_TIME);
	printf("Last-Modified: %s\r\n",date_time);

	expire_time=(time_t)0L;
	get_time_string(&expire_time,date_time,(int)sizeof(date_time),HTTP_DATE_TIME);
	printf("Expires: %s\r\n",date_time);

	printf("Content-type: text/xml\r\n\r\n");

	printf("<?xml version=\"1.0\" encoding=\"utf-8\"?>\n");
	printf("<tacinfo>\n");


	return;
        }


void document_footer(void){

	printf("</tacinfo>\n");

	return;
        }


int process_cgivars(void){
	char **variables;
	int error=FALSE;
	int x;

	variables=getcgivars();

	for(x=0;variables[x]!=NULL;x++){

		/* do some basic length checking on the variable identifier to prevent buffer overflows */
		if(strlen(variables[x])>=MAX_INPUT_BUFFER-1){
			continue;
		        }

		/* we found the embed option */
		else if(!strcmp(variables[x],"embedded"))
			embedded=TRUE;

		/* we found the noheader option */
		else if(!strcmp(variables[x],"noheader"))
			display_header=FALSE;

		/* we received an invalid argument */
		else
			error=TRUE;
	
	        }

	/* free memory allocated to the CGI variables */
	free_cgivars(variables);

	return error;
        }



void analyze_status_data(void){
	servicestatus *temp_servicestatus;
	service *temp_service;
	hoststatus *temp_hoststatus;
	host *temp_host;
	int problem=TRUE;


	/* check all services */
	for(temp_servicestatus=servicestatus_list;temp_servicestatus!=NULL;temp_servicestatus=temp_servicestatus->next){

		/* see if user is authorized to view this service */
		temp_service=find_service(temp_servicestatus->host_name,temp_servicestatus->description);
		if(is_authorized_for_service(temp_service,&current_authdata)==FALSE)
			continue;

		/******** CHECK FEATURES *******/

		/* check flapping */
		if(temp_servicestatus->flap_detection_enabled==FALSE)
			flap_disabled_services++;
		else if(temp_servicestatus->is_flapping==TRUE)
			flapping_services++;

		/* check notifications */
		if(temp_servicestatus->notifications_enabled==FALSE)
			notification_disabled_services++;

		/* check event handler */
		if(temp_servicestatus->event_handler_enabled==FALSE)
			event_handler_disabled_services++;

		/* active check execution */
		if(temp_servicestatus->checks_enabled==FALSE)
			active_checks_disabled_services++;

		/* passive check acceptance */
		if(temp_servicestatus->accept_passive_checks==FALSE)
			passive_checks_disabled_services++;


		/********* CHECK STATUS ********/

		problem=TRUE;

		if(temp_servicestatus->status==SERVICE_OK){
			if(temp_servicestatus->checks_enabled==FALSE)
				services_ok_disabled++;
			else
				services_ok_unacknowledged++;
			services_ok++;
		        }

		else if(temp_servicestatus->status==SERVICE_WARNING){
			temp_hoststatus=find_hoststatus(temp_servicestatus->host_name);
			if(temp_hoststatus!=NULL && (temp_hoststatus->status==HOST_DOWN || temp_hoststatus->status==HOST_UNREACHABLE)){
				services_warning_host_problem++;
				problem=FALSE;
			        }
			if(temp_servicestatus->scheduled_downtime_depth>0){
				services_warning_scheduled++;
				problem=FALSE;
			        }
			if(temp_servicestatus->problem_has_been_acknowledged==TRUE){
				services_warning_acknowledged++;
				problem=FALSE;
			        }
			if(temp_servicestatus->checks_enabled==FALSE){
				services_warning_disabled++;
				problem=FALSE;
			        }
			if(problem==TRUE)
				services_warning_unacknowledged++;
			services_warning++;
		        }

		else if(temp_servicestatus->status==SERVICE_UNKNOWN){
			temp_hoststatus=find_hoststatus(temp_servicestatus->host_name);
			if(temp_hoststatus!=NULL && (temp_hoststatus->status==HOST_DOWN || temp_hoststatus->status==HOST_UNREACHABLE)){
				services_unknown_host_problem++;
				problem=FALSE;
			        }
			if(temp_servicestatus->scheduled_downtime_depth>0){
				services_unknown_scheduled++;
				problem=FALSE;
			        }
			if(temp_servicestatus->problem_has_been_acknowledged==TRUE){
				services_unknown_acknowledged++;
				problem=FALSE;
			        }
			if(temp_servicestatus->checks_enabled==FALSE){
				services_unknown_disabled++;
				problem=FALSE;
			        }
			if(problem==TRUE)
				services_unknown_unacknowledged++;
			services_unknown++;
		        }

		else if(temp_servicestatus->status==SERVICE_CRITICAL){
			temp_hoststatus=find_hoststatus(temp_servicestatus->host_name);
			if(temp_hoststatus!=NULL && (temp_hoststatus->status==HOST_DOWN || temp_hoststatus->status==HOST_UNREACHABLE)){
				services_critical_host_problem++;
				problem=FALSE;
			        }
			if(temp_servicestatus->scheduled_downtime_depth>0){
				services_critical_scheduled++;
				problem=FALSE;
			        }
			if(temp_servicestatus->problem_has_been_acknowledged==TRUE){
				services_critical_acknowledged++;
				problem=FALSE;
			        }
			if(temp_servicestatus->checks_enabled==FALSE){
				services_critical_disabled++;
				problem=FALSE;
			        }
			if(problem==TRUE)
				services_critical_unacknowledged++;
			services_critical++;
		        }

		else if(temp_servicestatus->status==SERVICE_PENDING){
			if(temp_servicestatus->checks_enabled==FALSE)
				services_pending_disabled++;
			services_pending++;
		        }


		/* get health stats */
		if(temp_servicestatus->status==SERVICE_OK)
			total_service_health+=2;

		else if(temp_servicestatus->status==SERVICE_WARNING || temp_servicestatus->status==SERVICE_UNKNOWN)
			total_service_health++;

		if(temp_servicestatus->status!=SERVICE_PENDING)
			potential_service_health+=2;


		/* calculate execution time and latency stats */
		if(temp_servicestatus->check_type==SERVICE_CHECK_ACTIVE){

			total_active_service_checks++;

			if(min_service_latency==-1.0 || temp_servicestatus->latency<min_service_latency)
				min_service_latency=temp_servicestatus->latency;
			if(max_service_latency==-1.0 || temp_servicestatus->latency>max_service_latency)
				max_service_latency=temp_servicestatus->latency;

			if(min_service_execution_time==-1.0 || temp_servicestatus->execution_time<min_service_execution_time)
				min_service_execution_time=temp_servicestatus->execution_time;
			if(max_service_execution_time==-1.0 || temp_servicestatus->execution_time>max_service_execution_time)
				max_service_execution_time=temp_servicestatus->execution_time;

			total_service_latency+=temp_servicestatus->latency;
			total_service_execution_time+=temp_servicestatus->execution_time;
		        }
		else
			total_passive_service_checks++;


		total_services++;
	        }



	/* check all hosts */
	for(temp_hoststatus=hoststatus_list;temp_hoststatus!=NULL;temp_hoststatus=temp_hoststatus->next){

		/* see if user is authorized to view this host */
		temp_host=find_host(temp_hoststatus->host_name);
		if(is_authorized_for_host(temp_host,&current_authdata)==FALSE)
			continue;

		/******** CHECK FEATURES *******/

		/* check flapping */
		if(temp_hoststatus->flap_detection_enabled==FALSE)
			flap_disabled_hosts++;
		else if(temp_hoststatus->is_flapping==TRUE)
			flapping_hosts++;

		/* check notifications */
		if(temp_hoststatus->notifications_enabled==FALSE)
			notification_disabled_hosts++;

		/* check event handler */
		if(temp_hoststatus->event_handler_enabled==FALSE)
			event_handler_disabled_hosts++;

		/* active check execution */
		if(temp_hoststatus->checks_enabled==FALSE)
			active_checks_disabled_hosts++;

		/* passive check acceptance */
		if(temp_hoststatus->accept_passive_checks==FALSE)
			passive_checks_disabled_hosts++;


		/********* CHECK STATUS ********/

		problem=TRUE;

		if(temp_hoststatus->status==HOST_UP){
			if(temp_hoststatus->checks_enabled==FALSE)
				hosts_up_disabled++;
			else
				hosts_up_unacknowledged++;
			hosts_up++;
		        }

		else if(temp_hoststatus->status==HOST_DOWN){
			if(temp_hoststatus->scheduled_downtime_depth>0){
				hosts_down_scheduled++;
				problem=FALSE;
			        }
			if(temp_hoststatus->problem_has_been_acknowledged==TRUE){
				hosts_down_acknowledged++;
				problem=FALSE;
			        }
			if(temp_hoststatus->checks_enabled==FALSE){
				hosts_down_disabled++;
				problem=FALSE;
			        }
			if(problem==TRUE)
				hosts_down_unacknowledged++;
			hosts_down++;
		        }

		else if(temp_hoststatus->status==HOST_UNREACHABLE){
			if(temp_hoststatus->scheduled_downtime_depth>0){
				hosts_unreachable_scheduled++;
				problem=FALSE;
			        }
			if(temp_hoststatus->problem_has_been_acknowledged==TRUE){
				hosts_unreachable_acknowledged++;
				problem=FALSE;
			        }
			if(temp_hoststatus->checks_enabled==FALSE){
				hosts_unreachable_disabled++;
				problem=FALSE;
			        }
			if(problem==TRUE)
				hosts_unreachable_unacknowledged++;
			hosts_unreachable++;
		        }
		
		else if(temp_hoststatus->status==HOST_PENDING){
			if(temp_hoststatus->checks_enabled==FALSE)
				hosts_pending_disabled++;
			hosts_pending++;
		        }

		/* get health stats */
		if(temp_hoststatus->status==HOST_UP)
			total_host_health++;

		if(temp_hoststatus->status!=HOST_PENDING)
			potential_host_health++;

		/* check type stats */
		if(temp_hoststatus->check_type==HOST_CHECK_ACTIVE){

			total_active_host_checks++;

			if(min_host_latency==-1.0 || temp_hoststatus->latency<min_host_latency)
				min_host_latency=temp_hoststatus->latency;
			if(max_host_latency==-1.0 || temp_hoststatus->latency>max_host_latency)
				max_host_latency=temp_hoststatus->latency;

			if(min_host_execution_time==-1.0 || temp_hoststatus->execution_time<min_host_execution_time)
				min_host_execution_time=temp_hoststatus->execution_time;
			if(max_host_execution_time==-1.0 || temp_hoststatus->execution_time>max_host_execution_time)
				max_host_execution_time=temp_hoststatus->execution_time;

			total_host_latency+=temp_hoststatus->latency;
			total_host_execution_time+=temp_hoststatus->execution_time;
		        }
		else
			total_passive_host_checks++;

		total_hosts++;
	        }


	/* calculate service health */
	if(potential_service_health==0)
		percent_service_health=0.0;
	else
		percent_service_health=((double)total_service_health/(double)potential_service_health)*100.0;

	/* calculate host health */
	if(potential_host_health==0)
		percent_host_health=0.0;
	else
		percent_host_health=((double)total_host_health/(double)potential_host_health)*100.0;

	/* calculate service latency */
	if(total_service_latency==0L)
		average_service_latency=0.0;
	else
		average_service_latency=((double)total_service_latency/(double)total_active_service_checks);

	/* calculate host latency */
	if(total_host_latency==0L)
		average_host_latency=0.0;
	else
		average_host_latency=((double)total_host_latency/(double)total_active_host_checks);

	/* calculate service execution time */
	if(total_service_execution_time==0.0)
		average_service_execution_time=0.0;
	else
		average_service_execution_time=((double)total_service_execution_time/(double)total_active_service_checks);

	/* calculate host execution time */
	if(total_host_execution_time==0.0)
		average_host_execution_time=0.0;
	else
		average_host_execution_time=((double)total_host_execution_time/(double)total_active_host_checks);

	return;
        }




/* determine what hosts are causing network outages */
void find_hosts_causing_outages(void){
	hoststatus *temp_hoststatus;
	hostoutage *temp_hostoutage;
	host *temp_host;

	/* user must be authorized for all hosts in order to see outages */
	if(is_authorized_for_all_hosts(&current_authdata)==FALSE)
		return;

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
				add_hostoutage(temp_host);
		        }
	        }


	/* check all hosts that are causing problems and calculate the extent of the problem */
	for(temp_hostoutage=hostoutage_list;temp_hostoutage!=NULL;temp_hostoutage=temp_hostoutage->next){

		/* calculate the outage effect of this particular hosts */
		calculate_outage_effect_of_host(temp_hostoutage->hst,&temp_hostoutage->affected_child_hosts);

		if(temp_hostoutage->affected_child_hosts>1)
			total_blocking_outages++;
		else
			total_nonblocking_outages++;
	        }

	return;
        }





/* adds a host outage entry */
void add_hostoutage(host *hst){
	hostoutage *new_hostoutage;

	/* allocate memory for a new structure */
	new_hostoutage=(hostoutage *)malloc(sizeof(hostoutage));

	if(new_hostoutage==NULL)
		return;

	new_hostoutage->hst=hst;
	new_hostoutage->affected_child_hosts=0;

	/* add the structure to the head of the list in memory */
	new_hostoutage->next=hostoutage_list;
	hostoutage_list=new_hostoutage;

	return;
        }




/* frees all memory allocated to the host outage list */
void free_hostoutage_list(void){
	hostoutage *this_hostoutage;
	hostoutage *next_hostoutage;

	for(this_hostoutage=hostoutage_list;this_hostoutage!=NULL;this_hostoutage=next_hostoutage){
		next_hostoutage=this_hostoutage->next;
		free(this_hostoutage);
	        }

	return;
        }



/* calculates network outage effect of a particular host being down or unreachable */
void calculate_outage_effect_of_host(host *hst, int *affected_hosts){
	int total_child_hosts_affected=0;
	int temp_child_hosts_affected=0;
	host *temp_host;


	/* find all child hosts of this host */
	for(temp_host=host_list;temp_host!=NULL;temp_host=temp_host->next){

		/* skip this host if it is not a child */
		if(is_host_immediate_child_of_host(hst,temp_host)==FALSE)
			continue;

		/* calculate the outage effect of the child */
		calculate_outage_effect_of_host(temp_host,&temp_child_hosts_affected);

		/* keep a running total of outage effects */
		total_child_hosts_affected+=temp_child_hosts_affected;
	        }

	*affected_hosts=total_child_hosts_affected+1;

	return;
        }



/* tests whether or not a host is "blocked" by upstream parents (host is already assumed to be down or unreachable) */
int is_route_to_host_blocked(host *hst){
	hostsmember *temp_hostsmember;
	hoststatus *temp_hoststatus;

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






void display_tac_overview(void){
	char host_health_image[16];
	char service_health_image[16];

	printf("<meta>\n");
	printf("<program>nagioscore</program>\n");
	printf("<programversion>%s</programversion>\n",PROGRAM_VERSION);
	printf("</meta>\n");
	
	printf("<servicecheckexecution>\n");
	printf("<min>%.2f</min>\n",min_service_execution_time);
	printf("<max>%.2f</max>\n",max_service_execution_time);
	printf("<avg>%.2f</avg>\n",average_service_execution_time);
	printf("</servicecheckexecution>\n");
	
	printf("<servicechecklatency>\n");
	printf("<min>%.2f</min>\n",min_service_latency);
	printf("<max>%.2f</max>\n",max_service_latency);
	printf("<avg>%.2f</avg>\n",average_service_latency);
	printf("</servicechecklatency>\n");
	
	printf("<hostcheckexecution>\n");
	printf("<min>%.2f</min>\n",min_host_execution_time);
	printf("<max>%.2f</max>\n",max_host_execution_time);
	printf("<avg>%.2f</avg>\n",average_host_execution_time);
	printf("</hostcheckexecution>\n");
	
	printf("<hostchecklatency>\n");
	printf("<min>%.2f</min>\n",min_host_latency);
	printf("<max>%.2f</max>\n",max_host_latency);
	printf("<avg>%.2f</avg>\n",average_host_latency);
	printf("</hostchecklatency>\n");
	
	printf("<hostchecks>\n");
	printf("<active>%d</active>\n",total_active_host_checks);
	printf("<passive>%d</passive>\n",total_passive_host_checks);
	printf("</hostchecks>\n");
	
	printf("<servicechecks>\n");
	printf("<active>%d</active>\n",total_active_service_checks);
	printf("<passive>%d</passive>\n",total_passive_service_checks);
	printf("</servicechecks>\n");


	printf("<outages>\n");
	printf("<blockingoutages>");
	if(is_authorized_for_all_hosts(&current_authdata)==FALSE)
		printf("0");
	else
		printf("%d",total_blocking_outages);
	printf("</blockingoutages>\n");
	printf("</outages>\n");


	printf("<networkhealth>\n");
	printf("<hosthealth>%2.1f</hosthealth>\n",percent_host_health);
	printf("<servicehealth>%2.1f</servicehealth>\n",percent_service_health);
	printf("</networkhealth>\n");


	printf("<hoststatustotals>\n");
	
	printf("<down>\n");
	printf("<total>%d</total>\n",hosts_down);
	printf("<unhandled>%d</unhandled>\n",hosts_down_unacknowledged);
	printf("<scheduleddowntime>%d</scheduleddowntime>\n",hosts_down_scheduled);
	printf("<acknowledged>%d</acknowledged>\n",hosts_down_acknowledged);
	printf("<disabled>%d</disabled>\n",hosts_down_disabled);
	printf("</down>\n");

	printf("<unreachable>\n");
	printf("<total>%d</total>\n",hosts_unreachable);
	printf("<unhandled>%d</unhandled>\n",hosts_unreachable_unacknowledged);
	printf("<scheduledunreachabletime>%d</scheduledunreachabletime>\n",hosts_unreachable_scheduled);
	printf("<acknowledged>%d</acknowledged>\n",hosts_unreachable_acknowledged);
	printf("<disabled>%d</disabled>\n",hosts_unreachable_disabled);
	printf("</unreachable>\n");

	printf("<up>\n");
	printf("<total>%d</total>\n",hosts_up);
	printf("<disabled>%d</disabled>\n",hosts_up_disabled);
	printf("</up>\n");

	printf("<pending>\n");
	printf("<total>%d</total>\n",hosts_pending);
	printf("<disabled>%d</disabled>\n",hosts_pending_disabled);
	printf("</pending>\n");

	printf("</hoststatustotals>\n");

	
	
	printf("<servicestatustotals>\n");
	
	printf("<warning>\n");
	printf("<total>%d</total>\n",services_warning);
	printf("<unhandled>%d</unhandled>\n",services_warning_unacknowledged);
	printf("<scheduleddowntime>%d</scheduleddowntime>\n",services_warning_scheduled);
	printf("<acknowledged>%d</acknowledged>\n",services_warning_acknowledged);
	printf("<hostproblem>%d</hostproblem>\n",services_warning_host_problem);
	printf("<disabled>%d</disabled>\n",services_warning_disabled);
	printf("</warning>\n");

	printf("<unknown>\n");
	printf("<total>%d</total>\n",services_unknown);
	printf("<unhandled>%d</unhandled>\n",services_unknown_unacknowledged);
	printf("<scheduleddowntime>%d</scheduleddowntime>\n",services_unknown_scheduled);
	printf("<acknowledged>%d</acknowledged>\n",services_unknown_acknowledged);
	printf("<hostproblem>%d</hostproblem>\n",services_unknown_host_problem);
	printf("<disabled>%d</disabled>\n",services_unknown_disabled);
	printf("</unknown>\n");

	printf("<critical>\n");
	printf("<total>%d</total>\n",services_critical);
	printf("<unhandled>%d</unhandled>\n",services_critical_unacknowledged);
	printf("<scheduleddowntime>%d</scheduleddowntime>\n",services_critical_scheduled);
	printf("<acknowledged>%d</acknowledged>\n",services_critical_acknowledged);
	printf("<hostproblem>%d</hostproblem>\n",services_critical_host_problem);
	printf("<disabled>%d</disabled>\n",services_critical_disabled);
	printf("</critical>\n");

	printf("<ok>\n");
	printf("<total>%d</total>\n",services_ok);
	printf("<disabled>%d</disabled>\n",services_ok_disabled);
	printf("</ok>\n");

	printf("<pending>\n");
	printf("<total>%d</total>\n",services_pending);
	printf("<disabled>%d</disabled>\n",services_pending_disabled);
	printf("</pending>\n");
	
	printf("</servicestatustotals>\n");
	

	printf("<monitoringfeaturestatus>\n");
	
	printf("<flapdetection>\n");
	printf("<global>%d</global>\n",enable_flap_detection);
	printf("</flapdetection>\n");
	
	printf("<notifications>\n");
	printf("<global>%d</global>\n",enable_notifications);
	printf("</notifications>\n");
	
	printf("<eventhandlers>\n");
	printf("<global>%d</global>\n",enable_event_handlers);
	printf("</eventhandlers>\n");
	
	printf("<activeservicechecks>\n");
	printf("<global>%d</global>\n",execute_service_checks);
	printf("</activeservicechecks>\n");
	
	printf("<passiveservicechecks>\n");
	printf("<global>%d</global>\n",accept_passive_service_checks);
	printf("</passiveservicechecks>\n");
	
	
	printf("</monitoringfeaturestatus>\n");



	return;
        }

