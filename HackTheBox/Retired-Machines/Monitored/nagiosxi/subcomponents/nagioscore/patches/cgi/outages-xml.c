/**************************************************************************
 *
 * OUTAGES.C -  Nagios Network Outages CGI
 *
 * Copyright (c) 2010 Nagios Enterprises, LLC 
 * Copyright (c) 1999-2008 Ethan Galstad (egalstad@nagios.org)
 * Last Modified: 08-13-2010
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
#include "../include/statusdata.h"

#include "../include/cgiutils.h"
#include "../include/getcgi.h"
#include "../include/cgiauth.h"

extern int              refresh_rate;

extern hoststatus *hoststatus_list;
extern servicestatus *servicestatus_list;

extern char main_config_file[MAX_FILENAME_LENGTH];
extern char url_html_path[MAX_FILENAME_LENGTH];
extern char url_stylesheets_path[MAX_FILENAME_LENGTH];
extern char url_images_path[MAX_FILENAME_LENGTH];
extern char url_logo_images_path[MAX_FILENAME_LENGTH];
extern char log_file[MAX_FILENAME_LENGTH];


/* HOSTOUTAGE structure */
typedef struct hostoutage_struct{
	host *hst;
	int  severity;
	int  affected_child_hosts;
	int  affected_child_services;
	unsigned long monitored_time;
	unsigned long time_up;
	float percent_time_up;
	unsigned long time_down;
	float percent_time_down;
	unsigned long time_unreachable;
	float percent_time_unreachable;
	struct hostoutage_struct *next;
        }hostoutage;


/* HOSTOUTAGESORT structure */
typedef struct hostoutagesort_struct{
	hostoutage *outage;
	struct hostoutagesort_struct *next;
        }hostoutagesort;


void document_header(int);
void document_footer(void);
int process_cgivars(void);

void display_network_outages(void);
void find_hosts_causing_outages(void);
void calculate_outage_effects(void);
void calculate_outage_effect_of_host(host *,int *,int *);
int is_route_to_host_blocked(host *);
int number_of_host_services(host *);
void add_hostoutage(host *);
void sort_hostoutages(void);
void free_hostoutage_list(void);
void free_hostoutagesort_list(void);


authdata current_authdata;

hostoutage *hostoutage_list=NULL;
hostoutagesort *hostoutagesort_list=NULL;

int service_severity_divisor=4;            /* default = services are 1/4 as important as hosts */

int embedded=FALSE;
int display_header=TRUE;




int main(void){
	int result=OK;
	

	/* get the arguments passed in the URL */
	process_cgivars();

	/* reset internal variables */
	reset_cgi_vars();

	/* read the CGI configuration file */
	cgi_init(document_header, document_footer, READ_ALL_OBJECT_DATA, READ_ALL_STATUS_DATA);

	document_header(TRUE);

	/* get authentication information */
	get_authentication_information(&current_authdata);

	/* display network outage info */
	display_network_outages();

	document_footer();

	/* free memory allocated to comment data */
	free_comment_data();

	/* free all allocated memory */
	free_memory();

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

	/*printf("<?xml version=\"1.0\" encoding=\"utf-8\"?>\n");*/
	printf("<outages>\n");

	return;
        }


void document_footer(void){

	printf("</outages>\n");

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

		/* we found the service severity divisor option */
		if(!strcmp(variables[x],"service_divisor")){
			x++;
			if(variables[x]==NULL){
				error=TRUE;
				break;
			        }

			service_severity_divisor=atoi(variables[x]);
			if(service_severity_divisor<1)
				service_severity_divisor=1;
		        }

		/* we found the embed option */
		else if(!strcmp(variables[x],"embedded"))
			embedded=TRUE;

		/* we found the noheader option */
		else if(!strcmp(variables[x],"noheader"))
			display_header=FALSE;
	        }

	/* free memory allocated to the CGI variables */
	free_cgivars(variables);

	return error;
        }




