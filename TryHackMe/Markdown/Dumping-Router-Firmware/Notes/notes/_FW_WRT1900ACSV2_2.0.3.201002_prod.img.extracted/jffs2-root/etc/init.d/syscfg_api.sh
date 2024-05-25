syscfg_set()
{
	name=$1
	new_value=$2
	current_value=`syscfg get "$name"`
	if [ "$current_value" != "$new_value" ]; then
		syscfg set "$name" "$new_value"
		sysevent set SYSCFG_DIRTY "1"
	fi
}
syscfg_unset()
{
	name=$1
	commit=$2
	current_value=`syscfg get "$name"`
	if [ ! -z "$current_value" ]; then
		syscfg unset "$name"
		sysevent set SYSCFG_DIRTY "1"
	fi
}
syscfg_get()
{
	name=$1
	current_value=`syscfg get "$name"`
	echo "$current_value"
}
syscfg_commit()
{
	if [ "1" = "`sysevent get SYSCFG_DIRTY`" ]; then
		syscfg commit
		sysevent set SYSCFG_DIRTY "0"
	fi
}
