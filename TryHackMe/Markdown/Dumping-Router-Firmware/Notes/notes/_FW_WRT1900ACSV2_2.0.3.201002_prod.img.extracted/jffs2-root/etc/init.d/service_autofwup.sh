#!/bin/sh
source /etc/init.d/interface_functions.sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
SERVICE_NAME="autofwup"
cron_event()
{
	UPTIME=`cat /proc/uptime | cut -d'.' -f1`
	BASETIME="60"
	if [ $UPTIME -lt $BASETIME ]; then
		return 0
	fi
	AUTOFLAG=`utctx_cmd get fwup_autoupdate_flags`
	eval $AUTOFLAG
	if [ "${SYSCFG_fwup_autoupdate_flags}" -ne "2" ]; then
		CHECK_AFTER_BOOT=`sysevent get fwup_checked_after_boot`
		if [ "${CHECK_AFTER_BOOT}" = "0" ]; then
			sysevent set fwup_checked_after_boot 1
		fi
		FORCED_CHECK=`sysevent get fwup_forced_check`
		if [ "${FORCED_CHECK}" -ne "0" ]; then
			sysevent set fwup_forced_check 0
		fi
		return 0
	fi
	
	pidof fwupd > /dev/null
	if [ $? != "0" ] 
	then
		CHECK_AFTER_BOOT=`sysevent get fwup_checked_after_boot`
		if [ "${CHECK_AFTER_BOOT}" = "0" ]; then
			sysevent set fwup_forced_check 1
			fwupd -m 3 &
		else
			FORCED_CHECK=`sysevent get fwup_forced_check`
			CONFIG_FILE="/tmp/update.conf"
			if [ "${FORCED_CHECK}" = "1" ]; then
				if [ -e ${CONFIG_FILE} ]; then
					FORCED_UPDATE=`cat ${CONFIG_FILE} | grep "forced_firmware_update" | cut -d'=' -f2`
					if [ "${FORCED_UPDATE}" = "1" ]; then
						FORCED_UPDATE_DELAY=`cat ${CONFIG_FILE} | grep "forced_update_delay" | cut -d'=' -f2`
						if [ -z  ${FORCED_UPDATE_DELAY} ]; then
							FORCED_UPDATE_DELAY="1"
						fi
						sysevent set fwup_forced_update_delay ${FORCED_UPDATE_DELAY}
						
						FORCED_UPDATE_SIGNAL=`cat ${CONFIG_FILE} | grep "forced_update_signal" | cut -d'=' -f2`
						if [ -z  ${FORCED_UPDATE_SIGNAL} ]; then
							FORCED_UPDATE_SIGNAL="0"
						fi
						sysevent set fwup_forced_update_signal ${FORCED_UPDATE_SIGNAL}
						sysevent set fwup_forced_check 2
					else
						sysevent set fwup_forced_check 0
					fi
					
					rm -f ${CONFIG_FILE}
				else
					sysevent set fwup_forced_check 0
				fi
			elif [ "${FORCED_CHECK}" = "2" ]; then
				FORCED_UPDATE_DELAY=`sysevent get fwup_forced_update_delay`
				FORCED_UPDATE_DELAY_SEC=`expr $FORCED_UPDATE_DELAY \* 60`
				if [ $UPTIME -gt $FORCED_UPDATE_DELAY_SEC ]; then
					fwupd -m 4 -e &
				fi
			elif [ "${FORCED_CHECK}" = "3" ]; then
				fwupd -m 4 -e &
			else
				FORCED_UPDATE_DONE=`sysevent get fwup_forced_update_done`
				if [ "${FORCED_UPDATE_DONE}" = "1" ]; then
					if [ -e "/etc/led/sw_blink.sh" ]; then
						/etc/led/sw_blink.sh 1 > /dev/null
					fi
				else
					AUTOFLAG=`utctx_cmd get fwup_autoupdate_flags`
					eval $AUTOFLAG
					if [ "${SYSCFG_fwup_autoupdate_flags}" -ne "0" ]; then
						fwupd -e &
					fi
				fi
			fi
		fi
	fi		
}
update_event()
{
	ulog autofwup status "update_firmware_now event : ${UPDATEMODE}, ${ALTSERVERURL}"
    pidof fwupd > /dev/null
	if [ $? != "0" ] 
	then
        UPDATEMODE=`sysevent get update_firmware_now`
    	sysevent set update_firmware_now    
    	ALTSERVERURL=`syscfg get fwup_altserver_uri`
    	syscfg unset fwup_altserver_uri
		if [ "${UPDATEMODE}" = "1"  -o "${UPDATEMODE}" = "2" ]
		then
			if [ ! -z "${ALTSERVERURL}" ]
			then
    			fwupd -m ${UPDATEMODE} -u ${ALTSERVERURL} &
			else
    			fwupd -m ${UPDATEMODE} &
			fi
		fi
    fi		
}
check_mount() 
{
	PRIROOTFSMTD=`cat /proc/mtd | grep "rootfs" | grep -v "alt_" | cut -d':' -f1 | cut -f2 -d"d"`
	PRIROOTFSMTD2=`expr $PRIROOTFSMTD - 1`
	ALTROOTFSMTD=`cat /proc/mtd | grep "alt_rootfs" | cut -d':' -f1 | cut -f2 -d"d"`
	ALTROOTFSMTD2=`expr $ALTROOTFSMTD - 1`
	MOUNTEDMTD=`cat /proc/self/mountinfo | grep "\/dev\/root" | cut -f3 -d' ' | cut -f2 -d':'`
	
	IS_ALT=`cat /proc/mtd | grep mtd${MOUNTEDMTD} | grep "alt_"`
	
	if [ "$IS_ALT" ] ; then
		return 1
	else
		return 0
	fi
	
	if [ "$MOUNTEDMTD" -eq "$PRIROOTFSMTD" ] || [ "$MOUNTEDMTD" -eq "$PRIROOTFSMTD2" ]
	then
		return 0	
	fi
	if [ "$MOUNTEDMTD" -eq "$ALTROOTFSMTD" ] || [ "$MOUNTEDMTD" -eq "$ALTROOTFSMTD2" ]
	then
		return 1	
	fi
	return 255 
}
mount_downloads()
{
	mounted=0
	DOWNLOADS_DIR=/var/downloads
	DOWNLOADS_PARTITION=$(awk -F: '/downloads/ { print $1 }' /proc/mtd)
	if [ -z $DOWNLOADS_PARTITION ]; then
		ulog autofwup status  "Skip to mount downloads partition, /dev/$DOWNLOADS_PARTITION"
	elif mount | grep "/dev/mtd.* on /tmp " > /dev/null; then
		echo /tmp already mounted on MTD partition
		mkdir -p /tmp/var/downloads
	else
		MTD_DEVICE=/dev/${DOWNLOADS_PARTITION}
		MTD_BLOCK_DEVICE=/dev/$(echo ${DOWNLOADS_PARTITION} | sed s/mtd/mtdblock/)
		mkdir -p ${DOWNLOADS_DIR} || echo No mount point for downloads storage.
		if mount -t jffs2 -o noatime $MTD_BLOCK_DEVICE ${DOWNLOADS_DIR}; then
			mounted=1
		else
			echo Downloads persistent storage mount failed, attempting format
			if ! flash_eraseall -j ${MTD_DEVICE}; then
				echo Format downloads persistent storage failed.  Perhaps mkfs.jffs not installed.  Giving up.
			else
				if mount -t jffs2 -o noatime ${MTD_BLOCK_DEVICE} ${DOWNLOADS_DIR}; then
					echo Format succeeded, downloads mount still failed.  Giving up.
				fi
			fi
		fi
	fi
	
	if [ ${mounted} -ne 0 ]; then
		chmod 777 ${DOWNLOADS_DIR}	
	fi
}
init_variables()
{
    sysevent set fwup_state 0
    sysevent set fwup_progress 0
    sysevent set fwup_checked_after_boot 0
    sysevent set fwup_forced_update_done 0
    sysevent set fwup_forced_update_count 0
    sysevent set fwup_forced_check 0
    syscfg unset fwup_start_timewindow
    syscfg unset fwup_end_timewindow
    syscfg unset fwup_newfirmware_version
    syscfg unset fwup_newfirmware_date
    syscfg unset fwup_newfirmware_details
    syscfg unset fwup_newfirmware_status_details
    FWVERSION=`cat /etc/version`
    syscfg set fwup_firmware_version ${FWVERSION} 
    BUILDDATE=`cat /etc/builddate.timet`
    syscfg set fwup_firmware_date ${BUILDDATE} 
	if [ -e "/usr/sbin/nvram" ]
	then
		BOOTPART=`nvram get bootpartition`
		if [ $BOOTPART = "1" ]
		then
		    syscfg set fwup_boot_part 2
		else
		    syscfg set fwup_boot_part 1
		fi
	else
	    syscfg set fwup_boot_part 0
	    if [ -e /proc/mtd ] 
	    then
	        check_mount
    	    RET=$?
        	if [ $RET -eq 0 ] 
	        then
                syscfg set fwup_boot_part 1
            fi
            if [ $RET -eq 1 ] 
            then
                syscfg set fwup_boot_part 2
	        fi
	    fi
	fi
	rm -f /tmp/var/config/downloads/* 
	rm -f /tmp/var/config/*.tmp 
	rm -f /tmp/var/config/lighttpd-upload*
	BOOTPART=`syscfg get fwup_boot_part`
	CONFIGDIR="/tmp/var/config"
	LICENSEDIR="${CONFIGDIR}/license"
	DEFAULTLICENSE="FW_LICENSE_default.pdf"
	WEBLINKFORLICENSE="/tmp/license.pdf"
	if [ -e "${LICENSEDIR}/primary" ]; then
		PRIMARYLICENSE=`cat ${LICENSEDIR}/primary`
		syscfg set fwup_primary_licensefile ${PRIMARYLICENSE}
	else
		PRIMARYLICENSE=
		syscfg unset fwup_primary_licensefile
	fi
	if [ -e "${LICENSEDIR}/alternate" ]; then
		ALTERNATELICENSE=`cat ${LICENSEDIR}/alternate`
		syscfg set fwup_alternate_licensefile ${ALTERNATELICENSE}
	else
		ALTERNATELICENSE=
		syscfg unset fwup_alternate_licensefile
	fi
	if [ "${BOOTPART}" = "1" ]
	then
		if [ ! -z ${PRIMARYLICENSE} ] && [ -e "${LICENSEDIR}/${PRIMARYLICENSE}.gz" ]
		then
			cp -f ${LICENSEDIR}/${PRIMARYLICENSE}.gz /tmp/.
			gzip -df /tmp/${PRIMARYLICENSE}.gz
			mv -f /tmp/${PRIMARYLICENSE} ${WEBLINKFORLICENSE}
		else
			if [ -e "/etc/${DEFAULTLICENSE}.gz" ]
			then
				cp -f /etc/${DEFAULTLICENSE}.gz /tmp/.
				gzip -df /tmp/${DEFAULTLICENSE}.gz
				mv -f /tmp/${DEFAULTLICENSE} ${WEBLINKFORLICENSE}
			fi
		fi
	else
		if [ ! -z ${ALTERNATELICENSE} ] && [ -e "${LICENSEDIR}/${ALTERNATELICENSE}.gz" ]
		then
			cp -f ${LICENSEDIR}/${ALTERNATELICENSE}.gz /tmp/.
			gzip -df /tmp/${ALTERNATELICENSE}.gz
			mv -f /tmp/${ALTERNATELICENSE} ${WEBLINKFORLICENSE}
		else
			if [ -e "/etc/${DEFAULTLICENSE}.gz" ]
			then
				cp -f /etc/${DEFAULTLICENSE}.gz /tmp/.
				gzip -df /tmp/${DEFAULTLICENSE}.gz
				mv -f /tmp/${DEFAULTLICENSE} ${WEBLINKFORLICENSE}
			fi
		fi
	fi
	sysevent set LICENSE_Url ${WEBLINKFORLICENSE}
	KEEP=/var/config/files-to-keep.conf
	TEMPLATE=/etc/files-to-keep.conf
	touch $KEEP
	cat $KEEP $TEMPLATE | sort -u > $KEEP
	mount_downloads
}
update_license()
{
	BOOTPART=`syscfg get fwup_boot_part`
	CONFIGDIR="/tmp/var/config"
	LICENSEDIR="${CONFIGDIR}/license"
	mkdir -p ${LICENSEDIR}
	PRIMARYLICENSE=
	if [ -e "${LICENSEDIR}/primary" ]; then
		PRIMARYLICENSE=`cat ${LICENSEDIR}/primary`
	fi
	ALTERNATELICENSE=
	if [ -e "${LICENSEDIR}/alternate" ]; then
		PRIMARYLICENSE=`cat ${LICENSEDIR}/alternate`
	fi
	
	HOWMANYLICENSEDOC=`ls ${LICENSEDIR}/*.gz -1 | wc -l`
	if [ $HOWMANYLICENSEDOC -gt 2 ] 
	then
		mkdir -p /tmp/templicense
		if [ ! -z ${PRIMARYLICENSE} ] && [ -e "${LICENSEDIR}/${PRIMARYLICENSE}.gz" ]
		then
			cp -f ${LICENSEDIR}/${PRIMARYLICENSE}.gz /tmp/templicense/.
		fi
		if [ ! -z ${ALTERNATELICENSE} ] && [ -e "${LICENSEDIR}/${ALTERNATELICENSE}.gz" ]
		then
			cp -f ${LICENSEDIR}/${ALTERNATELICENSE}.gz /tmp/templicense/.
		fi
		if [ -e "${LICENSEDIR}/fw_license.pdf.gz" ]
		then
			cp -f ${LICENSEDIR}/fw_license.pdf.gz /tmp/templicense/.
		fi
		if [ -e "${LICENSEDIR}/primary" ]
		then
			cp -f ${LICENSEDIR}/primary /tmp/templicense/.
		fi
		if [ -e "${LICENSEDIR}/alternate" ]
		then
			cp -f ${LICENSEDIR}/alternate /tmp/templicense/.
		fi
		rm -f ${LICENSEDIR}/*.gz
		mv -f /tmp/templicense/* ${LICENSEDIR}/.
	fi
	if [ ! -z $1 ]
	then
		LICENSE_FILE=`echo "$1" | cut -f3 -d'/'`
		if [ "${BOOTPART}" = "1" ]
		then
			if [ ! -z ${ALTERNATELICENSE} ] && [ -e "${LICENSEDIR}/${ALTERNATELICENSE}.gz" ]
			then
				rm -f ${LICENSEDIR}/${ALTERNATELICENSE}.gz
			fi
			echo "${LICENSE_FILE}" > ${LICENSEDIR}/alternate
		else
			if [ ! -z ${PRIMARYLICENSE} ] && [ -e "${LICENSEDIR}/${PRIMARYLICENSE}.gz" ]
			then
				rm -f ${LICENSEDIR}/${PRIMARYLICENSE}.gz
			fi
			echo "${LICENSE_FILE}" > ${LICENSEDIR}/primary
		fi
		gzip -cf $1 > ${LICENSEDIR}/$LICENSE_FILE.gz
	else
		if [ "${BOOTPART}" = "1" ]
		then
			if [ ! -z ${ALTERNATELICENSE} ] && [ -e "${LICENSEDIR}/${ALTERNATELICENSE}.gz" ]
			then
				rm -f ${LICENSEDIR}/${ALTERNATELICENSE}.gz
			fi
			rm -f ${LICENSEDIR}/alternate
		else
			if [ ! -z ${PRIMARYLICENSE} ] && [ -e "${LICENSEDIR}/${PRIMARYLICENSE}.gz" ]
			then
				rm -f ${LICENSEDIR}/${PRIMARYLICENSE}.gz
			fi
			rm -f ${LICENSEDIR}/primary
		fi
	fi
	touch ${CONFIGDIR}/updated
}
verify_linksys_header () 
{
	LINKSYS_HDR="/tmp/linksys.hdr"
	FILE_LENGTH=`stat -c%s "$1"`
	IMAGE_LENTGH=`expr "$FILE_LENGTH" - 256`
	dd if="$1" of="$LINKSYS_HDR" skip="$IMAGE_LENTGH" bs=1 count=256 > /dev/console
	magic_string="`cat $LINKSYS_HDR | cut -b 1-9`"
	hdr_version="`cat $LINKSYS_HDR | cut -b 10-11`"
	hdr_length="`cat $LINKSYS_HDR | cut -b 12-16`"
	sku_length="`cat $LINKSYS_HDR | cut -b 17`"
	sku_end=`expr 18 + "$sku_length" - 2`
	sku_string="`cat $LINKSYS_HDR | cut -b 18-$sku_end`"
	img_cksum="`cat $LINKSYS_HDR | cut -b 33-40`"
	sign_type="`cat $LINKSYS_HDR | cut -b 41`"
	signer="`cat $LINKSYS_HDR | cut -b 42-48`"
	kernel_ofs="`cat $LINKSYS_HDR | cut -b 50-56`"
	rfs_ofs="`cat $LINKSYS_HDR | cut -b 58-64`"
	if [ "$magic_string" != ".LINKSYS." ]
	then
		ulog autofwup status  "Fail : verify magic string "
		exit 1
	fi
	crc1=`dd if="$1" bs="$IMAGE_LENTGH" count=1| cksum | cut -d' ' -f1`
	hex_cksum=`printf "%08X" "$crc1"`
	if [ "$img_cksum" != "$hex_cksum" ]
	then
		ulog autofwup status "Fail : verify image checksum "
    	exit 1
	fi
}
 
verify_header () 
{
	header_file="/tmp/img_hdr"
	magic="`cat $header_file | cut -b 1-6`"
	version="`cat $header_file | cut -b 7-8`"
	img_cksum="`cat $header_file | cut -b 25-32`"
	rm -rf $header_file
	if [ "$magic" != ".CSIH." ]
	then
		ulog autofwup status "Fail : verify magic "
		exit 1
	fi
	
	if [ "$version" != "01" ]
	then
		ulog autofwup status "Fail : verify version "
		exit 1
	fi
	crc1=`cksum $1 | cut -d' ' -f1`
	hex_cksum=`printf "%08X" "$crc1"`
	if [ "$img_cksum" != "$hex_cksum" ]
	then
		ulog autofwup status "Fail : verify checksum "
    	exit 1
	fi
}
service_start ()
{
   sysevent set ${SERVICE_NAME}-errinfo
   sysevent set ${SERVICE_NAME}-status started
}
service_stop ()
{
   sysevent set ${SERVICE_NAME}-errinfo
   sysevent set ${SERVICE_NAME}-status stopped
}
case "$1" in
   ${SERVICE_NAME}-start)
      service_start
      ;;
   ${SERVICE_NAME}-stop)
      service_stop
      ;;
   ${SERVICE_NAME}-restart)
      service_stop
      service_start
      ;;
   cron_every_minute)
      cron_event
      ;;
   update_firmware_now)
      update_event
      ;;   
   init_vars)
      init_variables
      ;;   
   license)
      update_license "$2" 
      ;;   
   verify_linksys)
      verify_linksys_header "$2" 
      ;;  
   verify)
      verify_header "$2" 
      ;;        
   *)
      echo "Usage: service-${SERVICE_NAME} [ ${SERVICE_NAME}-start | ${SERVICE_NAME}-stop | ${SERVICE_NAME}-restart]" > /dev/console
      exit 3
      ;;
esac
