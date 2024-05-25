#!/bin/sh

DISKS=`ls /sys/block/ | grep sd*`

if [ "$1" = "" ]; then 
  echo Content-Type: text/plain
  echo ""
fi

echo "------------------------ USB Storage Information ------------------------"

if [ "$DISKS" = "ls: /sys/block/sd*: No such file or directory" ] || [ "$DISKS" = "" ] ; then
	echo "No storage disks plugged in"
	return
fi

for disk in `echo $DISKS`
do
partitions=`ls /sys/block/$disk/ | grep "$disk*"`
echo "Storage Disk: $disk"

echo "`parted -s /dev/$disk print`"
echo ""

#Partition Info
for partition in `echo $partitions`
do
echo "Partition: $partition"
LABEL=`usblabel $partition`
echo "Label: $LABEL"

MOUNTPOINTS=`mount | grep "$partition"`
SAVE_IFS=$IFS
IFS=$'\n'
for mounts in `echo "$MOUNTPOINTS"`
do
echo "Mount Point: $mounts"
DF=`echo "$mounts" | awk '{print $3}'`
DF=`echo -e $DF`
echo "`df $DF`"
echo ""
done
IFS=$SAVE_IFS
echo ""
done

done
