#!/bin/bash

# Check if server is running
CHECKSSH=$(sudo systemctl status ssh)
echo $CHECKSSH


# Check if IP fowarding is enabled 
cat /proc/sys/net/ipv4/ip_forward
# Enable IP forwarding
sudo echo 1 > /proc/sys/net/ipv4/ip_forward

# Iptables rules 
# INTERFACE is whatever network device that is providing internet to your nic from `ip a` that will then forward to C2 server
# INF-IP is INTERFACE ip not include CIDR range
# RANGE is the range choosen or the CIDR range from INTERFACE
sudo iptables -A FORWARD -i tun0 -o $INTERFACE -m state --state RELATED,ESTABLISHED -j ACCEPT
sudo iptables -A FORWARD -i $INTERFACE -o tun0 -j ACCEPT
# Create masquerade/NAT rule to deal with connection not rewriting the ip adderss as Linux box is gateway, but not handling addresses
sudo iptables -t NAT -A POSTROUTING -s INF-IP/RANGE -o tun0 -j MASQUERADE
