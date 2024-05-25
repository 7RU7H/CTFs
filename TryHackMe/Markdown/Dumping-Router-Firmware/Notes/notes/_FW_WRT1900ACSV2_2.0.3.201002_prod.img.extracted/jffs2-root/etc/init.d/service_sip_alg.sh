#!/bin/sh
source /etc/init.d/ulog_functions.sh
SERVICE_NAME="sip_alg"
service_init ()
{
    eval `utctx_cmd get sip_alg_enabled` 
}
service_start ()
{
   if [ "$SYSCFG_sip_alg_enabled" = "1" ]; then
      ulog ${SERVICE_NAME} status "starting ${SERVICE_NAME} service" 
      MODULE_PATH=/lib/modules/`uname -r`/
      if [ -f $MODULE_PATH/nf_conntrack_sip.ko ]; then           
         insmod $MODULE_PATH/nf_conntrack_sip.ko > /dev/null 2>&1
      fi                                                         
      if [ $? != 0 ]; then                                       
         ERROR1="can't insert nf_conntrack_sip.ko"
      else                                        
         ERROR1=""                                
      fi          
      if [ -f $MODULE_PATH/nf_nat_sip.ko ]; then           
         insmod $MODULE_PATH/nf_nat_sip.ko > /dev/null 2>&1
      fi                                                   
      if [ $? != 0 ]; then                                 
         ERROR2="can't insert nf_nat_sip.ko"
      else                                  
         ERROR2=""                          
      fi          
      if [ -n "$ERROR1" -o -n "$ERROR2" ];then
         sysevent set ${SERVICE_NAME}-errinfo "$ERROR1 $ERROR2"
         sysevent set ${SERVICE_NAME}-status "error"           
      else                                                     
         sysevent set ${SERVICE_NAME}-errinfo                  
         sysevent set ${SERVICE_NAME}-status "started"         
      fi                                                       
   fi
}
service_stop () 
{
   ulog ${SERVICE_NAME} status "stopping ${SERVICE_NAME} service" 
   rmmod nf_nat_sip > /dev/null 2>&1
   rmmod nf_conntrack_sip > /dev/null 2>&1
   sysevent set ${SERVICE_NAME}-errinfo
   sysevent set ${SERVICE_NAME}-status "stopped"
}
service_restart () 
{
   service_stop
   service_start
}
service_init
case "$1" in
  ${SERVICE_NAME}-start)
     service_start
     ;;
  ${SERVICE_NAME}-stop)
     service_stop
     ;;
  ${SERVICE_NAME}-restart)
     service_restart
     ;;
  wan-status)
     service_start
     ;;
  *)
      echo "Usage: $SELF_NAME [${SERVICE_NAME}-start|${SERVICE_NAME}-stop|${SERVICE_NAME}-restart]" >&2
      exit 3
      ;;
esac
