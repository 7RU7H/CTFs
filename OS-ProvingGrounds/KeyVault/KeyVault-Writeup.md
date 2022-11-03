# KeyVault Writeup
Name: KeyVault
Date:  
Difficulty:  Intermediate
Goals:  OSCP Prep
Learnt: 

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](OS-ProvingGrounds/KeyVault/Screenshots/ping.png)

![](nmap-sc-sv.png)

PHP, Apache

Nuclei finds [[credentials-disclosure-http___192.168.141.207]], [[git-config-http___192.168.141.207_8080_.git_config]]

[BOOMR API](http://bluesmoon.github.io/boomerang/doc/api/BOOMR.html)
API_key="WH2TM-VVP9E-KZ9SR-39YA8-GGXQ9"

http://192.168.141.207:8080/.git/config

email = ray@keyvault
name = Ray

## Exploit

## Foothold

## PrivEsc

      
