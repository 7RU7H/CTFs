# Apex Writeup
Name: Apex
Date:  
Difficulty:  Intermediate
Goals:  OSCP Prep
Learnt:

## Recon

The time to live(TTL) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](OS-ProvingGrounds/Apex/Screenshots/ping.png)

Samba 4.7.6 is safe.

![](smbmap.png)

![](smbclient.png)

The `docs` share contains two pdfs for Openemr, opensource health care records for multiple OSes including Linux. [OpenEMR Wiki](https://www.open-emr.org/wiki/index.php/OpenEMR_Wiki_Home_Page)

![](nmapreadwriteshareweird.png)

![](openemrversionguess.png)

Adding the below to the openemr/ and it not executing the paylaod `version()` verifies that it not 5.0.2 for CVE-2019-16404. Therefore Openemr version is > 5.0.2
```
/interface/forms/eye/js/eye_base.php?providerID=1%27,%274%27,%27title%27
```

Exploit for 5.0.2.1 RCE needs the url changed as it gets redirected .

Fixed cookie issues and then ran into indentation issues, no CVE and not verified. It could actually be a python rabbithole. I did learn some more hardcore python debugging

## Exploit

The actually exploit requires Authenicated RCE, we need to get the MySQL Credentials  another way.

## Foothold

## PrivEsc

      
