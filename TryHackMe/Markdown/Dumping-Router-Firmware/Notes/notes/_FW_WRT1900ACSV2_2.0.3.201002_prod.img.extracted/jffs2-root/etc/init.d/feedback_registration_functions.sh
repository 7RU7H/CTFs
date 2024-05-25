map_sysevent_to_handler() {
   SMR_EVENT_STRING=$1
   SMR_TUPLE_FLAGS=0x00000002
   SMR_EVENT_NAME=`echo $SMR_EVENT_STRING | cut -f 1 -d '|' | sed 's/ //'` 
   SMR_EVENT_HANDLER=`echo "$SMR_EVENT_STRING" | cut -f 2 -d '|' | sed 's/ //'`
   SMR_PARAMETERS=`echo $SMR_EVENT_STRING | cut -f 3 -d '|' `
	      
   if [ -z "$SMR_EVENT_NAME" ] || [ -z "$SMR_EVENT_HANDLER" ] ; then
      return 1
   fi
   async_id=`sysevent async $SMR_EVENT_NAME $SMR_EVENT_HANDLER $SMR_PARAMETERS`
   sysevent setoptions $SMR_EVENT_NAME $SMR_TUPLE_FLAGS
} 	 
register_events_handler() {
   LISTOF_EVENTS=$1
   if [ -n "$LISTOF_EVENTS" ] && [ "NULL" != "$LISTOF_EVENTS" ] ; then 
	SAVEIFS=$IFS
	IFS=';'
	for custom in $LISTOF_EVENTS ; do
		if [ -n "$custom" ] && [ " " != "$custom" ] ; then
		IFS=$SAVEIFS
		map_sysevent_to_handler $custom
		IFS=';'
		fi
	done
	IFS=$SAVEIFS
   fi
}
