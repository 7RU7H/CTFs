#!/usr/bin/python3

import os
import socket

hostname = socket.gethostname()
IPaddr = socket.gethostbyname(hostname).replace(".12","")

correctOne = False
correctTwo = False
print (IPaddr)

f = open('/etc/openvpn/server.conf', 'r')
g = open('/tmp/server.conf', 'w')
lines = f.readlines()
counter = 21
IP = ""
for line in lines:
    if ('push "route 1' in line):
        IP = line.split(' ')[2].replace(".0","")
        if (IP == IPaddr + "." + str(counter)):
            print ("IP match, no need for reboot")
            correctOne = True
            counter += 1
            g.write(line)
            continue
        else:
            g.write('push "route ' + IPaddr + '.' + str(counter) + ' 255.255.255.255"\n')
            counter += 1
            continue
    g.write(line)
print (IP)
f.close()
g.close()


f = open('/etc/openvpn/client-common.txt', 'r')
g = open('/tmp/client-common.txt', 'w')
lines = f.readlines()
IP = ""
for line in lines:
    if ('remote ' in line):
        IP = line.split(' ')[1]
        if (IP == IPaddr + ".12"):
            print ("IP match, no need for reboot")
            correctTwo = True
            g.write(line)
            continue
        else:
            g.write('remote ' + IPaddr + '.12 1194\n')
            continue
    g.write(line)
print (IP)
f.close()
g.close()


if (correctOne and correctTwo):
    print ("No restart")
else:
    print ("Copy and restart")
    os.system('cp /tmp/server.conf /etc/openvpn/server.conf')
    os.system('cp /tmp/client-common.txt /etc/openvpn/client-common.txt')
    os.system('service openvpn restart')

#Random reboots just to make sure
import random

if random.randint(0,9) == 5:
    os.system('service openvpn restart')


