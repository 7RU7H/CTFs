### Enable RSS for Port 0
# Set smp_affinity to all CPUs used for this port
if [ -f /proc/irq/193/smp_affinity ]; then
echo 3 > /proc/irq/193/smp_affinity
fi
cd /sys/devices/platform/neta

### Configure port
# Attach single CPU core to each group
echo 0 0 1 > gbe/rss/cpu_group 
echo 0 1 2 > gbe/rss/cpu_group 

# Attach 4 RXQs to each group
echo 0 0 f > gbe/rss/rxq_group 
echo 0 1 f0 > gbe/rss/rxq_group 

# To check configuration
#echo 0 > gbe/napi

# Steer ingress traffic to 8 RXQs equally
ethtool -X eth0 equal 8
# to check confguartion 
#ethtool -x eth0

# Enable Load Balancing by PnC
ethtool -K eth0 rxhash on

# Separate egress traffic to different TXQ per CPU core
echo 0 0 0 0 > gbe/tx/txq_def
echo 0 0 1 1 > gbe/tx/txq_def



### Enable RSS for Port 1
# Set smp_affinity to all CPUs used for this port
if [ -f /proc/irq/194/smp_affinity ]; then
echo 3 > /proc/irq/194/smp_affinity
fi

cd /sys/devices/platform/neta

### Configure port
# Attach single CPU core to each group
echo 1 0 1 > gbe/rss/cpu_group 
echo 1 1 2 > gbe/rss/cpu_group 

# Attach 4 RXQs to each group
echo 1 0 f > gbe/rss/rxq_group 
echo 1 1 f0 > gbe/rss/rxq_group 

# To check configuration
#echo 1 > gbe/napi

# Steer ingress traffic to 8 RXQs equally
ethtool -X eth1 equal 8
# to check confguartion 
#ethtool -x eth1

# Enable Load Balancing by PnC
ethtool -K eth1 rxhash on

# Separate egress traffic to different TXQ per CPU core
echo 1 0 0 0 > gbe/tx/txq_def
echo 1 0 1 1 > gbe/tx/txq_def

