#!/bin/bash
#
# Manages SSL and HTTPS Configuration of Apache
# Copyright (c) 2018-2020 Nagios Enterprises, LLC. All rights reserved.
#

BASEDIR=$(dirname $(readlink -f $0))

# Default options
sslkey=""
sslcert=""
sslca=""
sslcac=""
redirect=0
dredirect=0

# Import xi-sys.cfg config vars
. $BASEDIR/../etc/xi-sys.cfg

# Apache configuration files
conf="$httpdconfdir/nagiosxi.conf"
if [ "$distro" == "Ubuntu" ] || [ "$distro" == "Debian" ]; then
    sslconf="/etc/apache2/sites-available/default-ssl.conf"
else
    sslconf="$httpdconfdir/ssl.conf"
fi

# Usage information
usage () {
    echo ""
    echo "This script updates the Apache configuration with SSL items given."
    echo ""
    echo "SSL Config Options:"
    echo " -k | --key               The SSL key file"
    echo " -c | --cert              The SSL certificate file (.pem or .crt)"
    echo " -a | --ca-cert           The SSL CA certificate (optional)"
    echo " -n | --ca-chain          The SSL CA certificate chain (optional, advanced)"
    echo ""
    echo "Apache Config Options:"
    echo " --redirect-http          Redirects HTTP requests to redirect to HTTPS"
    echo " --disable-redirect-http  Disables redirecting HTTP to HTTPS"
    echo ""
}

# Get command line options
while [ -n "$1" ]; do
    case "$1" in
        -h | --help)
            usage
            exit 0
            ;;
        -k | --key)
            sslkey=$2
            ;;
        -c | --cert)
            sslcert=$2
            ;;
        -a | --ca-cert)
            sslca=$2
            ;;
        -n | --ca-chain)
            sslcac=$2
            ;;
        --redirect-http)
            redirect=1
            ;;
        --disable-redirect-http)
            dredirect=1
            ;;
    esac
    shift
done

# Check the sanity for redirect
if [ $dredirect -eq 1 ] && [ $redirect -eq 1 ]; then
    echo "You can not set both --redirect-http and --disable-redirect-http,"
    echo "since it would enable and disable the redirect"
    exit 1
fi

# -----------------------
# HTTP Redirection
# -----------------------

# Disable http redirect
if [ $dredirect -eq 1 ]; then
    sed -i '/RewriteCond %{HTTPS} off/d' $conf
    sed -i '/RewriteRule (.*) https:\/\/%{HTTP_HOST}%{REQUEST_URI}/d' $conf
fi

# Add the http redirect
if [ $redirect -eq 1 ]; then
    if grep -Fxq 'RewriteCond %{HTTPS} off' $conf ; then
        echo "Your Apache config has already been set to redirect"
        exit 1
    else
        # Rewrite the new configuration or add it
        sed -i '/<\/IfModule>/i\
RewriteCond %{HTTPS} off\
RewriteRule (.*) https://%{HTTP_HOST}%{REQUEST_URI}' $conf
    fi
fi

# -----------------------
# Add Certificates
# -----------------------

if [ ! -z $sslkey ] && [ ! -z $sslcert ]; then
    if [ -f $sslkey ] && [ -f $sslcert ]; then

        # Add the certificates to the SSL conf
        sed -i "s|SSLCertificateFile.*|SSLCertificateFile $sslcert|" $sslconf
        sed -i "s|SSLCertificateKeyFile.*|SSLCertificateKeyFile $sslkey|" $sslconf

        # If there is a CA cert, add it
        if [ ! -z $sslca ] && [ -f $sslca ]; then
            sed -i 's/#SSLCACertificateFile/SSLCACertificateFile/' $sslconf
            sed -i "s|SSLCACertificateFile.*|SSLCACertificateFile $sslca|" $sslconf
        fi

        # If there is a CA chain, add it
        if [ ! -z $sslcac ] && [ -f $sslcac ]; then
            sed -i 's/#SSLCertificateChainFile/SSLCertificateChainFile/' $sslconf
            sed -i "s|SSLCertificateChainFile.*|SSLCertificateChainFile $sslcac|" $sslconf
        fi

    else
        echo "You must specify -c | --cert and -k | --key"
        exit 1
    fi
fi

# -----------------------
# Final
# -----------------------

# Restart Apache to apply the configuration
$BASEDIR/manage_services.sh restart httpd

echo "Completed all actions"
exit 0
