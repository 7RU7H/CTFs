# Nickel Writeup
Name: Nickel
Date:  
Difficulty:  Intermediate
Goals:  OSCP Prep 
Learnt:
- Verifying OS detection ASAP and 

## Recon

The time to live(TTL) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](OS-ProvingGrounds/Nickel/Screenshots/ping.png)

smb up on legacy port
nmap SSH windows build 8.1
nmap rdp  windows build 10.0.18362
Manaul RDP wants Kerberos, without Kerberos port up. 
RDP is also

![](amapoutput8089.png)

[Windows 8.1](https://en.wikipedia.org/wiki/Windows_8.1)

![](smbvuln.png)

srv2.sys but there is no 445

![980](nickelbeingmaybeacluethatwegethelp.png)

![](nohydra.png)

Want Kerberos even though there is no Kerberos running
![](kerberos-not-therebutrdpwanttgt.png)

## Exploit

## Foothold

## PrivEsc

      
