#!/bin/sh
get_current_time() {
   CURRENT_TIME=`date -u '+%m:%d:%y_%H:%M:%S'`
   echo "$CURRENT_TIME"
}
get_month() {
   TIMESTAMP=$1
   MONTH=0
   SAVEIFS=$IFS
   IFS=:
   for p in $TIMESTAMP
   do
      if [ "0" = "$MONTH" ] ; then
         MONTH=$p
      fi
   done
   IFS=$SAVEIFS
   echo "$MONTH"
}
get_day() {
   TIMESTAMP=$1
   DAY=0
   MONTH=0
   SAVEIFS=$IFS
   IFS=:
   for p in $TIMESTAMP
   do
      if [ "0" = "$MONTH" ] ; then
         MONTH=$p
      elif [ "0" = "$DAY" ] ; then
         DAY=$p
      fi
   done
   IFS=$SAVEIFS
   echo "$DAY"
}
get_year() {
   TIMESTAMP=$1
   DAY=0
   MONTH=0
   YEAR=0
   YEAR2=0
   SAVEIFS=$IFS
   IFS=:
   for p in $TIMESTAMP
   do
      if [ "0" = "$MONTH" ] ; then
         MONTH=$p
      elif [ "0" = "$DAY" ] ; then
         DAY=$p
      elif [ "0" = "$YEAR" ] ; then
         YEAR=$p
      fi
   done
   IFS=_
   for p in $YEAR
   do
      if [ "0" = "$YEAR2" ] ; then
         YEAR2=$p
      fi
   done
   IFS=$SAVEIFS
   echo "$YEAR2"
}
get_hour() {
   TIMESTAMP=$1
   STRING1=""
   STRING2=""
   SAVEIFS=$IFS
   IFS=_
   for p in $TIMESTAMP
   do
      if [ "" = "$STRING1" ] ; then
         STRING1=$p
      elif [ "" = "$STRING2" ] ; then
         STRING2=$p
      fi
   done
   HOUR=0
   MIN=0
   SEC=0
   IFS=:
   for p in $STRING2
   do
      if [ "0" = "$HOUR" ] ; then
         HOUR=$p
      elif [ "0" = "$MIN" ] ; then
         MIN=$p
      elif [ "0" = "$SEC" ] ; then
         SEC=$p
      fi
   done
   IFS=$SAVEIFS
   echo "$HOUR"
}
get_min() {
   TIMESTAMP=$1
   STRING1=""
   STRING2=""
   SAVEIFS=$IFS
   IFS=_
   for p in $TIMESTAMP
   do
      if [ "" = "$STRING1" ] ; then
         STRING1=$p
      elif [ "" = "$STRING2" ] ; then
         STRING2=$p
      fi
   done
   HOUR=0
   MIN=0
   SEC=0
   IFS=:
   for p in $STRING2
   do
      if [ "0" = "$HOUR" ] ; then
         HOUR=$p
      elif [ "0" = "$MIN" ] ; then
         MIN=$p
      elif [ "0" = "$SEC" ] ; then
         SEC=$p
      fi
   done
   IFS=$SAVEIFS
   echo "$MIN"
}
get_sec() {
   TIMESTAMP=$1
   STRING1=""
   STRING2=""
   SAVEIFS=$IFS
   IFS=_
   for p in $TIMESTAMP
   do
      if [ "" = "$STRING1" ] ; then
         STRING1=$p
      elif [ "" = "$STRING2" ] ; then
         STRING2=$p
      fi
   done
   HOUR=0
   MIN=0
   SEC=0
   IFS=:
   for p in $STRING2
   do
      if [ "0" = "$HOUR" ] ; then
         HOUR=$p
      elif [ "0" = "$MIN" ] ; then
         MIN=$p
      elif [ "0" = "$SEC" ] ; then
         SEC=$p
      fi
   done
   IFS=$SAVEIFS
   echo "$SEC"
}
get_seconds()
{
   DATE=$1
   ulog ddns status "getting seconds from $DATE"
   time=`echo $DATE | cut -f 2 -d '_'`
   ulog ddns status "time from $time"
   hr=`echo $time | cut -f 1 -d ':'`
   ulog ddns status "hour from $hr"
   min=`echo $time | cut -f 2 -d ':'`
   ulog ddns status "min from $min"
   sec=`echo $time | cut -f 3 -d ':'`
   ulog ddns status "sec from $sec"
   hr=`expr $hr \* 60`
   min=`expr $min + $hr`
   min=`expr $min \* 60`
   sec=`expr $sec + $min`
   
   ulog ddns status "calculated $sec"
   return $sec
   
}
days_in_month() {
   MONTH=$1
   if [ "2" = "$MONTH" ] ; then
      echo "28"
   elif [ "02" = "$MONTH" ] ; then
      echo "28"
   elif [ "4" = "$MONTH" ] ; then
      echo "30"
   elif [ "4" = "$MONTH" ] ; then
      echo "30"
   elif [ "04" = "$MONTH" ] ; then
      echo "30"
   elif [ "6" = "$MONTH" ] ; then
      echo "30"
   elif [ "06" = "$MONTH" ] ; then
      echo "30"
   elif [ "9" = "$MONTH" ] ; then
      echo "30"
   elif [ "09" = "$MONTH" ] ; then
      echo "30"
   elif [ "11" = "$MONTH" ] ; then
      echo "30"
   else
      echo "31"
   fi
}
days_in_months() {
   MONTH1=$1
   MONTH2=$2
   ACCUMULATOR=0
   if [ "$MONTH2" -ge "$MONTH1" ] ; then
      COUNTER=`expr $MONTH2 - $MONTH1`
      COUNTER=`expr $COUNTER + 1`
   else
      COUNTER=`expr $MONTH1 - $MONTH2`
      COUNTER=`expr 13 - $COUNTER`
   fi
   while [ $COUNTER -gt 0 ]
   do
      MONTH=`days_in_month $MONTH1`
      ACCUMULATOR=`expr $ACCUMULATOR + $MONTH`
      COUNTER=`expr $COUNTER - 1`
      MONTH1=`expr $MONTH1 + 1`
      if [ "12" -lt "$MONTH1" ] ; then
         MONTH1=1
      fi
   done
   echo "$ACCUMULATOR"
}
days_from_basedate() {
  YEAR1=00
  MONTH1=01
  DAY1=01
  YEAR2=`get_year $1`
  MONTH2=`get_month $1`
  DAY2=`get_day $1`
  ACCUMULATOR=0
  MULTIPLIER=`expr $YEAR2 - $YEAR1`
  PRODUCT=`expr $MULTIPLIER \* 365`
  ACCUMULATOR=`expr $ACCUMULATOR + $PRODUCT`
  PRODUCT=`days_in_months $MONTH1 $MONTH2`
  ACCUMULATOR=`expr $ACCUMULATOR + $PRODUCT`
  PRODUCT=`expr $DAY2 - $DAY1`
  ACCUMULATOR=`expr $ACCUMULATOR + $PRODUCT`
  echo "$ACCUMULATOR"
}
delta_days() {
   DAYS1=`days_from_basedate $1`
   DAYS2=`days_from_basedate $2`
   CUMMULATIVE_DAYS=`expr $DAYS2 - $DAYS1`
   echo "$CUMMULATIVE_DAYS"
}
mins_from_basedate() {
  HOUR1=00
  HOUR2=`get_hour $1`
  MIN2=`get_min $1`
  ACCUMULATOR=0
  MULTIPLIER=`expr $HOUR2 - $HOUR1`
  PRODUCT=`expr $MULTIPLIER \* 60`
  ACCUMULATOR=`expr $ACCUMULATOR + $PRODUCT`
  ACCUMULATOR=`expr $ACCUMULATOR + $MIN2`
  echo "$ACCUMULATOR"
}
delta_mins() {
   MINS1=`mins_from_basedate $1`
   MINS2=`mins_from_basedate $2`
   CUMMULATIVE_MINS=`expr $MINS2 - $MINS1`
   echo "$CUMMULATIVE_MINS"
}
