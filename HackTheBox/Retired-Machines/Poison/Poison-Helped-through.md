# Poison Walkthrough
Name: Poison
Date:  14/10/2022
Difficulty:  Medium
Goals: 
- OSCP Prep Handholding from Ippsec to save my terrible situation. 
- LFI there is a PG box with a LFI vulnerability that I failed at and dont want to get hints for or walkthrough
- Practice log poisoning 
- Port Redirection
Learnt:
- Log poisoning with PHP with more testing methodology and its limitations in regards to damaging log file. Trying harder and Trying again smarter
- The need to get into a headspace of "questioning" output as to how to be more objectively minded.  
 
This is purely me having a terrible day yesterday and I wont allow myself to let yesterday spill into today. Ippsec is an amazing person and resource and I am subscribed and like all the videos I watch. I am rewatching this as I watched all these over a year ago from this playlist to learn how far I needed to go am still going to improve. And today I need a sit down with someone just to get back up and continue. 

This has Ippsec unique way apperently so I follow along and also do the alternative easier way if necessary. For Reference: [Ippsec Video](https://www.youtube.com/watch?v=rs4zEwONzzk)

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![](HackTheBox/Retired-Machines/Poison/Screenshots/ping.png)

The nmap enumerates thathost is running a less common flavour of Linux OS I have come across in CTFs - [Freebsd](https://en.wikipedia.org/wiki/FreeBSD). 

![](noskips.png)

## Exploit

Tested in browser for lfi
![](testingbrowsedotphp.png)
And then in burp
![](testinburppng.png)
![](index-php-burp-lfi.png)

![950](filtertoexilphp.png)

Checking for RFI, found none
![](norfi.png)


![](lfipasswd.png)
`charix` 

Tried to lfi the ssh keys
![](charix-no-reading-perms-for-sshkeys.png)

[TL;DR this white paper makes a request a large enough to cuaese a rece condition such that the subsequent script enough time get code execution on the target before the cache flushes the file](https://insomniasec.com/downloads/publications/LFI%20With%20PHPInfo%20Assistance.pdf). [The script for this is from Payload all the things](https://raw.githubusercontent.com/swisskyrepo/PayloadsAllTheThings/master/File%20Inclusion/phpinfolfi.py).

![](templateforphpinfo.png)

![](badfileonthephpinfopage.png)

1. Edited script change the request in the http request to the correct url. 
1. Added php-reverse-shell code
1. Researched with python encoding translation - and thought about actually solution to running into the same problem with python

```bash
File "/tmp/lfiexploit.py", line 341, in <module>
main()
File "/tmp/lfiexploit.py", line 301, in main
offset = getOffset(host, port, reqphp)
File "/tmp/lfiexploit.py", line 246, in getOffset
s.send(phpinforeq)
TypeError: a bytes-like object is required, not 'str'
```

![](uname-poison-cmd-web.png)

![](listfilesformorefiles.png)

Allocated time to return to this phpinfo poisoning method. 

Regular log file poisoning, but I made a mistake with using `$_GET`

![](freebsdloglocal.png)

Tried by myself with:

![](submitinglocallog.png)
![](anypagedoesnotmatter.png)
![](itdoesendupinthelogfile.png)
![](superglobalgetcmd.png)
![](systemexecuted.png)

`/var/log/httpd-access.log`

## Foothold

I broke the log file using `$_GET` instead of `$_REQUEST` so I will get root and clear this file to try again. Decoded ssh password instead:
```bash
for i in $(seq 0 12); do echo -n ' | base64 -d'; done
cat pwdbackup.txt| base64 -d | base64 -d | base64 -d | base64 -d | base64 -d | base64 -d | base64 -d | base64 -d | base64 -d | base64 -d | base64 -d | base64 -d | base64 -d

Charix!2#4%6&8(0 
# From the LFI of passwd this is charix user is ssh password 
```

## PrivEsc

`ssh` -ed in, used php webserver to exfiltrate secret.zip, tried zip2john and rockyou.txt with `hashcat -m 17200` without success. 
![](phpexfil.png)
![](passwordlockedzipfile.png)
![](zip2johnoutput.png)
Linpeas.sh found tightvnc and path `/home/charix/bin`. Failed to use `/home/charix/bin`  as I could find a way to get root to use that. I will check after practicing the log poisoning.

Then went back to the video after failing to find anything to make root access in run with `$PATH:/home/charix/bin`. Used the ssh password to extract `secret`. Then following along with the video looking up what tightvnc is, TL;DR it is server client remoting application - running as root, should be as its own user with principle of least privilegetaken into account.

![](lp1.png)
![](rootprocess.png)
![](whatistightvnc.png)
![](netstatfortightvnc.png)

Then PrivEsc-ed by creating a dynamic port forward with ssh tunnel to allow my machines access to the vncviewer: `ssh -D 1080 -L6801:127.0.0.1:5801 -L6901:127.0.0.1:5901 charix@10.129.1.254`
![](vncviewer.png)
![](vncroot.png)

## Log Poisoning properly

With the root shell on vncviewer I cleared the access.log file and reattempted the log poisoning withg the `$_REQUEST` superglobal variable. I tried exactly way Ippsec walked through, but did not get command execution.

![](logfilepoisoning.png)
![](resetandfailed.png)

Reset the mahcine for the third time read from [0xdf's writeup](https://0xdf.gitlab.io/2018/09/08/htb-poison.html) that my first attempt with `$_GET` did work I had just either spammed it or spammed different attempts. 
![](logpoisoning.png)

