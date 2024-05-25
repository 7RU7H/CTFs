#!/bin/sh 
LOG_FILE="/tmp/share_parser_log.txt"
echo "" > $LOG_FILE
SMB_CFG_FILE=".smb_share.nfo"
FTP_CFG_FILE=".ftp_share.nfo"
MED_CFG_FILE=".med_share.nfo"
TOTAL_SMB_COUNT=0
TOTAL_FTP_COUNT=0
TOTAL_MED_COUNT=0
if [ -d "/mnt" ] ; then
USB_DRIVES=`ls /mnt/ | grep sd`
USB_DRIVE_COUNT=`ls /mnt/ | grep sd | wc -l`
fi
SHARE_NFO_FILE="$1"
SHARE_DEV="$2"
SMB_INFILES="" 
FTP_INFILES="" 
MED_INFILES="" 
smb_cur_count=0
ftp_cur_count=0
med_cur_count=0
fidx=0
sidx=0
midx=0
get_nfo_files()
{
  if [ -d "/mnt" ] ; then
  SMB_INFILES=`find /mnt/sd*/ -maxdepth 1 -name ".smb_share.nfo"`
  FTP_INFILES=`find /mnt/sd*/ -maxdepth 1 -name ".ftp_share.nfo"`
  MED_INFILES=`find /mnt/sd*/ -maxdepth 1 -name ".med_share.nfo"`
  fi
}
disk_shares_to_syscfg()
{
  INDEXS=`cat $1 | cut -d'|' -f1 | sed "s/{index://g" | tr -d '"'`
  NAMES=`cat $1 | cut -d'|' -f2 | sed "s/name:/+/g" | tr -d '"'`
  DRIVES=`cat $1 | cut -d'|' -f3 | sed "s/drive://g" | tr -d '"'`
  FOLDERS=`cat $1 | cut -d'|' -f5 | sed "s/folder:/+/g" | tr -d '"'`
  READONLY=`cat $1 | cut -d'|' -f6 | sed "s/readonly://g"`
  GROUPS=`cat $1 | cut -d'|' -f7 | sed "s/groups://g" | tr -d '"' | tr -d '}'`
  
  DRIVE_SHARE_COUNT=`cat $1 | grep "name:" | wc -l`
  echo `seq 1 $DRIVE_SHARE_COUNT` >> $LOG_FILE
  echo "NAMES = $NAMES" >> $LOG_FILE
  echo "FOLDERS = $FOLDERS" >> $LOG_FILE
  for num in `seq 1 $DRIVE_SHARE_COUNT`
  do
    echo "$idx===========================================" >> $LOG_FILE
    offset=`expr $num + 1`
    index=`echo $INDEXS | cut -d' ' -f${num}`
    name=`echo "$NAMES" | awk '{printf $0}' | cut -d'+' -f$offset | sed -e 's/ $//g'`
    
    drive=`echo -n "$1" | cut -d'/' -f3`
    folder=`echo "$FOLDERS" | awk '{printf $0}' | cut -d'+' -f$offset | sed -e 's/ $//g'`
    readonly=`echo $READONLY | cut -d' ' -f${num}`
    groups=`echo $GROUPS | cut -d' ' -f${num}`
    
    if [ "$2" == "smb" ] 
    then
      echo "num=$num, sidx=$sidx, scc=$smb_cur_count" >> $LOG_FILE
      sidx=`expr $num + $TOTAL_SMB_COUNT`
      echo "configuring smb shares $sidx" >> $LOG_FILE     
      echo "smb current count $smb_cur_count" >> $LOG_FILE
      idx=$sidx
    fi
    if [ "$2" == "ftp" ] ; then
      echo "num=$num, fidx=$fidx, fcc=$ftp_cur_count" >> $LOG_FILE
      fidx=`expr $num + $TOTAL_FTP_COUNT`
      echo "configuring ftp shares $fidx" >> $LOG_FILE
      echo "ftp current count $ftp_cur_count" >> $LOG_FILE
      idx=$fidx
    fi
    if [ "$2" == "med" ] ; then
     echo "num=$num, midx=$midx, mcc=$med_cur_count" >> $LOG_FILE
      midx=`expr $num + $TOTAL_MED_COUNT`
      echo "configuring med shares $midx" >> $LOG_FILE
      echo "med current count $med_cur_count" >> $LOG_FILE
      idx=$midx
    fi
   echo "" >> $LOG_FILE
   syscfg set $2_${idx} name "$name"
   syscfg set $2_${idx} drive $drive
   syscfg set $2_${idx} folder "$folder"
   syscfg set $2_${idx} readonly $readonly
   syscfg set $2_${idx} groups "$groups"
  done
  if [ "$num" == "" ] ; then
    num="0"
  fi
  DRIVE_SHARE_COUNT=`expr $num`
  
  echo "disk_shares_to_syscfg $1 $2 - end" >> $LOG_FILE
  echo "$DRIVE_SHARE_COUNT"
}
get_nfo_files
cnt=0
tcnt=0
for inf in $SMB_INFILES
do
  echo "parsing $inf" >> $LOG_FILE
  cat $inf >> $LOG_FILE
  smb_cur_count=$(disk_shares_to_syscfg $inf "smb") 
  echo "disk_shares_to_syscfg returned $smb_cur_count" >> $LOG_FILE
  echo "shares in  $inf $smb_cur_count" >> $LOG_FILE
  TOTAL_SMB_COUNT=`expr $smb_cur_count + $TOTAL_SMB_COUNT`
  sidx=$TOTAL_SMB_COUNT
done
t_count=`syscfg get SharedFolderCount`
if [ "$t_count" ] && [ "$t_count" != "$TOTAL_SMB_COUNT" ] ; then
  syscfg set SharedFolderCount $sidx
fi
for inf in $FTP_INFILES
do
  echo "parsing $inf" >> $LOG_FILE
  cat $inf >> $LOG_FILE
  ftp_cur_count=$(disk_shares_to_syscfg $inf "ftp") 
  echo "disk_shares_to_syscfg returned $ftp_cur_count" >> $LOG_FILE
  echo "shares in  $inf $ftp_cur_count" >> $LOG_FILE
  TOTAL_FTP_COUNT=`expr $ftp_cur_count + $TOTAL_FTP_COUNT`
  fidx=$TOTAL_FTP_COUNT
done
t_count=`syscfg get FTPFolderCount`
if [ "$t_count" ] && [ "$t_count" != "$TOTAL_FTP_COUNT" ] ; then
  syscfg set FTPFolderCount $fidx
fi
for inf in $MED_INFILES
do
  echo "parsing $inf" >> $LOG_FILE
  cat $inf >> $LOG_FILE
  med_cur_count=$(disk_shares_to_syscfg $inf "med") 
  echo "disk_shares_to_syscfg returned $med_cur_count" >> $LOG_FILE
  echo "shares in  $inf $med_cur_count" >> $LOG_FILE
  TOTAL_MED_COUNT=`expr $med_cur_count + $TOTAL_MED_COUNT`
  midx=$TOTAL_MED_COUNT
done
t_count=`syscfg get MedFolderCount`
if [ "$t_count" ] && [ "$t_count" != "$TOTAL_MED_COUNT" ] ; then
  syscfg set MedFolderCount $midx
fi
