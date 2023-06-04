#!/bin/bash

echo "10.200.89.11 mail.thereserve.loc MAIL.thereserve.loc  " | sudo tee -a /etc/hosts
echo "10.200.89.12 thereserve.loc " | sudo tee -a /etc/hosts
echo "10.200.89.13 web.thereserve.loc WEB.thereserve.loc " | sudo tee -a /etc/hosts
# vpn key required
echo "10.200.89.21 WRK1.corp.thereserve.loc" | sudo tee -a /etc/hosts
echo "10.200.89.22 WRK2.corp.thereserve.loc " | sudo tee -a /etc/hosts
