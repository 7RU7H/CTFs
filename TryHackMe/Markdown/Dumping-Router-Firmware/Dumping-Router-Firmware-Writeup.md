# Dumping-Router-Firmware Writeup

Name: Dumping-Router-Firmware
Date:  25/5/2024
Difficulty: Medium  
Goals:  
- Busybox BR reverse shells and persistance 
- Firmware Hacking revision
- Sweet points and something different
Learnt:
Beyond Root:

[Sq00ky](https://github.com/Sq00ky)

## Task 1

I decided to use ParrotOS, because of the 2023 changes to Python package on Kali and to continue to try love Parrot as much as I love Kali. 

- Linksys WRT1900ACS v2 Firmware found here: [https://github.com/Sq00ky/Dumping-Router-Firmware-Image/](https://github.com/Sq00ky/Dumping-Router-Firmware-Image/)[](https://www.linksys.com/us/support-article?articleNum=165487)
- Lastly, [ensure binwalk has JFFS2 support with the following command](https://github.com/ReFirmLabs/binwalk/blob/master/INSTALL.md):

Instructions given
```bash
# Installing pips as root is generally a bad idea, but every screenshot is done as root so..
sudo pip install cstruct; 
# git clone https://github.com/sviehb/jefferson; # Its the wrong git!
cd jefferson && sudo python setup.py install

git clone https://github.com/Sq00ky/Dumping-Router-Firmware-Image/ /opt/Dumping-Router-Firmware && cd /opt/Dumping-Router-Firmware/

7z x ./FW_WRT1900ACSV2_2.0.3.201002_prod.zip

echo dbbc9e8673149e79b7fd39482ea95db78bdb585c3fa3613e4f84ca0abcea68a4 && sha256sum FW_WRT1900ACSV2_2.0.3.201002_prod.img
```

What I did that worked... I could not get Jefferson to work with poetry so BREAKING ALL SYSTEM PACKAGES... on a VM so revert back after done.
```bash
cd /tmp
mkdir notes
apt install python3-cstruct binwalk
pip install --user jefferson --break-system-packages
# Jefferson usage:
# jefferson filesystem.img -d outdir
git clone |https://github.com/Sq00ky/Dumping-Router-Firmware-Image.git
cd Dumping-Router-Firmware-Image/
7z x ./FW_WRT1900ACSV2_2.0.3.201002_prod.zip
echo dbbc9e8673149e79b7fd39482ea95db78bdb585c3fa3613e4f84ca0abcea68a4 && sha256sum FW_WRT1900ACSV2_2.0.3.201002_prod.img
cp ../notes/FW_WRT1900ACSV2_2.0.3.201002_prod.img
cd ../notes
```
## Task 2

```bash
# Search each file specified and print any printable character strings found that are at least four characters long and followed by an unprintable character. 
# Often used to find human-readable content within binary files.
# Different strings encoding may reveal different hardcoded credentials on poor designed applications
strings -e s # 7-bit byte (used for ASCII, ISO 8859) - Default
strings -e S # 8-bit byte
strings -e b # 16-bit bigendian
strings -e l # 16-bit littleendian
```

```bash
strings -e s FW_WRT1900ACSV2_2.0.3.201002_prod.img | tee -a fw.strings-s;
strings -e S FW_WRT1900ACSV2_2.0.3.201002_prod.img | tee -a fw.strings-S;
strings -e b FW_WRT1900ACSV2_2.0.3.201002_prod.img | tee -a fw.strings-b;
strings -e l FW_WRT1900ACSV2_2.0.3.201002_prod.img | tee -a fw.strings-l;
more fw.strings-s
```

What does the first clear text line say when running strings on the file?
```
Linksys WRT1900ACS Router
```

Also, using strings, what operating system is the device running?
```bash
# I was trying too hard here, but is just linux
# manual included
linux
```

Scrolling through with strings, you may notice some other interesting lines like `/bin/busybox`
and various other lua files. It really makes you wonder what's going on inside there. `No answer`

Next, we will be dumping the filesystem from the image file. To do so, we will be using a tool called `binwalk`. Binwalk is a tool that checks for well-known file signatures within a given file. This can be useful for many things; it even has its uses in Steganography. A file could be hidden within the photo, and Binwalk would reveal that and help us extract it. We will be using it to extract the filesystem of the router in this instance. `No answer`

```bash
# Just because..
cat fw.strings-s | grep '.lua' | tee -a lua-files-from-fwe-strings.txt
cat fw.strings-s | grep '/bin' | tee -a bin-files-from-fwe-strings.txt
```

What option within Binwalk will allow us to extract files from the firmware image?
```bash
-e # guessed
```

```bash
binwalk --run-as=root -e FW_WRT1900ACSV2_2.0.3.201002_prod.img
binwalk --run-as=root -e FW_WRT1900ACSV2_2.0.3.201002_prod.img | tee -a binwalk-output-e.binwalk
```

I checked https://medium.com/@Akv0x/tryhackme-dumping-router-firmware-akv0x-2c55e47158e4, because I thought it was a file that had to be the first item


Now that we know how to extract the contents of the firmware image, what was the first item extracted?
```
uImage header
```
What was the creation date?
```
2020-04-22 11:07:2
```
The Cyclical Redundancy Check is used similarly to file hashing to ensure that the file contents were not corrupted and/or modified in transit. What is the CRC of the **image**?
```
 0xABEBC43
```

What is the image size?
```
4229755 bytes
```
What architecture does the device run?
```
ARM
```
Researching the results to question 10, is that true?
```
yes # I already OSINTed the manual
```

You will notice two files got extracted, one being the jffs2 file system and another that Binwalk believes in gzipping compressed data. You can attempt to extract the data, but you won't get anywhere. Binwalk misinterpreted the data. However, we can still do some analysis of it. Running strings on 6870, we notice a large chunk of clear text. We can actually rerun binwalk on this file to receive even more files to investigate. Interestingly enough, a copy of the Linux kernel is included. What version is it for?

```bash
# Only one digit because that is more realistic than going by the *.**.**
cat 6870.strings-s | grep -e '[0-9].'
```


```
3.10.39
```


Suppose you extract the contents of 6870 with Binwalk and run strings on 799E38.cpio, you may see a lot of hex towards the bottom of the file. Some of it can be translated into human-readable text. Some of it is interesting and makes you wonder about its purpose. Some additional investigation may reveal its purpose. I will leave you to explore that on your own, though :) `No answer` ...Continuing with the analysis, we have a jffs2 file system that we can examine the contents of. First, we must mount it, bringing us to the next section. `No answer`
![](extracting6870.png)



```
```


## Task 3

Step 1. If /dev/mtdblock0 exists, remove the file/directory and re-create the block device
`rm -rf /dev/mtdblock0`
`mknod /dev/mtdblock0 b 31 0`
Step 2. Create a location for the jffs2 filesysystem to live  
`mkdir /mnt/jffs2_file/`
Step 3. Load required kernel modules
`modprobe jffs2`
`modprobe mtdram`
`modprobe mtdblock`
Step 4. Write image to /dev/mtdblock0  
`dd if=/opt/Dumping-Router-Firmware-Image/_FW_WRT1900ACSV2_2.0.3.201002_prod.img.extracted/600000.jffs2 of=/dev/mtdblock0`
Step 5. Mount file system to folder location
`mount -t jffs2 /dev/mtdblock0 /mnt/jffs2_file/`  
Step 6. Lastly, move into the mounted filesystem.
`cd /mnt/jffs2_file/`

What I did:

Create a block device (mtdblock ([Memory Technology Device](https://en.wikipedia.org/wiki/Memory_Technology_Device))) that will allow us to dump the flash memory.


```bash
ls -la /dev/ | grep mtdblock0
# rm -rf /dev/mtdblock0 # if exists
mknod /dev/mtdblock0 b 31 0
mkdir /mnt/jffs2_file/
modprobe jffs2
modprobe mtdram
modprobe mtdblock
dd if=/tmp/notes/_FW_WRT1900ACSV2_2.0.3.201002_prod.img.extracted/600000.jffs2 of=/dev/mtdblock0
mount -t jffs2 /dev/mtdblock0 /mnt/jffs2_file/
cd /mnt/jffs2_file/
```
![](intotask3.png)


Running an `ls -la `reveals a lot of interesting information. First, we notice that many files are symbolically linked (similar to a shortcut). Where does `linuxrc` link to?  
```
bin/busybox
```

What parent folder do mnt, opt, and var link to?
```
/tmp/
```
What folder would store the router's HTTP server?
```
/www/
```


Scanning through a lot of these folders, you may begin to notice that they are empty. This is extremely strange, but that is because the router is not up and running. Remember, we are merely looking at a template of the filesystem that will be flashed onto the router, not the firmware from a router that has been dumped. Other information about the router may be contained in the previous section within the 6870 block. The first of the folders that aren't empty is /bin/; where do a majority of the files link to?
```
busybox
```

Why is that? Well, [busybox is more or less a tool suite of common executable commands within the Unix environment](https://ubuntuforums.org/archive/index.php/t-846852.html).

Interestingly, what database would be running within the bin folder if the router was online?  
```
sqlite3 # mysql related are just scripts to use mariadb for compatibility probably
```
The following notable folder of interest is /etc/. This folder contains many configuration files for the router, such as Access Point power levels regulated by certain countries. One you might recognize is the FCC (Federal Communications Commission).


We can even see the build date of the device. What is the build date? 
```bash
cat etc/builddate
# answer:
2020-04-22 11:44
```
There are even files related to the SSH server on the device. What SSH server does the machine run?
```bash
ls -la etc/ | grep ss
dropbear
```

Dropbears are not real. Dropbears are not real. Dropbears are not real.  The only reason I know that dropbear ssh servers is from some old exploit or vulnerability that happened and then someone tried to lie to me about dropbears for a joke for some southern hemisphere related reason.

We can even see the file for the media server, which company developed it? This company use to own Linksys at one point in time, which is likely why it is still being used.
```
# etc/mediaserver.ini
Cisco
```

Which file within /etc/ contains a list of standard Network services and their associated port numbers?
```
services
```

Which file contains the default system settings?
```
system_defaults
```
What is the specific firmware version within the /etc/ folder?
```
2.0.3.201002
```

Backing out into the JNAP folder, the JNAP API (formerly known as HNAP, the Home Network Administration Protocol) has been a potential attack vector and vulnerability in the past, which this article highlights [here](https://routersecurity.org/hnap.php). Interestingly enough, reminisce of it is still here today on Linksys devices. Going to http://<Default_Gateway>/JNAP/ on a Linksys router reveals an interesting 404. Much different than the standard 404.  

**Accessing /JNAP/**

![](https://i.imgur.com/vYy5Usa.png)

**Accessing any other invalid URI**

![](https://i.imgur.com/ASkUsj5.png)

This makes you wonder if something is still really there. If you investigate within /JNAP/modules folder back on the dumped filesystem, you will see some contents related to the device and what services it offers, some of them are firewalls, http proxies, QoS, VPN servers, uPnP, SMB, MAC filtering, FTP, etc. **Side note:** If you have a Linksys router and are interested in playing around further, I found this [Github Repository](https://github.com/jakekara/jnap) for tools to interact with JNAP, I chose not to include this within the room since not everyone has access to a Linksys router. I won't go much further than exploring the File System.   

  

What three networks have a folder within /JNAP/modules?
```
guest_lan, lan, wan
```


After the JNAP folder, lib is the only other folder with any contents whatsoever, and what's in there is standard in terms of libraries. The rest of the file system is relatively bare, leading us to this room's end.

I hope I made you all more curious about what's happening in your device; most importantly, I hope you enjoyed it. I encourage all of you to go out on your own and get your own router's Firmware, do some firmware dumping, and look at what's happening inside your device.

A room about Cable Modems may come in the future. However, Cable Modems firmware images are relatively difficult to access since they are only distributed to CMOs (Cable Modem Operators, like Charter, Xfinity, Cox, etc.) `No answer`

## Beyond Root

At some point I had some nasty reverse shell trying to execute on something and it lead me on a busybox BR 

https://linux.die.net/man/1/busybox oneliner alternative stagers
```

```

busybox persistance

busybox reverse shells