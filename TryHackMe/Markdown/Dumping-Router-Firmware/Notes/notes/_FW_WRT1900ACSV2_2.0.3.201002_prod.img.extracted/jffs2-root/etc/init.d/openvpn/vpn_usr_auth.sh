#!/bin/sh
authfile="/tmp/vpn/user_auth"
#i=2
size=`cat $authfile | wc -l`

TRYUSER=`head -n 1 $1`
TRYPASS=`tail -n 1 $1`

echo "VPN user:$TRYUSER, pass:$TRYPASS" >  /tmp/vpn_tries.txt

for i in `seq 1 $size`
do
#	user=`syscfg get openvpn::user_${i}_name`
#	pass=`syscfg get openvpn::user_${i}_pass`
	user="`cat $authfile | head -n $i | tail -n 1 | cut -d' ' -f1`"
	pass="`cat $authfile | head -n $i | tail -n 1 | cut -d' ' -f2`"
	if [ "$user" ] ; then
		if [ "$user" == "$TRYUSER" ] ; then
			if [ "$pass" == "$TRYPASS" ] ; then
				echo "VPN user:$TRYUSER, pass:$TRYPASS - AUTHENTICATION OK" >>  /tmp/vpn_tries.txt
				exit 0
			else
				echo "VPN user:$TRYUSER, pass:$TRYPASS - AUTHENTICATION REJECTED"  >>  /tmp/vpn_tries.txt
			fi
		fi
		echo "tried user combo - $user:$pass" >> /tmp/vpn_tries.txt
	fi
done
exit 1

# while expr $i "<=" $size
# do
# 	if cat $authfile | head -n $i | tail -n 2 | cmp -s $1
# 	then
# 		exit 0
# 	fi
# 	i=`expr $i + 2`
# done
# exit 1
