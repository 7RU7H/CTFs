# Networked Writeup
Name: Networked
Date:  
Difficulty:  Easy
Goals:  OSCP Prep
Learnt:

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](OS-ProvingGrounds/Apex/Screenshots/ping.png)

![](ferox.png)

[Nuclei detected a WAF](obsidian://open?vault=CTFs&file=HackTheBox%2FRetired-Machines%2FNetworked%2Fnuclei%2Fwaf-detect-http___10.129.4.156_-apachegeneric)

![](facemash.png)

![](backupdottar.png)

```bash
tar xvf backup.tar
```

![](insidethebackup.png)

lib.php

- file_mime_type

```
'/^([a-z\-]+\/[a-z0-9\-\.\+]+)(;\s.+)?$/'
```

- getnameCheck will:
	removes extension
- getnameUpload

mime type must be `image/`


## Exploit

## Foothold

## PrivEsc

      
