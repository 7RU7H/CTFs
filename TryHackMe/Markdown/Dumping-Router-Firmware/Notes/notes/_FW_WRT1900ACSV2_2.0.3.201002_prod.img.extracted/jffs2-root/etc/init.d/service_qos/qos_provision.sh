#!/bin/sh
NETWORK_CONTROL_FW_MARK="0x1"
VOICE_1_FW_MARK="0x2"
VOICE_2_FW_MARK="0x3"
VOICE_3_FW_MARK="0x4"
VOICE_4_FW_MARK="0x5"
VIDEO_1_FW_MARK="0x6"
VIDEO_2_FW_MARK="0x7"
VIDEO_3_FW_MARK="0x8"
VIDEO_4_FW_MARK="0x9"
BE_1_FW_MARK="0xa"
BE_2_FW_MARK="0xb"
BE_3_FW_MARK="0xc"
BE_4_FW_MARK="0xd"
BK_1_FW_MARK="0xe"
BK_2_FW_MARK="0xf"
BK_3_FW_MARK="0x10"
BK_4_FW_MARK="0x11"
WAN_UNCLASSIFIED_FW_MARK="0x12"
ROUTER_TO_LAN_FW_MARK="0x13"
GN_TC_FW_MARK="0x15"
adjust_queues_for_unclassified_wan () {
   if [ -n "$SYSCFG_wan_download_speed" ] ; then
      ROUNDED=`echo "$SYSCFG_wan_download_speed" | awk '{printf("%d\n",$1 + 0.5)}'`
   else 
      ROUNDED=0
   fi
   if [ "0" != "$ROUNDED" -a "$ROUNDED" -lt "$SYSCFG_dev_max_rate" ] ; then
      WAN_DESIRED_MBIT_RATE=`dc $SYSCFG_wan_download_speed $SYSEVENT_committed_bitrate - p`
      RES=`echo $WAN_DESIRED_MBIT_RATE | awk '{if (0 < $1) print 1; else print 0}'`
      if [ "$RES" = "0" ] ; then
         WAN_DESIRED_MBIT_RATE=".0001"
      fi
      GOLD_MIN=35
      SILVER_MIN=25
      BRONZE_MIN=15
      TIN_MIN=3
      TOTAL_MBIT=`dc $GOLD_RATE $SILVER_RATE + p`
      TOTAL_MBIT=`dc $TOTAL_MBIT $BRONZE_RATE + p`
      TOTAL_MBIT=`dc $TOTAL_MBIT $TIN_RATE + p`
      RES_MBIT=`dc $GOLD_MIN $SILVER_MIN + p`
      RES_MBIT=`dc $RES_MBIT $BRONZE_MIN + p`
      RES_MBIT=`dc $RES_MBIT $TIN_MIN + p`
      AVAIL_RATE_TO_WAN=`dc $AVAIL_RATE $RES_MBIT - p`
      RES=`echo $WAN_DESIRED_MBIT_RATE $TOTAL_MBIT | awk '{if ($1 > $2) print 1; else print 0}'`
      if [ "$RES" = "1" ] ; then
        WAN_DESIRED_MBIT_RATE=${TOTAL_MBIT} 
      fi
      RES=`echo $WAN_DESIRED_MBIT_RATE $AVAIL_RATE_TO_WAN | awk '{if ($1 > $2) print 1; else print 0}'`
      if [ "$RES" = "1" ] ; then
        WAN_DESIRED_MBIT_RATE=${AVAIL_RATE_TO_WAN} 
      fi
      RES=`echo $AVAIL_RATE_TO_WAN | awk '{if (0 < $1) print 1; else print 0}'`
      if [ -n $WAN_DESIRED_MBIT_RATE -a "$RES" = "1" ] ; then
         WAN_MBIT_RATE=${WAN_DESIRED_MBIT_RATE}"mbit"
         TIN_TMP=`dc $TIN_RATE  $TIN_MIN - p` 
         RES=`echo $WAN_DESIRED_MBIT_RATE $TIN_TMP | awk '{if ($1 <= $2) print 1; else print 0}'`
         if [ "$RES" = "1" ] ; then
            TIN_RATE=`dc $TIN_RATE  $WAN_DESIRED_MBIT_RATE - p`
         else
            TIN_RATE=$TIN_MIN
            WAN_DESIRED_MBIT_RATE=`dc $WAN_DESIRED_MBIT_RATE $TIN_TMP - p`
           
            RES=`echo $WAN_DESIRED_MBIT_RATE | awk '{if (0 < $1) print 1; else print 0}'`
            if [ "$RES" = "1" ] ; then
               BRONZE_TMP=`dc $BRONZE_RATE $BRONZE_MIN - p` 
               RES=`echo $WAN_DESIRED_MBIT_RATE $BRONZE_TMP | awk '{if ($1 <= $2) print 1; else print 0}'`
               if [ "$RES" = "1" ] ; then
                  BRONZE_RATE=`dc $BRONZE_RATE $WAN_DESIRED_MBIT_RATE - p`
               else
                  BRONZE_RATE=$BRONZE_MIN
                  WAN_DESIRED_MBIT_RATE=`dc $WAN_DESIRED_MBIT_RATE $BRONZE_TMP - p`
                  RES=`echo $WAN_DESIRED_MBIT_RATE | awk '{if (0 < $1) print 1; else print 0}'`
                  if [ "$RES" = "1" ] ; then
                     SILVER_TMP=`dc $SILVER_RATE $SILVER_MIN - p`
                     RES=`echo $WAN_DESIRED_MBIT_RATE $SILVER_TMP | awk '{if ($1 <= $2) print 1; else print 0}'`
                     if [ "$RES" = "1" ] ; then
                        SILVER_RATE=`dc $SILVER_RATE $WAN_DESIRED_MBIT_RATE - p`
                     else
                        SILVER_RATE=$SILVER_MIN
                        WAN_DESIRED_MBIT_RATE=`dc $WAN_DESIRED_MBIT_RATE $SILVER_TMP - p`
                        RES=`echo $WAN_DESIRED_MBIT_RATE | awk '{if (0 < $1) print 1; else print 0}'`
                        if [ "$RES" = "1" ] ; then
                           GOLD_RATE=`dc $GOLD_RATE $WAN_DESIRED_MBIT_RATE - p`
                           WAN_DESIRED_MBIT_RATE=0
                        fi
                     fi
                  fi
               fi
            fi
         fi
      fi
   fi
}
calculate_wan_shaping_htb_qos_parameters () {
   SYSCFG_dev_max_rate=`syscfg get ${1}_dev_max_rate`
   SYSCFG_wan_download_speed=`syscfg get wan_download_speed`
   SYSCFG_dev_avail_percent=`syscfg get ${1}_dev_avail_percent`
   if [ -z "$SYSCFG_dev_avail_percent" ] ; then 
      SYSCFG_dev_avail_percent=100
   fi
   SYSEVENT_unclassified_wan_ceiling_percent=`sysevent get unclassified_wan_ceiling_percent`
   if [ -z "$SYSEVENT_unclassified_wan_ceiling_percent" ] ; then
      SYSEVENT_unclassified_wan_ceiling_percent=100
   fi
   SYSCFG_gold_percent=`syscfg get ${1}_gold_percent`
   SYSCFG_silver_percent=`syscfg get ${1}_silver_percent`
   SYSCFG_bronze_percent=`syscfg get ${1}_bronze_percent`
   SYSCFG_tin_percent=`syscfg get ${1}_tin_percent`
   SYSEVENT_committed_bitrate=`sysevent get committed_bitrate`
   if [ -z "$SYSCFG_dev_max_rate" ] ; then
      SYSCFG_dev_max_rate=1000
   fi
   if [ -z "$SYSCFG_wan_download_speed" ] ; then
      SYSCFG_wan_download_speed=$SYSCFG_dev_max_rate
   fi
   if [ -z "$SYSCFG_gold_percent" ] ; then
      SYSCFG_gold_percent=60
   fi
   if [ -z "$SYSCFG_silver_percent" ] ; then
      SYSCFG_silver_percent=20
   fi
   if [ -z "$SYSCFG_bronze_percent" ] ; then
      SYSCFG_bronze_percent=10
   fi
   if [ -z "$SYSCFG_tin_percent" ] ; then
      SYSCFG_tin_percent=5
   fi
   if [ -z "$SYSEVENT_committed_bitrate" ] ; then
      SYSEVENT_committed_bitrate=0
   fi
   RES=`echo $SYSCFG_wan_download_speed $SYSCFG_dev_max_rate | awk '{if ($1 > $2) print 1; else print 0}'`
   if [ "$RES" = "1" ] ; then
      AVAIL_RATE=$SYSCFG_dev_max_rate 
   else
      AVAIL_RATE=$SYSCFG_wan_download_speed
   fi
   AVAIL_RATE=`dc $AVAIL_RATE  $SYSCFG_dev_avail_percent 100 / \* p`
   GOLD_RATE=`dc $AVAIL_RATE  $SYSCFG_gold_percent 100 / \* p`
   SILVER_RATE=`dc $AVAIL_RATE  $SYSCFG_silver_percent 100 / \* p`
   BRONZE_RATE=`dc $AVAIL_RATE  $SYSCFG_bronze_percent 100 / \* p`
   TIN_RATE=`dc $AVAIL_RATE  $SYSCFG_tin_percent 100 / \* p`
   WAN_DESIRED_MBIT_RATE=${TIN_RATE}
   GOLD_RATE=`dc $GOLD_RATE $WAN_DESIRED_MBIT_RATE - p`
   if [ -n "$SYSEVENT_committed_bitrate" ] ; then
      UNCLASSIFIED_WAN_CEIL=`dc $AVAIL_RATE $SYSEVENT_committed_bitrate - p`
   else
      UNCLASSIFIED_WAN_CEIL=$AVAIL_RATE
   fi
   UNCLASSIFIED_WAN_CEIL=`dc $UNCLASSIFIED_WAN_CEIL $SYSEVENT_unclassified_wan_ceiling_percent 100 / \* p`
   RES=`echo $WAN_DESIRED_MBIT_RATE $UNCLASSIFIED_WAN_CEIL | awk '{if ($1 > $2) print 1; else print 0}'`
   if [ "$RES" = "1" ] ; then
      UNCLASSIFIED_WAN_CEIL=$WAN_DESIRED_MBIT_RATE
   fi
   AVAIL_MBIT_RATE=${AVAIL_RATE}"mbit"
   GOLD_MBIT_RATE=${GOLD_RATE}"mbit"
   SILVER_MBIT_RATE=${SILVER_RATE}"mbit"
   BRONZE_MBIT_RATE=${BRONZE_RATE}"mbit"
   TIN_MBIT_RATE=${TIN_RATE}"mbit"
   UNCLASSIFIED_WAN_MBIT_RATE=${WAN_DESIRED_MBIT_RATE}"mbit"
   UNCLASSIFIED_WAN_CEIL_RATE=${UNCLASSIFIED_WAN_CEIL}"mbit"
}
calculate_lan_htb_qos_parameters () {
   SYSCFG_wan_download_speed=`syscfg get wan_download_speed`
   SYSCFG_dev_max_rate=`syscfg get ${1}_dev_max_rate`
   SYSCFG_gold_percent=`syscfg get ${1}_gold_percent`
   SYSCFG_silver_percent=`syscfg get ${1}_silver_percent`
   SYSCFG_bronze_percent=`syscfg get ${1}_bronze_percent`
   SYSCFG_tin_percent=`syscfg get ${1}_tin_percent`
   SYSEVENT_committed_bitrate=`sysevent get committed_bitrate`
   SYSCFG_hardware_vendor_name=`syscfg get hardware_vendor_name`
   if [ -z "$SYSCFG_hardware_vendor_name" -o "Broadcom" = "$SYSCFG_hardware_vendor_name" ] ; then  
      if [ -z "$SYSCFG_dev_max_rate" -o "0" = "$SYSCFG_dev_max_rate" ] ; then
         DEV=$1
         wl -i $DEV status|grep -q 80MHz
         if [ $? -eq 0 ] ; then
            SYSCFG_dev_max_rate="500"
         fi
         wl -i $DEV status|grep -q 40MHz
         if [ $? -eq 0 ] ; then
            SYSCFG_dev_max_rate="250"
         fi
         wl -i $DEV status|grep -q 20MHz
         if [ $? -eq 0 ] ; then
            SYSCFG_dev_max_rate="155"
         fi
      fi
   fi
   if [ -z "$SYSCFG_dev_max_rate" ] ; then
      SYSCFG_dev_max_rate="250"
   fi
   if [ -z "$SYSCFG_gold_percent" ] ; then
      SYSCFG_gold_percent=60
   fi
   if [ -z "$SYSCFG_silver_percent" ] ; then
      SYSCFG_silver_percent=25
   fi
   if [ -z "$SYSCFG_bronze_percent" ] ; then
      SYSCFG_bronze_percent=10
   fi
   if [ -z "$SYSCFG_tin_percent" ] ; then
      SYSCFG_tin_percent=5
   fi
   if [ -z "$SYSEVENT_committed_bitrate" ] ; then
      SYSEVENT_committed_bitrate="0"
   fi
   AVAIL_RATE=`dc $SYSCFG_dev_max_rate  $SYSEVENT_committed_bitrate - p`
   GOLD_RATE=`dc $AVAIL_RATE  $SYSCFG_gold_percent 100 / \* p`
   SILVER_RATE=`dc $AVAIL_RATE  $SYSCFG_silver_percent 100 / \* p`
   BRONZE_RATE=`dc $AVAIL_RATE  $SYSCFG_bronze_percent 100 / \* p`
   TIN_RATE=`dc $AVAIL_RATE  $SYSCFG_tin_percent 100 / \* p`
   adjust_queues_for_unclassified_wan
   AVAIL_MBIT_RATE=${AVAIL_RATE}"mbit"
   GOLD_MBIT_RATE=${GOLD_RATE}"mbit"
   SILVER_MBIT_RATE=${SILVER_RATE}"mbit"
   BRONZE_MBIT_RATE=${BRONZE_RATE}"mbit"
   TIN_MBIT_RATE=${TIN_RATE}"mbit"
}
remove_wan_shaping () {
   DEV=$1
   tc qdisc del dev ${DEV} root  2> /dev/null > /dev/null
}
prepare_wan_shaping () {
   DEV=$1
   remove_wan_shaping ${DEV}
   SYSCFG_wan_download_speed=`syscfg get wan_download_speed`
   if [ -z "$SYSCFG_wan_download_speed" -o "0" = "$SYSCFG_wan_download_speed" ] ; then
      return
   fi
   
   calculate_wan_shaping_htb_qos_parameters $DEV
   if [ -n "$UNCLASSIFIED_WAN_MBIT_RATE" ] ; then
      tc qdisc add dev ${DEV} handle 1:0 root htb default 50
   else
      tc qdisc add dev ${DEV} handle 1:0 root htb default 40
   fi
   tc class add dev ${DEV} parent 1: classid 1:1 htb rate $AVAIL_MBIT_RATE ceil $AVAIL_MBIT_RATE
   if [ "br0" = "$DEV" ] ; then
       tc class add dev ${DEV} parent 1: classid 1:2 htb rate 1000mbit ceil 1000mbit prio 1
   fi
   tc class add dev ${DEV} parent 1:1 classid 1:10 htb rate $GOLD_MBIT_RATE ceil $AVAIL_MBIT_RATE prio 0 
   tc class add dev ${DEV} parent 1:1 classid 1:20 htb rate $SILVER_MBIT_RATE ceil $AVAIL_MBIT_RATE prio 1 
   tc class add dev ${DEV} parent 1:1 classid 1:30 htb rate $BRONZE_MBIT_RATE ceil $AVAIL_MBIT_RATE prio 2 
   tc class add dev ${DEV} parent 1:1 classid 1:40 htb rate $TIN_MBIT_RATE ceil $AVAIL_MBIT_RATE prio 3 
   if [ -n "$UNCLASSIFIED_WAN_MBIT_RATE" ] ; then
      tc class add dev ${DEV} parent 1:1 classid 1:50 htb rate $UNCLASSIFIED_WAN_MBIT_RATE ceil $UNCLASSIFIED_WAN_CEIL_RATE prio 5 
   fi
   
   if [ "br0" = "$DEV" ] ; then
       tc class add dev ${DEV} parent 1:2 classid 1:60 htb rate 1000mbit ceil 1000mbit prio 1
   fi
   tc qdisc add dev ${DEV} parent 1:10 handle 110: bfifo limit 512k
   tc qdisc add dev ${DEV} parent 1:20 handle 120: bfifo limit 128k
   tc qdisc add dev ${DEV} parent 1:30 handle 130: bfifo limit 128k
   tc qdisc add dev ${DEV} parent 1:40 handle 140: bfifo limit 128k
   if [ -n "$UNCLASSIFIED_WAN_MBIT_RATE" ] ; then
      tc qdisc add dev ${DEV} parent 1:50 handle 150: bfifo limit 128k
   fi
   
   if [ "br0" = "$DEV" ] ; then
       tc qdisc add dev ${DEV} parent 1:60 handle 160: bfifo limit 512k
   fi
   tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${NETWORK_CONTROL_FW_MARK} fw classid 1:10
   tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${VOICE_1_FW_MARK} fw classid 1:10
   tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${VOICE_2_FW_MARK} fw classid 1:10
   tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${VOICE_3_FW_MARK} fw classid 1:10
   tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${VOICE_4_FW_MARK} fw classid 1:20
   tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${VIDEO_1_FW_MARK} fw classid 1:10
   tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${VIDEO_2_FW_MARK} fw classid 1:10
   tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${VIDEO_3_FW_MARK} fw classid 1:20
   tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${VIDEO_4_FW_MARK} fw classid 1:30
   tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${BE_1_FW_MARK} fw classid 1:20
   tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${BE_2_FW_MARK} fw classid 1:30
   tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${BE_3_FW_MARK} fw classid 1:30
   tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${BE_4_FW_MARK} fw classid 1:40
   tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${BK_1_FW_MARK} fw classid 1:40
   tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${BK_2_FW_MARK} fw classid 1:40
   tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${BK_3_FW_MARK} fw classid 1:40
   tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${BK_4_FW_MARK} fw classid 1:40
   if [ -n "$UNCLASSIFIED_WAN_MBIT_RATE" ] ; then
      tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${WAN_UNCLASSIFIED_FW_MARK} fw classid 1:50
   fi
   
   if [ "br0" = "$DEV" ] ; then
       tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${ROUTER_TO_LAN_FW_MARK} fw classid 1:60
   fi
}
recalculate_wan_shaping () {
   DEV=$1
   SYSCFG_wan_download_speed=`syscfg get wan_download_speed`
   if [ -z "$SYSCFG_wan_download_speed" -o "0" = "$SYSCFG_wan_download_speed" ] ; then
      return
   fi
   
   calculate_wan_shaping_htb_qos_parameters $DEV
   if [ -n "$UNCLASSIFIED_WAN_MBIT_RATE" ] ; then
      tc class change dev ${DEV} parent 1:1 classid 1:50 htb rate $UNCLASSIFIED_WAN_MBIT_RATE ceil $UNCLASSIFIED_WAN_CEIL_RATE prio 5 
   fi
}
remove_wireless_qos () {
   DEV=$1
   tc qdisc del dev ${DEV} root  2> /dev/null > /dev/null
}
prepare_wireless_lan_qos_queue_disciplines () {
    DEV=$1
    
    if [ "" = "`ifconfig $DEV  2>/dev/null`" ]; then
        return
    fi
    ifconfig $DEV|grep -q UP
    if [ $? -ne 0 ] ; then
        return
    fi
   remove_wireless_qos ${DEV}
   tc qdisc add dev ${DEV} handle 1:0 root dsmark indices 8 default_index 5
   tc class change dev ${DEV} classid 1:1 dsmark mask 0x1f value 0xc0
   tc class change dev ${DEV} classid 1:2 dsmark mask 0x1f value 0xe0
   tc class change dev ${DEV} classid 1:3 dsmark mask 0x1f value 0x80
   tc class change dev ${DEV} classid 1:4 dsmark mask 0x1f value 0xa0
   tc class change dev ${DEV} classid 1:5 dsmark mask 0xff value 0x00
   tc class change dev ${DEV} classid 1:6 dsmark mask 0x1f value 0x60
   tc class change dev ${DEV} classid 1:7 dsmark mask 0x1f value 0x20
   tc qdisc add dev ${DEV} parent 1:0 handle 2:0 prio bands 3 priomap 1 1 1 1 1 1 1 1 1 1 1 1 1 1 1 1
   tc qdisc add dev ${DEV} parent 2:1 handle 21: pfifo limit 10
   calculate_lan_htb_qos_parameters $DEV
   tc qdisc add dev ${DEV} parent 2:2 handle 22: htb default 30
   tc class add dev ${DEV} parent 22: classid 22:1 htb rate $AVAIL_MBIT_RATE ceil $AVAIL_MBIT_RATE
   tc class add dev ${DEV} parent 22:1 classid 22:10 htb rate $GOLD_MBIT_RATE ceil $AVAIL_MBIT_RATE prio 1 
   tc class add dev ${DEV} parent 22:1 classid 22:20 htb rate $SILVER_MBIT_RATE ceil $AVAIL_MBIT_RATE prio 2 
   tc class add dev ${DEV} parent 22:1 classid 22:30 htb rate $BRONZE_MBIT_RATE ceil $AVAIL_MBIT_RATE prio 3 
   tc class add dev ${DEV} parent 22:1 classid 22:40 htb rate $TIN_MBIT_RATE ceil $AVAIL_MBIT_RATE prio 4 
   if [ -n "$WAN_MBIT_RATE" ] ; then
      tc class add dev ${DEV} parent 22:1 classid 22:50 htb rate $WAN_MBIT_RATE ceil $AVAIL_MBIT_RATE prio 5 
   fi
   tc qdisc add dev ${DEV} parent 22:10 handle 10: sfq perturb 10
   tc qdisc add dev ${DEV} parent 22:20 handle 20: sfq perturb 10
   tc qdisc add dev ${DEV} parent 22:30 handle 30: sfq perturb 10
   tc qdisc add dev ${DEV} parent 22:40 handle 40: sfq perturb 10
   if [ -n "$WAN_MBIT_RATE" ] ; then
      tc qdisc add dev ${DEV} parent 22:50 handle 50: sfq perturb 10
   fi
   tc qdisc add dev ${DEV} parent 2:3 handle 23: pfifo limit 10
   tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${VOICE_1_FW_MARK} fw classid 1:1
   tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${VOICE_2_FW_MARK} fw classid 1:2
   MODEL_NAME=`syscfg get device::model_base`
   if [ -z "$MODEL_NAME" ] ; then
       MODEL_NAME=`syscfg get device::modelNumber`
       MODEL_NAME=${MODEL_NAME%-*}
       MODEL_NAME=${MODEL_NAME%-*}	
   fi
   if [ "$MODEL_NAME" = "EA6200" -o "$MODEL_NAME" = "EA6400" -o "$MODEL_NAME" = "EA6500" -o "$MODEL_NAME" = "EA6700" -o "$MODEL_NAME" = "EA6900" -o "$MODEL_NAME" = "EA9200" ]; then
       tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${VIDEO_1_FW_MARK} fw classid 1:4
       tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${VIDEO_2_FW_MARK} fw classid 1:4
       tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${VIDEO_3_FW_MARK} fw classid 1:4
       tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${VIDEO_4_FW_MARK} fw classid 1:4
   else
       tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${VIDEO_1_FW_MARK} fw classid 1:3
       tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${VIDEO_2_FW_MARK} fw classid 1:4
       tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${VIDEO_3_FW_MARK} fw classid 1:4
       tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${VIDEO_4_FW_MARK} fw classid 1:3
   fi
   tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${BE_1_FW_MARK} fw classid 1:6
   tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${BE_2_FW_MARK} fw classid 1:6
   tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${BE_3_FW_MARK} fw classid 1:6
   tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${BE_4_FW_MARK} fw classid 1:6
   tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${BK_1_FW_MARK} fw classid 1:7
   tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${BK_2_FW_MARK} fw classid 1:7
   tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${BK_3_FW_MARK} fw classid 1:7
   tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${BK_4_FW_MARK} fw classid 1:7
   tc filter add dev ${DEV} parent 2:0 protocol ip prio 1 handle ${VOICE_1_FW_MARK} fw classid 2:1
   tc filter add dev ${DEV} parent 2:0 protocol ip prio 1 handle ${VIDEO_1_FW_MARK} fw classid 2:1
   tc filter add dev ${DEV} parent 2:0 protocol ip prio 1 handle ${NETWORK_CONTROL_FW_MARK} fw classid 2:1
   tc filter add dev ${DEV} parent 22:0 protocol ip prio 1 handle ${VOICE_2_FW_MARK} fw classid 22:10
   tc filter add dev ${DEV} parent 22:0 protocol ip prio 1 handle ${VOICE_3_FW_MARK} fw classid 22:10
   tc filter add dev ${DEV} parent 22:0 protocol ip prio 1 handle ${VOICE_4_FW_MARK} fw classid 22:20
   tc filter add dev ${DEV} parent 22:0 protocol ip prio 1 handle ${VIDEO_2_FW_MARK} fw classid 22:10
   tc filter add dev ${DEV} parent 22:0 protocol ip prio 1 handle ${VIDEO_3_FW_MARK} fw classid 22:20
   tc filter add dev ${DEV} parent 22:0 protocol ip prio 1 handle ${VIDEO_4_FW_MARK} fw classid 22:30
   tc filter add dev ${DEV} parent 22:0 protocol ip prio 1 handle ${BE_1_FW_MARK} fw classid 22:20
   tc filter add dev ${DEV} parent 22:0 protocol ip prio 1 handle ${BE_2_FW_MARK} fw classid 22:30
   tc filter add dev ${DEV} parent 22:0 protocol ip prio 1 handle ${BE_3_FW_MARK} fw classid 22:30
   tc filter add dev ${DEV} parent 22:0 protocol ip prio 1 handle ${BE_4_FW_MARK} fw classid 22:30
   tc filter add dev ${DEV} parent 22:0 protocol ip prio 1 handle ${BK_1_FW_MARK} fw classid 22:40
   tc filter add dev ${DEV} parent 22:0 protocol ip prio 1 handle ${BK_2_FW_MARK} fw classid 22:40
   tc filter add dev ${DEV} parent 22:0 protocol ip prio 1 handle ${BK_3_FW_MARK} fw classid 22:40
   tc filter add dev ${DEV} parent 22:0 protocol ip prio 1 handle ${BK_4_FW_MARK} fw classid 22:40
   if [ -n "$WAN_MBIT_RATE" ] ; then
      tc filter add dev ${DEV} parent 22:0 protocol ip prio 1 handle ${WAN_UNCLASSIFIED_FW_MARK} fw classid 22:50
   fi
   tc filter add dev $DEV parent 22:0 prio 2 protocol ip u32 match u32 0x00b80000 0x00ff0000 at 0 flowid 22:10
   tc filter add dev $DEV parent 22:0 prio 2 protocol ip u32  match ip protocol 6 0xff  match u8 0x05 0x0f at 0  match u16 0x0000 0xffc0 at 2  match u8 0x10 0xff at 33  flowid 22:10
   tc filter add dev $DEV parent 22:0 prio 2 protocol ip u32 match u32 0x00100000 0x00ff0000 at 0 flowid 22:10
   tc filter add dev $DEV parent 22:0 prio 2 protocol ip u32 match u32 0x00080000 0x00ff0000 at 0 flowid 22:30
   tc filter add dev $DEV parent 22:0 prio 2 protocol ip u32 match u32 0x00040000 0x00ff0000 at 0 flowid 22:30
   tc filter add dev $DEV parent 22:0 prio 2 protocol ip u32 match u32 0x00020000 0x00ff0000 at 0 flowid 22:40
   tc filter add dev ${DEV} parent 2:0 protocol ip prio 1 handle ${BE_4_FW_MARK} fw classid 2:3
   tc filter add dev ${DEV} parent 2:0 protocol ip prio 1 handle ${BK_2_FW_MARK} fw classid 2:3
   tc filter add dev ${DEV} parent 2:0 protocol ip prio 1 handle ${BK_3_FW_MARK} fw classid 2:3
   tc filter add dev ${DEV} parent 2:0 protocol ip prio 1 handle ${BK_4_FW_MARK} fw classid 2:3
}
remove_lan_qos () {
   DEV=$1
   tc qdisc del dev $DEV root  2> /dev/null > /dev/null
}
prepare_lan_qos_queue_disciplines () {
   DEV=$1
   remove_lan_qos $DEV
   tc qdisc add dev $DEV root handle 1: prio bands 8 priomap 6 6 6 6 6 6 6 6 6 6 6 6 6 6 6 6
   tc qdisc add dev $DEV parent 1:1 handle 11: pfifo limit 10
   tc qdisc add dev $DEV parent 1:2 handle 12: pfifo limit 10
   tc qdisc add dev $DEV parent 1:3 handle 13: pfifo limit 20
   tc qdisc add dev $DEV parent 1:4 handle 14: pfifo limit 20
   tc qdisc add dev $DEV parent 1:5 handle 15: pfifo limit 20
   tc qdisc add dev $DEV parent 1:6 handle 16: pfifo limit 20
   tc qdisc add dev $DEV parent 1:7 handle 17: pfifo limit 15
   tc qdisc add dev $DEV parent 1:8 handle 18: pfifo limit 5
   tc filter add dev $DEV parent 1:0 prio 1 protocol ip u32 match u32 0x00100000 0x00ff0000 at 0 flowid 1:2
   tc filter add dev $DEV parent 1:0 prio 1 protocol ip u32 match u32 0x00b80000 0x00ff0000 at 0 flowid 1:3
   tc filter add dev $DEV parent 1:0 prio 1 protocol ip u32 match u32 0x00280000 0x00ff0000 at 0 flowid 1:3
   tc filter add dev $DEV parent 1:0 prio 1 protocol ip u32 match u32 0x00480000 0x00ff0000 at 0 flowid 1:4
   tc filter add dev $DEV parent 1:0 prio 1 protocol ip u32 match u32 0x00680000 0x00ff0000 at 0 flowid 1:4
   tc filter add dev $DEV parent 1:0 prio 1 protocol ip u32 match u32 0x00300000 0x00ff0000 at 0 flowid 1:4
   tc filter add dev $DEV parent 1:0 prio 1 protocol ip u32 match u32 0x00500000 0x00ff0000 at 0 flowid 1:4
   tc filter add dev $DEV parent 1:0 prio 1 protocol ip u32 match u32 0x00700000 0x00ff0000 at 0 flowid 1:4
   tc filter add dev $DEV parent 1:0 prio 1 protocol ip u32 match u32 0x00380000 0x00ff0000 at 0 flowid 1:5
   tc filter add dev $DEV parent 1:0 prio 1 protocol ip u32 match u32 0x00580000 0x00ff0000 at 0 flowid 1:5
   tc filter add dev $DEV parent 1:0 prio 1 protocol ip u32 match u32 0x00780000 0x00ff0000 at 0 flowid 1:5
   tc filter add dev $DEV parent 1:0 prio 1 protocol ip u32 match u32 0x00080000 0x00ff0000 at 0 flowid 1:6
   tc filter add dev $DEV parent 1:0 prio 1 protocol ip u32 match u32 0x00040000 0x00ff0000 at 0 flowid 1:7
   tc filter add dev $DEV parent 1:0 prio 1 protocol ip u32 match u32 0x00020000 0x00ff0000 at 0 flowid 1:8
   tc filter add dev $DEV parent 1:0 prio 1 protocol ip u32  match ip protocol 6 0xff  match u8 0x05 0x0f at 0  match u16 0x0000 0xffc0 at 2  match u8 0x10 0xff at 33  flowid 1:2
}
remove_wan_qos () {
   DEV=$1
   tc qdisc del dev $DEV root    2> /dev/null > /dev/null
}
prepare_wan_qos_queue_disciplines () {
   DEV=$1
   DOWNLINK=$2
   UPLINK=$3
   HIGH=`dc $UPLINK 8 \* 15 \/ p`
   MEDIUM=`dc $UPLINK 4 \* 15 \/ p`
   NORMAL=`dc $UPLINK 2 \* 15 \/ p`
   LOW=`dc $UPLINK 1 \* 15 \/ p`
   r2q=10
   q_l=1000
   q_u=200000
   MIN_QUANTA=1500
   QUANTUM1=`dc $HIGH 131072 \* $r2q \/ p`
   QUANTUM2=`dc $MEDIUM 131072 \* $r2q \/ p`
   QUANTUM3=`dc $NORMAL 131072 \* $r2q \/ p`
   QUANTUM4=`dc $LOW 131072 \* $r2q \/ p`
   QUANTUM1=`echo "$QUANTUM1" | awk '{printf("%d\n",$1 + 0.5)}'`
   QUANTUM2=`echo "$QUANTUM2" | awk '{printf("%d\n",$1 + 0.5)}'`
   QUANTUM3=`echo "$QUANTUM3" | awk '{printf("%d\n",$1 + 0.5)}'`
   QUANTUM4=`echo "$QUANTUM4" | awk '{printf("%d\n",$1 + 0.5)}'`
   if [ $q_l -lt $QUANTUM1 -a $QUANTUM1 -lt $q_u -a $q_l -lt $QUANTUM2 -a $QUANTUM2 -lt $q_u -a $q_l -lt $QUANTUM3 -a $QUANTUM3 -lt $q_u -a $q_l -lt $QUANTUM4 -a $QUANTUM4 -lt $q_u ] ; then
      QUANTUM1=""
      QUANTUM2=""
      QUANTUM3=""
      QUANTUM4=""
      QUANTUM1=""
      QUANTUM2=""
      QUANTUM3=""
      QUANTUM4=""
   else
      if [ $q_u -lt $QUANTUM1 -o $q_u -lt $QUANTUM2 -o $q_u -lt $QUANTUM3 -o $q_u -lt $QUANTUM4 ] ; then
         QUANTUM1=$q_u
         QUANTUM2=`dc $QUANTUM1 2 \/ p`
         QUANTUM3=`dc $QUANTUM2 2 \/ p`
         QUANTUM4=`dc $QUANTUM3 2 \/ p`
      fi
      if [ $q_l -gt $QUANTUM1 -o $q_l -gt $QUANTUM2 -o $q_l -gt $QUANTUM3 -o $q_l -gt $QUANTUM4 ] ; then
         QUANTUM4=$MIN_QUANTA
         QUANTUM3=`dc $QUANTUM4 2 \* p`
         QUANTUM2=`dc $QUANTUM3 2 \* p`
         QUANTUM1=`dc $QUANTUM2 2 \* p`
      fi
      QUANTUM1="quantum $QUANTUM1"
      QUANTUM2="quantum $QUANTUM2"
      QUANTUM3="quantum $QUANTUM3"
      QUANTUM4="quantum $QUANTUM4"
   fi
   NOPRIOHOSTSRC=
   NOPRIOHOSTDST=
   NOPRIOPORTSRC=
   NOPRIOPORTDST=
   
   remove_wan_qos ${DEV}
   tc qdisc add dev ${DEV} root handle 1: prio bands 3 priomap 2 2 2 2 2 2 2 2 2 2 2 2 2 2 2 2
   tc qdisc add dev ${DEV} parent 1:1 handle 11: pfifo limit 10
   tc qdisc add dev ${DEV} parent 1:2 handle 12: pfifo limit 10
   if [ -z "$UPLINK" -o "0" = "$UPLINK" ] ; then
      tc qdisc add dev ${DEV} parent 1:3 handle 13: pfifo limit 20
      tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${NETWORK_CONTROL_FW_MARK} fw classid 1:1
      tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${VOICE_1_FW_MARK} fw classid 1:2
      tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${VIDEO_1_FW_MARK} fw classid 1:2
   else
      tc qdisc add dev ${DEV} parent 1:3 handle 13: htb default 30 r2q $r2q
      tc class add dev ${DEV} parent 13: classid 13:1 htb rate ${UPLINK}mbit ceil ${UPLINK}mbit burst 6k
      tc class add dev ${DEV} parent 13:1 classid 13:10 htb rate ${HIGH}mbit ceil \
      ${UPLINK}mbit burst 6k prio 1 $QUANTUM1
      tc class add dev ${DEV} parent 13:1 classid 13:20 htb rate ${MEDIUM}mbit \
      ceil ${UPLINK}mbit burst 6k prio 2 $QUANTUM2
      tc class add dev ${DEV} parent 13:1 classid 13:30 htb rate ${NORMAL}mbit \
      ceil ${UPLINK}mbit burst 6k prio 2 $QUANTUM3 
      tc class add dev ${DEV} parent 13:1 classid 13:40 htb rate ${LOW}mbit ceil \
      ${UPLINK}mbit burst 6k prio 2 $QUANTUM4
      tc qdisc add dev ${DEV} parent 13:10 handle 10: sfq perturb 10
      tc qdisc add dev ${DEV} parent 13:20 handle 20: sfq perturb 10
      tc qdisc add dev ${DEV} parent 13:30 handle 30: sfq perturb 10
      tc qdisc add dev ${DEV} parent 13:40 handle 40: sfq perturb 10
      tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${NETWORK_CONTROL_FW_MARK} fw classid 1:1
      tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${VOICE_1_FW_MARK} fw classid 1:2
      tc filter add dev ${DEV} parent 1:0 protocol ip prio 1 handle ${VIDEO_1_FW_MARK} fw classid 1:2
      tc filter add dev $DEV parent 13:0 protocol ip prio 10 u32 match ip tos \
      0x10 0xff flowid 13:10
      tc filter add dev $DEV parent 13:0 protocol ip prio 10 u32 match ip \
      protocol 1 0xff flowid 13:10
      tc filter add dev $DEV parent 13: protocol ip prio 10 u32 \
         match ip protocol 6 0xff \
         match u8 0x05 0x0f at 0 \
         match u16 0x0000 0xffc0 at 2 \
         match u8 0x10 0xff at 33 \
         flowid 13:10
      tc filter add dev ${DEV} parent 13:0 protocol ip prio 10 u32 match u8 0xa0 \
      0xe0 at 1 flowid 13:10
      tc filter add dev ${DEV} parent 13:0 protocol ip prio 11 handle ${VOICE_2_FW_MARK} fw classid 13:10
      tc filter add dev ${DEV} parent 13:0 protocol ip prio 11 handle ${VIDEO_2_FW_MARK} fw classid 13:10
      tc filter add dev ${DEV} parent 13:0 protocol ip prio 11 handle ${VOICE_3_FW_MARK} fw classid 13:10
      tc filter add dev ${DEV} parent 13:0 protocol ip prio 11 handle ${VIDEO_3_FW_MARK} fw classid 13:10
      tc filter add dev ${DEV} parent 13:0 protocol ip prio 11 handle ${VOICE_4_FW_MARK} fw classid 13:10
      tc filter add dev ${DEV} parent 13:0 protocol ip prio 11 handle ${BE_1_FW_MARK} fw classid 13:10
      tc filter add dev ${DEV} parent 13:0 protocol ip prio 10 u32 match u8 0x80 \
      0xe0 at 1 flowid 13:20
      tc filter add dev ${DEV} parent 13:0 protocol ip prio 10 u32 match u8 0x60 \
      0xe0 at 1 flowid 13:20
      tc filter add dev ${DEV} parent 13:0 protocol ip prio 11 handle ${VIDEO_4_FW_MARK} fw classid 13:20
      tc filter add dev ${DEV} parent 13:0 protocol ip prio 11 handle ${BE_2_FW_MARK} fw classid 13:20
      tc filter add dev ${DEV} parent 13:0 protocol ip prio 10 u32 match u8 0x40 \
      0xe0 at 1 flowid 13:30
      tc filter add dev ${DEV} parent 13:0 protocol ip prio 11 handle ${BE_3_FW_MARK} fw classid 13:30
      tc filter add dev $DEV parent 13:0 protocol ip prio 10 u32 match u8 0x20 \
      0xe0 at 1 flowid 13:40
      tc filter add dev ${DEV} parent 13:0 protocol ip prio 11 handle ${BE_4_FW_MARK} fw classid 13:40
      tc filter add dev ${DEV} parent 13:0 protocol ip prio 11 handle ${BK_1_FW_MARK} fw classid 13:40
      tc filter add dev ${DEV} parent 13:0 protocol ip prio 11 handle ${BK_2_FW_MARK} fw classid 13:40
      tc filter add dev ${DEV} parent 13:0 protocol ip prio 11 handle ${BK_3_FW_MARK} fw classid 13:40
      tc filter add dev ${DEV} parent 13:0 protocol ip prio 11 handle ${BK_4_FW_MARK} fw classid 13:40
      tc filter add dev ${DEV} parent 13:0 protocol ip prio 12 handle ${GN_TC_FW_MARK} fw flowid 13:40
      for a in $NOPRIOPORTDST
      do
         tc filter add dev $DEV parent 13: protocol ip prio 14 u32  match ip dport $a 0xffff flowid 13:30
      done
      for a in $NOPRIOPORTSRC
      do
         tc filter add dev $DEV parent 13: protocol ip prio 15 u32  match ip sport $a 0xffff flowid 13:40
      done
      for a in $NOPRIOHOSTSRC
      do
         tc filter add dev $DEV parent 13: protocol ip prio 16 u32 match ip src $a flowid 13:40
      done
      for a in $NOPRIOHOSTDST
      do
         tc filter add dev $DEV parent 13: protocol ip prio 17 u32  match ip dst $a flowid 13:40
      done
      tc filter add dev $DEV parent 13: protocol ip prio 18 u32 match ip dst 0.0.0.0/0 flowid 13:30
   fi
}
