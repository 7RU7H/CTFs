# Brainfuck
Name: Brainfuck
Date:  
Difficulty:  Insane
Description:  Walkthough
Better Description:  [Ippsec Video]()
Goals:  OSCP Prep, Ippsec handholding, but read atleast another method from a written source, if possible try practice on-the-fly python scripting
Learnt: 
- Hosting PHP to request a cookie for my browser to 
- Inspecting Webpage to view dotted password obfuscation in console!


## Recon Pre Video Handheldery

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

Nmap, Certs:
brainfuck.htb
sup3rs3cr3t.brainfuck.htb
![cert](certviewer1.png)


#### brainfuck.htb

Wordpress
orestis@brainfuck.htb
![nuc](nuclei-cve2017-5487.png)

```bash
wpscan --url https://brainfuck.htb -e -o wpscan-brainfuck.htb --disable-tls-checks --api-token $(cat ~/wpscanAPI)
```

There is potential an potential to reset admin's password with email request to controlled email server, then access the Dovecot Pop3 service hosted OR the forum! on Brainfuck or WP get a Web Shell after authenicating into WP.
https://nvd.nist.gov/vuln/detail/CVE-2017-8295



#### sup3rs3cr3t.brainfuck.htb
![usersonforum](usersonforum.png)

![cookies](forumcookies.png)

## Start the Video

Started the [Brainfuck Ippsec Video](https://www.youtube.com/watch?v=o5x1yg3JnYI) as a good hour of scanning and walking each subdomain and testing and gathering information. Paused Video at 6:54, checked [RCE directory exists](https://wpscan.com/vulnerability/85d3126a-34a3-4799-a94b-76d7b835db5f) - Nope.

```bash
grep "includes/admin/attachment/" feroxbuster/brainfuck-raft-small-words
# Nothing..
```
Continued with video, I tried admin@brainfuck.htb to pre-emptively skip a step of trying this exploit. It failed. Changed it to the email found in the Cert and nmap scan.
```html
<form method="post" action="http://wp/wp-admin/admin-ajax.php">
        Username: <input type="text" name="username" value="admin">
        <input type="hidden" name="email" value="admin@brainfuck.htb">
        <input type="hidden" name="action" value="loginGuestFacebook">
        <input type="submit" value="Login">
</form>
```

## Exploit
This was awesome cookie exploit. Simply T(Undescriptive);D(id)R(ead, but hey here is an explanation) the 41006.txt file is that wp_set_auth_cookie can leveraged to force the wp service request the attacker browser set the cookies to the cookies of a logged in user. 

![wow](thatisnuts.png)

Paused the video again and tried to create or edit a wordpress plugin for a web shell; the connection hangs for the former and any changes are lost given that the files on box need to be writable to allow for pasting in a webshell. Continued with the video...

Settings ->  Easy WP SMTP -> 

![](smtpcreds.png)

Never done this before!
```
SMTP:orestis kHGuERB29DNiNE
```
Paused video, research other people OSCP SMTP Cheatsheets to compile into my own. Also want to find a good cli email client that can be scripted instead of evolution. This seemed like a bit of rabbit hole, for another day..

## Returning two days later
![rootsendsmail](rootsendsmail.png)

```
orestis : kIEnnfEKJ#9UmdO
```

Paused here and 
![](cthululanguage.png)

![](sshaccess-noorestis.png)


## Foothold

Best site in the known universe for code-breaking that I found was [boxentriq](https://www.boxentriq.com/). It has analysis tools to attempt to figure out the encoding. Most can perform an autosolve. With autosolve I just changed the defaults to: min key length 1; max key length 16.  It is [Vigenère Cipher](https://www.boxentriq.com/code-breaking/vigenere-cipher), with key  `fuckmybrain`, decoded it is:
```
Hey give me the url for my key bitch

Orestis - Hacking for fun and profit
Vko bwlbbr dgg s zgda nrtkm gy ia...

Toggdxhhu....
Kgivvkr - Ahraeck iqt enu pdz evrhks

There you go you stupid fuck, I hope you remember your key password because I dont
https://10.10.10.17/8ba5aa10e915218697d1c658cdee0bb8/orestis/id_rsa

Sa ewgxcjo, P'hq ngzla wttja nf
Dwwoknu - Owhwxsy bfw hbj fzs ujkwnv
```


Download and crack the SSH key with:
```bash
ssh2john id_rsa > id_rsa_crackable
```

![](crackthesshkey.png)

Remember to `chmod 600 id_rsa`, using the creds:
```
3poulakia!
```

## PrivEsc

Still paused I wanna sharpen some Linux PrivEsc...


![](likesencryption.png)

How random is it...
![](shannon-entropy.png)
Not really potental


![](linux-priv-esc-suggester.png)




      
