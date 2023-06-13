# Helpdesk Writeup
Name: Helpdesk
Date:  22/09/2022
Difficulty:  Easy
Goals:  OSCP Prep 2/5 machines a day testing (can use walkthrough)
Learnt: Write all the big Shadowbrokers exploits in Golang because python is python 

## Recon

The time to live(TTL) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](OS-ProvingGrounds/Helpdesk/Screenshots/ping.png)

![1600](nmap-lfi-and-auth.png)

![1200](smb-cve2009-3103.png)
Nuclei [[smb-v1-detection-192.168.120.43_445]]

## Exploit && Foothold && PrivEsc
It is same as [[Internal-Writeup]]


      
