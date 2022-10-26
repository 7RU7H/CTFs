# Banzai Walkthroughed
Name: Banzai
Date:  
Difficulty:  
Goals:  OSCP Prep
Learnt: 
- Web is a pyschological rabbit hole for me.
- Try default creds on all ftp 
- port number matter more than I thought in 

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)



![](diffiehellmanvuln.png)  

I downloaded the site, but there is no contact form...

![](noregnacontactform.png)

Found the above very disconcerting as it would hosted elsewhere. Or because its actually the same directory as ftp. I did loads of recon and exploit hunting, but at the end of the day Web is a pyschological rabbit hole for me on top of potential rabbithole.

## Exploit && Foothold

`admin:admin` on ftp... added this to a list things I am an idiot about. This was a massive ouch moment. I am sure there is a Alhazred quote in the back of my mind *"Sometime it just the ftp..."*. 

![](banzaiftpadmin.png)
![]()
Becuase you are `admin` you can upload a shell and due to it also being the web directory use a webshell. 
![](uploadwebshell.png)

It needs to be port 21, because of [[waf-detect-http___192.168.141.56_8295_-apachegeneric]]
![](revshell.png)

## PrivEsc

I decide to close the walkthrough for the next thirty miniutes. Had more trouble connecting back ...

admin user!

![](passinconfigphp.png)

`EscalateRaftHubris123`

/var/log/mail.log - denied

Why is there an mysql and postgres, and the mysql 
![](3306.png)

![](rootprivmysqlproc.png)

