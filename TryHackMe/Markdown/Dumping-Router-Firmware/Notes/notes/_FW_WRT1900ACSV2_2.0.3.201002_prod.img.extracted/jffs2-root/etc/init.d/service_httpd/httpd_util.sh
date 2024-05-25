#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/service_httpd/httpd_functions.sh
case "$1" in
  generate_authfile)
      gen_authfile "$2" "$3" $4
      ;;
  generate_passwd)
      gen_passwd "$2" "$3"
      ;;
  *)
        echo "Usage: $SELF_NAME [generate_authfile|generate_passwd]" >&2
        exit 3
        ;;
esac
