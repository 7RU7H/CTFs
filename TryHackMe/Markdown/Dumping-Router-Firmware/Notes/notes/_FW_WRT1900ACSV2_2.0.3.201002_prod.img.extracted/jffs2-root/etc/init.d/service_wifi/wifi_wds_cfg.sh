#!/bin/sh
BRIDGE_NAME=`syscfg get lan_ifname`
IF_PHY_IDX=""
IF_VAP_IDX="0"
IF_WDS_IDX="0"
PORT=0
CHAN_NUM=0
PEER_MAC=""
NETWORK_MODE=""
SECURITY_MODE=""
PASSPHRASE=""
print_help()
{
	if [ -n "$1" ]; then
		echo "$1"
	fi
	echo "Usage: $0 -c <channel> -m <peer mac> -n <network mode> -a <security> -p <passphrase>"
	echo "radio: [2g|5g]"
	echo "channel: 1-11 for 2.4Ghz, 36-48 and 149-161 for 5GHz"
	echo "peer mac: [AA:BB:CC:DD:EE:FF]"
	echo "network mode: [a|b|g|n|ac3"
	echo "security: [open|wpa-personal|wpa2-personal] also need passphrase"
	echo "passphrase: passphrase"
	exit
}
wds_guess_radio_from_channel()
{
	INPUT=$1
	CHAN_2GHZ="1 2 3 4 5 6 7 8 9 10 11"
	CHAN_5GHZ="36 40 44 48 149 153 157 161"
	STATUS="unmatched"
	for item in $CHAN_2GHZ
	do
		if [ "$INPUT" = "$item" ]; then
			STATUS="matched"
			OUTPUT="2g"
			break
		fi
	done
	if [ "unmatched" = "$STATUS" ]; then
		for item in $CHAN_5GHZ
		do
			if [ "$INPUT" = "$item" ]; then
				STATUS="matched"
				OUTPUT="5g"
				break
			fi
		done
	fi
	if [ "matched" = "$STATUS" ]; then
		echo $OUTPUT
	else
		print_help "Channel invalid"
	fi
}
wds_get_physical_index()
{
	RADIO=$1
	
	if [ -z "$RADIO" ]; then
		RADIO=`wds_guess_radio_from_channel $CHAN_STR`
	fi
	case "`echo $RADIO | tr [:upper:] [:lower:]`" in
		"2g")
		IDX=0
		;;
		"5g")
		IDX=1
		;;
		*)
		IDX=""
		;;
	esac
	if [ "0" != "$IDX" ] && [ "1" != "$IDX" ]; then
		print_help "Interface invalid"
	fi
	echo $IDX
}
wds_get_channel()
{
	INPUT_CHANNEL=$1
	STATUS="unmatched"
	LIST="1 2 3 4 5 6 7 8 9 10 11 36 40 44 48 149 153 157 161"
	for item in $LIST
	do
		if [ "$INPUT_CHANNEL" = "$item" ]; then
			STATUS="matched"
			break
		fi
	done
	if [ "matched" = "$STATUS" ]; then
		echo $INPUT_CHANNEL
	else
		print_help "Channel invalid"
	fi
}
wds_get_peer_mac()
{
	INPUT_MAC=$1
	MAC=`echo $INPUT_MAC | sed 's/[:]//g'`
	LEN_STR=`echo ${#MAC}`
	LEN=`expr $LEN_STR`
	if [ 12 -ne $LEN ]; then
		print_help "Peer MAC invalid"
	fi
	echo $MAC
}
wds_get_network_mode()
{
	INPUT=$1
	if [ -z "$INPUT" ]; then
		INPUT=ac3
	fi
	STATUS="unmatched"
	LIST="b g n a ac3"
	for item in $LIST
	do
		if [ "$INPUT" = "$item" ]; then
			STATUS="matched"
			break
		fi
	done
	if [ "matched" = "$STATUS" ]; then
		echo $INPUT
	else
		print_help "Network mode invalid"
	fi
}
wds_get_security_mode()
{
	INPUT=$1
	if [ -z "$INPUT" ]; then
		INPUT="open"
	fi
	STATUS="unmatched"
	LIST="open wpa-personal wpa2-personal"
	for item in $LIST
	do
		if [ "$INPUT" = "$item" ]; then
			STATUS="matched"
			break
		fi
	done
	if [ "matched" != "$STATUS" ]; then
		print_help "Security is not defined"
	fi
	case $INPUT in
		"open")
			RET=0
			;;
		"wpa-personal")
			RET=1
			;;
		"wpa2-personal")
			RET=2
			;;
		*)
			RET=""
	esac
	if [ ! -z "$RET" ]; then
		echo $RET
	else
		print_help
	fi
}
wds_get_passphrase()
{
	INPUT=$1
	if [ "1" = "$SECURITY_MODE" ] || [ "2" = "$SECURITY_MODE" ]; then
		if [ ! -n "$INPUT" ]; then 
			print_help "Should specify passphrase"
		fi
	fi
	echo $INPUT
}
wds_apply_setting()
{
	IF_PHY=wdev"$IF_PHY_IDX"
	IF_VAP=wdev"$IF_PHY_IDX"ap"$IF_VAP_IDX"
	IF_WDS=wdev"$IF_PHY_IDX"ap"$IF_VAP_IDX"wds"$IF_WDS_IDX"
	iwpriv $IF_PHY autochannel 0
	iwconfig $IF_PHY channel "$CHAN_NUM"
	sleep 1
	iwconfig $IF_PHY commit
	case "$SECURITY_MODE" in
		0)
			iwpriv $IF_VAP wpawpa2mode 0
			;;
		1)
			iwpriv $IF_VAP wpawpa2mode 1
            iwpriv $IF_VAP ciphersuite "wpa tkip"
			iwpriv $IF_VAP passphrase "wpa $PASSPHRASE"
			;;
		2)
			iwpriv $IF_VAP wpawpa2mode 2
            iwpriv $IF_VAP ciphersuite "wpa2 aes-ccmp"
			iwpriv $IF_VAP passphrase "wpa2 $PASSPHRASE"
			;;
	esac
	iwpriv $IF_VAP wdsmode 1
	iwpriv $IF_VAP setwds "$PORT $PEER_MAC $NETWORK_MODE"
	sleep 1
	iwconfig $IF_VAP commit
	ifconfig $IF_WDS up
	brctl addif "$BRIDGE_NAME" $IF_WDS
}
if [ $# -lt 4 ]; then
	print_help
fi
while [ $# -gt 0 ]; do
	case "$1" in
		"-r")
			echo "Radio is" $2
			RADIO_STR=$2
			INPUT_CHECK=1
			;;
		"-c")
			echo "Channel is" $2
			CHAN_STR=$2
			INPUT_CHECK=1
			;;
		"-m")
			echo "Peer MAC address is" $2
			MAC_STR=$2
			INPUT_CHECK=1
			;;
		"-n")
			echo "Network Mode is" $2
			NETWORK_STR=$2
			INPUT_CHECK=1
			;;
		"-a")
			echo "Security is" $2
			SECURITY_STR=$2
			INPUT_CHECK=1
			;;
		"-p")
			echo "Passphrase is" $2
			PASSPHRASE_STR=$2
			INPUT_CHECK=1
			;;
		*)
			INPUT_CHECK=0
			break
			;;
	esac
	shift	
	shift	
done
if [ "1" != "$INPUT_CHECK" ] || [ -z "$CHAN_STR" ] || [ -z "$MAC_STR" ]; then
	STATUS="failed"
	echo $STATUS
	print_help
fi
IF_PHY_IDX=`wds_get_physical_index $RADIO_STR`
CHAN_NUM=`wds_get_channel $CHAN_STR`
PEER_MAC=`wds_get_peer_mac $MAC_STR`
NETWORK_MODE=`wds_get_network_mode $NETWORK_STR`
SECURITY_MODE=`wds_get_security_mode $SECURITY_STR`
PASSPHRASE=`wds_get_passphrase $PASSPHRASE_STR`
wds_apply_setting
exit 0
