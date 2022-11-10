#!/bin/bash

for port in 22 25 80 443 8080 8443; do 
	(echo Hello > /dev/tcp/$1/$port && echo "open - $port") 2> /dev/null
done
