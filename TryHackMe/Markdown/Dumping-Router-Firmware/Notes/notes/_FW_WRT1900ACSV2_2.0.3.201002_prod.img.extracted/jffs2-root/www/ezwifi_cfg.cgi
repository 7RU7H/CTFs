#!/bin/sh
#

#SCRIPT_DIR="/etc/init.d/service_wifi/brcm/"
#SCRIPT_DIR=/etc/init.d/service_wifi/tools/
SCRIPT_DIR=/etc/init.d/service_wifi/
#SCRIPT_NAME=test_manual_setting.sh

ssid_5_start="`syscfg get wl0_ssid`"
ssid_2_start="`syscfg get wl1_ssid`"



get_cgi_val () {
  if [ "$1" == "" ] ; then
    echo ""
    return
  fi
  form_var="$1"
  var_value=`echo "$QUERY_STRING" | sed -n "s/^.*$form_var=\([^&]*\).*$/\1/p" | sed "s/%20/ /g" | sed "s/+/ /g" | sed "s/%2F/\//g"`
  echo -n "$var_value"
}

#IF5G=`syscfg show | grep '=wl1$' | cut -d'_' -f1`
#IF24G=`syscfg show | grep '=wl0$' | cut -d'_' -f1 | head -n1"`


CMD=$(get_cgi_val "CMD")

echo "Content-Type: text/html"
echo ""
echo ""
echo "<script type='text/javascript'>" 

echo "function ConfigureWIFIBridge(){"
echo "cmd = document.getElementById(\"CMD\");" 
echo "cmd.value = \"configure\";"
echo "document.getElementById(\"wifiform\").submit();" 
echo "}"

echo "function ScanSSIDs(){"
echo "cmd = document.getElementById(\"CMD\");" 
echo "cmd.value = \"scan\";"
echo "document.getElementById(\"wifiform\").submit();" 
echo "}"

echo "function SetRadioIndex( idx ){"
echo "document.getElementById(\"radio\").selectedIndex = idx;" 
echo "}"

echo "function SetSSIDVal( ssid ){"
echo "document.getElementById(\"ssid\").value = ssid;" 
echo "}"

echo "function SetPassphraseType( type ){" 
echo "if( (type == \"open\") || ( type == \"failed\" )) {"
echo "document.getElementById(\"passphrase\").value = type;"
echo "document.getElementById(\"passphrase\").setAttribute('readonly', 'readonly');"
echo "} else {"
echo "document.getElementById(\"passphrase\").value = type + \" passphrase\";"
echo "document.getElementById(\"passphrase\").removeAttribute('readonly');"
echo "}"
echo "}"

echo "function BasicSubmit(){"
echo "document.getElementById(\"wifiform\").submit();"
echo "}"

echo "</script>"

if [ "$CMD" != "List" ] ; then
echo "<h3>Configure wifi bridge settings</h3>"
echo "<table border=0><tr><td>"
echo "</td><td>"
echo "<form id=\"wifiform\" action=\"ezwifi_cfg.cgi\" method=\"get\">"
echo "</td></tr></table>"
echo "<input type=\"hidden\" id=CMD name=\"CMD\">"
echo "<table border=0><tr><td>2.4Ghz SSID</td><td><input type=\"text\" size=32 id=2ssid name=\"2ssid\" value=$ssid_2_start></td></tr>"
echo "<tr><td>2.4Ghz Passphrase</td><td><input type=\"text\" size=32 id=2passphrase name=\"2passphrase\"></td></tr>"
echo "<tr><td>2.4Ghz Mode</td><td>"
 echo "<select name=\"2mode\" id=2mode>"
 echo "<option value=\"11b\">11b</option>"
 echo "<option value=\"11g\">11g</option>"
 echo "<option value=\"11n\">11n</option>"
 echo "<option value=\"11b 11g 11n 11ac\">11b 11g 11n 11ac</option>"
 echo "</select>"
echo "</td></tr>"
echo "<tr><td>--------</td><td>---------</td></tr>"
echo "<tr><td>5Ghz SSID</td><td><input type=\"text\" size=32 id=5ssid name=\"5ssid\" value=$ssid_5_start></td></tr>"
echo "<tr><td>5Ghz Passphrase</td><td><input type=\"text\" size=32 id=5passphrase name=\"5passphrase\"></td></tr>"
echo "<tr><td>5Ghz Mode</td><td>"
 echo "<select name=\"5mode\" id=5mode>"
 echo "<option value=\"11a\">11a</option>"
 echo "<option value=\"11n\">11n</option>"
 echo "<option value=\"11a 11n 11ac\">11a 11n 11ac</option>"
 echo "</select>"
echo "</td></tr>"
echo "</table>"
echo "<hr>"
fi

if [ "$CMD" ] ; then
	if [ "$CMD" == "configure" ] ; then
		
		SSID2=$(get_cgi_val "2ssid")
		PASSPHRASE2=$(get_cgi_val "2passphrase")
		MODE2=$(get_cgi_val "2mode")
		
		SSID5=$(get_cgi_val "5ssid")
		PASSPHRASE5=$(get_cgi_val "5passphrase")
		MODE5=$(get_cgi_val "5mode")
		
		
		if [ "$SSID2" ] && [ "$PASSPHRASE2" ] && [ "$MODE2" ] ; then
			echo "2.4Ghz settings received"
			if [ "$SSID5" ] && [ "$PASSPHRASE5" ] && [ "$MODE5" ] ; then
			echo "5Ghz settings received"
				echo "<table border=1>"
				echo "<tr><td></td><th>SSID</th><th>Passphrase</th><th>Mode</th></tr>"
				echo "<tr><td>2.4Ghz</td><td>$SSID2</td><td>$PASSPHRASE2</td><td>$MODE2</td></tr>"
				echo "<tr><td>5Ghz</td><td>$SSID5</td><td>$PASSPHRASE5</td><td>$MODE5</td></tr>"
				echo "</table>"
				
				echo "running <br> $SCRIPT_DIR/test_manual_setting.sh \"$SSID2\" \"$PASSPHRASE2\" \"$MODE2\" \"$SSID5\" \"$PASSPHRASE5\" \"$MODE5\""
				$SCRIPT_DIR/test_manual_setting.sh "$SSID2" "$PASSPHRASE2" "$MODE2" "$SSID5" "$PASSPHRASE5" "$MODE5"  
				
			else
				echo "<br>could not get settings for 5Ghz Radio"
			fi
		else
			echo "<br>could not get settings for 2.4Ghz Radio"
		fi
		
	fi
	echo "<hr>"
else
	echo ""
fi
	#echo "<INPUT Type=\"BUTTON\" VALUE=\"Basic\" ONCLICK=\"BasicSubmit();\">"
	echo "<INPUT Type=\"BUTTON\" VALUE=\"Configure\" ONCLICK=\"ConfigureWIFIBridge();\">"
echo "</form>"
echo ""
echo ""

