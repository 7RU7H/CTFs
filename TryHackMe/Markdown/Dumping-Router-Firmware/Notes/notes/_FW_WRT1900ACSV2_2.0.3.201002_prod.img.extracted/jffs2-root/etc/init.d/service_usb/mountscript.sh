#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/usb_functions.sh 
SERVICE_NAME="usb_mountscript"
PID="($$)"
USBINFO="/tmp/.usbinfo"
sysevent set usb_info_dir $USBINFO
all_fat="vfat" 
all_linux="ext2 ext3 ext4"
all_ntfs="ntfs"
macOS_ufsd="hfs hfsplus"
moptions="defaults,fmask=0002,dmask=0002"
ufsd_debug="trace=0xffffffff,log=/var/log/ufsd.log"
ufsd_supported_fs="hfs ntfs"
check_fs()
{
   FS_NAME=$1
   PART=$2
   UTIL=/sbin/chk${FS_NAME}
   RET_CODE=
   echo $SERVICE_NAME $PID checking filesystem $FS_NAME on partition $PART
      
   if [ ! -x $UTIL ] ; then
      return 2
   fi
   
   if [ ! -e $PART ]; then
      return 3
   fi
   
   $UTIL -a -f $PART; RET_CODE=$?
   if [[ $RET_CODE -ge 0 && $RET_CODE -lt 5 ]] ; then
      return 0
   elif [ "$RET_CODE" = "4" ] ; then   # Errors found but cannot fix
      ulog usb_mountscript status "$PID found unrecoverable error on $PART"
      sysevent set ${SERVICE_NAME}-status error
      sysevent set ${SERVICE_NAME}-errinfo "Unrecoverable filesystem error"
      return 1
   elif [ "$RET_CODE" = "6" ] ; then
      return 4
   fi
   ulog usb_mountscript status "$PID found Unreconized filesystem error($RETCODE) on $PART"
   sysevent set ${SERVICE_NAME}-status error
   sysevent set ${SERVICE_NAME}-errinfo "Unreconized filesystem error"
   
   return 5
}
check_partition()
{
   PART=$1
   
   echo $SERVICE_NAME $PID checking partition $PART
   
   for fs_name in $ufsd_supported_fs
   do
      check_fs $fs_name $PART; RET_CODE=$?
      if [ ! "$RET_CODE" = "4" ] ; then
         break
      fi
   done
    
   case $RET_CODE in
      0)
         return 0
      ;;
      2)    
         ulog usb_mountscript status "$PID cannot find the chk utility"
      ;;
      3) 
         ulog usb_mountscript status "$PID bad partition $PART"
         return 1
      ;;
      4) 
         ulog usb_mountscript status "$PID unsupported file system on $PART"
         sysevent set ${SERVICE_NAME}-status error
         sysevent set ${SERVICE_NAME}-errinfo "Unsupported filesystem error"
         return 3
      ;;
      *)
      ;;
   esac
   
   return 2 
}
finish_mount_drives()
{
  numPartitions=$2
  partitionCnt=$1
  DRIVE_COUNT=0;
  DEVS=`ls /dev/ | grep -r "sd[a-z]" | uniq`
  if [ "$DEVS" != "" ] ; then
  for d in $DEVS
  do
    if [ -d "/mnt/$d" ] ; then
      mnt_pt_check=`mount | grep /tmp/mnt/$d | wc -l`
      if [ $mnt_pt_check -gt 0 ] ; then
        DRIVE_COUNT=`expr $DRIVE_COUNT + 1`
      fi
    fi
  done
  fi
  mnt_pt_check=`mount | grep /tmp/mnt/$devblock | wc -l`
  xx=`expr $mnt_pt_check + 1`
  echo "DRIVE_COUNT = $DRIVE_COUNT, CUR_COUNT = $xx, numPartitions=$numPartitions, partitionCnt=$partitionCnt" > /dev/console
  if [ "$numPartitions" == "$partitionCnt" ]; then
    dc=`sysevent get no_usb_drives`
    if [ ! "$dc" ] ; then
			dc="0"
    fi
    echo "setting current device count from $dc to $DRIVE_COUNT" >> /dev/console
    sysevent set no_usb_drives $DRIVE_COUNT
    sysevent set mount_usb_drives "finished"
    sysevent set usb_no_partitions_$devblock ""
    sysevent set usb_curr_partition_cnt_$devblock ""
    usb_mount_drives=`ls "/tmp/mnt/" | grep "$devblock" | wc -l`
    sysevent set usb_mount_cnt_$devblock $usb_mount_drives
  else
		if [ "$numPartitions" == "0" ] || [ "$xx" == "$numPartitions" -a "$numPartitions" -gt "9" ] ; then
			if [ "$partitionCnt" != "0" ] ; then
			echo "partition count mismatch $numPartitions:$partitionCnt" >> /dev/console
			echo "trying to mount anyway" >> /dev/console
			dc=`sysevent get no_usb_drives`
			if [ ! "$dc" ] ; then
				dc="0"
			fi
			echo "setting current drive count from $dc to $DRIVE_COUNT" >> /dev/console
			sysevent set no_usb_drives $DRIVE_COUNT
			sysevent set mount_usb_drives "finished"
			sysevent set usb_no_partitions_$devblock ""
			sysevent set usb_curr_partition_cnt_$devblock ""
			usb_mount_drives=`ls "/tmp/mnt/" | grep "$devblock" | wc -l`
			sysevent set usb_mount_cnt_$devblock $usb_mount_drives
			fi
		fi
  fi
}
get_usb_size()
{
  local ret_string
  total=`df /mnt/$1/ | tail -1 | awk '{print $2}'`
  used=`df /mnt/$1/ | tail -1 | awk '{print $3}'`
  free=`df /mnt/$1/ | tail -1 | awk '{print $4}'`
  percent=`df /mnt/$1/ | tail -1 | awk '{print $5}'`
  ret_string="$total $used $free $percent"
  echo $ret_string  
}
add_sysevent_usb_info()
{
  storage_devices=`sysevent get usb_storage_devices`
  if [ -z "$storage_devices" ] ; then
    sysevent set usb_storage_devices "$1"
  else
    echo "$storage_devices" | grep -q "$1"
    if [ "$?" != "0" ] ; then
      sysevent set usb_storage_devices "$storage_devices $1"
    fi
  fi
  partitions=`sysevent get usb_${1}_partitions`
  if [ -z "$partitions" ] ; then
    sysevent set usb_${1}_partitions "$2"
  else
    sysevent set usb_${1}_partitions "$partitions $2"
  fi
  dlabel=`usblabel $2`
  dsize=`get_usb_size $2`
  sysevent set usb_${2}_status "$3"
  sysevent set usb_${2}_label "$dlabel"
  sysevent set usb_${2}_fstype "$4"
  sysevent set usb_${2}_sizes "$dsize" 
}
del_sysevent_usb_info()
{
  storage_devices=`sysevent get usb_storage_devices`
  
  if [ -n "$storage_devices" ] ; then
    echo "$storage_devices" | grep -q "$1$"
    if [ "$?" = "0" ] ; then
      storage_devices=`echo $storage_devices | sed "s/$1//g"`
    else
      storage_devices=`echo $storage_devices | sed "s/$1 //g"`
    fi
    sysevent set usb_storage_devices "$storage_devices"
  fi
  partitions=`sysevent get usb_${1}_partitions`
  
  if [ -n "$partitions" ] ; then
    echo "$partitions" | grep -q "$2$"
    if [ "$?" = "0" ] ; then
      partitions=`echo $partitions | sed "s/$2//g"`
    else
      partitions=`echo $partitions | sed "s/$2 //g"`
    fi
    sysevent set usb_${1}_partitions "$partitions"
  fi
  sysevent set usb_${1}_info 
  sysevent set usb_${2}_status
  sysevent set usb_${2}_label
  sysevent set usb_${2}_fstype
  sysevent set usb_${2}_sizes
  sysevent set usb_no_partitions_${1}
  sysevent set usb_curr_partition_cnt_${1}
}
del_sysevent_allusb_info()
{
  storage_devices=`sysevent get usb_storage_devices`
  
  if [ -n "$storage_devices" ] ; then
    echo "$storage_devices" | grep -q "$1$"
    if [ "$?" = "0" ] ; then
      storage_devices=`echo $storage_devices | sed "s/$1//g"`
    else
      storage_devices=`echo $storage_devices | sed "s/$1 //g"`
    fi
    sysevent set usb_storage_devices "$storage_devices"
  fi
  partitions=`sysevent get usb_${1}_partitions`
  
  if [ -n "$partitons" ] ; then
    for part in $partitons 
    do
      sysevent set usb_${part}_status
      sysevent set usb_${part}_label
      sysevent set usb_${part}_fstype
      sysevent set usb_${part}_sizes
    done
  fi
  sysevent set usb_${1}_info
  sysevent set usb_${1}_partitions
  sysevent set usb_no_partitions_${1}
  sysevent set usb_curr_partition_cnt_${1}
}
umount_check_wrt1900ac()
{
    local blockdir
    local blockname
    blockdir="/sys/block/$1"
    if [ -d "$blockdir" ]; then
        blockname=`ls -la $blockdir`
        echo "$blockname" | grep -q "/devices/platform/sata_mv.0"
        if [ "$?" = "0" ]; then
            UMOUNT="esata"
            return
        fi
        echo "$blockname" | grep -q "/devices/platform/ehci_marvell.0/usb"
        if [ "$?" = "0" ]; then
            UMOUNT="usb1"
            return
        fi
        echo "$blockname" | grep -q "/devices/pci0000:00/0000:00:00.0/usb"
        if [ "$?" = "0" ]; then
            UMOUNT="usb2"
            return
        fi
    fi
}
umount_check_wrt1200ac1900acv2()
{
    local usbport=$1
    local usbtype=$2
        if [ "$usbtype" = "esata" -a "$usbport" = "1" ]; then
            UMOUNT="esata"
            return
        fi
        if [ "$usbtype" = "usb" -a "$usbport" = "1" ]; then
            UMOUNT="usb1"
            return
        fi
        if [ "$usbtype" = "usb" -a "$usbport" = "2" ]; then
            UMOUNT="usb2"
            return
        fi
}
create_drive_info_file()
{
    if [ ! -d $USBINFO ] ; then
        mkdir -p $USBINFO
    fi
    local devname=$1
    local devport=$2
    local devtype=$3
    local parttype=$4
    local parttable=$5
    
    local usbfile="$USBINFO/$devname.nfo"
    touch "$usbfile"
    echo -e "pname:$devname" > "$usbfile"
    echo -e "dname:${devname::3}" >> "$usbfile"
    echo -e "type:$devtype" >> "$usbfile"
    echo -e "port:$devport" >> "$usbfile"
    echo -e "label:`/usr/sbin/usblabel $devname`" >> "$usbfile"
    echo -e "format:$parttype" >> "$usbfile"
    echo -e "partitiontable:$parttable" >> "$usbfile"
    if [ "$parttype" != "unsupported" ] && [ "$parttable" != "unsupported" ]; then
        Hotplug_GetInfo "$devname"
        echo -e "size:`df /tmp/$devname | grep $devname | awk '{print $2}'`"  >> "$usbfile"
        echo -e "manufacturer:$DEVICE_VENDOR" >> "$usbfile"
        echo -e "product:$DEVICE_MODEL" >> "$usbfile"
        echo -e "speed:$DEVICE_SPEED" >> "$usbfile"
    else
        echo -e "size:"  >> "$usbfile"
        echo -e "manufacturer:" >> "$usbfile"
        echo -e "product:" >> "$usbfile"
        echo -e "speed:" >> "$usbfile"
    fi
}
sysevent set ${SERVICE_NAME}-status 
sysevent set ${SERVICE_NAME}-errinfo 
if [ ! -d "/tmp/mnt" ] ; then
  mkdir -p /tmp/mnt