/* shows all hosts that are causing network outages */
void display_network_outages(void){
	char temp_buffer[MAX_INPUT_BUFFER];
	int number_of_problem_hosts=0;
	int number_of_blocking_problem_hosts=0;
	hostoutagesort *temp_hostoutagesort;
	hostoutage *temp_hostoutage;
	hoststatus *temp_hoststatus;
	int odd=0;
	char *bg_class="";
	char *status="";
	int days;
	int hours;
	int minutes;
	int seconds;
	int total_comments;
	time_t t;
	time_t current_time;
	char state_duration[48];
	int total_entries=0;

	/* user must be authorized for all hosts.. */
	if(is_authorized_for_all_hosts(&current_authdata)==FALSE){

		return;
	        }

	/* find all hosts that are causing network outages */
	find_hosts_causing_outages();

	/* calculate outage effects */
	calculate_outage_effects();

	/* sort the outage list by severity */
	sort_hostoutages();

	/* count the number of top-level hosts that are down and the ones that are actually blocking children hosts */
	for(temp_hostoutage=hostoutage_list;temp_hostoutage!=NULL;temp_hostoutage=temp_hostoutage->next){
		number_of_problem_hosts++;
		if(temp_hostoutage->affected_child_hosts>1)
			number_of_blocking_problem_hosts++;
	        }
			
			

	/* display the problem hosts... */

	for(temp_hostoutagesort=hostoutagesort_list;temp_hostoutagesort!=NULL;temp_hostoutagesort=temp_hostoutagesort->next){

		temp_hostoutage=temp_hostoutagesort->outage;
		if(temp_hostoutage==NULL)
			continue;

		/* skip hosts that are not blocking anyone */
		if(temp_hostoutage->affected_child_hosts<=1)
			continue;

		temp_hoststatus=find_hoststatus(temp_hostoutage->hst->name);
		if(temp_hoststatus==NULL)
			continue;

		/* make	sure we only caught valid state types */
		if(temp_hoststatus->status != SD_HOST_DOWN && temp_hoststatus->status != SD_HOST_UNREACHABLE)
			continue;

		total_entries++;
		
		printf("<hostoutage>\n");

		printf("<host>%s</host>\n",temp_hostoutage->hst->name);
		printf("<severity>%d</severity>\n",temp_hostoutage->severity);
		printf("<state>%d</state>\n",temp_hoststatus->status);

		total_comments=number_of_host_comments(temp_hostoutage->hst->name);
		printf("<comments>%d</comments>\n",total_comments);


		current_time=time(NULL);
		if(temp_hoststatus->last_state_change==(time_t)0)
			t=current_time-program_start;
		else
			t=current_time-temp_hoststatus->last_state_change;
		get_time_breakdown((unsigned long)t,&days,&hours,&minutes,&seconds);
		snprintf(state_duration,sizeof(state_duration)-1,"%2dd %2dh %2dm %2ds%s",days,hours,minutes,seconds,(temp_hoststatus->last_state_change==(time_t)0)?"+":"");
		state_duration[sizeof(state_duration)-1]='\x0';
		
		printf("<duration>%lu</duration>\n",t);
		printf("<affectedhosts>%d</affectedhosts>\n",temp_hostoutage->affected_child_hosts);
		printf("<affectedservices>%d</affectedservices>\n",temp_hostoutage->affected_child_services);
		
			printf("</hostoutage>\n");
	        }

	
	printf("<total>%d</total>\n",total_entries);

	/* free memory allocated to the host outage list */
	free_hostoutage_list();
	free_hostoutagesort_list();

	return;
        }





