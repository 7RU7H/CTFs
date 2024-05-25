if [ "`syscfg get log_counters_enabled`" == "1" ]; then
	echo "================================================"
	echo "Log Counters:"
	echo ""
	echo "cat /tmp/counters/core_log_counters.txt"
	cat /tmp/counters/core_log_counters.txt
	echo ""
	echo "cat /tmp/counters/wifi_log_counters.txt"
	cat /tmp/counters/wifi_log_counters.txt
	echo ""
fi
