#!/bin/sh
echo Content-Type: text/plain
echo ""

SSID=`syscfg get tc_vap_ssid`
PASS=`syscfg get tc_vap_passphrase`

echo "SSID=`nfc_obfuscation -e $SSID`"
echo "Passphrase=`nfc_obfuscation -e $PASS`"
