getValueForKey() {
   local filename=$1
   grep "^$2:" $filename | cut -d':' -f2
}
setValueForKey() {
   local filename=$1
   local tmp_file="${filename}.tmp"
   local key=$2
   local val=$3
   if ! grep -q "^$key:" $filename;
   then
      echo "$key:$val" >> $filename
   else
      sed "s/$key[:].*/$key:$val/" $filename > $tmp_file
      mv -f $tmp_file $filename
   fi
}
get_cgi_val () {
  if [ "$1" == "" ] ; then
    echo ""
    return
  fi
  local form_var="$1"
  local var_value=`echo "$QUERY_STRING" | sed -n "s/^.*$form_var=\([^&]*\).*$/\1/p" | sed "s/%20/ /g" | sed "s/+/ /g" | sed "s/%2F/\//g" | sed "s/,/ /g"`
  echo -n "$var_value"
}
