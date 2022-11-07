
Name: Hololive
Date:  
Difficulty: Hard
Description: Holo is an Active Directory (AD) and Web-App attack lab that aims to teach core web attack vectors and more advanced AD attack techniques. This network simulates an external penetration test on a corporate network. 
Better Description:  
Goals: OSCP revision, demonstrate how far I have come since Throwback..
Learnt:
- 

Returning to this after lots of Hack the Box I decided to follow along with [Alh4zr3d](https://www.youtube.com/watch?v=PqnnKMU3XMk) purely to fit with my work schedule that include another siz hours plus after this. I need mentorship and I need practical experience that is less-head-smashing-problem-solving-stress to pace out the the marathon of learning everything for the exam I need. Some humor and Cthulu mythos is great addition to my day. 

## Initial Recon

The inital nmap:
![initnmap](init-recon-nmap-webserver.png)

![nikto](nikto-init-webserver.png)

#### Task 8 Answers

What is the last octet of the IP address of the public-facing web server?
```
33
```
How many ports are open on the web server?
```
3
```
What CME is running on port 80 of the web server?
```
wordpress
```
What version of the CME is running on port 80 of the web server?
```
5.5.3
```
What is the HTTP title of the web server?
```
holo.live
```

## Web App Exploitation

#### Task 9 Answers

![task9](Screenshots/gobuster-task9.png)

```bash
gobuster vhost -u http://holo.live -w /usr/share/amass/wordlists/subdomains-top1mil-110000.txt
```

What domains loads images on the first web page?
```
www.holo.live
```
What are the two other domains present on the web server? Format: Alphabetical Order
```
admin.holo.live, dev.holo.live
```

```bash
git clone https://github.com/danielmiessler/SecLists.git
```


#### Task 10

![](localpathwp.png)

What file leaks the web server's current directory?

```
robots.txt
```

What file loads images for the development domain?

`dev.holo.live/img.php?file=images/$file.jpg`

```
img.php
```

What is the full path of the credentials file on the administrator domain?

`admin.hole.live/robots.txt`

```
/var/www/admin/supersecretdir/creds.txt
```

#### Task 11

I suggest Learning about Ansible and Packer or you could use test file inclusion 
`dev.holo.live/img.php?file=images/$file.jpg`

Downloading a Ubuntu server and going back to a ToDo list of mine in the background...

![1000](fileinclusion.png)

#### Task 12

What file is vulnerable to LFI on the development domain?
```
img.php
```
What parameter in the file is vulnerable to LFI?
```
file
```

What file found from the information leak returns an HTTP error code 403 on the administrator domain?

- I missed this because already tried then went back to use the lfi while I download a ubuntu server to try build some Vulnerable and Safe VMs machines with packer

```
/var/www/admin/supersecretdir/creds.txt
```

Using LFI on the development domain read the above file. What are the credentials found from the file?
```
admin:DBManagerLogin
```

Because the lfi you can then grab all the files!

![](passwordchanges.png)

![](vistors.png)

[Alh4zr3d](https://www.youtube.com/channel/UCz-Z-d2VPQXHGkch0-_KovA) spotted this I just LFI rather than log in
![1000](alspottedthis.png)

#### Task 13

![](vulnerabledashboard.png)

What file is vulnerable to RCE on the administrator domain?

```
dashboard.php
```

What parameter is vulnerable to RCE on the administrator domain?

```
cmd
```

What user is the web server running as?

```
www-data
```

At 2:11:42 - https://www.youtube.com/watch?v=PqnnKMU3XMk - I am a ahead