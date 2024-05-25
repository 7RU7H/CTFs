#!/bin/sh
source /etc/init.d/service_wan/ppp_helpers.sh
source /etc/init.d/service_wan/wan_helper_functions
service_init()
{
   parse_wan_namespace_sysevent $1
   wan_info_by_namespace $NAMESPACE
}
service_init $1
case "$EVENT" in
   prepare_options)
      prepare_pppd_options
   ;;
   prepare_secrets)
      prepare_pppd_secrets
   ;;
  *)
   exit 0
   ;;
esac
