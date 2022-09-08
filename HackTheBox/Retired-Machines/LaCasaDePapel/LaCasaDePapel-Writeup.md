# LaCasaDePapel
Name: LaCasaDePapel
Date:  
Difficulty:  Easy
Goals:  OSCP prep
Learnt:

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

![](ssl.png)

Returning to this 

Nuclei found the vsftp version a displayed that there is a backdoor in this version of the application [rapid7](https://www.rapid7.com/db/modules/exploit/unix/ftp/vsftpd_234_backdoor/)

![](vsftg.png)


Due to my goals I will not use the metasploit version of this exploit, instead the public exploit avaliable on [exploitdb](https://www.exploit-db.com/exploits/49757). This does not work as the deoding of the telnetlib.interact does not decode. Then tried [ahervias](https://github.com/ahervias77/vsftpd-2.3.4-exploit/blob/master/vsftpd_234_exploit.py)

![](ahervias.png)




## Exploit


	


## Foothold

## PrivEsc

      