/* determine what hosts are causing network outages */
void find_hosts_causing_outages(void){
	hoststatus *temp_hoststatus;
	host *temp_host;

	/* check all hosts */
	for(temp_hoststatus=hoststatus_list;temp_hoststatus!=NULL;temp_hoststatus=temp_hoststatus->next){

		/* check only hosts that are not up and not pending */
		if(temp_hoststatus->status!=SD_HOST_UP && temp_hoststatus->status!=HOST_PENDING){

			/* find the host entry */
			temp_host=find_host(temp_hoststatus->host_name);

			if(temp_host==NULL)
				continue;

			/* if the route to this host is not blocked, it is a causing an outage */
			if(is_route_to_host_blocked(temp_host)==FALSE)
				add_hostoutage(temp_host);
		        }
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
	new_hostoutage->severity=0;
	new_hostoutage->affected_child_hosts=0;
	new_hostoutage->affected_child_services=0;

	/* add the structure to the head of the list in memory */
	new_hostoutage->next=hostoutage_list;
	hostoutage_list=new_hostoutage;

	return;
        }



/* frees all memory allocated to the host outage list */
void free_hostoutage_list(void){
	hostoutage *this_hostoutage;
	hostoutage *next_hostoutage;

	/* free all list members */
	for(this_hostoutage=hostoutage_list;this_hostoutage!=NULL;this_hostoutage=next_hostoutage){
		next_hostoutage=this_hostoutage->next;
		free(this_hostoutage);
	        }

	/* reset list pointer */
	hostoutage_list=NULL;

	return;
        }



/* frees all memory allocated to the host outage sort list */
void free_hostoutagesort_list(void){
	hostoutagesort *this_hostoutagesort;
	hostoutagesort *next_hostoutagesort;

	/* free all list members */
	for(this_hostoutagesort=hostoutagesort_list;this_hostoutagesort!=NULL;this_hostoutagesort=next_hostoutagesort){
		next_hostoutagesort=this_hostoutagesort->next;
		free(this_hostoutagesort);
	        }

	/* reset list pointer */
	hostoutagesort_list=NULL;

	return;
        }



/* calculates network outage effect of all hosts that are causing blockages */
void calculate_outage_effects(void){
	hostoutage *temp_hostoutage;

	/* check all hosts causing problems */
	for(temp_hostoutage=hostoutage_list;temp_hostoutage!=NULL;temp_hostoutage=temp_hostoutage->next){

		/* calculate the outage effect of this particular hosts */
		calculate_outage_effect_of_host(temp_hostoutage->hst,&temp_hostoutage->affected_child_hosts,&temp_hostoutage->affected_child_services);

		temp_hostoutage->severity=(temp_hostoutage->affected_child_hosts+(temp_hostoutage->affected_child_services/service_severity_divisor));
	        }

	return;
        }




/* calculates network outage effect of a particular host being down or unreachable */
void calculate_outage_effect_of_host(host *hst, int *affected_hosts, int *affected_services){
	int total_child_hosts_affected=0;
	int total_child_services_affected=0;
	int temp_child_hosts_affected=0;
	int temp_child_services_affected=0;
	host *temp_host;


	/* find all child hosts of this host */
	for(temp_host=host_list;temp_host!=NULL;temp_host=temp_host->next){

		/* skip this host if it is not a child */
		if(is_host_immediate_child_of_host(hst,temp_host)==FALSE)
			continue;

		/* calculate the outage effect of the child */
		calculate_outage_effect_of_host(temp_host,&temp_child_hosts_affected,&temp_child_services_affected);

		/* keep a running total of outage effects */
		total_child_hosts_affected+=temp_child_hosts_affected;
		total_child_services_affected+=temp_child_services_affected;
	        }

	*affected_hosts=total_child_hosts_affected+1;
	*affected_services=total_child_services_affected+number_of_host_services(hst);

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
		if(temp_hoststatus->status == SD_HOST_UP || temp_hoststatus->status == HOST_PENDING)
			return FALSE;
	        }

	return TRUE;
        }



/* calculates the number of services associated a particular host */
int number_of_host_services(host *hst){
	int total_services=0;
	service *temp_service;

	/* check all services */
	for(temp_service=service_list;temp_service!=NULL;temp_service=temp_service->next){

		if(!strcmp(temp_service->host_name,hst->name))
			total_services++;
	        }

	return total_services;
        }



/* sort the host outages by severity */
void sort_hostoutages(void){
	hostoutagesort *last_hostoutagesort;
	hostoutagesort *new_hostoutagesort;
	hostoutagesort *temp_hostoutagesort;
	hostoutage *temp_hostoutage;

	if(hostoutage_list==NULL)
		return;

	/* sort all host outage entries */
	for(temp_hostoutage=hostoutage_list;temp_hostoutage!=NULL;temp_hostoutage=temp_hostoutage->next){

		/* allocate memory for a new sort structure */
		new_hostoutagesort=(hostoutagesort *)malloc(sizeof(hostoutagesort));
		if(new_hostoutagesort==NULL)
			return;

		new_hostoutagesort->outage=temp_hostoutage;

		last_hostoutagesort=hostoutagesort_list;
		for(temp_hostoutagesort=hostoutagesort_list;temp_hostoutagesort!=NULL;temp_hostoutagesort=temp_hostoutagesort->next){

			if(new_hostoutagesort->outage->severity >= temp_hostoutagesort->outage->severity){
				new_hostoutagesort->next=temp_hostoutagesort;
				if(temp_hostoutagesort==hostoutagesort_list)
					hostoutagesort_list=new_hostoutagesort;
				else
					last_hostoutagesort->next=new_hostoutagesort;
				break;
		                }
			else
				last_hostoutagesort=temp_hostoutagesort;
	                }

		if(hostoutagesort_list==NULL){
			new_hostoutagesort->next=NULL;
			hostoutagesort_list=new_hostoutagesort;
	                }
		else if(temp_hostoutagesort==NULL){
			new_hostoutagesort->next=NULL;
			last_hostoutagesort->next=new_hostoutagesort;
	                }
	        }

	return;
        }