fi
if [ -d "/mnt/cache" ] ; then
  rm -rf /mnt/cache
fi
echo "mountscript $1 $2 $3 $4" > /dev/console
devname=$2
devblock=`echo "$devname" | cut -b 1-3`
DEVICE_PORT=$3
DEVICE_TYPE=$4
case $1 in
    add)
        numPartitions=`sysevent get usb_no_partitions_"$devblock"`
        currPartition=`sysevent get usb_curr_partition_cnt_"$devblock"`
        if [ "$numPartitions" == "" ] || [ "$numPartitions" == "0" ]; then 
            numPartitions=`ls "/sys/block/$devblock/" | grep "$devblock" | wc -l`
            currPartition="0"
            sysevent set usb_no_partitions_$devblock $numPartitions
            sysevent set usb_curr_partition_cnt_$devblock "0"
        else 
            currPartition=`expr $currPartition + 1`
            sysevent set usb_curr_partition_cnt_$devblock $currPartition
        fi  
        echo "[utopia][usb hotplug] Loading partition $currPartition/$numPartitions" > /dev/console
        partedOutput=`parted -s "/dev/$devblock" print`
        while [ "$?" != "0" ]
        do
          partedOutput=`parted -s "/dev/$devblock" print`
          logger "parted -s failed"
        done
        model=`echo "$partedOutput" | grep "^Model:" | awk '{print $2}'`
        totalSize=`echo "$partedOutput" | grep "^Disk" | awk '{print $3}'`
        partition_type=`echo "$partedOutput" | grep "Partition Table: " | awk '{print $3}'`
        usb_info=`sysevent get usb_${devblock}_info`
        if [ -z "$usb_info" ] ; then
            sysevent set usb_${devblock}_info "$model $totalSize $partition_type"
        fi
        disk=`echo "$2" | tr -cd [0-9]`
        if [ ! -z "$disk" ]; then
            case "$partition_type" in
            "gpt")
                local partedInfo=`echo "$partedOutput" | grep "^ *$disk "`
                local disk_flag=`echo "$partedInfo" | awk '{print $NF}'`
                if [ "$disk_flag" = "boot" ]; then
                    if echo "$partedInfo" | grep -q "EFI System Partition"; then
                        echo "[utopia][usb hotplug] not mounted: $2 EFI system boot partition" > /dev/console
                        finish_mount_drives $currPartition $numPartitions
                        exit 0
                    fi
                fi
                ;;
            "msdos")
                local partedInfo=`echo "$partedOutput" | grep "^ *$disk "`
                local partType=`echo "$partedInfo" | awk '{print $5}'`
                if [ "$partType" == "extended" ]; then
                    echo "[utopia][usb hotplug] skipping $devname partition $disk because it's extended" > /dev/console
                    finish_mount_drives $currPartition $numPartitions
                    exit 0
                fi
                ;;
            *)
                echo "[utopia][usb hotplug] not mounted: $devname partition table ($partition_type) unsupported" > /dev/console
                finish_mount_drives $currPartition $numPartitions
                exit 0
                ;;
            esac
        else
            if [ "$partition_type" == "gpt" ] || \
               [ "$partition_type" == "msdos" ]; then
                echo "[utopia][usb hotplug] not mounted: $2 is not a partition" > /dev/console
                create_drive_info_file "$devblock" "$DEVICE_PORT" "$DEVICE_TYPE" "N/A" "$partition_type"
            else
                echo "[utopia][usb hotplug] not mounted: $devname partition table ($partition_type) unsupported" > /dev/console
                create_drive_info_file "$devblock" "$DEVICE_PORT" "$DEVICE_TYPE" "unsupported" "unsupported"
                create_drive_info_file "${devblock}1" "$DEVICE_PORT" "$DEVICE_TYPE" "unsupported" "unsupported"
            fi
            finish_mount_drives $currPartition $numPartitions
            exit 0
        fi
        disk_partid=`/sbin/blkid -o value -s TYPE /dev/$devname`
        if [ -n "$disk_partid" ] ; then
            if echo " $all_linux " | grep -q " $disk_partid "; then
                disk_parttype="linux"
            elif echo " $all_fat " | grep -q " $disk_partid "; then
                disk_parttype="fat"
            elif echo " $all_ntfs " | grep -q " $disk_partid "; then
                disk_parttype="ntfs"
            elif echo " $macOS_ufsd " | grep -q " $disk_partid "; then
                disk_parttype="macOS"
            else
                echo "[utopia][usb hotplug] not mounted: $2 ($disk_partid) unsupported" > /dev/console
                create_drive_info_file "$devname" "$DEVICE_PORT" "$DEVICE_TYPE" "unsupported" "$partition_type"
                finish_mount_drives $currPartition $numPartitions
                exit 0
            fi
        else
                echo "[utopia][usb hotplug] not mounted: $2 unknown filesystem" > /dev/console
                create_drive_info_file "$devname" "$DEVICE_PORT" "$DEVICE_TYPE" "unsupported" "$partition_type"
                finish_mount_drives $currPartition $numPartitions
                exit 0
        fi
        echo "[utopia][usb hotplug] mount $2 ($disk_partid)" > /dev/console
        ulog usb_mountscript hotplug "mount $2 ($disk_partid)"
        mkdir -p /tmp/$2
        chmod 0775 /tmp/$2
  
        ret_mount="mounted"
        case "$disk_parttype" in
            ntfs)
       if [ "$DEVICE_TYPE" = "esata" ]; then
           if [ "$PROD_NAME" = "caiman" -o "$PROD_NAME" = "cobra" -o "$PROD_NAME" = "shelby" ]; then
                    lsmod | grep "usb_storage" ;
                    if [ "0" = "$?" ] ; then
                        ulog usb autodetect "$PID storage drivers already installed on usb port $DEVICE_PORT"
                    else
                        ulog usb autodetect "$PID installing storage drivers on usb port $DEVICE_PORT"
                        add_storage_drivers 
                    fi
           fi
       fi
                moptions="$moptions,nls=utf8"
    get_syscfg_ssd_trim_enabled
    if [ -f "${MODULE_PATH}/tntfs.ko" ]; then
                moptions="umask=0002"
		if [ "$SYSCFG_ssd_trim_enabled" != 1 ]; then
		    moptions="$moptions,discard=0"
		fi
                mount -t tntfs -o $moptions /dev/$2 /tmp/$2
    else
                mount -t ufsd -o $moptions /dev/$2 /tmp/$2 ||
                    ( /sbin/chkntfs -f /dev/$2; mount -t ufsd -o $moptions /dev/$2 /tmp/$2 )
    fi
                mkdir -p /tmp/mnt/$2; mount -o bind /tmp/$2 /tmp/mnt/$2 
                ;;
            macOS)
       if [ "$DEVICE_TYPE" = "esata" ]; then
           if [ "$PROD_NAME" = "caiman" -o "$PROD_NAME" = "cobra" -o "$PROD_NAME" = "shelby" ]; then
                    lsmod | grep "usb_storage" ;
                    if [ "0" = "$?" ] ; then
                        ulog usb autodetect "$PID storage drivers already installed on usb port $DEVICE_PORT"
                    else
                        ulog usb autodetect "$PID installing storage drivers on usb port $DEVICE_PORT"
                        add_storage_drivers 
                    fi
           fi
       fi
                moptions="rw,nls=utf8"
    if [ -f "${MODULE_PATH}/thfsplus.ko" ]; then
                mount -t thfsplus -o nomode,umask=2 /dev/$2 /tmp/$2
    else
                mount -t ufsd -o $moptions /dev/$2 /tmp/$2 ||
                    (/sbin/chkhfs -f /dev/$2; mount -t ufsd -o $moptions /dev/$2 /tmp/$2)
    fi
                mkdir -p /tmp/mnt/$2; mount -o bind /tmp/$2 /tmp/mnt/$2 
                chown -R root:0 /tmp/mnt/$2
                ;;
            fat)
    if [ -f "${MODULE_PATH}/tfat.ko" ]; then
                moptions="umask=0002,allow_utime=20"
                mount -t tfat -o $moptions /dev/$2 /tmp/$2 && ( mkdir -p /tmp/mnt/$2; mount -o bind /tmp/$2 /tmp/mnt/$2 )
    else
                moptions="$moptions,utf8"
                mount -t vfat -o $moptions /dev/$2 /tmp/$2 && ( mkdir -p /tmp/mnt/$2; mount -o bind /tmp/$2 /tmp/mnt/$2 )
    fi
                ;;
            linux)
                mount -t $disk_partid /dev/$2 /tmp/$2 && ( mkdir -p /tmp/mnt/$2; mount -o bind /tmp/$2 /tmp/mnt/$2 )
                ;;
        esac
        create_drive_info_file "$devname" "$DEVICE_PORT" "$DEVICE_TYPE" "$disk_partid" "$partition_type"
  
        add_sysevent_usb_info $devblock $2 $ret_mount $disk_partid 
        finish_mount_drives $currPartition $numPartitions
        ;;
    remove)
        if [ ! -z "$2" ] && [ -z "$3" ] ; then
            Hotplug_GetId $2
        else
            DEVICE_PORT="$3"
        fi
        echo "remove: disk = $2, port = $DEVICE_PORT, devblock = $devblock" > /dev/console
        if [ "$PROD_NAME" = "blk-mamba" ]; then
            umount_check_wrt1900ac "$2"
        fi
        if [ "$PROD_NAME" = "cobra" -o "$PROD_NAME" = "caiman" -o "$PROD_NAME" = "shelby" ]; then
            umount_check_wrt1200ac1900acv2 "$3" "$4"
        fi
        if [ "$devblock" = "$2" ] ; then
            del_sysevent_allusb_info $devblock $2
        else
            del_sysevent_usb_info $devblock $2
        fi
        /usr/sbin/usbrmdrive $2
        if [ "$PROD_NAME" = "blk-mamba" -o "$PROD_NAME" = "cobra" -o "$PROD_NAME" = "caiman" -o "$PROD_NAME" = "shelby" ]; then
            case $UMOUNT in
            esata)
                sysevent set esata_1_umount
                ;;
            usb1)
                sysevent set usb_port_1_umount
                ;;
            usb2)
                sysevent set usb_port_2_umount
                ;;
            esac
        fi
        ;;
            
    stale)
        for f in `ls --color=none /tmp`; do
            if [ -z `mount | grep -o $f` ]; then
                echo "removing stale $f" >> /dev/console
                rm -rf /tmp/$f
                echo "removing usb info file $USBINFO/$2.nfo"
                rm -rf "$USBINFO/$2.nfo"
            fi
        done
        ;;
esac
